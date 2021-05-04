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

//поиск праков по переданной дате
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

// есть ли свободное место
function isPlace($idMatch): bool
{
    $qtyPlaces = 18;
    $qtyOccupiedPlaces = getParticipationByMatchId($idMatch);
    if ($qtyPlaces == count($qtyOccupiedPlaces)) {
        return false;
    }
    return true;
}

//function sortMatches($arItems){
//    foreach($arItems as $arItem) {
//        $tmp = getParticipationByMatchId($arItem["ID"]);
//    }
//}

//Принимает на вход arItem с данными игры/ Возвращает букву первой свободной группы
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
<?foreach($arResult["ITEMS"] as $arItem) {
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

    //Если группа возвращаемая нам функцией равна группе в arItems то мы выводим элемент в расписании
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
  <div class="flex-table-new--row new-mobile-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <span>

          <div class="new-game-schedule__type-game">
            <?php if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
              <div class="new-game-schedule__icon-type-game new-game-schedule__icon-type-game_prac">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.6 22.8"><path d="M963.34,529.28h-7.7l-4.4,12.48h6.6v8.32l11-13.52h-7.7Z" transform="translate(-950.24 -528.28)"/></svg>
              </div>
                <a href="<?php echo $arItem["DETAIL_PAGE_URL"];?>" class="new-game-schedule__link">KICKGAME Scrims Group <?php echo $arItem["PROPERTIES"]["GROUP"]["VALUE"];?></a>
            <?php } elseif($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
              <div class="new-game-schedule__icon-type-game new-game-schedule__icon-type-game_tournament">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.67 21.2">
                <path d="M676.21,374.4H689v7.68a6.41,6.41,0,0,1-6.4,6.4h0a6.4,6.4,0,0,1-6.4-6.4Z" transform="translate(-671.27 -373.4)"/>
                <path d="M689,377h1.3a2.42,2.42,0,0,1,2.57,2.83c-.42,1.8-1.43,3.86-3.87,4.21" transform="translate(-671.27 -373.4)"/>
                <path d="M676.21,377H674.9a2.42,2.42,0,0,0-2.57,2.83c.42,1.8,1.44,3.86,3.88,4.21" transform="translate(-671.27 -373.4)"/>
                <path d="M682.61,388.48v5.12" transform="translate(-671.27 -373.4)"/>
                <path d="M678.77,393.6h7.68" transform="translate(-671.27 -373.4)"/>
              </svg>
            </div>
                <a href="<?php echo $arItem["DETAIL_PAGE_URL"];?>" class="new-game-schedule__link"><?php echo $arItem["PROPERTY_TOURNAMENT_NAME"] ?></a>
            <?php } ?>
              <div class="game-qty">
                  <?php echo $type; ?>
              </div>
          </div>

        </span>
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
                              <span class="place-span"> <?php echo count($tmp);?>/18 Занято</span><?php
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
    <div class="flex-table--row new-desktop-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <span>
          <div class="new-game-schedule__type-game">
            <?php if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
                <div class="new-game-schedule__icon-type-game new-game-schedule__icon-type-game_prac">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.6 22.8"><path d="M963.34,529.28h-7.7l-4.4,12.48h6.6v8.32l11-13.52h-7.7Z" transform="translate(-950.24 -528.28)"/></svg>
              </div>
              <div class="color-practical"><?php echo $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["~VALUE"]; ?></div>
            <?php } elseif($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
                <div class="new-game-schedule__icon-type-game new-game-schedule__icon-type-game_tournament">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.67 21.2">
                <path d="M676.21,374.4H689v7.68a6.41,6.41,0,0,1-6.4,6.4h0a6.4,6.4,0,0,1-6.4-6.4Z" transform="translate(-671.27 -373.4)"/>
                <path d="M689,377h1.3a2.42,2.42,0,0,1,2.57,2.83c-.42,1.8-1.43,3.86-3.87,4.21" transform="translate(-671.27 -373.4)"/>
                <path d="M676.21,377H674.9a2.42,2.42,0,0,0-2.57,2.83c.42,1.8,1.44,3.86,3.88,4.21" transform="translate(-671.27 -373.4)"/>
                <path d="M682.61,388.48v5.12" transform="translate(-671.27 -373.4)"/>
                <path d="M678.77,393.6h7.68" transform="translate(-671.27 -373.4)"/>
              </svg>
            </div>
              <div class="color-tournament"><?php echo $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["~VALUE"]; ?></div>
            <?php } ?>
              <?php
              if($tmp = getParticipationByMatchId($arItem["ID"])) {
                  $tmp = array_flip($tmp);
                  if (isset($tmp[$teamID])) { ?>
                      <div class="new-game-schedule__participation-label">Слот № <?php echo $tmp[$teamID];?></div>
                  <?php }
              }
              ?>
          </div>
        </span>
    <span>
          <a href="<?php echo $arItem["DETAIL_PAGE_URL"];?>" class="game-schedule__link">
            <?php
            $name = 'KICKGAME Scrims GROUP '.$arItem["PROPERTIES"]["GROUP"]["VALUE"];//'У меня нет названия';

            if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                $name = $arItem["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')';
            }
            echo $name;

            ?>
          </a>
        </span>
          <span class="new-game-schedule__param-wrap">
          <div class="new-game-schedule__param">Дата проведения</div>
          <?php
          $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
          echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?>
        </span>
          <span class="new-game-schedule__param-wrap">
          <div class="new-game-schedule__param">Рейтинг</div>
          <?php echo $arItem["PROPERTIES"]['MIN_RATING']["VALUE"]. " - " . $arItem["PROPERTIES"]['MAX_RATING']["VALUE"]?>
        </span>
          <span class="new-game-schedule__param-wrap">
            <div class="new-game-schedule__param">Режим</div>
            <div class="new-game-schedule__mode">
              <div><?php echo $type; ?></div>
            </div>
        </span>
          <span class="new-game-schedule__param-wrap">
          <div class="new-game-schedule__param">Комментатор</div>
          <?php if (!empty($arItem["PROPERTY_STREAMER_NAME"])) { ?>
              <?php echo $arItem["PROPERTY_STREAMER_NAME"]; ?>
          <?php } else { ?>
            -
          <?php } ?>
        </span>
        <span class="new-game-schedule__param-wrap">
        <?php echo 18 - count($tmp); ?>
      </div>



<?php //}
} ?>

<?=$arResult["NAV_STRING"]?>