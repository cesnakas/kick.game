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


//function sortMatches($arItems){
//    foreach($arItems as $arItem) {
//        $tmp = getParticipationByMatchId($arItem["ID"]);
//    }
//}

//Принимает на вход arItem с данными игры/ Возвращает букву первой свободной группы
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
        $fill =  - count($tmp);
        if(count($tmp) ==  $totalFree) $freeGroup  = $match;
// Случай если осталось меньше часа, отображаем новые группы только если в них до этого регались
        if($fill <= ( $totalFree -1) && $diff < 3600 && count($tmp) !=  $totalFree){
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
$bigTournaments = getBigTournaments();
//dump($bigTournaments, 1);

if(!$_GET["PAGEN_1"]){
foreach ($bigTournaments as $bigTournament)
{
    $tournamentDates = getTournamentPeriod($bigTournament['ID']);
    if(strtotime($tournamentDates["max"]) < time()) continue;
   // dump($tournamentDates["mode"], 1);
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

    <div class="flex-table-new--row new-mobile-item" id="<?=$this->GetEditAreaId($bigTournament['ID']);?>">
        <span>

          <div class="new-game-schedule__type-game">

                <div class="new-game-schedule__icon-type-game new-game-schedule__icon-type-game_tournament">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.67 21.2">
                <path d="M676.21,374.4H689v7.68a6.41,6.41,0,0,1-6.4,6.4h0a6.4,6.4,0,0,1-6.4-6.4Z" transform="translate(-671.27 -373.4)"/>
                <path d="M689,377h1.3a2.42,2.42,0,0,1,2.57,2.83c-.42,1.8-1.43,3.86-3.87,4.21" transform="translate(-671.27 -373.4)"/>
                <path d="M676.21,377H674.9a2.42,2.42,0,0,0-2.57,2.83c.42,1.8,1.44,3.86,3.88,4.21" transform="translate(-671.27 -373.4)"/>
                <path d="M682.61,388.48v5.12" transform="translate(-671.27 -373.4)"/>
                <path d="M678.77,393.6h7.68" transform="translate(-671.27 -373.4)"/>
              </svg>
            </div>
                <a href="<?=SITE_DIR?>tournament-page/?tournamentID=<?php echo $bigTournament['ID'] ?> " class="new-game-schedule__link"><?php echo $bigTournament['NAME']; ?></a>

              <div class="game-qty">
                  <?php echo $type; ?>
              </div>
          </div>

        </span>
        <div class="game-info">
            <div class="game-info__row">
                <div class="game-info__item-row">
                    <div class="game-info__item">
                        <span><?=GetMessage('DATE_EVENT_COLON')?></span>
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
                            <span><?=GetMessage('PRIZE')?></span>
                            <?php
                            $name = $bigTournament["PRIZE_FUND"]["VALUE"] . "€";
                            echo $name;
                            ?>
                        </div>
                    <div class="game-info__item game-info__item_right">

                        <?php
                        //if display
                            ?>
                                <span class="place-span"> <?php echo $countReal ."/".$countTotal;?> <?=GetMessage('RATING_SEATS_OCCUPIED')?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex-table--row new-desktop-item" id="<?=$this->GetEditAreaId($bigTournament['ID']);?>">
        <span>
          <div class="new-game-schedule__type-game">

                <div class="new-game-schedule__icon-type-game new-game-schedule__icon-type-game_tournament">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.67 21.2">
                <path d="M676.21,374.4H689v7.68a6.41,6.41,0,0,1-6.4,6.4h0a6.4,6.4,0,0,1-6.4-6.4Z" transform="translate(-671.27 -373.4)"/>
                <path d="M689,377h1.3a2.42,2.42,0,0,1,2.57,2.83c-.42,1.8-1.43,3.86-3.87,4.21" transform="translate(-671.27 -373.4)"/>
                <path d="M676.21,377H674.9a2.42,2.42,0,0,0-2.57,2.83c.42,1.8,1.44,3.86,3.88,4.21" transform="translate(-671.27 -373.4)"/>
                <path d="M682.61,388.48v5.12" transform="translate(-671.27 -373.4)"/>
                <path d="M678.77,393.6h7.68" transform="translate(-671.27 -373.4)"/>
              </svg>
            </div>
                <div class="color-tournament">
                    <?php
                    if (LANGUAGE_ID == 'ru') {
                        echo 'Турнир';
                    } elseif (LANGUAGE_ID == 'en') {
                        echo 'Tournament';
                    }
                    ?>
                </div>
          </div>
        </span>
        <span>
          <a href="<?php echo SITE_DIR."tournament-page/?tournamentID=" . $bigTournament["ID"];?>" class="new-game-schedule__link">
            <?php
            $name = $bigTournament["NAME"] . " (". $bigTournament["PRIZE_FUND"]["VALUE"]. "€" . ")"; ;
            echo $name;
            ?>
          </a>
        </span>
        <span class="new-game-schedule__param-wrap">
          <div class="new-game-schedule__param"><?=GetMessage('DATE_EVENT')?></div>
          <?php
          if (IsAmPmMode()) {
              echo strtotime($tournamentDates["min"]) != strtotime($tournamentDates["max"]) ? FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'MM/DD/YYYY \a\t H:MI T') .'<br>'. FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["max"])), 'MM/DD/YYYY \a\t H:MI T') : FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'MM/DD/YYYY \a\t H:MI T');
          } else {
              echo strtotime($tournamentDates["min"]) != strtotime($tournamentDates["max"]) ? FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'DD.MM.YYYY \в HH:MI') .'<br>'. FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["max"])), 'DD.MM.YYYY \в HH:MI') : FormatDateFromDB(date("d.m.Y H:i:s", strtotime($tournamentDates["min"])), 'DD.MM.YYYY \в HH:MI');
          }
          ?>
        </span>
        <span class="new-game-schedule__param-wrap">
          <div class="new-game-schedule__param"><?=GetMessage('RATING')?></div>
         -
        </span>
        <span class="new-game-schedule__param-wrap">
            <div class="new-game-schedule__param"><?=GetMessage('MODE')?></div>
            <div class="new-game-schedule__mode">
              <div><?php echo $type; ?></div>
            </div>
        </span>
        <span class="new-game-schedule__param-wrap">
          <div class="new-game-schedule__param"><?=GetMessage('COMMENTATOR')?></div>
              -
        </span>
        <span class="new-game-schedule__param-wrap">
        <?php echo $countFree; ?>
    </div>

<?}
}

    foreach($arResult["ITEMS"] as $arItem) {
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




      //Если группа возвращаемая нам функцией равна группе в arItems то мы выводим элемент в расписании
    $freeGroup = getAvailableGroup($arItem);
    //заменяем данные выводимой строки на данные матча со свободными местами

if(isset($freeGroup) && $freeGroup["PROPERTY_53"] != $arItem["PROPERTIES"]["GROUP"]["VALUE"] && $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) {

    //URL
    $arItem["DETAIL_PAGE_URL"] = SITE_DIR."game-schedule/".$freeGroup["CODE"]."/";

    //ID
    $arItem['ID'] = $freeGroup['ID'];
    //GROUP
    $arItem["PROPERTIES"]["GROUP"]["VALUE"] = $freeGroup["PROPERTY_53"];
}
            $type = $arItem["PROPERTIES"]["GAME_MODE"]["VALUE"]

    ?>
  <div class="flex-table-new--row new-mobile-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <span>

          <div class="new-game-schedule__type-game">
            <?php if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
              <div class="new-game-schedule__icon-type-game new-game-schedule__icon-type-game_prac">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.6 22.8"><path d="M963.34,529.28h-7.7l-4.4,12.48h6.6v8.32l11-13.52h-7.7Z" transform="translate(-950.24 -528.28)"/></svg>
              </div>
                <a href="<?php echo $arItem["DETAIL_PAGE_URL"];?>" class="new-game-schedule__link"><?php echo $arItem["PROPERTIES"]["SCRIMS_NAME"]["VALUE"] ?> Group <?php echo $arItem["PROPERTIES"]["GROUP"]["VALUE"];?></a>
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
                <a href="<?=SITE_DIR?>tournament-page/?tournamentID=<?php echo $arItem["PROPERTIES"]["TOURNAMENT"]["VALUE"] ?> " class="new-game-schedule__link"><?php echo $arItem["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')'; ?></a>
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
                      <span><?=GetMessage('DATE_EVENT_COLON')?></span>
                      <?php
                      $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
                      // echo $dateTime[0] . GetMessage('DATE_EVENT_COLON_AT') . substr($dateTime[1], 0, 5);
                      if (IsAmPmMode()) {
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
                          <span><?=GetMessage('RATING_COLON')?></span>
                  <?php echo $arItem["PROPERTIES"]['MIN_RATING']["VALUE"]. " - " . $arItem["PROPERTIES"]['MAX_RATING']["VALUE"]?>
                      </div>
                  <?php } elseif($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
                      <div class="game-info__item">
                          <span><?=GetMessage('STAGE_COLON')?></span>
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
                <span class="slot-span"><?=GetMessage('SLOT_NO')?><?php echo $slot;?></span>
            <?php } else {
                $freeSlots =  $totalSlots - count($tmp);
                if($freeSlots > 0){
                    ?>
                    <span class="place-span"><?php echo (count($tmp));?> / <?php echo $totalSlots;?> <?=GetMessage('RATING_SEATS_OCCUPIED')?></span><?php
                } else { ?>
                    <span class="no-slots-span"><?=GetMessage('NO_SEATS')?></span><?php
                }
            }
        }
            ?>
                  </div>
              </div>
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
                <div class="color-practical">
                    <?php
                    if (LANGUAGE_ID == 'ru') {
                        echo $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["~VALUE"];
                    } elseif (LANGUAGE_ID == 'en') {
                        echo 'Scrims';
                    }
                    ?>
                </div>
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
                <div class="color-tournament">
                    <?php
                    if (LANGUAGE_ID == 'ru') {
                        echo $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["~VALUE"];
                    } elseif (LANGUAGE_ID == 'en') {
                        echo 'Tournament';
                    }
                    ?>
                </div>
            <?php } ?>
              <?php
             // dump($arItem);
              if($tmp = getParticipationByMatchId($arItem["ID"])) {
                  $totalSlots = count($tmp);
                  $tmp = array_flip(array_filter($tmp));
                  $slot = $tmp[$teamID];
                  if($arItem["PROPERTIES"]["GAME_MODE"]["VALUE_ENUM_ID"] == 12){
                      $slot = $tmp[$userID];
                  }
                  if (isset($slot)) { ?>
                      <div class="new-game-schedule__participation-label"><?=GetMessage('SLOT_NO')?><?php echo $slot;?></div>
                  <?php }
              }
              ?>
          </div>
        </span>
          <span>
          <a href="<?php echo $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5 ? SITE_DIR."tournament-page/?tournamentID=" . $arItem["PROPERTIES"]["TOURNAMENT"]["VALUE"] : $arItem["DETAIL_PAGE_URL"];?>" class="new-game-schedule__link">
            <?php
            $name = $arItem["PROPERTIES"]["SCRIMS_NAME"]["VALUE"].' GROUP '.$arItem["PROPERTIES"]["GROUP"]["VALUE"];//'У меня нет названия';

            if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                $name = $arItem["PROPERTY_TOURNAMENT_NAME"];
            }
            echo $name;

            ?>
          </a>
        </span>
          <span class="new-game-schedule__param-wrap">
          <div class="new-game-schedule__param"><?=GetMessage('DATE_EVENT')?></div>
          <?php
          $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
          // echo $dateTime[0] . GetMessage('DATE_EVENT_COLON_AT') . substr($dateTime[1], 0, 5);
          if (IsAmPmMode()) { // Определяем используется ли 12-и часовой формат времени. Если true, то = 12-часовой формат
              echo FormatDateFromDB($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], 'MM/DD/YYYY \a\t H:MI T');
          } else {
              echo FormatDateFromDB($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], 'DD.MM.YYYY \в HH:MI');
          }
          ?>
        </span>
          <span class="new-game-schedule__param-wrap">
          <div class="new-game-schedule__param"><?=GetMessage('RATING')?></div>
          <?php echo $arItem["PROPERTIES"]['MIN_RATING']["VALUE"]. " - " . $arItem["PROPERTIES"]['MAX_RATING']["VALUE"]?>
        </span>
          <span class="new-game-schedule__param-wrap">
            <div class="new-game-schedule__param"><?=GetMessage('MODE')?></div>
            <div class="new-game-schedule__mode">
              <div><?php echo $type; ?></div>
            </div>
        </span>
          <span class="new-game-schedule__param-wrap">
          <div class="new-game-schedule__param"><?=GetMessage('COMMENTATOR')?></div>
          <?php if (!empty($arItem["PROPERTY_STREAMER_NAME"])) { ?>
              <?php echo $arItem["PROPERTY_STREAMER_NAME"]; ?>
          <?php } else { ?>
              -
          <?php } ?>
        </span>
        <span class="new-game-schedule__param-wrap">
        <?php
        if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) {
            echo $totalSlots - (count($tmp));
        } else {
            echo $countFree;
        }
        ?>

      </div>



<?php //}
} ?>

<?=$arResult["NAV_STRING"]?>