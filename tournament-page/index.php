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
    ?>
        <div class="layout__content">
            <section class="tournament">
                <div class="container">

                    <div class="row justify-content-center">
                        <div class="col-lg-11 col-md-12">
                            <div class="layout__content-heading-with-btn-back">
                                <a href="/game-schedule/" class="btn-italic-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 11.62">
                                        <path d="M955.22,534.89a1,1,0,0,1,0,1.33l-3,3.27h18.2a.94.94,0,0,1,0,1.88h-18.2l3,3.27a1,1,0,0,1,0,1.32.81.81,0,0,1-1.21,0l-4.49-4.88h0a1,1,0,0,1,0-1.33h0l4.49-4.87A.81.81,0,0,1,955.22,534.89Z" transform="translate(-949.26 -534.62)"/>
                                    </svg> Назад в расписание
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
                                                    <div>Режим</div>
                                                    <div>Squad</div>
                                                </div>
                                            </div>
                                            <div class="tournament-info__type tournament-info__type_next">
                                                <div class="tournament-info__type-icon tournament-info__type-icon_league">
                                                    <i></i>
                                                </div>
                                                <div class="tournament-info__mode">
                                                    <div>Тип</div>
                                                    <div>League</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <ul class="tournament-info__list">
                                            <li>
                                                <div class="tournament-info__list-type">
                                                    Тип:
                                                </div>
                                                <div class="tournament-info__list-description">
                                                    <div class="tournament-info__list-description-league">
                                                        <span>League</span><i></i>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="tournament-info__list-type">
                                                    Статус:
                                                </div>
                                                <?php
                                                 $tournamentDates = getTournamentPeriod($tournamentId);
                                                 if ($tournamentDates){

                                                 }?>
                                                <div class="tournament-info__list-description">
                                                    Идёт регистрация до 23 апреля 2021, 15:00
                                                </div>
                                            </li>
                                            <li>
                                                <div class="tournament-info__list-type">
                                                    Призовой фонд:
                                                </div>
                                                <div class="tournament-info__list-description">
                                                    € 1080
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
                                                                    <a href="/teams/<?php echo $teamID ?>/" class="match-participants__team-link"><?php echo $team["NAME_TEAM"]["VALUE"]. " [".$team["TAG_TEAM"]["VALUE"]; ?>]</a>
                                                                </div>
                                                            </div>
                                                            <a href="#" class="btn__change">Изменить состав</a>
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
                                                        <a href="#" class="tournament-info__link"><?php echo $nextGame["STAGE_TOURNAMENT"]["VALUE"] ?>, Группа №65,</a> <?php echo $nextTime ?>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <div class="tournament-info__action">
                                            <?php if (isCaptain($userID, $teamID)){ ?>
                                            <?php if (!$nextGameID){ ?>
                                                    <div><a href="/tournament-page/join-game/?mid=random&tournament=<?php echo $tournamentId ?>" class="btn">Подать заявку</a></div>
                                                <?php } else { ?>
                                                    <div><a href="/tournament-page/join-game/?mid=random&tournament=<?php echo $tournamentId ?>" class="btn-change-big">Отменить участие</a></div>
                                                <?php }
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
                                    "SORT_BY2" => "SORT",
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
                            <div>€ 340</div>
                        </div>
                        <div class="top-places__item">
                            <div>2 место</div>
                            <div>€ 280</div>
                        </div>
                        <div class="top-places__item">
                            <div>3 место</div>
                            <div>€ 220</div>
                        </div>
                        <div class="top-places__item">
                            <div>4 место</div>
                            <div>€ 160</div>
                        </div>
                        <div class="top-places__item">
                            <div>5 место</div>
                            <div>€ 80</div>
                        </div>

                    </div>
                </div>
            </section>
        </div>
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
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

