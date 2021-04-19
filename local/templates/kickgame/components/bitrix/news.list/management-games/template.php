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

// есть ли свободное место
function isPlace($idMatch): bool
{
    $qtyPlaces = 4;
    $qtyOccupiedPlaces = getParticipationByMatchId($idMatch);
    if ($qtyPlaces == count($qtyOccupiedPlaces)) {
        return false;
    }
    return true;
}
//проверка на капитана
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
$isCaptain = isCaptain($userID, $teamID);
if(!$isCaptain) {
    LocalRedirect("/personal/");
}
?>
<?php /*
<div class="container">
  <h1 class="my-3">Список актуальных матчей</h1>
<div class="row justify-content-center">

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="col-md-12 my-3" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
    <?php if(!isPlace($arItem['ID'])) { ?>
      мест нет
    <?php } else {
        $qtyOccupiedPlaces = getParticipationByMatchId($arItem['ID']);
        echo '<span class="badge badge-warning">Занято '  . count($qtyOccupiedPlaces) . ' из 4 мест</span><br>';
    } ?>
      <?php
      $placeName = '';
      $star = '';
      if($tmp = getParticipationByMatchId($arItem["ID"])) {
          $tmp = array_flip($tmp);
          if (isset($tmp[$teamID])) {
              $star = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="orangered" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
              </svg>';
              $placeName = '<span class="badge badge-danger">Ваше место в матче № ' . $tmp[$teamID] . '</span>';
          }

      }

      ?>
      <?=$placeName;?>

    <h2 style="word-wrap: break-word">
      <a href="/management-games/join-game/?mid=<?echo $arItem["ID"]?>"><?=$star;?> #<?echo $arItem["ID"]?> <?echo $arItem["NAME"]?></a>
    </h2>




    <div>
<p>Тип матча <span class="badge badge-primary"><?php echo $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["~VALUE"]; ?></span></p>
        <?php
//dump($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["~VALUE"]);
        //if ($arr = ParseDateTime($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], "DD.MM.YYYY HH:MI"))
        //{
            //echo "День:    ".$arr["DD"]."<br>";    // День: 21
            //echo "Месяц:   ".$arr["MM"]."<br>";    // Месяц: 1
            //echo "Год:     ".$arr["YYYY"]."<br>";  // Год: 2004
            //echo "Часы:    ".$arr["HH"]."<br>";    // Часы: 23
            //echo "Минуты:  ".$arr["MI"]."<br>";    // Минуты: 44
            //echo "Секунды: ".$arr["SS"]."<br>";    // Секунды: 15
        //}
        //else echo "Ошибка!";

        $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
        echo $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["NAME"] . ' <span class="badge rounded-pill bg-success">' . $dateTime[0] . ' в ' . $dateTime[1] . '</span>';?>


    </div>

	</div>
<?endforeach;?>
</div>
</div>
*/?>

<?foreach($arResult["ITEMS"] as $arItem) {
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    //dump($arItem);
    ?>
  <div class="flex-table--row" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <span>
          <div class="game-schedule__type-game">
            <?php if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
              <div class="game-schedule__icon-type-game game-schedule__icon-type-game_prac">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.6 22.8"><path d="M963.34,529.28h-7.7l-4.4,12.48h6.6v8.32l11-13.52h-7.7Z" transform="translate(-950.24 -528.28)"/></svg>
              </div>
              <div class="color-practical"><?php echo $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["~VALUE"]; ?></div>
            <?php } elseif($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
              <div class="game-schedule__icon-type-game game-schedule__icon-type-game_tournament">
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
                    <div class="game-schedule__participation-label">Слот № <?php echo $tmp[$teamID];?></div>
                  <?php }
              }
              ?>
          </div>
        </span>
    <span>
          <a href="/management-games/join-game/?mid=<?echo $arItem["ID"]?>" class="game-schedule__link">
            <?php
            $name = 'KICKGAME Scrims';

            if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                $name = $arItem["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')';
            }
            echo $name;

            ?>
          </a>
        </span>
    <span class="game-schedule__param-wrap">
          <div class="game-schedule__param">Дата проведения</div>
          <?php
          $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
          echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?>
        </span>
    <span class="game-schedule__param-wrap">
          <div class="game-schedule__param">Рейтинг</div>
          3.0
        </span>
    <span class="game-schedule__param-wrap">
            <div class="game-schedule__param">Режим</div>
            <div class="game-schedule__mode">
              <i></i>
              <div>x<?php echo $arItem["PROPERTIES"]["COUTN_TEAMS"]["VALUE"]; ?></div>
            </div>
        </span>
    <span class="game-schedule__param-wrap">
          <div class="game-schedule__param">Комментатор</div>
          <?php if (!empty($arItem["PROPERTY_STREAMER_NAME"])) { ?>
              <?php echo $arItem["PROPERTY_STREAMER_NAME"]; ?>
          <?php } else { ?>
            -
          <?php } ?>
        </span>
  </div>
<?php } ?>

<?=$arResult["NAV_STRING"]?>