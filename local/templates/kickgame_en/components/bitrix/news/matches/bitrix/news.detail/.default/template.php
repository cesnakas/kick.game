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

// получаем участгиков матча
function getMembersIdsTeamByMatchId($matchId) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array(
        "IBLOCK_ID" => 4,
        "PROPERTY_WHICH_MATCH" => $matchId,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $teamIds = [];

   if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        //dump($arProps);
        foreach ($arProps as $k=>$v) {
            $arFields[$k] = $v['VALUE'];
        }
       $teamIds[] = $arFields["TEAM_PLACE_03"];
       $teamIds[] = $arFields["TEAM_PLACE_04"];
       $teamIds[] = $arFields["TEAM_PLACE_05"];
       $teamIds[] = $arFields["TEAM_PLACE_06"];
       $teamIds[] = $arFields["TEAM_PLACE_07"];
       $teamIds[] = $arFields["TEAM_PLACE_08"];
       $teamIds[] = $arFields["TEAM_PLACE_09"];
       $teamIds[] = $arFields["TEAM_PLACE_10"];
       $teamIds[] = $arFields["TEAM_PLACE_11"];
       $teamIds[] = $arFields["TEAM_PLACE_12"];
       $teamIds[] = $arFields["TEAM_PLACE_13"];
       $teamIds[] = $arFields["TEAM_PLACE_14"];
       $teamIds[] = $arFields["TEAM_PLACE_15"];
       $teamIds[] = $arFields["TEAM_PLACE_16"];
       $teamIds[] = $arFields["TEAM_PLACE_17"];
       $teamIds[] = $arFields["TEAM_PLACE_18"];
       $teamIds[] = $arFields["TEAM_PLACE_19"];
       $teamIds[] = $arFields["TEAM_PLACE_20"];
    }
   return $teamIds;

}



// получаем состав команды
function getCoreTeam($teamID)
{
    $filter = Array("GROUPS_ID" => Array(7), ["UF_ID_TEAM" => $teamID]);
    $arParams["SELECT"] = array("UF_*");
    $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
    $output = [];
    while ($rsUser = $elementsResult->Fetch())
    {
        $output[] = $rsUser;
    }
    return $output;
}

// получаем участников команды
function getPlayersSquadByIdMatch($idMatch, $teamId)
{
    //dump($teamId);
    $coreTeam = getCoreTeam($teamId);
    foreach ($coreTeam as $key => $val) {
        $coreTeam[$key] = $val['ID'];
    }
    //dump($coreTeam);
    $arSelect = Array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_*",
    );
    $arFilter = Array(
        "IBLOCK_ID" =>6,
        "PROPERTY_MATCH_STAGE_ONE" => $idMatch,
        "PROPERTY_TEAM_ID" => $teamId,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );

    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $arrPlayers = [];
    if($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $arrPlayers[] = $arProps["PLAYER_1"]["VALUE"]+0;
        $arrPlayers[] = $arProps["PLAYER_2"]["VALUE"]+0;
        $arrPlayers[] = $arProps["PLAYER_3"]["VALUE"]+0;
        $arrPlayers = array_flip($arrPlayers);
        unset($arrPlayers[0]);
        $arrPlayers = array_flip($arrPlayers);
        //dump($arrPlayers);
        return $arrPlayers;
    }
    return false;
}



?>
  <h1 class="my-5">#<?=$arResult["ID"]?> <?=$arResult["NAME"]?></h1>
<div class="row align-items-center">
  <div class="col-md-4">
      <h4>Дата и время начала</h4>
      <p><span class="badge badge-pill badge-primary"><?php echo $arResult["DISPLAY_PROPERTIES"]["DATE_START"]['VALUE']; ?></span></p>
  </div>
  <div class="col-md-4">
    <h4>Тип матча</h4>
    <p><span class="badge badge-pill badge-primary"><?php echo $arResult["DISPLAY_PROPERTIES"]["TYPE_MATCH"]['VALUE']; ?></span></p>
  </div>
  <div class="col-md-4">
    <h4>Кол-во команд</h4>
    <p><span class="badge badge-pill badge-primary"><?php echo $arResult["DISPLAY_PROPERTIES"]["COUTN_TEAMS"]['VALUE']; ?></span></p>
  </div>
  <div class="col-md-12">
    <h4>Ссылка на трансляцию</h4>
    <div class="form-group">
      <input class="form-control" value="<?php echo $arResult["DISPLAY_PROPERTIES"]["URL_STREAM"]['VALUE']; ?>">
    </div>
  </div>
</div>


<?php
$teamIds = getMembersIdsTeamByMatchId($arResult["ID"]);
$teamIds = array_diff($teamIds, array(''));

if (!empty($teamIds)) { ?>
  <h2>Команды которые участвуют в этом матче</h2>
  <div class="row">
  <?php foreach ($teamIds as $teamId) {
    if (!empty($teamId)) {
        $team = getTeamById($teamId);
        //dump($team);
        $coreTeam = getCoreTeam($teamId);
        $squadMembers = [];
        if ($squadMembers = getPlayersSquadByIdMatch($arResult["ID"], $teamId)) {
            $squadMembers = array_flip($squadMembers);
        }
        ?>
        <div class="col-md-6">
          <div class="card mb-3">

            <div class="card-body">
              <div class="text-center mb-3"><img style="width: 150px; height: 150px" src="<?php echo CFile::GetPath($team["LOGO_TEAM"]['VALUE']); ?>" class="card-img-top" alt=""></div>
              <h5 class="card-title">#<?php echo $team['ID']; ?> <?php echo $team['NAME']; ?> [<?php echo $team["TAG_TEAM"]['VALUE']; ?>]</h5>
              <p class="card-text"><?php echo $team["DESCRIPTION_TEAM"]["VALUE"]["TEXT"]; ?></p>
              <span class="badge badge-dark">Список игроков</span>
              <table class="table table-striped table-dark ">
                <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Nick</th>
                  <th scope="col">Сыграно игр</th>
                  <th scope="col">Фраги</th>
                  <th scope="col">Рейтинг</th>
                </tr>
                </thead>
                <tbody>
                <?php

                foreach ($coreTeam as $player)  { ?>
                  <?php if(isset($squadMembers[$player['ID']])) { ?>
                    <tr>
                      <td><?php echo $player['ID']?></td>
                      <td><?php echo $player['LOGIN']?></td>
                      <td><?php echo !$player["UF_PLAYED_GAMES"] ? '0' : $player["UF_PLAYED_GAMES"];?></td>
                      <td><?php echo !$player["UF_FRAGS"] ? '0' : $player["UF_FRAGS"]; ?></td>
                      <td><?php echo !$player["UF_RATING"] ? '0' : $player["UF_RATING"];?></td>
                    </tr>
                  <?php } ?>
                <?php } ?>

                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php
    }
  } ?>
  </div>
<?php  } else { ?>
<h2>Команд участвующих в этом матче нет</h2>
<? } ?>

<h2>Здесь будет статистика по играм</h2>
