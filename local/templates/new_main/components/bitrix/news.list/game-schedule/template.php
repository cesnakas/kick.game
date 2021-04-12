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


<div class="games">
  <table class="games__list">
    <thead>
    <tr>
      <th></th>
      <th>Тип игры</th>
      <th>Название</th>
      <th>Дата проведения</th>
      <th>
        Рейтинг
        <span class="tooltip">
                  ?
                  <span class="tooltip__text">
                    Минимальный рейтинг игрока, необходимый для записи на эту игру
                  </span>
                </span>
      </th>
      <th>Режим</th>
      <th>Комментатор</th>
    </tr>
    </thead>
    <tbody>
    <?foreach($arResult["ITEMS"] as $arItem) {
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
              $name = '';// 'У меня нет названия';

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
        <td>3.00</td>
        <td>
          <div class="games-type">
            <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="15" viewBox="0 0 17 21"><circle cx="8.5" cy="4.75" r="3.75" stroke="#fff"/><path d="M1 17.25a6.5 6.5 0 016.5-6.5h2a6.5 6.5 0 016.5 6.5v0a3.25 3.25 0 01-3.25 3.25h-8.5A3.25 3.25 0 011 17.25v0z" stroke="#fff"/></svg>
            x<?php echo $arItem["PROPERTIES"]["COUTN_TEAMS"]["VALUE"]; ?>
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
            Турнир
          </td>
          <td><a class="games__link" href="<?php echo $arItem["DETAIL_PAGE_URL"];?>">
                  <?php
                  $name = '';// 'У меня нет названия';

                  if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                      $name = $arItem["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')';
                  }
                  echo $name;

                  ?>
            </a></td>
          <td><?php
              $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
              echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?></td>
          <td>3.00</td>
          <td>
            <div class="games-type">
              <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="15" viewBox="0 0 17 21"><circle cx="8.5" cy="4.75" r="3.75" stroke="#fff"/><path d="M1 17.25a6.5 6.5 0 016.5-6.5h2a6.5 6.5 0 016.5 6.5v0a3.25 3.25 0 01-3.25 3.25h-8.5A3.25 3.25 0 011 17.25v0z" stroke="#fff"/></svg>
              x<?php echo $arItem["PROPERTIES"]["COUTN_TEAMS"]["VALUE"]; ?>
            </div>
          </td>
          <td><?php if (!empty($arItem["PROPERTY_STREAMER_NAME"])) { ?>
                  <?php echo $arItem["PROPERTY_STREAMER_NAME"]; ?>
              <?php } else { ?>
              -
              <?php } ?></td>
        </tr>
        <?php } ?>

    <?php } ?>
    </tbody>
  </table>
  <div class="games__list-mobile">
      <?foreach($arResult["ITEMS"] as $arItem) {
          $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
          $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
          ?>
    <div class="games__list-mobile-item">
        <?php if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
      <div class="game-type game-type--practice">
        <img
          width="60"
          src="<?php echo SITE_TEMPLATE_PATH;?>/images/practice.png" alt="practice"
          srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/practice.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/practice@2x.png 2x"
        >
        Практическая игра
          <?php
          if($tmp = getParticipationByMatchId($arItem["ID"])) {
              $tmp = array_flip($tmp);
              if (isset($tmp[$teamID])) { ?>
                <span>Слот № <?php echo $tmp[$teamID];?></span>
              <?php }
          }
          ?>
      </div>
      <?php } elseif($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
          <div class="game-type game-type--tournament">
            <img
              width="60"
              src="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table.png" alt="tournament-table"
              srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/tournament-table@2x.png 2x"
            >
            Турнир
              <?php
              if($tmp = getParticipationByMatchId($arItem["ID"])) {
                  $tmp = array_flip($tmp);
                  if (isset($tmp[$teamID])) { ?>
                    <span>Слот № <?php echo $tmp[$teamID];?></span>
                  <?php }
              }
              ?>
          </div>
      <?php } ?>
      <div class="game-link">
        <a class="games__link" href="<?php echo $arItem["DETAIL_PAGE_URL"];?>">
            <?php
            $name = '';// 'У меня нет названия';

            if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                $name = $arItem["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')';
            }
            echo $name;

            ?>
        </a>
      </div>
      <div class="game-info">
        <div class="game-info__row">
          <div class="game-info__item">
            <span>Дата проведение</span>
              <?php
              $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
              echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?>
          </div>
          <div class="game-info__item">
            <span>Рейтинг</span>
            3.0
          </div>
        </div>
        <div class="game-info__row">
          <div class="game-info__item">
            <span>Режим</span>
            <div class="game-qty">
              <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="15" viewBox="0 0 17 21"><circle cx="8.5" cy="4.75" r="3.75" stroke="#fff"/><path d="M1 17.25a6.5 6.5 0 016.5-6.5h2a6.5 6.5 0 016.5 6.5v0a3.25 3.25 0 01-3.25 3.25h-8.5A3.25 3.25 0 011 17.25v0z" stroke="#fff"/></svg>
              x<?php echo $arItem["PROPERTIES"]["COUTN_TEAMS"]["VALUE"]; ?>
            </div>
          </div>
          <div class="game-info__item">
            <span>Комментатор</span>
              <?php if (!empty($arItem["PROPERTY_STREAMER_NAME"])) { ?>
                  <?php echo $arItem["PROPERTY_STREAMER_NAME"]; ?>
              <?php } else { ?>
                -
              <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</div>





