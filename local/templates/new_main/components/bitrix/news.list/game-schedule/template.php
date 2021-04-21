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

// есть ли свободное место
function isPlace($idMatch)
{
    $qtyPlaces = 18;
    $qtyOccupiedPlaces = getParticipationByMatchId($idMatch);
    if ($qtyPlaces == count($qtyOccupiedPlaces)) {
        return false;
    }
    return true;
}

// проверка команды на участие
function getParticipationByMatchId($idMatch)
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
    $key = [
        1 => "TEAM_PLACE_01",
        2 => "TEAM_PLACE_02",
        3 => "TEAM_PLACE_03",
        4 => "TEAM_PLACE_04",
        5 => "TEAM_PLACE_05",
        6 => "TEAM_PLACE_06",
        7 => "TEAM_PLACE_07",
        8 => "TEAM_PLACE_08",
        9 => "TEAM_PLACE_09",
        10 => "TEAM_PLACE_10",
        11 => "TEAM_PLACE_11",
        12 => "TEAM_PLACE_12",
        13 => "TEAM_PLACE_13",
        14 => "TEAM_PLACE_14",
        15 => "TEAM_PLACE_15",
        16 => "TEAM_PLACE_16",
        17 => "TEAM_PLACE_17",
        18 => "TEAM_PLACE_18",
        19 => "TEAM_PLACE_19",
        20 => "TEAM_PLACE_20",
    ];
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        foreach ($key as $place=>$name) {
            if ($arProps[$name]['VALUE']+0 > 0) {
                $arrTeams[$place] = $arProps[$name]['VALUE'];
            }
        }
        return $arrTeams;
    }
    return  false;
}

function checkRegistrationTeamOnTournament($idTeam, $idTournament)
{
    $matchesId = [];
    $idRegistrationMatch = false;
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" =>4,
        //"?NAME" => '#'.$idTournament.'_TOURNAMENT',
        //"?NAME" => '_GROUP4_STAGE1',
        array(
            "LOGIC" => "AND",
            array(
                "?NAME" => '#'.$idTournament.'_TOURNAMENT'
            ),
            array(
                "?NAME" => '_GROUP4_STAGE1'
            ),
        ),
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        //$arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $matchesId[] = $arProps["WHICH_MATCH"]['VALUE']+0;
    }
    if (!empty($matchesId)) {
        foreach ($matchesId as $id) {
            if($tmp = getParticipationByMatchId($id)) {
                $tmp = array_flip($tmp);
                if (isset($tmp[$idTeam])) {
                    $idRegistrationMatch = $id;
                }
            }
        }
    }
    return $idRegistrationMatch;
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

//Через функцию getMatchesByDate возвращаем массив и разбиваем его в foreach

    foreach(getMatchesByDate($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], $arItem["PROPERTIES"]['MIN_RATING']["VALUE"], $arItem["PROPERTIES"]['MAX_RATING']["VALUE"]) as $match){
//Проверяем есть ли свободные места в матче, если да то сохраняем match и выходим из цикла c break
        $freeGroup = $match;

        if(isPlace($match['ID'])){
            break;
        }
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
    <?foreach($arResult["ITEMS"] as $arItem) {

    $freeGroup = getAvailableGroup($arItem);
    //заменяем данные выводимой строки на данные матча со свободными местами
    if($freeGroup["PROPERTY_53"] != $arItem["PROPERTIES"]["GROUP"]["VALUE"]) {
        //URL
        $arItem["DETAIL_PAGE_URL"] = "/game-schedule/".$freeGroup["CODE"]."/";
        //ID
        $arItem['ID'] = $freeGroup['ID'];
        //GROUP
        $arItem["PROPERTIES"]["GROUP"]["VALUE"] = $freeGroup["PROPERTY_53"];
    }

        switch($arItem["PROPERTIES"]["COUTN_TEAMS"]["VALUE"]) {
            case 4:
                $type = "SQUAD";
                break;
            case 2:
                $type = "DUO";
                break;
            case 1:
                $type = "SOLO";
                break;
        }
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
                $tmp = array_flip($tmp);
                if (isset($tmp[$teamID])) { ?>
                  <span>Слот № <?php echo $tmp[$teamID];?></span>
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
              $name = 'Kickgame Scrims GROUP '. $arItem["PROPERTIES"]["GROUP"]["VALUE"];// 'У меня нет названия';

              if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                  $name = $arItem["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')';
              }
              echo $name;

              ?>
          </a>
        </td>
        <td><?php
            $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
            echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?></td>
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
                  $tmp = array_flip($tmp);
                  if (isset($tmp[$teamID])) { ?>
                    <span>Слот № <?php echo $tmp[$teamID];?></span>
                  <?php }
              }
              ?>
          </td>
          <td>
            <?=GetMessage('GS_TABLE_TYPE')?>
          </td>
          <td><a class="games__link" href="<?php echo $arItem["DETAIL_PAGE_URL"];?>">
                  <?php
                  $name = "";// 'У меня нет названия';

                  if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                      $name = $arItem["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')';
                  }
                  echo $name;

                  ?>
            </a></td>
          <td><?php
              $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
              echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?></td>
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
      <?foreach($arResult["ITEMS"] as $arItem) {

          $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
          $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

          $freeGroup = getAvailableGroup($arItem);
          //заменяем данные выводимой строки на данные матча со свободными местами
          if($freeGroup["PROPERTY_53"] != $arItem["PROPERTIES"]["GROUP"]["VALUE"]) {
              //URL
              $arItem["DETAIL_PAGE_URL"] = "/game-schedule/".$freeGroup["CODE"]."/";
              //ID
              $arItem['ID'] = $freeGroup['ID'];
              //GROUP
              $arItem["PROPERTIES"]["GROUP"]["VALUE"] = $freeGroup["PROPERTY_53"];
          }

          switch($arItem["PROPERTIES"]["COUTN_TEAMS"]["VALUE"]) {
              case 4:
                  $type = "SQUAD";
                  break;
              case 2:
                  $type = "DUO";
                  break;
              case 1:
                  $type = "SOLO";
                  break;
          }

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
                  $name = 'KICKGAME Scrims GROUP '.$arItem["PROPERTIES"]["GROUP"]["VALUE"];// 'У меня нет названия';
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
              <a class="games__link" href="<?php echo $arItem["DETAIL_PAGE_URL"];?>">
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
                echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?>
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
                <span>Этап: </span>
                    <?php
                    $name = $arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'];
                    echo $name;
                    ?>
                </div>
                <?php } ?>
                    <div class="game-info__item game-info__item_right">

                        <?php

                        $tmp = getParticipationByMatchId($arItem["ID"]);
                            $tmp = array_flip($tmp);
                            if (isset($tmp[$teamID])) {
                                ?>
                                <span class="slot-span">Слот № <?php echo $tmp[$teamID];?></span>
                            <?php } else {
                                $freeSlots = 18 - count($tmp);
                                if($freeSlots > 0){
                                ?>
                                <span class="place-span"><?php echo count($tmp);?>/18 Занято</span><?php
                        } else {
                                    ?>
                                    <span class="no-slots-span">Мест нет</span><?php
                                }
                                }
                        ?>

                    </div>
            </div>
<!--          <div class="game-info__item">-->
<!--            <span>Дата проведение</span>-->
<!--              --><?php
//              $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
//              echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?>
<!--          </div>-->
<!--          <div class="game-info__item">-->
<!--            <span>Рейтинг</span>-->
<!--            3.0-->
<!--          </div>-->

<!--          <div class="game-info__item">-->
<!--            <span>Комментатор</span>-->
<!--              --><?php //if (!empty($arItem["PROPERTY_STREAMER_NAME"])) { ?>
<!--                  --><?php //echo $arItem["PROPERTY_STREAMER_NAME"]; ?>
<!--              --><?php //} else { ?>
<!--                --->
<!--              --><?php //} ?>
<!--          </div>-->
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</div>





