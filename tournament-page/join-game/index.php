<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Зарегистрироваться на игру");
CModule::IncludeModule("iblock");
$lang = 'RU';
$messages = [
  'RU' => [
      'UPDATE_SQUAD_SUCCESS' => 'Состав команды на игру успешно обновлен',
      'YOU_ALREADY_ADDED_ON_TOURNAMENT' => 'Ты уже зарегестрирован на этот турнир',
      'GO_TO_MATCH'=>'Перейти к матчу'
  ] ,
    'EN' => [
        'UPDATE_SQUAD_SUCCESS' => 'Squad members changed',
    ]
];
// получаем текушего пользователя
// дергаем у него поле id команды

$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];

// id матча который пришел
//$mId = $_GET['tournament']+0;
$mId = $_GET['mid']+0;
if ($mId == 0) LocalRedirect(SITE_DIR."game-schedule/");
// собираем все ошибки
$errors = [];

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

function getLastTournamentGameTime($tournamentId){

    GLOBAL $DB;
    $sql = "SELECT m.PROPERTY_4 as date FROM b_iblock_element_prop_s3 as m  WHERE m.PROPERTY_3 = ".$tournamentId." ORDER BY m.PROPERTY_4 DESC LIMIT 1";

    $res = $DB->Query($sql);
    $time = "";
    if( $row = $res->Fetch() ) {

        $time = $row["date"];
    }
    return $time;
}

function countPointsByUserID( $userID ){
    GLOBAL $DB;
    $userID += 0;
    if( $userID ){
        $sql = 'SELECT  sum(t.TOTAL) AS total, sum(t.KILLS) AS kills
				FROM b_squad_member_result AS t 
				WHERE t.USER_ID = '.$userID.' AND t.TYPE_MATCH = 6
				GROUP BY t.USER_ID';
        $res = $DB->Query($sql);
        if( $row = $res->Fetch() ) {
            $points = [ 'kills' => $row['kills'], 'total' => $row['total'] ];
            return $points;
        }
    }
    return false;
}

function countPointsAllUsers(){
    GLOBAL $DB;
    $sql = 'SELECT t.USER_ID, count(t.USER_ID) as count_matches, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills
			FROM b_squad_member_result AS t 
			WHERE t.TYPE_MATCH = 6
			GROUP BY t.USER_ID';
    $res = $DB->Query($sql);
    $points = [];
    while( $row = $res->Fetch() ) {
        $points[ $row['USER_ID'] ] = [ 'kills' => $row['kills'], 'total' => $row['total'], 'count_matches' => $row['count_matches'] ];
    }
    return $points;
}

function getQtyPlayedGames($teamId)
{
    GLOBAL $DB;
    $sql = 'SELECT g.GROUP_ID, u.LOGIN, u.PERSONAL_PHOTO, u.ID, IF(total IS NOT Null,total, 0) + IF(r.UF_RATING IS NOT Null, r.UF_RATING, 300) as total, kills 
      FROM  b_user as u 
            LEFT JOIN (SELECT t.USER_ID, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills FROM b_squad_member_result AS t WHERE t.TYPE_MATCH = 6 GROUP BY t.USER_ID) AS r1 ON r1.USER_ID = u.ID 
            LEFT JOIN b_uts_user AS r ON r.VALUE_ID = u.ID
            INNER JOIN b_user_group AS g ON g.USER_ID = u.ID 
            AND g.GROUP_ID = 7
            WHERE r.UF_ID_TEAM = '.$teamId;

    $res = $DB->Query($sql);
    $players = [];
    while( $row = $res->Fetch() ) {
        $players[ $row['ID'] ] = [ 'kills' => $row['kills'],
            'total' => $row['total'],
            'login' => $row['LOGIN'],
            'count_matches' => '',
            'photo' => $row['PERSONAL_PHOTO']
        ];
    }

    $sql = 'SELECT t.USER_ID, count(DISTINCT r1.id1) as count_matches  FROM b_squad_member_result as t
        INNER JOIN 
        (SELECT m.IBLOCK_ELEMENT_ID as id1, m2.IBLOCK_ELEMENT_ID as id2, m3.IBLOCK_ELEMENT_ID as id3 FROM b_iblock_element_prop_s3 as m
        INNER JOIN b_iblock_element_prop_s3 as m2 ON m2.PROPERTY_8 = m.IBLOCK_ELEMENT_ID
        INNER JOIN b_iblock_element_prop_s3 as m3 ON m3.PROPERTY_8 = m2.IBLOCK_ELEMENT_ID
        WHERE m.PROPERTY_8 IS NULL) as r1 ON t.MATCH_ID IN(r1.id1, r1.id2, r1.id3)
        INNER JOIN b_uts_user as u ON u.VALUE_ID = t.USER_ID
        AND u.UF_ID_TEAM = '.$teamId.' 
        GROUP BY t.USER_ID';

    $res = $DB->Query($sql);

    while( $row = $res->Fetch() ) {
        $players[ $row['USER_ID'] ]['count_matches'] = $row['count_matches'];
    }

    return $players;

}


// проверяем, есть ли результаты
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
// получаем матч по id
function getMatchById($matchId) {
    $arSelect = Array("ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_STREAMER.NAME",
        "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" =>3, "ID" => $matchId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
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
/*
function checkTournamentStagePass($teamID, $mId){
    $arMatch = getMatchById($mId);
    $resTeam = getTeamById($teamID);

    if (($resTeam["STAGE_PASS"]["VALUE"] == $arMatch["KEY_STAGE_PASS"]["VALUE"]) || $arMatch["STAGE_TOURNAMENT"]["VALUE_ENUM_ID"] == 7){
        return true;
    }
    return false;
}*/
function checkTournamentStagePass($teamID, $mId){
    $arMatch = getMatchById($mId);
    $resTeam = getTeamById($teamID);

    if(!empty($arMatch["KEY_STAGE_PASS"]["VALUE"])) {
        if ($resTeam["STAGE_PASS"]["VALUE"] == $arMatch["KEY_STAGE_PASS"]["VALUE"]) {
            return true;
        } else {
            return false;
        }
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

// получаем цепочку матчей по id родителя
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


//Проверяем учавствует ли комманда в другом матче в это же время
function isSameTime($teamID, $gameTime, $matchID){
    global $DB;

    $matches = getChainMatches( $matchID );

    $gamesCount = count($matches["chain"]);
    $noRegTime = $gamesCount * 2100;

    $sql = "SELECT m.IBLOCK_ELEMENT_ID as matchID, m.PROPERTY_4  FROM b_iblock_element_prop_s4 as t
    INNER JOIN b_iblock_element_prop_s3 as m ON m.IBLOCK_ELEMENT_ID = t.PROPERTY_13
    WHERE UNIX_TIMESTAMP(m.PROPERTY_4) BETWEEN (UNIX_TIMESTAMP('". $gameTime->format('Y-m-d H:i:s') ."') - 2100) AND (UNIX_TIMESTAMP('". $gameTime->format('Y-m-d H:i:s') ."') + ". $noRegTime. ")
    AND (t.PROPERTY_36 = " . $teamID . "
    OR t.PROPERTY_35 = " . $teamID . "
    OR t.PROPERTY_33 = " . $teamID . "
    OR t.PROPERTY_34 = " . $teamID . "
    OR t.PROPERTY_9 = " . $teamID . "
    OR t.PROPERTY_10 = " . $teamID . "
    OR t.PROPERTY_11 = " . $teamID . "
    OR t.PROPERTY_12 = " . $teamID . "
    OR t.PROPERTY_39 = " . $teamID . "
    OR t.PROPERTY_40 = " . $teamID . "
    OR t.PROPERTY_41 = " . $teamID . "
    OR t.PROPERTY_42 = " . $teamID . "
    OR t.PROPERTY_43 = " . $teamID . "
    OR t.PROPERTY_51 = " . $teamID . "
    OR t.PROPERTY_52 = " . $teamID . ")";
    $rsData = $DB->Query($sql);

    $matches = [];
        while($res = $rsData->fetch()){
            $matches[$res['matchid']] = $res;
        }
    return count($matches);
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
    $key = getPlacesKeys();

    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        foreach ($key as $place=>$name) {
            //dump($arProps[$name]['VALUE']);
          if ($arProps[$name]['VALUE']+0 > 0) {
            $arrTeams[$place] = $arProps[$name]['VALUE'];
          }
        }
        return $arrTeams;
    }
    return  false;
}

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
    $key = getPlacesKeys();
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

//
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


//
function checkRegistrationTeamOnTournament($idTeam, $idTournament, $idStage)
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
                "?NAME" => '_STAGE1'
            ),
            array(
                "?NAME" => '_GROUP'.$idStage
            )
        ),
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        //$arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $matchesId[] = $arProps["WHICH_MATCH"]['VALUE']+0;
    }
    //dump($matchesId);
    if (!empty($matchesId)) {
        foreach ($matchesId as $id) {
            if($tmp = getParticipationByMatchId($id)) {
             // dump($tmp);
                $tmp = array_flip($tmp);
                if (isset($tmp[$idTeam])) {
                    $idRegistrationMatch = $id;
                }
            }
        }
    }
    return $idRegistrationMatch;
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

function getRating($teamID)
{
    global $DB;
    $sql = 'SELECT t2.IBLOCK_ELEMENT_ID as teamID, (IF(t2.PROPERTY_31 IS NOT NULL, t2.PROPERTY_31, 300) + IF(total IS NOT NULL, total, 0)) as rating, t2.PROPERTY_21 as name FROM b_iblock_element_prop_s1 as t2
		LEFT JOIN
      (SELECT t.PROPERTY_15 AS teamID, sum(t.PROPERTY_18) AS total, sum(t.PROPERTY_17) AS kills
      FROM b_iblock_element_prop_s5 AS t
      INNER JOIN b_iblock_element_prop_s3 AS m ON t.PROPERTY_14 = m.IBLOCK_ELEMENT_ID WHERE m.PROPERTY_23 = 6 GROUP BY t.PROPERTY_15) as r1
      ON r1.teamID = t2.IBLOCK_ELEMENT_ID
      WHERE t2.IBLOCK_ELEMENT_ID = '.$teamID;
    $res = $DB->Query($sql);
    if( $row = $res->Fetch()  ) {
        return $row;
    }
    return false;
}

function updateMembers($props = [], $id)
{
    CIBlockElement::SetPropertyValuesEx($id, 4, $props);
}

$redirectUrlAction = false;
$actionErrorText = false;
$actionSuccessText = false;


// текущий матч
$curMatch = getMatchById($mId);


switch($curMatch["COUTN_TEAMS"]["VALUE"]) {
    case 4:
        $mode = "SQUAD";
        break;
    case 2:
        $mode = "DUO";
        break;
    case 1:
        $mode = "SOLO";
        break;
}

// собираем цепочку матчей
$chainMatches = [];
$chainMatches[] = $curMatch['ID'];
$idCurMatch = $mId;


$isPlace = isPlace($curMatch['ID']);



do {
    $match = getMatchByParentId($idCurMatch);
    if ($match != null) {
        $nextMatch = true;
        $chainMatches[] = $match['ID'];
        $idCurMatch = $match['ID'];
    } else {
        $nextMatch = false;
    }
} while($nextMatch == true);


// проверка на участие в игре
$placeName = '';
$star = '';
$squadMembers = [];

if ($tmp = getParticipationByMatchId($mId)) {
    $tmp = array_flip($tmp);

    if (isset($tmp[$teamID])) {

        $star = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="orangered" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
              </svg>';
        $placeName = '<span class="badge badge-danger">' . GetMessage('JG_BADGE') . $tmp[$teamID] . '</span>';

        if ($squadMembers = getPlayersSquadByIdMatch($mId, $teamID)) {
            $squadMembers = array_flip($squadMembers);
        }
    }

}
//dump($squadMembers);

// получаем запись текущего сквада
$curSquad = getSquadByIdMatch($mId, $teamID);

// формирование команды
$squad = [];
$squadId = null;
$playerKeys = [
    'PLAYER_1',
    'PLAYER_2',
    'PLAYER_3',
    'PLAYER_4',
    'PLAYER_5',
    'PLAYER_6',
];
$minLimPlayers = 4;
$maxLimPlayers = 6;
$alertManagementSquad = '';
$objDateTime = new DateTime($curMatch['DATE_START']['VALUE']);
$userProductGroups = CustomSubscribes::getActualUserSubscribeGroup($userID);
if (check_bitrix_sessid() && isset($_REQUEST['btn_create_squad'])) {
//dump($_POST, 1);
    if (!empty($_POST['squad'])) {
            $squad = $_POST['squad'];
            $props = [];
            $tmp = [];
            foreach ($playerKeys as $key => $name) {
                $props[$name] = $squad[$key]+0;
                if ($props[$name] > 0) {
                    $tmp[] = $key;
                }
            }
            $props['MATCH_STAGE_ONE'] = $mId;
            $props['TEAM_ID'] = $teamID;
            $code = 'SQUAD_' . $curMatch['NAME'];



            if (count($tmp) >= $minLimPlayers && count($tmp) <= $maxLimPlayers) {
                if (isset($_POST['update_squad']) || $placeName != '') {
                  //dump($_POST, 1);
                    $props['UPDATE_SQUAD'] = 9;
                    updateSquad($props, $mId);
                    createSession('management-games_success', $messages[$lang]['UPDATE_SQUAD_SUCCESS']);
                    $redirectUrlAction = SITE_DIR."tournament-page/join-game/?mid=".$mId;
                } else {


                    $time = $curMatch['DATE_START']['VALUE'];
                    if($curMatch["TYPE_MATCH"]["VALUE_ENUM_ID"] == 5){
                        $tournamentId = $curMatch["PROPERTY_3"];
                        $time = getLastTournamentGameTime($tournamentId);
                    }

                   // if(willTeamPrem($teamID, $time) || $curMatch["TYPE_MATCH"]["VALUE_ENUM_ID"] == 6){

                    //Проверка регистрации на одно время
                    $teamRating = getRating($teamID);

                    if (($teamRating['rating']>= $curMatch['MIN_RATING']['VALUE'] && $teamRating['rating'] <= $curMatch['MAX_RATING']['VALUE']) || $curMatch['TYPE_MATCH']["VALUE_ENUM_ID"] != 6) {

                        if (isSameTime($teamID, $objDateTime, $curMatch["ID"]) == 0) {
                            if ($idTournament = isTournament($mId)) {

                                $registeredMatchId = checkRegistrationTeamOnTournament($teamID, $idTournament, $curMatch["STAGE_TOURNAMENT"]["VALUE_ENUM_ID"]);

                                if(!empty($userProductGroups) && count($userProductGroups)) {

                                    $now = date('d.m.Y H:i:s');
                                    $convertDateSubscribeTo = ConvertDateTime($userProductGroups[0]["DATE_ACTIVE_TO"], "DD.MM.YYYY HH:MI:SS");
                                    $convertDateToday = ConvertDateTime($now, "DD.MM.YYYY HH:MI:SS");
                                    $dateToday = DateTime::createFromFormat('d.m.Y H:i:s', $convertDateToday);
                                    $datePremTo = new DateTime($convertDateSubscribeTo);
                                    $dayDiffToStartTournament = $dateToday->diff($datePremTo)->format('%R%a') + 0;

                                }

                                if ($dayDiffToStartTournament < 3) {
                                    $alertManagementSquad = GetMessage('JG_BEFORE_TOURNAMENT_REGISTER');
                                    createSession('management-games_error', $alertManagementSquad);
                                    //dump($dayDiffToStartTournament, 1);
                                } else if ($registeredMatchId > 0) {
                                    $alertManagementSquad = $messages[$lang]['YOU_ALREADY_ADDED_ON_TOURNAMENT'] . '<br>
                                <a href="'.SITE_DIR.'tournament-page/join-game/?mid=' . $registeredMatchId . '">' . $messages[$lang]['GO_TO_MATCH'] . '</a>';
                                    createSession('management-games_error', $alertManagementSquad);
                                    //LocalRedirect(SITE_DIR."management-games/join-game/?mid=".$mId);
                                } else if (!checkTournamentStagePass($teamID,$mId)){
                                    $alertManagementSquad = GetMessage('ALERTS_NOT_ACCESS_STAGE');
                                    createSession('management-games_error', $alertManagementSquad);
                                } else {
                                    if ($isPlace) {
                                        $squadId = createSquad($props, $code);
                                        $alertManagementSquad = GetMessage('ALERTS_SUCCESS_FORMED_TEAM_FROM_GAME');
                                        createSession('management-games_success', $alertManagementSquad);
                                        //LocalRedirect(SITE_DIR."management-games/join-game/?mid=".$mId);
                                    } else {
                                        $alertManagementSquad = GetMessage('ALERTS_LATE_NO_SEATS');
                                        createSession('management-games_error', $alertManagementSquad);
                                        //LocalRedirect(SITE_DIR."management-games/join-game/?mid=".$mId);
                                    }
                                }
                            } else {
                                if ($isPlace) {
                                    $squadId = createSquad($props, $code);
                                    //dump($squadId);

                                    $alertManagementSquad = GetMessage('ALERTS_SUCCESS_FORMED_TEAM');
                                    //dump($alertManagementSquad);
                                    createSession('management-games_success', $alertManagementSquad);
                                    // not working
                                    // header('Location: /management-games/join-game/?mid='.$mId);
                                    //LocalRedirect("/management-games/join-game/?mid=".$mId);
                                } else {
                                    $alertManagementSquad = GetMessage('ALERTS_LATE_NO_SEATS');
                                    createSession('management-games_error', $alertManagementSquad);
                                    //LocalRedirect("/management-games/join-game/?mid=".$mId);
                                }
                            }
                        } else {
                            $alertManagementSquad = GetMessage('ALERTS_ERROR_REGISTERED_FOR_ANOTHER_GAME');
                            createSession('management-games_error', $alertManagementSquad);
                        }

                    } else if($teamRating['rating'] <= $curMatch['MIN_RATING']['VALUE']){
                        $alertManagementSquad = GetMessage('ALERTS_ERROR_TEAM_MIN_RATING');
                        createSession('management-games_error', $alertManagementSquad);

                    } else if($teamRating['rating'] >= $curMatch['MAX_RATING']['VALUE']){
                        $alertManagementSquad = GetMessage('ALERTS_ERROR_TEAM_MAX_RATING');
                        createSession('management-games_error',  $alertManagementSquad );
                    }
                    $redirectUrlAction = SITE_DIR."tournament-page/join-game/?mid=".$mId;
                    //LocalRedirect("/management-games/join-game/?mid=".$mId);
//                    } else {
//
//                        $alertManagementSquad = GetMessage('ALERTS_ERROR_SUBSCRIPTION_EXPIRES').'<br><a href="'.SITE_DIR.'subscription-plans/" target="_blank" class="btn-italic mt-1">'.GetMessage('ALERTS_LINK_BUY_SUBSCRIPTION').'</a>';
//                        createSession('management-games_error', $alertManagementSquad);
//                    }
                    }
            } else {
              $alertManagementSquad = GetMessage('ALERTS_NOT_FULLY_FROM') . $minLimPlayers . GetMessage('ALERTS_NOT_FULLY_TO') . $maxLimPlayers . GetMessage('ALERTS_NOT_FULLY_PARTICIPANTS');
              createSession('management-games_error', $alertManagementSquad);
              //LocalRedirect("/management-games/join-game/?mid=".$mId);
                $redirectUrlAction = SITE_DIR."tournament-page/join-game/?mid=".$mId;
            }

    }
}


if (check_bitrix_sessid() && !empty($_REQUEST['removeTeam'])) {

    $results = getResultByMatchTeam($mId, $teamID);

    $now = date('d.m.Y H:i:s');
    $dateStartMatch = $curMatch["DATE_START"]['VALUE'];

    $dateA = DateTime::createFromFormat('d.m.Y H:i:s', $now);
    $dateB = DateTime::createFromFormat('d.m.Y H:i:s', $dateStartMatch);

    if ($results) {
        $alertManagementSquad = GetMessage('ALERTS_RESULTS_ALREADY');
        createSession('management-games_error', $alertManagementSquad);
        $redirectUrlAction = SITE_DIR."tournament-page/join-game/?mid=".$mId;
    } else if($dateA > $dateB) {
        $alertManagementSquad = GetMessage('ALERTS_MATCH_ALREADY_PASSED');
        createSession('management-games_error', $alertManagementSquad);
        $redirectUrlAction = SITE_DIR."tournament-page/join-game/?mid=".$mId;
    } else {
        // получаем squad
        $curSquadId = getSquadByIdMatch($mId, $teamID);
        // получили место
        if ($propertyPlace = getPropertyPlace($chainMatches)) {
            $propertyPlace = array_flip($propertyPlace);
            $propertyPlace = $propertyPlace[$teamID];

            // есть цепочка матчей тут $chainMatches
            // получаем участников матчей
            $resMembersMatches = getMembersByMatchId($chainMatches);
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
                    $alertManagementSquad = GetMessage('ALERTS_REMOVED_TEAM_FROM_GAME');
                    createSession('management-games_success', $alertManagementSquad);
                    $redirectUrlAction = SITE_DIR."tournament-page/join-game/?mid=".$mId;
                }
            }


        }
    }


}

// todo not working

if ($squadId) {
//dump($squadId);
// получаем участников матчей
$resMembersMatches = getMembersByMatchId($chainMatches);
$membersMatches = [];

if ($resMembersMatches) {
    foreach ($resMembersMatches as $membersMatch) {

        $membersMatches[] = $membersMatch['ID'];
    }
}

//dump($membersMatches);
$match = getMembersByMatchId($curMatch['ID']);
$match = $match[0];
//dump($match);



$propertiesCases = getPlacesKeys();

$emptyPlace = false;
foreach ($propertiesCases as $case) {
    if ($match[$case]+0 == 0) {
        $emptyPlace = $case;
        break;
    }
}

// сделать проверку, что моей команды еще нет в участниках матча, если моя команду существует в этом матче, то $emptyplace = falce
if ($emptyPlace != false) {
    foreach ($membersMatches as $membersMatchId) {
        CIBlockElement::SetPropertyValues($membersMatchId, 4, $teamID, $emptyPlace);
    }
} else {
    CIBlockElement::Delete($squadId);
    $alertManagementSquad = GetMessage('ALERTS_TEAM_NOT_PLACED');
    createSession('management-games_success', $alertManagementSquad);
    $redirectUrlAction = SITE_DIR."tournament-page/join-game/?mid=".$mId;
}

}


// капитан или нет
$isCaptain = isCaptain($userID, $teamID);
if(!$isCaptain) {
    LocalRedirect(SITE_DIR."personal/");
}
$resTeam = getTeamById($teamID);

// получаем список команды
$coreTeam = getCoreTeam($teamID);
// ставим капитана на первое место

foreach ($coreTeam as $k => $player) {

    if ($resTeam['AUTHOR']["VALUE"] == $player['ID']) {
        $coreTeam = [$k => $player] + $coreTeam;
        break;
    }
}

?>
<?php
$tournamentId = isTournament($curMatch['ID']);
//dump($tournamentId);
//dump($curMatch);
if ($redirectUrlAction != false) {
    LocalRedirect($redirectUrlAction);
}
?>
<?php
if(isset($_SESSION['management-games_success'])) { ?>
  <div class="alert-container">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo $_SESSION['management-games_success'];?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  <?php
  unset($_SESSION['management-games_success']);
} else if(isset($_SESSION['management-games_error'])){ ?>
  <div class="alert-container">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?php echo $_SESSION['management-games_error'];?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php }
unset($_SESSION['management-games_error']);
?>
  <section class="tournament">
    <div class="container">

      <div class="row justify-content-center">
        <div class="col-lg-11 col-md-12">
          <div class="layout__content-heading-with-btn-back">
            <a href="/tournament-page/?tournamentID=<?php echo $tournamentId;?>" class="btn-italic-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 11.62">
                <path d="M955.22,534.89a1,1,0,0,1,0,1.33l-3,3.27h18.2a.94.94,0,0,1,0,1.88h-18.2l3,3.27a1,1,0,0,1,0,1.32.81.81,0,0,1-1.21,0l-4.49-4.88h0a1,1,0,0,1,0-1.33h0l4.49-4.87A.81.81,0,0,1,955.22,534.89Z" transform="translate(-949.26 -534.62)"/>
              </svg> Назад к турниру
            </a>
            <h1 class="text-center tournament__heading">
              Состав команды<br>
              “<?php echo $resTeam['NAME'];?>”
            </h1>
          </div>

          <?php
          //dump($squadMembers);
          if (empty($squadMembers)) { ?>
            <div class="core-team__heading-core-team">Сформируй состав на игру</div>
          <?php } ?>
          <div class="core-team core-team__list">
            <div class="flex-table">
              <div class="flex-table--header">
                <div class="flex-table--categories">
                  <span class="text-center">
                      Игрок
                  </span>
                  <span>Место в рейтинге</span>
                  <span>Рейтинг</span>
                  <span>Киллы</span>
                  <span>Кол-во игр</span>
                </div>
              </div>
            </div>
          </div>
          <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
              <?=bitrix_sessid_post()?>
            <div class="accordion core-list" id="core-list">
            <?php

            // take 7 users
            $coreTeam = array_slice($coreTeam, 0, 6);
            // собираем id игроков в команде
            $idsCorePlayers = [];
            foreach ($coreTeam as $player) {
                $idsCorePlayers[] = $player["ID"];
            }
            //dump($idsCorePlayers);

            $coreTeamList = getListUsersManage($idsCorePlayers);
           // dump($coreTeamList);

            foreach ($coreTeam as $n => $player)  {

                $cntMatches = '..';
                $rank = '..';
                $kills = '..';
                $total = '..';
                if( isset($coreTeamList) ){
                    $rank = ceil($coreTeamList[$player['ID']]['rank']);
                    $cntMatches = ceil($coreTeamList[$player['ID']]['count_matches']);
                    $kills = ceil($coreTeamList[$player['ID']]['kills']);
                    $total = ceil($coreTeamList[$player['ID']]['total']);
                }
                ?>
              <?php if(count($squadMembers) == 0) { ?>
              <div class="card">
                <div class="card-header" id="headingUser_<?php echo $player['ID'];?>">
                  <h2 class="mb-0">
                    <button class="core-list__btn-collapse <?php echo ($n==0) ? '' : ' collapsed'; ?>" type="button" data-toggle="collapse" data-target="#collapseUser_<?php echo $player['ID'];?>" aria-expanded="true" aria-controls="collapseUser_<?php echo $player['ID'];?>">
                      <div class="core-team__user">
                        <i class="core-list__icon-drag-and-drop"></i>
                        <div class="core-team__user-avatar"
                            <?php if (!empty($player["PERSONAL_PHOTO"])) { ?>
                              style="background-image: url(<?php echo CFile::GetPath($player["PERSONAL_PHOTO"]); ?>)"
                            <?php } else { ?>
                              style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                            <?php } ?>>
                          <?php if ($resTeam['AUTHOR']["VALUE"] == $player['ID']) { ?>
                          <div class="core-team__user-avatar-icon_captain">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                              <circle  cx="11" cy="11" r="10"/>
                              <path d="M682.39,379.09a.65.65,0,0,1,1.22,0l.82,2.5a.65.65,0,0,0,.61.43h2.66a.62.62,0,0,1,.38,1.13l-2.16,1.54a.63.63,0,0,0-.23.71l.82,2.49a.63.63,0,0,1-1,.7l-2.16-1.54a.63.63,0,0,0-.74,0l-2.16,1.54a.63.63,0,0,1-1-.7l.82-2.49a.63.63,0,0,0-.23-.71l-2.16-1.54a.62.62,0,0,1,.38-1.13H681a.65.65,0,0,0,.61-.43Z" transform="translate(-672 -373)"/>
                            </svg>
                          </div>
                          <?php } ?>
                          <div class="core-team__user-avatar-delete-user"></div>
                        </div>
                        <div>
                          <a href="<?=SITE_DIR?>players/<?php echo $player['ID'].'_'.$player['LOGIN'].'/';?>" class="core-team__user-link"><?php echo $player['LOGIN']?></a>
                        </div>

                      </div>
                    </button>
                  </h2>
                </div>
                <div id="collapseUser_<?php echo $player['ID'];?>" class="collapse <?php echo ($n == 0) ? ' show' : ''; ?>" aria-labelledby="headingUser_<?php echo $player['ID'];?>" data-parent="#core-list">
                  <div class="card-body">
                    <div class="core-team">
                      <div class="flex-table">
                        <div class="flex-table--body">
                          <div class="flex-table--row">
                          <span>
                            <div class="core-team__user">
                              <label class="label-checkbox" style="display: none;">
                                <input type="checkbox" name="squad[]" value="<?php echo $player['ID']?>"
                                <?php if(isset($squadMembers[$player['ID']])) echo 'checked';?> <?php if(count($squadMembers) == 0) echo 'checked';?>>
                                <div class="label-checkbox__checkmark"></div>
                              </label>
                              <i class="core-list__icon-drag-and-drop"></i>
                              <div class="core-team__user-avatar"
                                  <?php if (!empty($player["PERSONAL_PHOTO"])) { ?>
                                style="background-image: url(<?php echo CFile::GetPath($player["PERSONAL_PHOTO"]); ?>)"
                              <?php } else { ?>
                                style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                              <?php } ?>>
                              <?php if ($resTeam['AUTHOR']["VALUE"] == $player['ID']) { ?>
                                <div class="core-team__user-avatar-icon_captain">
                                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                    <circle  cx="11" cy="11" r="10"/>
                                    <path d="M682.39,379.09a.65.65,0,0,1,1.22,0l.82,2.5a.65.65,0,0,0,.61.43h2.66a.62.62,0,0,1,.38,1.13l-2.16,1.54a.63.63,0,0,0-.23.71l.82,2.49a.63.63,0,0,1-1,.7l-2.16-1.54a.63.63,0,0,0-.74,0l-2.16,1.54a.63.63,0,0,1-1-.7l.82-2.49a.63.63,0,0,0-.23-.71l-2.16-1.54a.62.62,0,0,1,.38-1.13H681a.65.65,0,0,0,.61-.43Z" transform="translate(-672 -373)"/>
                                  </svg>
                                </div>
                              <?php } ?>
                              </div>
                              <div>
                                <a href="<?=SITE_DIR?>players/<?php echo $player['ID'].'_'.$player['LOGIN'].'/';?>" class="core-team__user-link"><?php echo $player['LOGIN']?></a>
                              </div>
                            </div>
                          </span>
                            <span class="core-list__param-wrap">
                              <div class="core-team__param">Место в рейтинге</div>
                              <div>
                                <?php echo $rank;?>
                              </div>
                            </span>
                            <span class="core-list__param-wrap">
                              <div class="core-team__param">Рейтинг</div>
                              <?php echo $total;?>
                            </span>
                            <span class="core-list__param-wrap">
                              <div class="core-team__param">Киллы</div>
                              <?php echo $kills;?>
                            </span>
                            <span class="core-list__param-wrap">
                              <div class="core-team__param">Кол-во игр</div>
                                <?php echo $cntMatches;?>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <button class="core-list__delete-user" type="submit" name="user_id_del" value="<?php echo $player['ID']?>"></button>
                  </div>
                </div>
              </div>
                <?php } ?>
              <?php if(isset($squadMembers[$player['ID']])) { ?>
                <div class="card">
                  <div class="card-header" id="headingUser_<?php echo $player['ID'];?>">
                    <h2 class="mb-0">
                      <button class="core-list__btn-collapse <?php echo ($n==0) ? '' : ' collapsed'; ?>" type="button" data-toggle="collapse" data-target="#collapseUser_<?php echo $player['ID'];?>" aria-expanded="true" aria-controls="collapseUser_<?php echo $player['ID'];?>">
                        <div class="core-team__user">
                          <i class="core-list__icon-drag-and-drop"></i>
                          <div class="core-team__user-avatar"
                            <?php if (!empty($player["PERSONAL_PHOTO"])) { ?>
                              style="background-image: url(<?php echo CFile::GetPath($player["PERSONAL_PHOTO"]); ?>)"
                            <?php } else { ?>
                              style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                            <?php } ?>>
                            <?php if ($resTeam['AUTHOR']["VALUE"] == $player['ID']) { ?>
                              <div class="core-team__user-avatar-icon_captain">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                  <circle  cx="11" cy="11" r="10"/>
                                  <path d="M682.39,379.09a.65.65,0,0,1,1.22,0l.82,2.5a.65.65,0,0,0,.61.43h2.66a.62.62,0,0,1,.38,1.13l-2.16,1.54a.63.63,0,0,0-.23.71l.82,2.49a.63.63,0,0,1-1,.7l-2.16-1.54a.63.63,0,0,0-.74,0l-2.16,1.54a.63.63,0,0,1-1-.7l.82-2.49a.63.63,0,0,0-.23-.71l-2.16-1.54a.62.62,0,0,1,.38-1.13H681a.65.65,0,0,0,.61-.43Z" transform="translate(-672 -373)"/>
                                </svg>
                              </div>
                            <?php } ?>
                            <div class="core-team__user-avatar-delete-user"></div>
                          </div>
                          <div>
                            <a href="<?=SITE_DIR?>players/<?php echo $player['ID'].'_'.$player['LOGIN'].'/';?>" class="core-team__user-link"><?php echo $player['LOGIN']?></a>
                          </div>

                        </div>
                      </button>
                    </h2>
                  </div>
                  <div id="collapseUser_<?php echo $player['ID'];?>" class="collapse <?php echo ($n == 0) ? ' show' : ''; ?>" aria-labelledby="headingUser_<?php echo $player['ID'];?>" data-parent="#core-list">
                    <div class="card-body">
                      <div class="core-team">
                        <div class="flex-table">
                          <div class="flex-table--body">
                            <div class="flex-table--row">
                          <span>
                            <div class="core-team__user">
                              <label class="label-checkbox" style="display: none;">
                                <input type="checkbox" name="squad[]" value="<?php echo $player['ID']?>"
                                <?php if(isset($squadMembers[$player['ID']])) echo 'checked';?> <?php if(count($squadMembers) == 0) echo 'checked';?>>
                                <div class="label-checkbox__checkmark"></div>
                              </label>
                              <i class="core-list__icon-drag-and-drop"></i>
                              <div class="core-team__user-avatar"
                                  <?php if (!empty($player["PERSONAL_PHOTO"])) { ?>
                                    style="background-image: url(<?php echo CFile::GetPath($player["PERSONAL_PHOTO"]); ?>)"
                                  <?php } else { ?>
                                    style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                                  <?php } ?>>
                              <?php if ($resTeam['AUTHOR']["VALUE"] == $player['ID']) { ?>
                                <div class="core-team__user-avatar-icon_captain">
                                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                    <circle  cx="11" cy="11" r="10"/>
                                    <path d="M682.39,379.09a.65.65,0,0,1,1.22,0l.82,2.5a.65.65,0,0,0,.61.43h2.66a.62.62,0,0,1,.38,1.13l-2.16,1.54a.63.63,0,0,0-.23.71l.82,2.49a.63.63,0,0,1-1,.7l-2.16-1.54a.63.63,0,0,0-.74,0l-2.16,1.54a.63.63,0,0,1-1-.7l.82-2.49a.63.63,0,0,0-.23-.71l-2.16-1.54a.62.62,0,0,1,.38-1.13H681a.65.65,0,0,0,.61-.43Z" transform="translate(-672 -373)"/>
                                  </svg>
                                </div>
                              <?php } ?>
                              </div>
                              <div>
                                <a href="<?=SITE_DIR?>players/<?php echo $player['ID'].'_'.$player['LOGIN'].'/';?>" class="core-team__user-link"><?php echo $player['LOGIN']?></a>
                              </div>
                            </div>
                          </span>
                              <span class="core-list__param-wrap">
                              <div class="core-team__param">Место в рейтинге</div>
                              <div>
                                <?php echo $rank;?>
                              </div>
                            </span>
                              <span class="core-list__param-wrap">
                              <div class="core-team__param">Рейтинг</div>
                              <?php echo $total;?>
                            </span>
                              <span class="core-list__param-wrap">
                              <div class="core-team__param">Киллы</div>
                              <?php echo $kills;?>
                            </span>
                              <span class="core-list__param-wrap">
                              <div class="core-team__param">Кол-во игр</div>
                                <?php echo $cntMatches;?>
                            </span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <button class="core-list__delete-user" type="submit" name="user_id_del" value="<?php echo $player['ID']?>"></button>
                    </div>
                  </div>
                </div>
                <?php } ?>
            <?php } ?>
            </div>
            <?php if (count($squadMembers) > 0 && count($squadMembers) < 6 && !$curSquad['UPDATE_SQUAD']["VALUE"]) { ?>
              <a href="#" class="core-list__add-user" data-toggle="modal" data-target="#core-list-add-user">
                <i></i><span>Добавить нового игрока</span>
              </a>
            <?php } ?>
              <?php
              $btnTitle = 'Зарегистрировать команду';

              if (count($squadMembers) > 0) {
                  $btnTitle = 'Обновить команду';
                  echo '<input type="hidden" name="update_squad" value="1">'; ?>
                <p class="text-center mt-3">Обновить состав игроков можно только один раз</p>
              <?php } ?>
            <?php if(!$curSquad['UPDATE_SQUAD']["VALUE"]) { ?>
            <div class="core-list__btn">
              <button type="submit" class="btn" name="btn_create_squad"><?php echo $btnTitle;?></button>
            </div>
           <?php } ?>
            <div class="modal fade" id="core-list-add-user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="btn-modal-close" data-dismiss="modal" aria-label="Close">
                      <i></i>
                    </button>
                  </div>
                  <div class="modal-body">
                    <h3 class="modal-body__title">Добавление нового игрока в основной состав</h3>
                    <div class="modal-body__content mb-3">

                        <div class="form-field">
                          <label for="newUser" class="form-field__label">Новый игрок</label>
                          <select id="newUser" name="squad[]"  data-dropdown>
                            <option value="">Выбрать</option>
                            <?php foreach ($coreTeam as $n => $player)  {
                              if(!isset($squadMembers[$player['ID']])) { ?>
                                <option value="<?php echo $player['ID']?>"><?php echo $player['LOGIN'];?></option>
                              <?php } ?>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="text-center">
                          <button type="submit" name="btn_create_squad" class="btn mt-25">Добавить</button>
                        </div>

                    </div>
                  </div>
                  <div class="modal-footer__new">
                    <i></i>Добавить игрока можно только один раз
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <?php/* if (!empty($coreTeam)) {
    $points = countPointsAllUsers();
    ?>
    <section class="pb-8">
    <div class="container">
        <h2 class="core-team__heading"><?=GetMessage('JG_CORE_TEAM')?></h2>
      <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
          <?=bitrix_sessid_post()?>
        <div class="core-team">
          <div class="flex-table">
            <div class="flex-table--header bg-default">
              <div class="flex-table--categories">
                  <span>
                    <div style="display: flex; align-items: center">
                    <label class="label-checkbox">
                      <input type="checkbox" id="c-all">
                      <div class="label-checkbox__checkmark"></div>
                    </label>
                      <div><?=GetMessage('JG_TABLE_PLAYER')?></div>
                  </div>

                  </span>
                <span><?=GetMessage('JG_TABLE_NUMBER_GAME')?></span>
                <span><?=GetMessage('JG_TABLE_KILLS')?></span>
                <span><?=GetMessage('JG_TABLE_TOTAL')?></span>
                <span><?=GetMessage('JG_TABLE_RATING')?></span>
                <span><?=GetMessage('JG_TABLE_SUBSCRIPTION')?></span>
              </div>
            </div>
            <div class="flex-table--body">
            <?php foreach ($coreTeam as $player)  {
                $cntMatches = '..';
                $kills = '..';
                $total = '..';
                if( isset($points[$player['ID']]) ){
                    $cntMatches = ceil($points[$player['ID']]['count_matches']);
                    $kills = ceil($points[$player['ID']]['kills']);
                    $total = ceil($points[$player['ID']]['total']);
                }
              ?>
              <div class="flex-table--row">
                <span>
                  <div class="core-team__user">
                    <label class="label-checkbox">
                      <input type="checkbox" name="squad[]" value="<?php echo $player['ID']?>"
                      <?php if(isset($squadMembers[$player['ID']])) echo 'checked';?>>
                      <div class="label-checkbox__checkmark"></div>
                    </label>
                    <div class="core-team__user-avatar"
                         <?php if (!empty($player["PERSONAL_PHOTO"])) { ?>
                           style="background-image: url(<?php echo CFile::GetPath($player["PERSONAL_PHOTO"]); ?>)"
                         <?php } else { ?>
                           style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                         <?php } ?>>
                      <?php if ($resTeam['AUTHOR']["VALUE"] == $player['ID']) { ?>
                        <div class="core-team__user-avatar-icon_captain">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                <circle  cx="11" cy="11" r="10"/>
                                <path d="M682.39,379.09a.65.65,0,0,1,1.22,0l.82,2.5a.65.65,0,0,0,.61.43h2.66a.62.62,0,0,1,.38,1.13l-2.16,1.54a.63.63,0,0,0-.23.71l.82,2.49a.63.63,0,0,1-1,.7l-2.16-1.54a.63.63,0,0,0-.74,0l-2.16,1.54a.63.63,0,0,1-1-.7l.82-2.49a.63.63,0,0,0-.23-.71l-2.16-1.54a.62.62,0,0,1,.38-1.13H681a.65.65,0,0,0,.61-.43Z" transform="translate(-672 -373)"/>
                              </svg>
                            </div>
                      <?php } ?>
                    </div>
                    <a href="<?=SITE_DIR?>players/<?php echo $player['ID'].'_'.$player['LOGIN'].'/';?>" class="core-team__user-link"><?php echo $player['LOGIN']?></a>
                  </div>
                </span>
                <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('JG_TABLE_NUMBER_GAME')?></div>
                  <?php echo $cntMatches;?>
                </span>
                <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('JG_TABLE_KILLS')?></div>
                  <?php echo $kills;?>
                </span>
                <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('JG_TABLE_TOTAL')?></div>
                  <?php echo $total;?>
                </span>
                <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('JG_TABLE_RATING')?></div>
                  <?php if(!$player['UF_RATING']) { ?>
                    300
                  <?php } else { ?>
                      <?php echo $player['UF_RATING'];?>
                  <?php } ?>
                </span>
                <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('JG_TABLE_SUBSCRIPTION')?></div>
                    <?php $resultPrem = isPrem($player['UF_DATE_PREM_EXP']); ?>
                  <?php if ($resultPrem <= 0) { ?>
                      <?=GetMessage('JG_TABLE_NO_SUBSCRIPTION')?>
                <?php  } else { ?>

                    <?php echo num_decline( $resultPrem, GetMessage('JG_TABLE_SUBSCRIPTION_REMAINED'), false );?> <?php echo num_decline( $resultPrem, GetMessage('JG_TABLE_SUBSCRIPTION_DAYS') );?>

                <?php  } ?>
                </span>
              </div>
            <?php } ?>
            </div>
          </div>
        </div>
        <div class="core-team__btn-flex">
            <?php if (!empty($squadMembers)) { ?>
              <button type="submit" name="removeTeam" value="1" class="btn-icon btn-icon_red btn-icon_close-red mr-1"><i></i> <?=GetMessage('JG_BTN_REMOVE')?></button>
            <?php } ?>

            <?php
                $btnTitle = GetMessage('JG_BTN_CREATE');
            if (count($squadMembers) > 0) {
                $btnTitle = GetMessage('JG_BTN_UPDATE');
                echo '<input type="hidden" name="update_squad" value="1">';
            }
            ?>
          <button type="submit" class="btn-icon btn-icon_check" name="btn_create_squad"><i></i> <?php echo $btnTitle; ?></button>
        </div>
      </form>
    </div>
  </section>

  <?php } */?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>