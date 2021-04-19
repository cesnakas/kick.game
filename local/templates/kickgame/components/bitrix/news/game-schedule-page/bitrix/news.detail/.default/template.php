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

$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];

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
        $arrPlayers[] = $arProps["PLAYER_4"]["VALUE"]+0;
        $arrPlayers[] = $arProps["PLAYER_5"]["VALUE"]+0;
        $arrPlayers[] = $arProps["PLAYER_6"]["VALUE"]+0;
        $arrPlayers = array_flip($arrPlayers);
        unset($arrPlayers[0]);
        $arrPlayers = array_flip($arrPlayers);
        //dump($arrPlayers);
        return $arrPlayers;
    }
    return false;
}
//dump($arResult);
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
$btnValue = "Принять участие";
?>

<div class="container">
  <a href="/game-schedule/" class="btn-italic-icon">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 11.62">
      <path d="M955.22,534.89a1,1,0,0,1,0,1.33l-3,3.27h18.2a.94.94,0,0,1,0,1.88h-18.2l3,3.27a1,1,0,0,1,0,1.32.81.81,0,0,1-1.21,0l-4.49-4.88h0a1,1,0,0,1,0-1.33h0l4.49-4.87A.81.81,0,0,1,955.22,534.89Z" transform="translate(-949.26 -534.62)"/>
    </svg> Назад
  </a>
  <section class="game">
    <div class="row align-items-center justify-content-lg-center">
      <div class="col-lg-6">
        <div class="game__block">
          <div class="game__block-img" style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/profile-avatar.jpg)">
            <div class="game__block-img-rating-bg">
              <div class="game__block-img-rating">3.00</div>
            </div>
          </div>
            <?php
            if($tmp = getParticipationByMatchId($arResult["ID"])) {
                $tmp = array_flip($tmp);
                if (isset($tmp[$teamID])) {
                    $btnValue = 'Обновить команду';
                    ?>
                  <div class="game__participation-label">Слот № <?php echo $tmp[$teamID];?></div>
                <?php }

            }
            ?>
          <h1><?php
              $name = 'KICKGAME Scrims';// 'У меня нет названия';

              if ($arResult["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                  $name = $arResult["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arResult["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')';
              }
              echo $name;

              ?></h1>
            <?php if ($arResult["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
              <div class="game__block-type"><i></i> Практическая игра</div>
            <?php } elseif($arResult["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
              <div class="game__block-type game__block-type_tournament"><i></i> Турнирная игра</div>
            <?php } ?>
          <?php if ($isCaptain) { ?>
            <a href="/management-games/join-game/?mid=<?php echo $arResult["ID"];?>" class="btn"><?php echo $btnValue; ?></a>

          <div class="game__block-call">
            <a href="#" class="btn-italic">Связаться с модератором</a>
          </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="row">
          <div class="col-6 col-xl-4">
            <div class="info-item">
              <div>Дата проведения</div>
              <div>
                  <?php
                  $dateTime = explode(' ', $arResult["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
                  echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?>
              </div>
            </div>
          </div>
          <div class="col-6 col-xl-4">
            <div class="info-item">
              <div>Количество матчей</div>
              <div>3 (2 часа)</div>
            </div>
          </div>
          <div class="col-6 col-xl-4">
            <div class="info-item">
              <div>Комментатор</div>
              <div>
                <?php if (!empty($arResult["PROPERTY_STREAMER_NAME"])) { ?>
                    <?php echo $arResult["PROPERTY_STREAMER_NAME"]; ?>
                <?php } else { ?>
                  -
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="col-6 col-xl-4">
            <div class="info-item">
              <div>Ссылка на трансляцию</div>
              <div>
                  <?php if (!empty($arResult["PROPERTIES"]["URL_STREAM"]['VALUE'])) { ?>
                    <a href="<?php echo $arResult["PROPERTIES"]["URL_STREAM"]['VALUE'];?>" target="_blank" class="btn-blue">В эфире</a>
                  <?php } else { ?>
                    -
                  <?php } ?>
              </div>
            </div>
          </div>
          <div class="col-6 col-xl-4">
            <div class="info-item">
              <div>Режим игры</div>
              <div>
                      <span class="info-item__mode-block">
                        <!--<span class="info-item__mode-description">Сквад</span>-->
                        <div class="info-item__mode">
                          <i></i>
                          <div>x<?php echo $arResult["PROPERTIES"]["COUTN_TEAMS"]["VALUE"];?></div>
                        </div>
                      </span>
              </div>
            </div>
          </div>
          <div class="col-6 col-xl-4">
            <div class="info-item">
              <div>Количество мест</div>
              <div>
                  <?php if(!isPlace($arResult['ID'])) { ?>
                    мест нет
                  <?php } else {
                      $qtyOccupiedPlaces = getParticipationByMatchId($arResult['ID']);
                      echo 'Занято '  . count($qtyOccupiedPlaces) . ' из 18 мест';
                  } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php
$teamIds = getMembersIdsTeamByMatchId($arResult["ID"]);
$teamIds = array_diff($teamIds, array(''));

Function getChainMatches( $firstMaitchID ){
  GLOBAL $DB;
  $firstMaitchID += 0;
  $sql = 'SELECT  m.IBLOCK_ELEMENT_ID AS matchID 
                    ,m.PROPERTY_8 AS parentMatchID
                    ,m.PROPERTY_22 AS stageMatch
                    ,m.PROPERTY_23 AS typeMatch
              FROM b_iblock_element_prop_s3 AS m 
              WHERE m.IBLOCK_ELEMENT_ID = '.$firstMaitchID;
  $res = $DB->Query($sql);
  if($row = $res->Fetch()) {
    $chain = $row;
    $chain[ 'chain' ] = [ $firstMaitchID ];
    $mID = $firstMaitchID;
    do {
      $sql = 'SELECT  m.IBLOCK_ELEMENT_ID AS matchID 
                  FROM b_iblock_element_prop_s3 AS m 
                  WHERE m.PROPERTY_8 = '.$mID;
      $res = $DB->Query($sql);
      if($row = $res->Fetch()) {
        $mID = $row['matchID']+0;
        $chain[ 'chain' ][] = $mID;
      } else {
        $mID = false;
      }
    } while( $mID );
    return $chain;
  }
  return false;
}


function countPointsByMatchesIDs( $IDs = array() ){
  GLOBAL $DB;
  if( is_array($IDs) && count($IDs) ){
    foreach( $IDs as $k=>$v ){
      $IDs[$k] = $v+0;
    }
    $sql = 'SELECT   t.PROPERTY_15 AS teamID
                    ,sum(t.PROPERTY_17) AS kills 
                    ,sum(t.PROPERTY_18) AS total
              FROM b_iblock_element_prop_s5 AS t 
              WHERE t.PROPERTY_14 IN ('.implode(',',$IDs).')
              GROUP BY t.PROPERTY_15';
    $res = $DB->Query($sql);
    $points = [];
    while( $row = $res->Fetch() ) {
      //dump( $row );
      $points[ $row['teamID'] ] = [ 'kills' => $row['kills'], 'total' => $row['total'] ];
    }
    return $points;
  }
  return false;
}

function countKillsByMatchesIDs( $IDs = array() ){
    GLOBAL $DB;
    if( is_array($IDs) && count($IDs) ){
        foreach( $IDs as $k=>$v ){
            $IDs[$k] = $v+0;
        }
        $sql = 'SELECT   t.PROPERTY_15 AS teamID
                    , sum(t.PROPERTY_17) AS kills 
                    , t.PROPERTY_14 AS matchID 
              FROM b_iblock_element_prop_s5 AS t 
              WHERE t.PROPERTY_14 IN ('.implode(',',$IDs).')
              GROUP BY t.PROPERTY_15, t.PROPERTY_14 
              ORDER BY t.PROPERTY_15';
        $res = $DB->Query($sql);
        $IDs = array_flip($IDs);
        //dump($IDs);
        $kills = [];
        //$i = 0;
        while( $row = $res->Fetch() ) {


           // if ($row['matchID'] != $tmp) {

            //}
            $nm = $IDs[$row['matchID']];
            $kills[$row['teamID']][$nm] = ceil($row['kills']);


            //if ($row['matchID'] != $tmp) {
              //$i++;
            //}
           // $tmp = $row['matchID'];

            //$tmp = $row['matchID'];
        }
        return $kills;
    }
    return false;
}

function countWWCD( $IDs = array() ){
    GLOBAL $DB;
    if( is_array($IDs) && count($IDs) ){
        foreach( $IDs as $k=>$v ){
            $IDs[$k] = $v+0;
        }
        /*$sql = 'SELECT   t.PROPERTY_15 AS teamID
                    ,sum(t.PROPERTY_17) AS kills
                    ,sum(t.PROPERTY_18) AS total
              FROM b_iblock_element_prop_s5 AS t
              WHERE t.PROPERTY_14 IN ('.implode(',',$IDs).')
              GROUP BY t.PROPERTY_15';*/
        $sql = 'SELECT t.PROPERTY_15 AS teamID, 
       COUNT(t.PROPERTY_16) AS wins 
        FROM b_iblock_element_prop_s5 AS t 
        WHERE t.PROPERTY_16 = 1 
        AND t.PROPERTY_14 IN ('.implode(',',$IDs).')
        GROUP BY t.PROPERTY_15';
        $res = $DB->Query($sql);
        $wins = [];
        while( $row = $res->Fetch() ) {

            $wins[ $row['teamID'] ] = [ 'wins' => $row['wins'] ];
        }
        return $wins;
    }
    return false;
}


//function



if (!empty($teamIds)) {
  $chainMatches = getChainMatches( $arResult['ID'] );
  //dump( $chainMatches );
  $points = countPointsByMatchesIDs( $chainMatches['chain'] );
  //dump($points);
  $wins =countWWCD($chainMatches['chain']);

  $matchKills = countKillsByMatchesIDs($chainMatches['chain']);

  //dump($wins);
    $titleParticipants = 'Участники игры';
    if(!empty( $points)) $titleParticipants = 'Результаты игры';

    //dump($points);

  ?>
<section class="match-participants <?php if(!empty($wins)) echo ' match-participants_rank';?> bg-blue-lighter">
  <div class="container">
    <h2 class="game-schedule__heading text-center"><?php echo $titleParticipants; ?></h2>
    <div class="game-schedule-table">
      <div class="flex-table">
        <div class="flex-table--header bg-blue-lighter">
          <div class="flex-table--categories">
            <?php if(!empty( $wins)) { ?>
              <span>Ранк</span>
            <?php } ?>
            <span>Команда</span>
            <span>WWCD</span>
            <span>KILL PTS</span>
              <span>PLACE PTS</span>
            <span>TOTAL PTS</span>
          </div>
        </div>
        <div class="flex-table--body">
    <?php
    $arrForRank = [];
    $n = 0;
    foreach ($teamIds as $teamId) {
        //$n=+1;

        $team = getTeamById($teamId);
        $total = '...';
        $kills = '...';
        $wwcd = '0';
        $place = '...';

        if( isset( $points[$teamId] ) ){
            $total = ceil($points[$teamId]['total']);
            $kills = ceil($points[$teamId]['kills']);
            $place = $total - $kills;
            //dump($place);
        }
        if(isset($wins[$teamId])) {
            $wwcd = $wins[$teamId]['wins'];
        }
        $rank = '-';
        $arrForRank[$n] = [
            'total' =>$total,
            'kills' => $kills,
            'wwcd' => $wwcd,
            'place' => $place,
            'team' => $team
        ];
        $n = $n+1;
    }

//dump($arrForRank);

    function sortRank(&$arrForRank, $matchKills)
    {
      //dump($matchKills);

        $n = sizeof($arrForRank);
        $len = sizeof($matchKills[$arrForRank[0]['team']['ID']]);
        // Traverse through all array elements
        for($i = 0; $i < $n; $i++)
        {
            // Last i elements are already in place
            for ($j = 0; $j < $n - $i - 1; $j++)
            {
                // traverse the array from 0 to n-i-1
                // Swap if the element found is greater
                // than the next element
                if ($arrForRank[$j]['total'] < $arrForRank[$j+1]['total'])
                {
                    $t = $arrForRank[$j];
                    $arrForRank[$j] = $arrForRank[$j + 1];
                    $arrForRank[$j + 1] = $t;
                } else if (($arrForRank[$j]['total'] == $arrForRank[$j+1]['total']) && ($arrForRank[$j]['kills'] < $arrForRank[$j+1]['kills'])) {
                    $t = $arrForRank[$j];
                    $arrForRank[$j] = $arrForRank[$j+1];
                    $arrForRank[$j+1] = $t;
                } else if (
                  ($arrForRank[$j]['total'] == $arrForRank[$j+1]['total']) &&
                  ($arrForRank[$j]['kills'] == $arrForRank[$j+1]['kills']) &&
                  ($matchKills[$arrForRank[$j]['team']['ID']][$len-1] < $matchKills[$arrForRank[$j+1]['team']['ID']][$len-1])) {
                    $t = $arrForRank[$j];
                    $arrForRank[$j] = $arrForRank[$j+1];
                    $arrForRank[$j+1] = $t;
                } else if (
                    ($arrForRank[$j]['total'] == $arrForRank[$j+1]['total']) &&
                    ($arrForRank[$j]['kills'] == $arrForRank[$j+1]['kills']) &&
                    ($matchKills[$arrForRank[$j]['team']['ID']][$len-1] == $matchKills[$arrForRank[$j+1]['team']['ID']][$len-1]) &&
                    ($matchKills[$arrForRank[$j]['team']['ID']][$len-2] < $matchKills[$arrForRank[$j+1]['team']['ID']][$len-2])
                ) {
                    $t = $arrForRank[$j];
                    $arrForRank[$j] = $arrForRank[$j+1];
                    $arrForRank[$j+1] = $t;
                }
            }
        }

    }

    sortRank($arrForRank, $matchKills);
    //dump($arrForRank);


    foreach ($arrForRank as $rank => $teamRank) {
      $rank+=1;
      //dump($teamRank);
            //$team = getTeamById($teamId);
            //dump($team);
            //dump($team);
            /*$total = '...';
            $kills = '...';
            $wwcd = '0';
            if( isset( $points[$teamId] ) ){
                $total = ceil($points[$teamId]['total']);
                $kills = ceil($points[$teamId]['kills']);
                $place = $total - $kills;
                //dump($place);
            }
            if(isset($wins[$teamId])) {
                $wwcd = $wins[$teamId]['wins'];
            }
            $rank = '-';*/

        ?>
          <div class="flex-table--row">
              <?php if(!empty( $wins)) { ?>
                <span class="flex-table__param-wrap">
                 <div class="flex-table__param">Rank</div>
                  <?php echo $rank; ?>
                </span>
              <?php } ?>
                <span>
                  <div class="match-participants__team">
                    <div class="match-participants__team-logo" style="background-image: url(<?php echo CFile::GetPath($teamRank['team']["LOGO_TEAM"]['VALUE']); ?>">
                    </div>
                    <a href="/teams/<?php echo $teamRank['team']['ID'];?>/" class="match-participants__team-link"><?php echo $teamRank['team']['NAME']; ?> [<?php echo $teamRank['team']["TAG_TEAM"]['VALUE']; ?>]</a>
                  </div>
                </span>
            <span class="flex-table__param-wrap">
                 <div class="flex-table__param">WWCD</div>
                  <?php echo $teamRank['wwcd'] ?>
                </span>

            <span class="flex-table__param-wrap">
                 <div class="flex-table__param">KillPTS</div>
                 <?php echo $teamRank['kills'] ?>
                </span>
              <span class="flex-table__param-wrap">
                 <div class="flex-table__param">PLACE PTS</div>
                 <?php echo $teamRank['place'] ?>
                </span>
              <span class="flex-table__param-wrap">
                 <div class="flex-table__param">Total</div>
                 <?php echo $teamRank['total'] ?>
                </span>
          </div>
    <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php  } else { ?>
  <h2 class="game-schedule__heading text-center">Команд участвующих в этом матче еще нет</h2>
<? } ?>