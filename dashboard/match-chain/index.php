<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Цепочка матчей");
CModule::IncludeModule("iblock");
?>

<?php
$firstMatchId = $_GET['id']+0;
$activeRes = " show active ";
$activeR = " active ";
$activeInfo = "";
$activeI = "";
if($_SESSION["activeInfo"]){
    $activeRes = "";
    $activeR = "";
    $activeInfo = " show active ";
    $activeI = " active ";
    unset($_SESSION["activeInfo"]);
}
// собираем все ошибки
$errors = [];
// переделать в singleton
GLOBAL $matchMembersResult;
function addMatchMembersResult($idMatch)
{
    GLOBAL $matchMembersResult;
    if (!isset($matchMembersResult[$idMatch])) {
        $membersResult = new \App\MemberResultTable();

        $result = [];

        $res = $membersResult::getList([
            'filter' => [
                'MATCH_ID' => $idMatch
            ]

        ]);
        while ($row = $res->fetch())
        {
            $result[$row['USER_ID']] = $row;
        }
        $matchMembersResult[$idMatch] = $result;
    }
}

function getWinners ($firstMatchID){
    GLOBAL $DB;

    $select = "";
    $join = "";
    $order = "";

    $chainMatches = getChainMatches($firstMatchID);

    foreach ($chainMatches["chain"] as $k => $match ){
        $select = $select.", m{$k}.PROPERTY_17, m{$k}.PROPERTY_18";
        $join = $join." LEFT JOIN b_iblock_element_prop_s5 as m{$k} ON m{$k}.PROPERTY_15 = t.IBLOCK_ELEMENT_ID AND m{$k}.PROPERTY_14 = {$match} ";
        $order =  $order.", m{$k}.PROPERTY_17 DESC";
    }

    $sql = " SELECT t.PROPERTY_21 as name, t.PROPERTY_19 as avatar, t.PROPERTY_1 as tag, (total - kills) as placement, kills, total, r1.teamID as id_team" . $select . " FROM (SELECT t.PROPERTY_28 as teamID FROM b_iblock_element_prop_s6 as t
INNER JOIN b_iblock_element_prop_s1 as n ON t.PROPERTY_28 = n.IBLOCK_ELEMENT_ID
AND t.PROPERTY_27 = ".$firstMatchID.") as r1
INNER JOIN (SELECT t.PROPERTY_15 AS teamID
                    , sum(t.PROPERTY_18) AS total
                    , sum(t.PROPERTY_17) AS kills
              FROM b_iblock_element_prop_s5 AS t 
              WHERE t.PROPERTY_14 IN (" . implode(',',$chainMatches["chain"]) . ")
              GROUP BY t.PROPERTY_15) as r2 ON r2.teamID = r1.teamID
INNER JOIN b_iblock_element_prop_s1 as t ON r1.teamID = t.IBLOCK_ELEMENT_ID
" . $join . "
ORDER BY total DESC, kills DESC". $order ." LIMIT 6";
    $rsData = $DB->Query($sql);
    $winners = [];
    while($el = $rsData->fetch()) {
        $winners[] = $el["id_team"];
    }
    return $winners;
}


function getChainMatches( $firstMatchID ){
    GLOBAL $DB;
    $firstMatchID += 0;
    $sql = 'SELECT  m.IBLOCK_ELEMENT_ID AS matchID 
                    ,m.PROPERTY_8 AS parentMatchID
                    ,m.PROPERTY_22 AS stageMatch
                    ,m.PROPERTY_23 AS typeMatch
              FROM b_iblock_element_prop_s3 AS m 
              WHERE m.IBLOCK_ELEMENT_ID = '.$firstMatchID;
    $res = $DB->Query($sql);
    if($row = $res->Fetch()) {
        $chain = $row;
        $chain[ 'chain' ] = [ $firstMatchID ];
        $mID = $firstMatchID;
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

function updateTeam($props = [], $id)
{
    CIBlockElement::SetPropertyValuesEx($id, 1, $props);
}

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

function setStagePass($firstMatchID, $stageKeyPass){
    $matchChainIds = getChainMatches( $firstMatchID );
    $points = countPointsByMatchesIDs($matchChainIds['chain']);
    $matchKills = countKillsByMatchesIDs($matchChainIds['chain']);

    $teamIds = getMembersIdsTeamByMatchId($firstMatchID);
    $teamIds = array_diff($teamIds, array(''));

    $arrForRank = [];
    $n = 0;
    foreach ($teamIds as $teamId) {

        $total = '...';
        $kills = '...';
        $place = '...';

        if( isset( $points[$teamId] ) ){
            $total = ceil($points[$teamId]['total']);
            $kills = ceil($points[$teamId]['kills']);
            $place = $total - $kills;
        }

        $arrForRank[$n] = [
                'id'=>$teamId,
                'total' =>$total,
                'kills' => $kills,
                'place' => $place,
        ];
        $n = $n+1;
    }
    sortRank($arrForRank, $matchKills);

    for ($i=0; $i < 6; $i++){
        $props["STAGE_PASS"] = $stageKeyPass;
        updateTeam($props, $arrForRank[$i]["id"]);
    }
}


function isMatchRes($matchID){
    GLOBAL $DB;
    $sql = 'SELECT * FROM b_iblock_element_prop_s5 as t WHERE t.PROPERTY_14 = '.$matchID;
    $res = $DB->Query($sql);
    $points = [];
    while( $row = $res->Fetch() ) {
        $points[] = $row;
    }
    $isRes = false;
    if (count($points) > 0){
        $isRes = true;
    }
    return $isRes;
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

//    $coreTeam = getCoreTeam($teamId);
//    foreach ($coreTeam as $key => $val) {
//        $coreTeam[$key] = $val['ID'];
//    }

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

        return $arrPlayers;
    }
    return false;
}

function getUsers($ids)
{
    $filter = Array('ID' => implode('|', $ids));
    // $arParams["SELECT"] = array("UF_*");
    $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, false);
    // $elementsResult->NavStart(50);
    $output = [];
    while ($rsUser = $elementsResult->Fetch())
    {
        $output[] = $rsUser;
    }
    return $output;
}





// получаем матч по id
function getMatchById($matchId) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" =>3, "ID" => $matchId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        return $arFields;
    }
    return null;
}
//PROPERTY_STREAMER.NAME
function getMatchByIds( $matchesId) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*", "PROPERTY_STREAMER.NAME", "PROPERTY_STREAMER.ID");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" =>3, "ID" => $matchesId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

function getMatchByParentId($parentId) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" => 3, "PROPERTY_PREV_MATCH" => $parentId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

function getMembersListByMatchId($matchId) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" => 4, "PROPERTY_WHICH_MATCH" => $matchId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

function getResultByMatchTeam($matchId, $teamId) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" => 5, "PROPERTY_WHICH_MATCH" => $matchId, "PROPERTY_WHICH_TEAM" => $teamId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

function makeFormMemberResult($matchId, $teamId)
{
    $matchId+=0;
    $teamId+=0;
    if ($teamId > 0) {
        $kill = $total = $place = '';
        $results = getResultByMatchTeam($matchId, $teamId);
        if ($results) {
            $kill = $results['KILL']['VALUE'];
            $place = $results['PRIZE_PLACE']['VALUE'];
            $total = $results['TOTAL']['VALUE'];
        }
        echo '<div class="col-md-3">
            <div class="form-group">
                <label>Total</label>
                <input type="text" class="form-control" name="total['.$teamId.']" value="'.$total.'" readonly>
            </div>
          </div>';
        echo '<div class="col-md-3">
          <div class="form-group">
              <label>Kill</label>
              <input type="text" class="form-control" name="kill['.$teamId.']"  value="'.$kill.'" readonly>
          </div>
        </div>';
        echo '<div class="col-md-3">
        <div class="form-group">
            <label>Place</label>
            <input type="text" class="form-control" name="place['.$teamId.']" value="'.$place.'">
        </div>
      </div>';
    }
}

// получаем участников команды
function getSquadByIdMatch($idMatch, $idTeam)
{
    $arSelect = Array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_*",
    );
    $arFilter = Array(
        "IBLOCK_ID" =>6,
        "PROPERTY_MATCH_STAGE_ONE" => $idMatch,
        "PROPERTY_TEAM_ID" => $idTeam,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $squad = [];
    if($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $squad = array_merge($arFields, $arProps);
        return $squad;
    }
    return false;
}

// получим результат
function getScoreMatch($idMatch, $idTeam)
{
    $arSelect = Array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_*",
    );
    $arFilter = Array(
        "IBLOCK_ID" =>5,
        "PROPERTY_WHICH_MATCH" => $idMatch,
        "PROPERTY_WHICH_TEAM" => $idTeam,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

    if($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        return $arFields;

    }
    return false;
}

function getStreamers()
{

    $arSelect = array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM");
    $arFilter = array(
        "IBLOCK_ID" => 8,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $arFields = [];
    while ($ob = $res->GetNextElement()) {
        $arFields[] = $ob->GetFields();
    }
    return $arFields;
}

function createResults($props = [], $code)
{
    $el = new CIBlockElement;
    $iblock_id = 5;
    $params = Array(
        "max_len" => "100", // обрезает символьный код до 100 символов
        "change_case" => "L", // буквы преобразуются к нижнему регистру
        "replace_space" => "-", // меняем пробелы на нижнее подчеркивание
        "replace_other" => "-", // меняем левые символы на нижнее подчеркивание
        "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
        "use_google" => "false", // отключаем использование google
    );
    $fields = array(
        "DATE_CREATE" => date("d.m.Y H:i:s"), //Передаем дата создания
        "CREATED_BY" => $GLOBALS['USER']->GetID(),    //Передаем ID пользователя кто добавляет
        "IBLOCK_SECTION_ID" => false,
        "CODE" => CUtil::translit($code, "ru" , $params),
        "IBLOCK_ID" => $iblock_id, //ID информационного блока он 24-ый
        "PROPERTY_VALUES" => $props, // Передаем массив значении для свойств
        "NAME" => $code,
        "ACTIVE" => "Y", //поумолчанию делаем активным или ставим N для отключении поумолчанию
    );
    //Результат в конце отработки
    if ($ID = $el->Add($fields)) {
        return $ID;
    } else {
        return "Error: ".$el->LAST_ERROR;
    }
}

// обновление даты и времени, лобби id, ссылки на трансляцию
function updateMatch($props = [], $idMatch, $firstMatchId)
{
    CIBlockElement::SetPropertyValuesEx($idMatch, 3, $props);
    //header('Location: /dashboard/match-chain/?id='.$firstMatchId);
}

function updateResults($props = [], $idResult)
{
    CIBlockElement::SetPropertyValuesEx($idResult, 5, $props);
}


// делает форму для внесеня резельтата
function makeFormSquadResult($firstMatchId, $matchId, $teamId)
{
    GLOBAL $matchMembersResult;
    $teamId+=0;
    $coreTeam = getUsers(getPlayersSquadByIdMatch($firstMatchId, $teamId));
    $squadMembers = [];

    if ($squadMembers = getPlayersSquadByIdMatch($firstMatchId, $teamId)) {
        $squadMembers = array_flip($squadMembers);
    }


    //return;
    //$matchId+=0;
    if ($teamId > 0) {
        if (isset($coreTeam)) {
            foreach ($coreTeam as $player)  {
                if(isset($squadMembers[$player['ID']])) {
                    $memberKill = '';
                    if (isset($matchMembersResult[$matchId]) && isset($matchMembersResult[$matchId][$player['ID']])) {
                        $memberKill = $matchMembersResult[$matchId][$player['ID']]['KILLS'];
                    }
                    echo '
                      <div class="col-md-3 text-right"><span class="badge badge-dark">#'. $player['ID']. ' ' . $player['LOGIN']. '</span></div>
                     <div class="col-md-3"></div>
                      <div class="col-md-3">
                        <div class="form-group">
                              <input type="text" class="form-control" name="memberKill['.$teamId.']['.$player['ID'].']"  value="'.$memberKill.'">
                         </div>
                    </div><div class="col-md-3"></div>';
                }
            }
        }
    }
    /*if ($teamId > 0) {
        //dump(getSquadByIdMatch($matchId));
        //$kill = $total = $place = '';
        //$results = getResultByMatchTeam($matchId, $teamId);
        //if ($results) {
            //$kill = $results['KILL']['VALUE'];
           // $place = $results['PRIZE_PLACE']['VALUE'];
            //$total = $results['TOTAL']['VALUE'];
        //}

        echo '<td></td>';
        echo '<td>Kill <input name="kill['.$teamId.']"  value="'.$kill.'"></td>';
        echo '<td></td>';
    }*/
}

function showMatchInfo($match = null)
{

    $firstMatchId = $_GET['id']+0;
    $members = getMembersListByMatchId($match['ID']);
    $streamers = getStreamers();
    $mods = getUsersByGroup(8);

    if ($members) {
        addMatchMembersResult($match['ID']);
        echo '<form id="deleteForm" action="'.POST_FORM_ACTION_URI.'" method="post">'. bitrix_sessid_post() .'</form>
<form  action="#" method="post">';
        echo '<div class="row">
          <div class="col-md-4">
          <div class="form-group">
             <label>Укажите время</label>
              <div style="position: relative;"><input type="text" class="form-control dashboard-time" value="'.$match["DATE_START"]['VALUE'].'" name="date_time_match"></div>
           </div>
           </div>';
        echo '<div class="col-md-4">
            <div class="form-group">
             <label>Ссылка на трансляцию</label>
              <input type="text" class="form-control" value="'.$match["URL_STREAM"]['VALUE'].'" name="url_stream">
           </div>
           </div>';
        echo '<div class="col-md-4">
            <div class="form-group">
             <label>PUBG LOBBY ID</label>
              <input type="text" class="form-control" value="'.$match["PUBG_LOBBY_ID"]['VALUE'].'" name="pubg_lobby_id">
           </div>
           </div>';

        echo '<div class="col-md-4">
            <div class="form-group">
            
                <label>Выберите комментатора</label>
                <select class="form-control" name="streamer">
                <option value="">0</option>';

        foreach ($streamers as $streamer) {
            echo '<option value="'.$streamer['ID'].'"';
            if($streamer['ID'] == $match["STREAMER"]['VALUE']) { echo ' selected'; }
            echo '>'.$streamer['NAME'].'</option>';
        }
        echo '</select>
              </div>
           </div>';

        if ( CSite::InGroup( array(1) ) ){
            echo '<div class="col-md-4">
            <div class="form-group">
            
                <label>Выберите модератора</label>
                <select class="form-control" name="mod">
                <option value="">0</option>';

            foreach ($mods as $mod) {
                echo '<option value="'.$mod['ID'].'"';
                if($mod['ID'] == $match["MODERATOR"]['VALUE']) { echo ' selected'; }
                echo '>'.$mod['LOGIN'].'</option>';
            }
            echo '</select>
              </div>
           </div>';
        }

        echo '</div>';
        echo '<input type="submit" class="btn btn-success my-3" name="sendInfo" value="Сохранить и отправить данные">';
        echo '<input type="hidden" value="'.$match['ID'].'" name="matchId">';
        echo '</form>';


    }
}

function showMatchResults($match = null, $next)
{

    $firstMatchId = $_GET['id']+0;
    $members = getMembersListByMatchId($match['ID']);
    $streamers = getStreamers();
    $mods = getUsersByGroup(8);

    if ($members) {
        addMatchMembersResult($match['ID']);
        echo '<form id="deleteForm" action="'.POST_FORM_ACTION_URI.'" method="post">'. bitrix_sessid_post() .'</form>
<form  action="#" method="post">';

        //echo '<table>';
        $tmpKeys = [
            '03',
            '04',
            '05',
            '06',
            '07',
            '08',
            '09',
            '10',
            '11',
            '12',
            '13',
            '14',
            '15',
            '16',
            '17',
            '18',
            '19',
            '20',
        ];

        foreach ($tmpKeys as $key) {
            $curTeamId = $members["TEAM_PLACE_$key"]['VALUE'];
            $teamName = '';


            if(!empty($curTeamId)) {

                $team = getTeamById($curTeamId);
                // $coreTeam = getCoreTeam($curTeamId);

                $squadMembers = [];
//            if ($squadMembers = getPlayersSquadByIdMatch($firstMatchId, $curTeamId)) {
//                $squadMembers = array_flip($squadMembers);
//            }

                $teamName = '<div>
                            <img width="50" style="margin-right: 5px" src="'. CFile::GetPath($team["LOGO_TEAM"]['VALUE']) .'" alt="">
                            <span class="badge badge-danger"><a href="/teams/?ELEMENT_ID='.$team['ID'].'" class="text-white">' . $team['NAME'] . ' ['. $team["TAG_TEAM"]['VALUE']. ']</a></span>
                            
                                <div class="text-center">
                                
                                <button type="submit" class="btn btn-outline-danger" name="removeTeamFromMatch" form="deleteForm" value="'.$team['ID'].'"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
  <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
</svg></button>
                                </div>
                      
                        </div>';
                /* $teamName.= '<span class="badge badge-dark">Список игроков</span>
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
                     <tbody>';
                 foreach ($coreTeam as $player)  {
                     if(isset($squadMembers[$player['ID']])) {
                       $frag = !$player["UF_FRAGS"] ? '0' : $player["UF_FRAGS"];
                       $playedGames = !$player["UF_PLAYED_GAMES"] ? '0' : $player["UF_PLAYED_GAMES"];
                       $rating = !$player["UF_RATING"] ? '0' : $player["UF_RATING"];
                       $teamName.= '<tr>
                           <td>'. $player['ID'].'</td>
                           <td>'. $player['LOGIN'].'</td>
                           <td>'. $playedGames .'</td>
                                            <td>'. $frag.'</td>
                                            <td>'. $rating.'</td>

                         </tr>';
                     }
                 }
                 $teamName.= '</tbody>
                              </table>';*/
            }
            echo '<div class="row my-3 align-items-center">
                <div class="col-md-3">TEAM-PLACE-'.$key.':<br> ' . $teamName . '</div>';
            if ($curTeamId+0 > 0) {

                makeFormMemberResult($match['ID'], $curTeamId);

                makeFormSquadResult($firstMatchId, $match['ID'], $curTeamId);

            }
            echo '</div>';
        }

        echo '<input type="submit" class="btn btn-success my-3" name="sendResults" value="Сохранить и отправить результаты">';
        if ($next){
            echo '<input type="hidden" value="'.$next.'" name="last">';
        }
        echo '<input type="hidden" value="'.$match['ID'].'" name="matchId">';
        echo '</form>';

    }
}

function createMembersResult($userId, $matchId, $kill, $total = 0, $place = 0, $typeMatch = null)
{

    if(isset($kill) && $kill !== '')  {
        $membersResult = new \App\MemberResultTable();
        $result = $membersResult::add(array(
            'USER_ID' => $userId,
            'MATCH_ID' => $matchId,
            'TOTAL' => $total,
            'KILLS' => $kill,
            'PLACE' => $place,
            'TYPE_MATCH' => $typeMatch,
        ));
        if ($result->isSuccess())
        {
            return true;
        }
    }
    return  false;
}

function updateMemberResult($idRecord, $kill, $total = 0, $place = 0, $typeMatch = null)
{
    $membersResult = new \App\MemberResultTable();
    $result = $membersResult::update($idRecord, array(
        'KILLS' => $kill,
        'TOTAL' => $total,
        'PLACE' => $place,
        'TYPE_MATCH' => $typeMatch,
    ));
    if ($result->isSuccess())
    {
        return true;
    }
    return  false;
}
// собираем цепочку матчей
function getChainMatchesByParentId($parentId)
{
    $chainMatches = [];
    $chainMatches[] = $parentId;
    do {
        $match = getMatchByParentId($parentId);

        if ($match != null) {
            $nextMatch = true;
            $chainMatches[] = $match['ID'];
            $parentId = $match['ID'];
        } else {
            $nextMatch = false;
        }


    } while($nextMatch == true);
    return $chainMatches;
}

/*function for remove teame with match*/
function getPropertyPlace($idMatch)
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
                $arrTeams[$name] = $arProps[$name]['VALUE'];
            }
        }
        return $arrTeams;
    }
    return  false;
}

// получаем участников матчей по полю which_match передавая id
function getMembersByMatchIdForRemoveTeamWithMatch($matchId) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array(
        "IBLOCK_ID" => 4,
        "PROPERTY_WHICH_MATCH" => $matchId,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $output = [];

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();

        foreach ($arProps as $k=>$v) {
            $arFields[$k] = $v['VALUE'];
        }
        $output[] = $arFields;
    }
    return $output;
}

function updateMembers($props = [], $id)
{
    CIBlockElement::SetPropertyValuesEx($id, 4, $props);
}
/*end function for remove teame with match*/


function countResultsByTeam($teamId, $typeMatch) {
    $teamId+=0;

    $kill = 0;
    //if ($_POST["memberKill"]) {}
    //$squad = getPlayersSquadByIdMatch($_POST["matchId"]+0, $teamId);

    if (isset($_POST["memberKill"][$teamId]) && is_array($_POST["memberKill"][$teamId]) != '') {
        foreach ($_POST["memberKill"][$teamId] as $k) {
            $k+=0;
            $kill = $kill+$k;
        }
    }
    $total = 0;
    if (isset($_POST["place"][$teamId]) && $_POST["place"][$teamId] != '') {
        $t = $_POST["place"][$teamId]+0;

        if ($typeMatch == 6) {
            switch ($t) {
                case 1:
                    $total = 12;
                    break;
                case 2:
                    $total = 10;
                    break;
                case 3:
                    $total = 8;
                    break;
                case 4:
                    $total = 6;
                    break;
                case 5:
                    $total = 4;
                    break;
                case 6:
                    $total = 2;
                    break;
                case 7:
                    $total = 1;
                    break;
                case 8:
                    $total = 1;
                    break;
                case 9:
                    $total = 0;
                    break;
                case 10:
                    $total = 0;
                    break;
                case 11:
                    $total = -2;
                    break;
                case 12:
                    $total = -4;
                    break;
                case 13:
                    $total = -6;
                    break;
                case 14:
                    $total = -8;
                    break;
                case 15:
                    $total = -10;
                    break;
                case 16:
                    $total = -12;
                    break;
                case 17:
                    $total = -14;
                    break;
                case 18:
                    $total = -16;
                    break;
                case 19:
                    $total = -16;
                    break;
                case 20:
                    $total = -16;
                    break;
            }
        } else if ($typeMatch == 5) {
            switch ($t) {
                case 1:
                    $total = 15;
                    break;
                case 2:
                    $total = 12;
                    break;
                case 3:
                    $total = 10;
                    break;
                case 4:
                    $total = 8;
                    break;
                case 5:
                    $total = 6;
                    break;
                case 6:
                    $total = 4;
                    break;
                case 7:
                    $total = 2;
                    break;
                case 8:
                    $total = 1;
                    break;
                case 9:
                    $total = 1;
                    break;
                case 10:
                    $total = 1;
                    break;
                case 11:
                    $total = 1;
                    break;
                case 12:
                    $total = 1;
                    break;
                case 13:
                    $total = 0;
                    break;
                case 14:
                    $total = 0;
                    break;
                case 15:
                    $total = 0;
                    break;
                case 16:
                    $total = 0;
                    break;
                case 17:
                    $total = 0;
                    break;
                case 18:
                    $total = 0;
                    break;
                case 19:
                    $total = 0;
                    break;
                case 20:
                    $total = 0;
                    break;
            }
        }

    }
    $total += $kill;
    $result = [
        'kill' => ''.$kill,
        'total' => ''.$total,
    ];
    return $result;
}

function saveMembersResult($teamId, $matchId, $countResult, $place, $typeMatch)
{
    $res = false;
    if (isset($_POST['memberKill'][$teamId]) && is_array($_POST['memberKill'][$teamId])) {
        GLOBAL $matchMembersResult;
        $total = $countResult['total']+0;

        foreach ($_POST['memberKill'][$teamId] as $memberId => $qtwKill) {

            // проверить существует ли в сущности результатов игрокоав записи по этому матчу
            if ($matchMembersResult[$matchId][$memberId]) {
                $recordId = $matchMembersResult[$matchId][$memberId]['ID'];
                if ($qtwKill === '') {
                    // delete record  by $recordId
                    $membersResult = new \App\MemberResultTable();
                    $result = $membersResult::delete($recordId);
                } else {
                    //
                    updateMemberResult($recordId, $qtwKill, $total, $place, $typeMatch);
                }
            } else {
                if ($qtwKill !== '') {
                    createMembersResult($memberId, $matchId, $qtwKill, $total, $place, $typeMatch);
                }
            }
        }
        $res = true;

    }
    return $res;
}

$firstMatch = getMatchByIds($firstMatchId);
$stageKeyPass = $firstMatch["TOURNAMENT"]["VALUE"]. "." .$firstMatch["STAGE_TOURNAMENT"]["VALUE_ENUM_ID"];
// снимаем команду с матча
if (check_bitrix_sessid() && (!empty($_REQUEST["removeTeamFromMatch"]))) {

    $now = date('d.m.Y H:i:s');
    $dateStartMatch = $firstMatch["DATE_START"]['VALUE'];
    $dateA = DateTime::createFromFormat('d.m.Y H:i:s', $now);
    $dateB = DateTime::createFromFormat('d.m.Y H:i:s', $dateStartMatch);


    // получаем цепочку матчей
    $chainMatches = getChainMatchesByParentId($firstMatchId);

    $results = getResultByMatchTeam($firstMatchId, $_POST["removeTeamFromMatch"]);
    if ($results) {
        createSession("match-chain_error", 'По матчу уже сформированы результаты');
    } else if($dateA > $dateB) {
        createSession("match-chain_error", 'Матч уже прошел');
    } else {
        // получаем squad
        $curSquadId = getSquadByIdMatch($firstMatchId, $_POST["removeTeamFromMatch"]);
        // получили место
        if ($propertyPlace = getPropertyPlace($chainMatches)) {
            $propertyPlace = array_flip($propertyPlace);
            $propertyPlace = $propertyPlace[$_POST["removeTeamFromMatch"]];

            // есть цепочка матчей тут $chainMatches
            // получаем участников матчей
            $resMembersMatches = getMembersByMatchIdForRemoveTeamWithMatch($chainMatches);
            // создаем массив id записей участников
            $membersMatches = [];
            // если пришел список участников
            if ($resMembersMatches) {
                foreach ($resMembersMatches as $membersMatch) {
                    // наполняем $membersMatches
                    $membersMatches[] = $membersMatch['ID'];
                }
                foreach ($membersMatches as $id) {
                    $props = [];
                    $props[$propertyPlace] = null;
                    // бежим по записям и удаляем нашу команду с места
                    updateMembers($props, $id);

                }
                // удаляем squd
                if (!empty($curSquadId)) {
                    CIBlockElement::Delete($curSquadId['ID']);
                    //header('Location: /dashboard/match-chain/?id='.$firstMatchId);
                }
            }

        }
    }
}

if (!empty($_POST["sendResults"])) {
    $matchId = $_POST['matchId']+0;
    addMatchMembersResult($matchId);

    if (!empty($matchId)) {

        $scoreError = false;
        $scoreKeys = [
            'kill',
            'total',
            'place'
        ];
        $scoreTeamIds = [];
        $scoreTeamCount = 0;
        foreach ($scoreKeys as $key) {
            if (isset($_POST[$key]) && is_array($_POST[$key])) {
                // проверили что в массиве одинаковое кол-во команд
                if ($scoreTeamCount == 0) {
                    $scoreTeamCount = count($_POST[$key]);
                } else {
                    if ($scoreTeamCount != count($_POST[$key])) {
                        $scoreError = true;
                    }
                }
                foreach ($_POST[$key] as $k => $v) {
                    $scoreTeamIds[$k] = $k;
                }
            }
        }
        if ($scoreError == false && $scoreTeamCount > 0) {

            if (count($scoreTeamIds) == $scoreTeamCount) {


                foreach ($scoreTeamIds as $teamId) {
                    $score = getScoreMatch($matchId, $teamId);
                    // получили матч по id
                    $resMatch = getMatchById($matchId);
                    $countResult = countResultsByTeam($teamId, $resMatch["PROPERTY_23"]);
                    $resSaveTeamPlace = false;


                    if ($score) {
                        // update этой записи

                        $props = [];
                        $props['PRIZE_PLACE'] = $_POST["place"][$teamId];
                        $props['KILL'] = $countResult['kill'];//$_POST["kill"][$teamId];
                        $props['TOTAL'] = $countResult['total'];//$_POST["total"][$teamId];


                        $resSaveTeamPlace = updateResults($props, $score["ID"]);

                    } else {
                        // сделать новую запись


                        if ($resMatch) {
                            $props = [];
                            $props['WHICH_TEAM'] = $teamId;
                            $props['WHICH_MATCH'] = $matchId;
                            $props['PRIZE_PLACE'] = $_POST["place"][$teamId];
                            $props['KILL'] = $countResult['kill'];//$_POST["kill"][$teamId];
                            $props['TOTAL'] = $countResult['total'];//$_POST["total"][$teamId];
                            $code = 'RESULT_TEAM_ID_'.$teamId.'#' . $resMatch['ID'] . $resMatch['NAME'];
                            // создадим запись с результатами
                            $resSaveTeamPlace = createResults($props, $code);

                        }

                    }
                    // todo проверить что $resSaveTeamPlace success

                    $res = saveMembersResult($teamId, $matchId, $countResult, $_POST["place"][$teamId], $resMatch["PROPERTY_23"]);

                }
                if(isset($_POST["last"]) && isMatchRes($matchId) && ($firstMatch["TYPE_MATCH"]["VALUE_ENUM_ID"] == 5 && $firstMatch["STAGE_TOURNAMENT"]["VALUE_ENUM_ID"] != 1)){

                    $nextStageId = getNextStage($firstMatch["STAGE_TOURNAMENT"]["VALUE_ENUM_ID"]);
                    $winnersIds = getWinners($firstMatchId);
                    foreach ($winnersIds as $winner){

                    $idNextMatch = findFreeGame($firstMatch["TOURNAMENT"]["VALUE"],  $nextStageId);
                    if($idNextMatch){
                    $chainMatches[] = $idNextMatch;
                    do {
                        $match = getMatchByParentId($idNextMatch);
                        if ($match != null) {
                            $nextMatch = true;
                            $chainMatches[] = $match['ID'];
                            $idNextMatch = $match['ID'];
                        } else {
                            $nextMatch = false;
                        }
                    } while($nextMatch == true);
                    moveWinners($firstMatch, $chainMatches, $winner);
                    setStagePass($firstMatchId, $stageKeyPass);
                    }
                }
                }
                LocalRedirect('/dashboard/match-chain/?id='.$firstMatchId . '&success_update='.$matchId);

            } else {
                createSession("match-chain_error",'С данными что-то не то!');
                LocalRedirect('/dashboard/match-chain/?id='.$firstMatchId);
            }
        } else {
            createSession("match-chain_error",'Данные не однородные');
            LocalRedirect('/dashboard/match-chain/?id='.$firstMatchId);
        }


    }

}

if (!empty($_POST["sendInfo"])) {
    $matchId = $_POST['matchId']+0;
    addMatchMembersResult($matchId);

    if (!empty($matchId) && !empty($_POST['date_time_match'])) {

        $props = [];
        if (CSite::InGroup( array(1))) $props['MODERATOR'] = $_POST['mod'];
        $props['DATE_START'] = $_POST['date_time_match'];
        $props['URL_STREAM'] = $_POST["url_stream"];
        $props['PUBG_LOBBY_ID'] = $_POST["pubg_lobby_id"];
        $props['STREAMER'] = $_POST["streamer"];

        updateMatch($props, $matchId, $firstMatchId);
        $_SESSION["activeInfo"] =  true;
        LocalRedirect('/dashboard/match-chain/?id='.$firstMatchId . '&success_update='.$matchId);

    } else {
        createSession("match-chain_error",'Укажите время матча');
        LocalRedirect('/dashboard/match-chain/?id='.$firstMatchId);
    }

}



//echo $firstMatch["DATE_START"]['VALUE'];
//echo '<br>';
//echo $firstMatch["TYPE_MATCH"]['VALUE'];


?>
    <div class="container my-5">
        <?php if (isset($_SESSION["match-chain_error"])) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">Error!</h4>
                    <p><strong><?php echo $_SESSION["match-chain_error"]; ?></strong></p>
                <hr>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php
            unset($_SESSION['match-chain_error']);
        } ?>
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $activeR ; ?>" id="pills-results-tab" data-toggle="pill" href="#pills-results" role="tab" aria-controls="pills-results" aria-selected="true">Результаты</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $activeI ; ?>" id="pills-info-tab" data-toggle="pill" href="#pills-info" role="tab" aria-controls="pills-info" aria-selected="false">Данные матча</a>
        </li>
    </ul>
      <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade <?php echo $activeRes ; ?>" id="pills-results" role="tabpanel" aria-labelledby="pills-results-tab">
                <h3>Матч 1</h3>
                <?php showMatchResults($firstMatch, false); ?>

                <?php $parentId = $firstMatch['ID'];


                $num = 2;
                do {
                    $match = getMatchByParentId($parentId);
                    $next = (getMatchByParentId($parentId+2) == null);
                    if ($match != null) {

                        ?>
                        <div class="container">
                            <h3>Матч <?php echo $num; ?></h3>
                            <?php showMatchResults($match, $next); ?>
                        </div>
                        <?php $nextMatch = true;
                        $parentId = $match['ID'];
                    } else {
                        $nextMatch = false;
                    }

                $num+=1;
                } while($nextMatch == true);

                ?>
            </div>
            <div class="tab-pane fade <?php echo $activeInfo ; ?>" id="pills-info" role="tabpanel" aria-labelledby="pills-info-tab">
                <h2 class="mb-3">Матч 1</h2>
                <?php
                showMatchInfo($firstMatch); ?>

                <?php $parentId = $firstMatch['ID'];
                $num = 2;
                do {
                    $match = getMatchByParentId($parentId);
                    if ($match != null) {

                        ?>

                            <h2 class="mb-3">Матч <?php echo $num; ?></h2>
                            <?php showMatchInfo($match); ?>

                        <?php $nextMatch = true;
                        $parentId = $match['ID'];
                    } else {
                        $nextMatch = false;
                    }

                    $num+=1;
                } while($nextMatch == true);

                ?>
            </div>
        </div>


    </div>


<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>