<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);

// получаем текушего пользователя
// дергаем у него поле id команды

$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];

// получаем матч по id
function getMatchById($matchId) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" =>3, "ID" => $matchId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}


function isTournament($idMatch)
{
    $match = getMatchById($idMatch);
    if ($match) {
        $idTournament = $match['TOURNAMENT']['VALUE']+0;
        if ($idTournament > 0) {
            return $idTournament;
        }
        return false;
    }
    return null;
}

function getMatchesByDate($date, $minRating, $maxRating) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "CODE", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше

    $arOrder = array(
        "SORT"=>"ASC"
    );

    $arFilter = Array(
        "IBLOCK_ID" =>3,
        "PROPERTY_PREV_MATCH" => false,
        "PROPERTY_TYPE_MATCH" => 6,
        "PROPERTY_MIN_RATING" => $minRating,
        "PROPERTY_MAX_RATING" => $maxRating,
        "PROPERTY_DATE_START" => ConvertDateTime($date, "YYYY-MM-DD HH:MI:SS"),
        //  "<=PROPERTY_DATE_START" => ConvertDateTime($date, "YYYY-MM-DD")." 23:59:59",
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $output = [];

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        //$arProps = $ob->GetProperties();
        $output[] = $arFields;
    }
    return $output;
}

function getAvailableGroup($arItem) {
    $diff =  strtotime($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]) - time();

    $matches = getMatchesByDate($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], $arItem["PROPERTIES"]['MIN_RATING']["VALUE"], $arItem["PROPERTIES"]['MAX_RATING']["VALUE"]);
    //Через функцию getMatchesByDate возвращаем массив и разбиваем его в foreach
    // $k это позиция на которой мы сейчас находимся
    $k = 0;
    foreach($matches as $match){
        $tmp = getParticipationByMatchId($match["ID"]);
        $totalFree = count($tmp);
        $tmp = array_flip(array_filter($tmp));
        $fill = $totalFree - count($tmp);
        if(count($tmp) == $totalFree) $freeGroup  = $match;
// Случай если осталось меньше часа, отображаем новые группы только если в них до этого регались
        if($fill <= ($totalFree) && $diff < 3600 && count($tmp) != $totalFree){
            $freeGroup  = $matches[$k];
            break;
        }
// Случай если осталось больше часа, отображаем новые группы если в них вообще есть свободное место
        if(($fill > 0 && $diff > 3600)){
            $freeGroup  = $matches[$k];
            break;
        }
        $k= $k+1;
    }

    return $freeGroup;
}

?>


<div class="games">
    <table class="games__list">
        <thead>
        <tr>
            <th></th>
            <th><?=GetMessage('GS_TYPE')?></th>
            <th><?=GetMessage('GS_TITLE')?></th>
            <th><?=GetMessage('GS_DATE_EVENT')?></th>
            <th>
                <?=GetMessage('GS_RATING')?>
                <span class="tooltip">
                  ?
                  <span class="tooltip__text">
                    <?=GetMessage('GS_RATING_INFO')?>
                  </span>
                </span>
            </th>
            <th><?=GetMessage('GS_MODE')?></th>
            <th><?=GetMessage('GS_COMMENTATOR')?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        $bigTournaments = getBigTournaments();
        //dump($bigTournaments, 1);

        foreach ($bigTournaments as $bigTournament)
        {
            $tournamentDates = getTournamentPeriod($bigTournament['ID']);
            if(strtotime($tournamentDates["max"]) < time()) continue;
            switch($tournamentDates["mode"]) {
                case 14:
                    $type = "SQUAD";
                    break;
                case 13:
                    $type = "DUO";
                    break;
                case 12:
                    $type = "SOLO";
                    break;
            }
            $firstGameID = findGame($bigTournament['ID']);
            $tmp = getParticipationByMatchId($firstGameID);

            $gamesTotal = getStagePeriod($tournamentDates["firstStage"], $bigTournament['ID']);
            $countTotal = $gamesTotal["games"] * count($tmp);
            $countReal = countTournamentTeams($tournamentDates["firstStage"], $bigTournament['ID']);
            $countFree = $countTotal - $countReal;
            ?>

            <tr class="tournament">
                <td>
                    <img
                            width="60"
                            src="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table.png" alt="tournament-table"
                            srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table@2x.png 2x"
                    >
                </td>
                <td>
                    <?=GetMessage('GS_TABLE_TYPE')?>
                </td>
                <td><a class="games__link" href="<?php echo SITE_DIR."tournament-page/?tournamentID=" . $bigTournament["ID"];?>">
                        <?php
                        $name =  $bigTournament["NAME"] . ' (' .$bigTournament["PRIZE_FUND"]["VALUE"] . "€" . ')';
                        echo $name;
                        ?>
                    </a></td>
                <td>
                    <?php
                    if (IsAmPmMode()) {
                        echo strtotime($tournamentDates["min"]) != strtotime($tournamentDates["max"]) ? FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'MM/DD/YYYY \a\t H:MI T') .'<br>'. FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["max"])), 'MM/DD/YYYY \a\t H:MI T') : FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'MM/DD/YYYY \a\t H:MI T');
                    } else {
                        echo strtotime($tournamentDates["min"]) != strtotime($tournamentDates["max"]) ? FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'DD.MM.YYYY \в HH:MI') .'<br>'. FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["max"])), 'DD.MM.YYYY \в HH:MI') : FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'DD.MM.YYYY \в HH:MI');
                    }
                    ?>
                </td>
                <td>-</td>
                <td>
                    <div class="games-type">
                        <?php echo $type; ?>
                    </div>
                </td>
                <td> - </td>
            </tr>
        <?php } ?>


        <?foreach($arResult["ITEMS"] as $arItem) {

            $freeGroup = getAvailableGroup($arItem);
            //заменяем данные выводимой строки на данные матча со свободными местами
            if(isset($freeGroup) && $freeGroup["PROPERTY_53"] != $arItem["PROPERTIES"]["GROUP"]["VALUE"] && $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) {
                //URL
                $arItem["DETAIL_PAGE_URL"] = "/game-schedule/".$freeGroup["CODE"]."/";
                //ID
                $arItem['ID'] = $freeGroup['ID'];
                //GROUP
                $arItem["PROPERTIES"]["GROUP"]["VALUE"] = $freeGroup["PROPERTY_53"];
            }
            $type =$arItem["PROPERTIES"]["GAME_MODE"]["VALUE"];

            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <?php if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
                <tr class="practice">
                    <td>
                        <img
                                width="60"
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/practice.png" alt="practice"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/practice.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/practice@2x.png 2x"
                        >
                        <?php
                        if($tmp = getParticipationByMatchId($arItem["ID"])) {
                            $tmp = array_flip(array_filter($tmp));
                            $slot = $tmp[$teamID];
                            if($arItem["PROPERTIES"]["GAME_MODE"]["VALUE_ENUM_ID"] == 12){
                                $slot = $tmp[$userID];
                            }
                            if (isset($slot)) { ?>
                                <span>Слот № <?php echo $slot;?></span>
                            <?php }
                        }
                        ?>
                    </td>
                    <td>
                        Практическая игра
                    </td>
                    <td>
                        <a class="games__link" href="<?php echo $arItem["DETAIL_PAGE_URL"];?>">
                            <?php
                            $name = $arItem["PROPERTIES"]["SCRIMS_NAME"]["VALUE"] . ' GROUP '. $arItem["PROPERTIES"]["GROUP"]["VALUE"];// 'У меня нет названия';

                            if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                                $name = $arItem["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')';
                            }
                            echo $name;

                            ?>
                        </a>
                    </td>
                    <td><?php
                        $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
                        // echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5);
                        if (IsAmPmMode()) { // Определяем используется ли 12-и часовой формат времени. Если true, то = 12-часовой формат
                            echo FormatDateFromDB($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], 'MM/DD/YYYY \a\t H:MI T');
                        } else {
                            echo FormatDateFromDB($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], 'DD.MM.YYYY \в HH:MI');
                        }
                        ?>
                    </td>
                    <td><?php echo $arItem["PROPERTIES"]['MIN_RATING']["VALUE"]. " - " . $arItem["PROPERTIES"]['MAX_RATING']["VALUE"] ?></td>
                    <td>
                        <div class="games-type">
                            <?php echo $type; ?>
                        </div>
                    </td>
                    <td><?php if (!empty($arItem["PROPERTY_STREAMER_NAME"])) { ?>
                            <?php echo $arItem["PROPERTY_STREAMER_NAME"]; ?>
                        <?php } else { ?>
                            -
                        <?php } ?></td>
                </tr>
            <?php } elseif($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
                <tr class="tournament">
                    <td>
                        <img
                                width="60"
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table.png" alt="tournament-table"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table@2x.png 2x"
                        >
                        <?php
                        if($tmp = getParticipationByMatchId($arItem["ID"])) {
                            $tmp = array_flip(array_filter($tmp));
                            if (isset($tmp[$teamID])) { ?>
                                <span>Слот № <?php echo $tmp[$teamID];?></span>
                            <?php }
                        }
                        ?>
                    </td>
                    <td>
                        <?=GetMessage('GS_TABLE_TYPE')?>
                    </td>
                    <td><a class="games__link" href="<?php echo SITE_DIR."tournament-page/?tournamentID=" . $arItem["PROPERTIES"]["TOURNAMENT"]['VALUE'];?>">
                            <?php
                            $name = $arItem["PROPERTY_TOURNAMENT_NAME"];
                            echo $name;

                            ?>
                        </a></td>
                    <td><?php
                        $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
                        // echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5);
                        if (IsAmPmMode()) { // Определяем используется ли 12-и часовой формат времени. Если true, то = 12-часовой формат
                            echo FormatDateFromDB($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], 'MM/DD/YYYY \a\t H:MI T');
                        } else {
                            echo FormatDateFromDB($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], 'DD.MM.YYYY \в HH:MI');
                        }
                        ?>
                    </td>
                    <td><?php echo $arItem["PROPERTIES"]['MIN_RATING']["VALUE"]. " - " . $arItem["PROPERTIES"]['MAX_RATING']["VALUE"] ?></td>
                    <td>
                        <div class="games-type">
                            <?php echo $type; ?>
                        </div>
                    </td>
                    <td><?php if (!empty($arItem["PROPERTY_STREAMER_NAME"])) { ?>
                            <?php echo $arItem["PROPERTY_STREAMER_NAME"]; ?>
                        <?php } else { ?>
                            -
                        <?php } ?></td>
                </tr>
            <?php } ?>

        <?php }?>
        </tbody>
    </table>
    <div class="games__list-mobile">
        <?php
        foreach ($bigTournaments as $bigTournament)
        {
            $tournamentDates = getTournamentPeriod($bigTournament['ID']);
            if(strtotime($tournamentDates["max"]) < time()) continue;
            switch($tournamentDates["mode"]) {
                case 14:
                    $type = "SQUAD";
                    break;
                case 13:
                    $type = "DUO";
                    break;
                case 12:
                    $type = "SOLO";
                    break;
            }
            $firstGameID = findGame($bigTournament['ID']);
            $tmp = getParticipationByMatchId($firstGameID);

            $gamesTotal = getStagePeriod($tournamentDates["firstStage"], $bigTournament['ID']);
            $countTotal = $gamesTotal["games"] * count($tmp);
            $countReal = countTournamentTeams($tournamentDates["firstStage"], $bigTournament['ID']);
            $countFree = $countTotal - $countReal;
            ?>

            <div class="games__list-mobile-item">
                <div class="game-type game-type--tournament">
                    <img
                            width="40"
                            src="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table.png" alt="tournament-table"
                            srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table@2x.png 2x"
                    >
                    <a class="games__link" href="<?php echo SITE_DIR."tournament-page/?tournamentID=" . $bigTournament["ID"];?>">
                        <?php
                        $name = $name =  $bigTournament["NAME"];
                        echo $name;
                        ?>
                    </a>
                    <div class="game-qty">
                        <?php echo $type; ?>
                    </div>
                </div>
                <div class="game-info">
                    <div class="game-info__row">
                        <div class="game-info__item-row">
                            <div class="game-info__item">
                                <span>Дата проведения: </span>
                                <?php
                                if (IsAmPmMode()) {
                                    echo strtotime($tournamentDates["min"]) != strtotime($tournamentDates["max"]) ? FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'MM/DD/YYYY \a\t H:MI T') .' - '. FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["max"])), 'MM/DD/YYYY \a\t H:MI T') : FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'MM/DD/YYYY \a\t H:MI T');
                                } else {
                                    echo strtotime($tournamentDates["min"]) != strtotime($tournamentDates["max"]) ? FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'DD.MM.YYYY \в HH:MI') .' - '. FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["max"])), 'DD.MM.YYYY \в HH:MI') : FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'DD.MM.YYYY \в HH:MI');
                                }
                                ?>
                            </div>
                        </div>
                        <div class="game-info__item-row">
                            <div class="game-info__item">
                                <span>Приз: </span>
                                <?php
                                $name = $bigTournament["PRIZE_FUND"]['VALUE'] . "€";
                                echo $name;
                                ?>
                            </div>
                            <div class="game-info__item game-info__item_right">
                                <?php
                                if($countTotal >= $countReal){
                                    ?>
                                    <span class="place-span"><?php echo $countReal . "/" .$countTotal;?> Занято</span>
                                <?php } else { ?>
                                    <span class="no-slots-span">Мест нет</span>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?foreach($arResult["ITEMS"] as $arItem) {

            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

            if($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5){
                $tournamentDates = getTournamentPeriod($arItem["PROPERTIES"]["TOURNAMENT"]["VALUE"]);
                $tmp = getParticipationByMatchId($arItem['ID']);

                $gamesTotal = getStagePeriod($tournamentDates["firstStage"], $arItem["PROPERTIES"]['TOURNAMENT']["VALUE"]);
                $countTotal = $gamesTotal["games"] * count($tmp);
                $countReal = countTournamentTeams($tournamentDates["firstStage"], $arItem["PROPERTIES"]['TOURNAMENT']["VALUE"]);
                $countFree = $countTotal - $countReal;
            }

            $freeGroup = getAvailableGroup($arItem);
            //заменяем данные выводимой строки на данные матча со свободными местами
            if(isset($freeGroup) && $freeGroup["PROPERTY_53"] != $arItem["PROPERTIES"]["GROUP"]["VALUE"]) {
                //URL
                $arItem["DETAIL_PAGE_URL"] = "/game-schedule/".$freeGroup["CODE"]."/";
                //ID
                $arItem['ID'] = $freeGroup['ID'];
                //GROUP
                $arItem["PROPERTIES"]["GROUP"]["VALUE"] = $freeGroup["PROPERTY_53"];
            }

            $type = $arItem["PROPERTIES"]["COUTN_TEAMS"]["VALUE"];


            ?>
            <div class="games__list-mobile-item">
                <?php if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
                    <div class="game-type game-type--practice">
                        <img
                                width="40"
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/practice.png" alt="practice"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/practice.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/practice@2x.png 2x"
                        >
                        <div class="game-link">
                            <a class="games__link" href="<?php echo $arItem["DETAIL_PAGE_URL"];?>">
                                <?php
                                $name = $arItem["PROPERTIES"]["SCRIMS_NAME"]["VALUE"] . ' GROUP '.$arItem["PROPERTIES"]["GROUP"]["VALUE"];// 'У меня нет названия';
                                echo $name;
                                ?>
                            </a>
                        </div>
                        <div class="game-qty">
                            <?php echo $type; ?>
                        </div>

                    </div>
                <?php } elseif($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
                    <div class="game-type game-type--tournament">
                        <img
                                width="40"
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table.png" alt="tournament-table"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table@2x.png 2x"
                        >
                        <a class="games__link" href="<?php echo SITE_DIR."tournament-page/?tournamentID=" . $arItem["PROPERTIES"]["TOURNAMENT"]["VALUE"];?>">
                            <?php
                            $name = $arItem["PROPERTY_TOURNAMENT_NAME"];
                            echo $name;
                            ?>
                        </a>
                        <div class="game-qty">
                            <?php echo $type; ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="game-info">
                    <div class="game-info__row">
                        <div class="game-info__item-row">
                            <div class="game-info__item">
                                <span>Дата проведения: </span>
                                <?php
                                $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
                                // echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5);
                                if (IsAmPmMode()) { // Определяем используется ли 12-и часовой формат времени. Если true, то = 12-часовой формат
                                    echo FormatDateFromDB($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], 'MM/DD/YYYY \a\t H:MI T');
                                } else {
                                    echo FormatDateFromDB($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], 'DD.MM.YYYY \в HH:MI');
                                }
                                ?>
                            </div>
                        </div>
                        <div class="game-info__item-row">
                            <?php if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
                                <div class="game-info__item">
                                    <span>Рейтинг: </span>
                                    <?php echo $arItem["PROPERTIES"]['MIN_RATING']["VALUE"]. " - " . $arItem["PROPERTIES"]['MAX_RATING']["VALUE"]?>
                                </div>
                            <?php } elseif($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
                                <div class="game-info__item">
                                    <span>Отборочный этап: </span>
                                    <?php
                                    $name = $arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'];
                                    echo $name;
                                    ?>
                                </div>
                            <?php } ?>
                            <div class="game-info__item game-info__item_right">
                                <?php if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] != 5) {

                                $tmp = getParticipationByMatchId($arItem["ID"]);

                                $totalSlots = count($tmp);
                                $tmp = array_flip(array_filter($tmp));
                                $slot = $tmp[$teamID];
                                if($arItem["PROPERTIES"]["GAME_MODE"]["VALUE_ENUM_ID"] == 12){
                                    $slot = $tmp[$userID];
                                }
                                if (isset($slot)) { ?>
                                    <span class="slot-span">Слот № <?php echo $slot;?></span>
                                <?php } else {
                                    $freeSlots = 18 - count($tmp);
                                    if($freeSlots > 0){
                                        ?>
                                        <span class="place-span"><?php echo (count($tmp));?> / <?php echo $totalSlots;?> Занято</span><?php
                                    } else { ?>
                                        <span class="no-slots-span">Мест нет</span><?php
                                    }
                                }
                                ?>



                                <?php } else {
                                    if($countTotal >= $countReal) { ?>
                                        <span class="place-span"><?php echo $countReal . "/" .$countTotal;?> Занято</span>
                                    <?php } else { ?>
                                        <span class="no-slots-span">Мест нет</span>
                                    <?php }
                                }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>





