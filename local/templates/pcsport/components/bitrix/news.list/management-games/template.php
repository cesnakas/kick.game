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

?>
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
        /*if ($arr = ParseDateTime($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], "DD.MM.YYYY HH:MI"))
        {
            echo "День:    ".$arr["DD"]."<br>";    // День: 21
            echo "Месяц:   ".$arr["MM"]."<br>";    // Месяц: 1
            echo "Год:     ".$arr["YYYY"]."<br>";  // Год: 2004
            echo "Часы:    ".$arr["HH"]."<br>";    // Часы: 23
            echo "Минуты:  ".$arr["MI"]."<br>";    // Минуты: 44
            echo "Секунды: ".$arr["SS"]."<br>";    // Секунды: 15
        }
        else echo "Ошибка!";*/

        $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
        echo $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["NAME"] . ' <span class="badge rounded-pill bg-success">' . $dateTime[0] . ' в ' . $dateTime[1] . '</span>';?>


    </div>

	</div>
<?endforeach;?>
</div>
</div>
