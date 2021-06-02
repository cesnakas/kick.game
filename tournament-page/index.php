<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Tournament Page");
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];
$tournamentId = $_GET["tournamentID"];
?>
<?php if ($tournamentId) {
    $tournament = getTournamentById($tournamentId);
}

function isCaptain($idUser, $idTeam)
{
    if ($idTeam) {
        $resTeam = getTeamById($idTeam);
        if ($resTeam['AUTHOR']["VALUE"] == $idUser) {
            return true;
        } else {
            return false;
        }
    }
    return  false;
}

function getPropertyPlace($idMatch)
{
    $arSelect = Array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_*",
    );
    $arFilter = Array(
        "IBLOCK_ID" =>4,
        "PROPERTY_WHICH_MATCH" => $idMatch,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $arrTeams = [];
    $key = getPlacesKeys();
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        foreach ($key as $place=>$name) {
            if ($arProps[$name]['VALUE']+0 > 0) {
                $arrTeams[$name] = $arProps[$name]['VALUE'];
            }
        }
        return $arrTeams;
    }
    return  false;
}

// получаем цепочку матчей по id родителя
function getMatchByParentId($parentId) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" => 3, "PROPERTY_PREV_MATCH" => $parentId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

function updateMembers($props = [], $id)
{
    CIBlockElement::SetPropertyValuesEx($id, 4, $props);
}

function removeMatchMember($matchID, $teamID){

    $idNextMatch = $matchID;
    $chainMatches[] = $matchID;

    do {
        $match = getMatchByParentId( $idNextMatch);
        if ($match != null) {
            $nextMatch = true;
            $chainMatches[] = $match['ID'];
            $idNextMatch = $match['ID'];
        } else {
            $nextMatch = false;
        }
    } while($nextMatch == true);


    if ($propertyPlace = getPropertyPlace($chainMatches)) {
        $propertyPlace = array_flip($propertyPlace);
        $propertyPlace = $propertyPlace[$teamID];
        // есть цепочка матчей тут $chainMatches
        // получаем участников матчей
        $resMembersMatches = getMembersByMatchId($chainMatches);
        // создаем массив id записей участников
        $membersMatches = [];
        // если пришел список участников
        if ($resMembersMatches) {
            foreach ($resMembersMatches as $membersMatch) {
                // наполняем $membersMatches
                $membersMatches[] = $membersMatch['ID'];
            }

            foreach ($membersMatches as $id) {
                $props = [];
                $props[$propertyPlace] = null;
                // бежим по записям и удаляем нашу команду с места
                updateMembers($props, $id);
            }
        }


    }
}

function getSquadByIdMatch($idMatch, $idTeam)
{
    $arSelect = Array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_*",
    );
    $arFilter = Array(
        "IBLOCK_ID" =>6,
        "PROPERTY_MATCH_STAGE_ONE" => $idMatch,
        "PROPERTY_TEAM_ID" => $idTeam,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $squad = [];
    if($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $squad = array_merge($arFields, $arProps);
        return $squad;
    }
    return false;
}

function addMatchMember ($matchID, $teamID){

    $idNextMatch = $matchID;
    $chainMatches[] = $matchID;

    do {
        $match = getMatchByParentId( $idNextMatch);
        if ($match != null) {
            $nextMatch = true;
            $chainMatches[] = $match['ID'];
            $idNextMatch = $match['ID'];
        } else {
            $nextMatch = false;
        }
    } while($nextMatch == true);

    // получаем участников матчей
    $resMembersMatches = getMembersByMatchId($chainMatches);
    $membersMatches = [];

    if ($resMembersMatches) {
        foreach ($resMembersMatches as $membersMatch) {
            $membersMatches[] = $membersMatch['ID'];
        }
    }

    $match = getMembersByMatchId($chainMatches[0]);
    $match = $match[0];

    $propertiesCases = getPlacesKeys();

    $emptyPlace = false;
    foreach ($propertiesCases as $case) {
        if ($match[$case]+0 == 0) {
            $emptyPlace = $case;
            break;
        }
    }

    // сделать проверку, что моей команды еще нет в участниках матча, если моя команду существует в этом матче, то $emptyplace = falce
    if ($emptyPlace != false) {
        foreach ($membersMatches as $membersMatchId) {
            CIBlockElement::SetPropertyValues($membersMatchId, 4, $teamID, $emptyPlace);
        }
    }
}

function getMatchById($matchId) {
    $arSelect = Array("ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_STREAMER.NAME",
        "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" =>3, "ID" => $matchId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}
$mId = findFreeGame($tournamentId);

$props["TEAM_ID"] = $teamID;
$props["MATCH_STAGE_ONE"] = $_POST["idMatch"];

if (isset($_POST["changeGame"])){

    if($_POST["idMatch"]){
        $prevGame = getNextGame($teamID, $tournamentId);
        $isMoved = moveSquad($props, $prevGame);
        if($isMoved){
        addMatchMember($_POST["idMatch"], $teamID);
        removeMatchMember($prevGame, $teamID);
            $alertTournamentPage = "Ты успешно сменил группу";
            createSession('tournament-page_success', $alertTournamentPage);
        } else {
            $alertTournamentPage = "Ты опоздал, в этой группе не осталось мест";
            createSession('tournament-page_error', $alertTournamentPage);
        }
        LocalRedirect(SITE_DIR."tournament-page/?tournamentID=".$tournamentId);
    }
}
    ?>
<?php
if(isset($_SESSION['tournament-page_success'])) { ?>
    <div class="alert-container">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['tournament-page_success'];?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php
    unset($_SESSION['tournament-page_success']);
} else if(isset($_SESSION['tournament-page_error'])){ ?>
    <div class="alert-container">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['tournament-page_error'];?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php }
unset($_SESSION['tournament-page_error']);
?>
            <section class="tournament">
                <div class="container">

                    <div class="row justify-content-center">
                        <div class="col-lg-11 col-md-12">
                            <div class="layout__content-heading-with-btn-back">
                                <a href="<?=SITE_DIR?>game-schedule/" class="btn-italic-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 11.62">
                                        <path d="M955.22,534.89a1,1,0,0,1,0,1.33l-3,3.27h18.2a.94.94,0,0,1,0,1.88h-18.2l3,3.27a1,1,0,0,1,0,1.32.81.81,0,0,1-1.21,0l-4.49-4.88h0a1,1,0,0,1,0-1.33h0l4.49-4.87A.81.81,0,0,1,955.22,534.89Z" transform="translate(-949.26 -534.62)"/>
                                    </svg><?=GetMessage('TP_BACK')?>
                                </a>
                                <h1 class="text-center tournament__heading"><?php echo $tournament['NAME'] ?></h1>
                            </div>
                            <div class="tournament__img-bg">
                                <div class="tournament__img" style="background-image: url(<?php echo CFile::GetPath($tournament["PREVIEW_PICTURE"]); ?>)">
                                    <!--<div class="tournament__img-rating-bg">
                                      <div class="tournament__img-rating">1000 - 3000</div>
                                    </div>-->
                                </div>
                            </div>
                            <div class="tournament-info">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="tournament-info__type-wrap">
                                            <div class="tournament-info__type">
                                                <div class="tournament-info__type-icon tournament-info__type-icon_tournament">
                                                    <i></i>
                                                </div>
                                                <div class="tournament-info__mode">
                                                    <div><?=GetMessage('TP_MODE')?></div>
                                                    <div>Squad</div>
                                                </div>
                                            </div>
                                            <div class="tournament-info__type tournament-info__type_next">
                                                <div class="tournament-info__type-icon tournament-info__type-icon_league">
                                                    <i></i>
                                                </div>
                                                <div class="tournament-info__mode">
                                                    <div><?=GetMessage('TP_TYPE')?></div>
                                                    <div><?=GetMessage('TP_LEAGUE')?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <ul class="tournament-info__list">
                                            <li>
                                                <div class="tournament-info__list-type">
                                                    <?=GetMessage('TP_MODE')?>:
                                                </div>
                                                <div class="tournament-info__list-description">
                                                    <div class="tournament-info__list-description-league">
                                                        <span><?=GetMessage('TP_LEAGUE')?></span><i></i>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="tournament-info__list-type">
                                                    <?=GetMessage('TP_STATUS')?>:
                                                </div>
                                                <?php
                                                 $tournamentDates = getTournamentPeriod($tournamentId);
                                                 if ($tournamentDates){

                                                 }?>
                                                <div class="tournament-info__list-description">
                                                    <?=GetMessage('TP_REGISTRATION')?>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="tournament-info__list-type">
                                                    <?=GetMessage('TP_FUND')?>:
                                                </div>
                                                <div class="tournament-info__list-description">
                                                    € 3000
                                                </div>
                                            </li>
                                            <?php

                                            if($nextGameID = getNextGame($teamID, $tournamentId)){
                                                $nextGame = getMatchById($nextGameID);
                                                //dump($nextGame["DATE_START"]["VALUE"]);
                                                $nextTime = formDate($nextGame["DATE_START"]["VALUE"], "d MMMM yyyy, HH:mm");
                                                $team = getTeamById($teamID);

                                                ?>
                                                <li>
                                                    <div class="tournament-info__list-type">
                                                        Моя команда:
                                                    </div>
                                                    <div class="tournament-info__list-description">
                                                        <div class="tournament-info__team">
                                                            <div class="match-participants__team">

                                                                <div class="match-participants__team-logo" style="background-image: url(<?php echo CFile::GetPath($team["LOGO_TEAM"]["VALUE"]); ?>)">
                                                                </div>
                                                                <div>
                                                                    <a href="<?=SITE_DIR?>teams/<?php echo $teamID ?>/" class="match-participants__team-link"><?php echo $team["NAME_TEAM"]["VALUE"]. " [".$team["TAG_TEAM"]["VALUE"]; ?>]</a>
                                                                </div>
                                                            </div>
                                                            <?php if (strtotime($nextGame["DATE_START"]["VALUE"]) > time()){ ?>
                                                                <a href="<?=SITE_DIR?>tournament-page/join-game/?mid=<?php echo $nextGameID;?>" class="btn__change">Изменить состав</a>
                                                          <?php  } ?>

                                                        </div>


                                                    </div>
                                                </li>
                                                <li>


                                                    <?php if (strtotime($nextGame["DATE_START"]["VALUE"]) > time()){ ?>

                                                    <div class="tournament-info__list-type">
                                                        Моя следующая игра:
                                                    </div>

                                                    <?php } else { ?>

                                                        <div class="tournament-info__list-type">
                                                            Моя предыдущая игра:
                                                        </div>

                                                        <?php } ?>

                                                    <div class="tournament-info__list-description">
                                                        <a href="#" class="tournament-info__link"><?php echo $nextGame["STAGE_TOURNAMENT"]["VALUE"] ?>, Группа №<?php echo $nextGame["GROUP"]["VALUE"] ?>,</a> <?php echo $nextTime ?>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <div class="tournament-info__action">
                                            <?php if (isCaptain($userID, $teamID)){ ?>
                                            <?php if (!$nextGameID){ ?>
                                                    <div><a href="/tournament-page/join-game/?mid=<?php echo $mId;?>" class="btn">Подать заявку</a></div>
                                                <?php } else {
                                                    if (strtotime($nextGame["DATE_START"]["VALUE"]) > time()) {
                                                        ?>
                                                    <div><a href="/tournament-page/join-game/?mid=<?php echo $nextGameID;?>" class="btn-change-big">Отменить участие</a></div>
                                                <?php }
                                                }
                                             } ?>
                                            <div><a href="#" class="btn-italic-dotted" data-toggle="modal" data-target="#regulation">Регламент/Правила участия</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?$curDate = date('Y-m-d H:i:s', time());
                            $finalsDate = date('Y-m-d H:i:s', time()-(3600*24*3));
                            GLOBAL $arrFilterDateTime;
                            $arrFilterDateTime=Array(
                                "ACTIVE" => "Y",
                                "PROPERTY_TYPE_MATCH" => 5,
                                "PROPERTY_PREV_MATCH" => false,
                                "PROPERTY_COUTN_TEAMS" => 4,
                                "PROPERTY_TOURNAMENT" => $tournamentId
                                //"PROPERTY_TYPE_MATCH" => 5
                                //"PROPERTY_STAGE_TOURNAMENT" => 4,
                                //"!=PROPERTY_TOURNAMENT" => false, // турниры
                                //"=PROPERTY_TOURNAMENT" => false, // праки
                            );

                            $APPLICATION->IncludeComponent(
                                "bitrix:news.list",
                                "tournament-page",
                                array(
                                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                                    "ADD_SECTIONS_CHAIN" => "N",
                                    "AJAX_MODE" => "N",
                                    "AJAX_OPTION_ADDITIONAL" => "",
                                    "AJAX_OPTION_HISTORY" => "N",
                                    "AJAX_OPTION_JUMP" => "N",
                                    "AJAX_OPTION_STYLE" => "Y",
                                    "CACHE_FILTER" => "N",
                                    "CACHE_GROUPS" => "Y",
                                    "CACHE_TIME" => "36000000",
                                    "CACHE_TYPE" => "A",
                                    "CHECK_DATES" => "Y",
                                    "DETAIL_URL" => "",
                                    "DISPLAY_BOTTOM_PAGER" => "N",
                                    "DISPLAY_DATE" => "Y",
                                    "DISPLAY_NAME" => "Y",
                                    "DISPLAY_PICTURE" => "Y",
                                    "DISPLAY_PREVIEW_TEXT" => "Y",
                                    "DISPLAY_TOP_PAGER" => "Y",
                                    "FIELD_CODE" => array(
                                        0 => "",
                                        1 => "PROPERTY_TOURNAMENT.NAME",
                                        2 => "PROPERTY_TOURNAMENT.DETAIL_PICTURE",
                                        3 => "PROPERTY_TOURNAMENT.DETAIL_PAGE_URL",
                                        4 => "PROPERTY_STREAMER.NAME",
                                        5 => "",
                                    ),
                                    "FILTER_NAME" => "arrFilterDateTime",
                                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                                    "IBLOCK_ID" => "3",
                                    "IBLOCK_TYPE" => "matches",
                                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                                    "INCLUDE_SUBSECTIONS" => "Y",
                                    "MESSAGE_404" => "",
                                    "NEWS_COUNT" => "1000",
                                    "PAGER_BASE_LINK_ENABLE" => "N",
                                    "PAGER_DESC_NUMBERING" => "N",
                                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                                    "PAGER_SHOW_ALL" => "N",
                                    "PAGER_SHOW_ALWAYS" => "N",
                                    "PAGER_TEMPLATE" => "ajax_pager",
                                    "PAGER_TITLE" => "Новости",
                                    "PARENT_SECTION" => "",
                                    "PARENT_SECTION_CODE" => "",
                                    "PREVIEW_TRUNCATE_LEN" => "",
                                    "PROPERTY_CODE" => array(
                                        0 => "PUBG_LOBBY_ID",
                                        1 => "DATE_START",
                                        2 => "TYPE_MATCH",
                                        3 => "TOURNAMENT",
                                        4 => "",
                                    ),
                                    "SET_BROWSER_TITLE" => "N",
                                    "SET_LAST_MODIFIED" => "N",
                                    "SET_META_DESCRIPTION" => "N",
                                    "SET_META_KEYWORDS" => "N",
                                    "SET_STATUS_404" => "Y",
                                    "SET_TITLE" => "N",
                                    "SHOW_404" => "Y",
                                    "SORT_BY1" => "PROPERTY_DATE_START",
                                    "SORT_BY2" => "GROUP",
                                    "SORT_ORDER1" => "ASC",
                                    "SORT_ORDER2" => "ASC",
                                    "STRICT_SECTION_CHECK" => "N",
                                    "COMPONENT_TEMPLATE" => "tournament-page",
                                    "FILE_404" => ""
                                ),
                                false
                            );?>
                        </div>
                    </div>

                </div>
            </section>
            <section class="top-places bg-blue-lighter">
                <div class="container">
                    <h2 class="top-places__heading text-center">Призовые места</h2>
                    <div class="top-places-wrap">
                        <div class="top-places__item">
                            <div>1 место</div>
                            <div>€ 960</div>
                        </div>
                        <div class="top-places__item">
                            <div>2 место</div>
                            <div>€ 600</div>
                        </div>
                        <div class="top-places__item">
                            <div>3 место</div>
                            <div>€ 360</div>
                        </div>
                        <div class="top-places__item">
                            <div>4 место</div>
                            <div>€ 300</div>
                        </div>
                        <div class="top-places__item">
                            <div>5 место</div>
                            <div>€ 180</div>
                        </div>

                    </div>
                </div>
            </section>

    <div class="modal fade " id="regulation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-modal-close" data-dismiss="modal" aria-label="Close">
                        <i></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 class="modal-body__title">Регламент/Правила участия в турнире</h3>
                    <div class="modal-body__content mb-3">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam animi architecto aut, dignissimos distinctio, dolore eum expedita facere facilis fugiat hic nisi non obcaecati, pariatur quae similique vitae! Accusamus, inventore?</p>
                        <ul>
                            <li>Lorem ipsum dolor sit amet.</li>
                            <li>Lorem ipsum dolor sit amet.</li>
                            <li>Lorem ipsum dolor sit amet.</li>
                            <li>Lorem ipsum dolor sit amet.</li>
                        </ul>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A ad, adipisci aspernatur assumenda corporis ipsum laboriosam magni maiores, minima possimus quasi quos ut, voluptas? Id placeat quisquam quos! Architecto atque, deserunt ducimus est incidunt laboriosam possimus quo ratione vitae. Blanditiis commodi cumque dignissimos, dolore ducimus eligendi eos explicabo fugiat hic illo impedit in ipsam iste magni molestias mollitia possimus praesentium quod recusandae reiciendis saepe similique sunt vero, voluptatem voluptates? Ad adipisci architecto aut debitis, dolorem dolores dolorum enim error et excepturi itaque iure iusto magnam minima molestiae nobis odit porro quisquam quod, similique soluta sunt suscipit ut veniam voluptatem. Minus.</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A ad, adipisci aspernatur assumenda corporis ipsum laboriosam magni maiores, minima possimus quasi quos ut, voluptas? Id placeat quisquam quos! Architecto atque, deserunt ducimus est incidunt laboriosam possimus quo ratione vitae. Blanditiis commodi cumque dignissimos, dolore ducimus eligendi eos explicabo fugiat hic illo impedit in ipsam iste magni molestias mollitia possimus praesentium quod recusandae reiciendis saepe similique sunt vero, voluptatem voluptates? Ad adipisci architecto aut debitis, dolorem dolores dolorum enim error et excepturi itaque iure iusto magnam minima molestiae nobis odit porro quisquam quod, similique soluta sunt suscipit ut veniam voluptatem. Minus.</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A ad, adipisci aspernatur assumenda corporis ipsum laboriosam magni maiores, minima possimus quasi quos ut, voluptas? Id placeat quisquam quos! Architecto atque, deserunt ducimus est incidunt laboriosam possimus quo ratione vitae. Blanditiis commodi cumque dignissimos, dolore ducimus eligendi eos explicabo fugiat hic illo impedit in ipsam iste magni molestias mollitia possimus praesentium quod recusandae reiciendis saepe similique sunt vero, voluptatem voluptates? Ad adipisci architecto aut debitis, dolorem dolores dolorum enim error et excepturi itaque iure iusto magnam minima molestiae nobis odit porro quisquam quod, similique soluta sunt suscipit ut veniam voluptatem. Minus.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>

