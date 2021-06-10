<?php
// получаем команду по id
function getTeamById($teamID) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");
    $arFilter = Array("IBLOCK_ID" => 1, "ID" => $teamID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

function getAllTeams(){
    GLOBAL $DB;
    $sql = "SELECT t.IBLOCK_ELEMENT_ID as teamID, t.PROPERTY_21 as name FROM b_iblock_element_prop_s1 as t";

    $res = $DB->Query($sql);
    $teams = [];
    while( $row = $res->Fetch() ) {
        $teams[] = $row;
    }
    return $teams;
}

function getUsrById($id)
{
    $filter = Array("GROUPS_ID" => Array(7), 'ID' => $id);
    $arParams["SELECT"] = array("UF_*");
    $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
    $output = false;
    if ($rsUser = $elementsResult->Fetch())
    {
        $output = $rsUser;
    }
    return $output;

}

function getSquad($idMatch, $idTeam)
{
    $squadIds = getSquadByIdMatch($idMatch, $idTeam);


    if(!$squadIds) return false;
    for ($i = 1; $i < 7 ; $i++){
        $squad[] = getUsrById($squadIds["PLAYER_".$i]["VALUE"]);
    }
    return $squad;
}

// получаем турнир по id
function getTournamentById($tournamentId) {
    $arSelect = Array("ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PREVIEW_TEXT",
        "PREVIEW_PICTURE",
        "DETAIL_TEXT",
        "PROPERTY_*"
    );//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" =>7, "ID" => $tournamentId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

function getMatchesByTournamentID($tournamentID, $stageID = ''){
    GLOBAL $DB;

    if(!empty($stageID)) {
        $sql = "SELECT m.IBLOCK_ELEMENT_ID as IDs FROM b_iblock_element_prop_s3 as m WHERE m.PROPERTY_3 = ". $tournamentID." AND m.PROPERTY_8 is NULL AND UNIX_TIMESTAMP(m.PROPERTY_4) > UNIX_TIMESTAMP() AND m.PROPERTY_22 =". $stageID;
    } else {
        $sql = "SELECT m.IBLOCK_ELEMENT_ID as IDs FROM b_iblock_element_prop_s3 as m WHERE m.PROPERTY_3 = ". $tournamentID." AND m.PROPERTY_8 is NULL AND UNIX_TIMESTAMP(m.PROPERTY_4) > UNIX_TIMESTAMP()";
    }


    $res = $DB->Query($sql);
    $matches = [];
    while( $row = $res->Fetch() ) {
        $matches[] = $row["IDs"];
    }
    return $matches;

}

function updateSquad($props = [], $idMatch)
{
    $squadRes = getSquadByIdMatch($idMatch, $props['TEAM_ID']);

    $squadId = $squadRes['ID']+0;
    //dump($squadId);
    //dump($props);
    CIBlockElement::SetPropertyValues($squadId, 6, $props, false);
    //header('Location: /management-games/join-game/?mid='.$idMatch);
}

function moveSquad($props = [], $idMatch)
{
    $squadRes = getSquadByIdMatch($idMatch, $props['TEAM_ID']);
    $squadId = $squadRes['ID']+0;

    if(countTeams($props["MATCH_STAGE_ONE"]) >= 18){
        return false;
    }
    CIBlockElement::SetPropertyValues($squadId, 6, $props["MATCH_STAGE_ONE"], "MATCH_STAGE_ONE");
    return true;
}

function updateSquadMatchID($props = [], $idMatch)
{
    $squadRes = getSquadByIdMatch($idMatch, $props['TEAM_ID']);
    $squadId = $squadRes['ID']+0;

    CIBlockElement::SetPropertyValues($squadId, 6, $props, false);

    return $squadId;
}

// созданеие записи с участниками
function createSquad($props = [], $code)
{
    $el = new CIBlockElement;
    $iblock_id = 6;
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

function getPlacesKeys()
{
    return [
        3 => "TEAM_PLACE_03",
        4 =>"TEAM_PLACE_04",
        5 =>"TEAM_PLACE_05",
        6 =>"TEAM_PLACE_06",
        7 =>"TEAM_PLACE_07",
        8 =>"TEAM_PLACE_08",
        9 =>"TEAM_PLACE_09",
        10 =>"TEAM_PLACE_10",
        11 =>"TEAM_PLACE_11",
        12 =>"TEAM_PLACE_12",
        13 =>"TEAM_PLACE_13",
        14 =>"TEAM_PLACE_14",
        15 =>"TEAM_PLACE_15",
        16 =>"TEAM_PLACE_16",
        17 =>"TEAM_PLACE_17",
        18 =>"TEAM_PLACE_18",
        19 =>"TEAM_PLACE_19",
        20 =>"TEAM_PLACE_20",
    ];
}

// получаем участников матчей по полю which_match передавая id
function getMembersByMatchId($matchId) {
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
        //dump($arProps);
        foreach ($arProps as $k=>$v) {
            $arFields[$k] = $v['VALUE'];
        }
        $output[] = $arFields;
    }
    return $output;
}

function moveWinners($prevMatch, $nextMatchIDs, $teamID){

    $firstMatchId = $nextMatchIDs[0];

    $nextSquad = getSquadByIdMatch($firstMatchId, $teamID);
    if($nextSquad) return;

    $playerKeys = [
        'PLAYER_1',
        'PLAYER_2',
        'PLAYER_3',
        'PLAYER_4',
        'PLAYER_5',
        'PLAYER_6',
    ];

    $squad = getSquadByIdMatch($prevMatch["ID"], $teamID);

    $props = [];
    $tmp = [];

    foreach ($playerKeys as $key => $name) {
        $props[$name] = $squad[$name]["VALUE"];
        if ($props[$name] > 0) {
            $tmp[] = $key;
        }
    }

    $props['MATCH_STAGE_ONE'] = $firstMatchId;
    $props['TEAM_ID'] = $teamID;
    $code = 'SQUAD_' . $prevMatch['NAME'];

    $squadId = createSquad($props, $code);

    if ($squadId) {

// получаем участников матчей
        $resMembersMatches = getMembersByMatchId($nextMatchIDs);
        $membersMatches = [];

        if ($resMembersMatches) {
            foreach ($resMembersMatches as $membersMatch) {
                $membersMatches[] = $membersMatch['ID'];
            }
        }

        $match = getMembersByMatchId($firstMatchId);
        $match = $match[0];

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
        }

    }
}



function  getNextStage($stage){
    //Я сейчас пишу это в тотальном а*уе, почему нельзя было сделать ID с нормальной последовательностью?
    if ($stage == 7){
        $stage = 4;
        return $stage;
    }

    $stage = $stage - 1;
    return $stage;
}

function findFreeGame($tournamentID, $stageID=''){


        $IDs = getMatchesByTournamentID($tournamentID, $stageID);
        foreach($IDs as $gameID){
            $match = getMembersByMatchId($gameID);
            $match = $match[0];
        //dump($match);

            $propertiesCases = getPlacesKeys();
            foreach ($propertiesCases as $case) {
                if ($match[$case]+0 == 0) {
                    return $gameID;
                }
            }
        }
    return false;

}

function formDate($date, $format){
    global $intlFormatter;
    $date = new DateTime($date);
    $intlFormatter->setPattern($format);
    $fDate = $intlFormatter->format($date);

    return $fDate;
}

function getNextGame($teamID, $tournamentID){
    GLOBAL $DB;
    if(!$teamID) return false;
    $sql = "SELECT m.IBLOCK_ELEMENT_ID as matchID FROM b_iblock_element_prop_s6 as t 
            INNER JOIN b_iblock_element_prop_s3 as m ON t.PROPERTY_27 = m.IBLOCK_ELEMENT_ID
            WHERE t.PROPERTY_28 =" .$teamID. "
            AND m.PROPERTY_3 =" .$tournamentID. "
            ORDER BY m.PROPERTY_4 DESC LIMIT 1";

    $res = $DB->Query($sql);
    if( $row = $res->Fetch() ) {
        $game = $row["matchID"];
        return $game;
    }
    return false;
}

function countTeams($matchID){
    GLOBAL $DB;
    $sql = "SELECT count(*) as teamsCount FROM b_iblock_element_prop_s6 as t 
WHERE t.PROPERTY_27 = ".$matchID;

    $res = $DB->Query($sql);
    if( $row = $res->Fetch() ) {
        $count = $row["teamsCount"];
        return $count;
    }
    return false;
}

function getSquadByIdPlayer($idTeam, $idUser)
{
    GLOBAL $DB;
    $idUser = trim(strip_tags($idUser));
    $sql = "SELECT * FROM  
                (SELECT CONCAT(ROUND(s.PROPERTY_24,0), '.', ROUND(s.PROPERTY_25,0), '.', ROUND(s.PROPERTY_26,0), '.', ROUND(s.PROPERTY_45,0), '.', ROUND(s.PROPERTY_46,0), '.', ROUND(s.PROPERTY_47,0)) as players, s.PROPERTY_27, s.PROPERTY_28, m.PROPERTY_4 FROM b_iblock_element_prop_s6 as s  
                INNER JOIN b_iblock_element_prop_s3 as m ON m.IBLOCK_ELEMENT_ID = s.PROPERTY_27 
                WHERE s.PROPERTY_28 = " .$idTeam. " 
                AND UNIX_TIMESTAMP(m.PROPERTY_4) > UNIX_TIMESTAMP(CURTIME())) as r1 
                WHERE r1.players LIKE CONCAT('%', ".$idUser." ,'%') ";

    $res = $DB->Query($sql);
    $players = [];
    while( $row = $res->Fetch() ) {
        $players[] = $row;
    }
    return $players;
}

// create flash messages
function createSession($sessionName, $sessionValue)
{
    $_SESSION[$sessionName] = $sessionValue;
}
/**
 * Склонение слова после числа.
 *
 *     // Примеры вызова:
 *     num_decline( $num, 'книга,книги,книг' )
 *     num_decline( $num, 'book,books' )
 *     num_decline( $num, [ 'книга','книги','книг' ] )
 *     num_decline( $num, [ 'book','books' ] )
 *
 * @param  int|string    $number       Число после которого будет слово. Можно указать число в HTML тегах.
 * @param  string|array  $titles       Варианты склонения или первое слово для кратного 1.
 * @param  bool          $show_number  Указываем тут 00, когда не нужно выводить само число.
 *
 * @return string Например: 1 книга, 2 книги, 10 книг.
 *
 * @version 3.0
 */
function num_decline( $number, $titles, $show_number = 1 ){

    if( is_string( $titles ) )
        $titles = preg_split( '/, */', $titles );

    // когда указано 2 элемента
    if( empty( $titles[2] ) )
        $titles[2] = $titles[1];

    $cases = [ 2, 0, 1, 1, 1, 2 ];

    $intnum = abs( (int) strip_tags( $number ) );

    $title_index = ( $intnum % 100 > 4 && $intnum % 100 < 20 )
        ? 2
        : $cases[ min( $intnum % 10, 5 ) ];

    return ( $show_number ? "$number " : '' ) . $titles[ $title_index ];
}

function isTeamPrem($teamID){
    $coreTeam = getCoreTeam($teamID);
    foreach ($coreTeam as $teamMember){
        if(isPrem($teamMember["UF_DATE_PREM_EXP"]) <= 0){
            return false;
        }
    }
    return true;
}

function willTeamPrem($teamID, $matchTime){
    $coreTeam = getCoreTeam($teamID);

    foreach ($coreTeam as $teamMember){

        if(willBePrem($teamMember["UF_DATE_PREM_EXP"], $matchTime) <= 0){
            return false;
        }
    }
    return true;
}

function getStagePeriod($stage, $tournament){
    GLOBAL $DB;
    $sql = "SELECT count(*) as gamesCount, min(m.PROPERTY_4) as min, max(m.PROPERTY_4) as max FROM b_iblock_element_prop_s3 as m WHERE m.PROPERTY_3 = ".$tournament." AND m.PROPERTY_8 is NULL AND m.PROPERTY_22 = ".$stage;
    $res = $DB->Query($sql);

    if($row = $res->Fetch()){
        $dates["min"] = formDate($row["min"], 'd MMMM');
        $dates["max"] = formDate($row["max"], 'd MMMM yyyy');
        $dates["games"] = $row["gamesCount"];
        return $dates;
    }
    return false;
}

function getTournamentPeriod($tournament){
    GLOBAL $DB;
    $sql = "SELECT min(m.PROPERTY_4) as min, max(m.PROPERTY_4) as max FROM b_iblock_element_prop_s3 as m WHERE m.PROPERTY_3 = ".$tournament;
    $res = $DB->Query($sql);

    if($row = $res->Fetch()){
        $dates["min"] = formDate($row["min"], 'd MMMM');
        $dates["max"] = formDate($row["max"], 'd MMMM yyyy');
        return $dates;
    }
    return false;
}

function getUsersByGroup($groupID){

    if ($result = CUser::GetList(($by="ID"), ($order="ASC"),
        array(
            'GROUPS_ID'=>[$groupID],
            'ACTIVE' => 'Y'
        )
    )){
        $users = [];
        while($rsUsers = $result->Fetch()){
            $users[] = $rsUsers;
        }

        return $users;
    }
    return false;
}

function getMatchesByTeam($teamID) {
    $propNameMatchID = 'PROPERTY_13';
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", $propNameMatchID);

    $orFiletrPlaces = ['LOGIC' => 'OR'];
    $minPlace = 3; $maxPlace = 20;
    for( $i = $minPlace; $i <= $maxPlace; $i++ ){
        $propName = 'PROPERTY_TEAM_PLACE_'.(( $i< 10)?'0':'').$i;
        $orFiletrPlaces[] = [ $propName  => $teamID ];
    }
    //dump( $orFiletrPlaces );


    $arFilter = Array(
        //'PROPERTY_TEAM_PLACE_03' => $teamID,
        $orFiletrPlaces,
        'NAME' => '%STAGE1_%',
        "IBLOCK_ID" =>4,
        //"PROPERTY_PREV_MATCH" => false,
        //">=PROPERTY_DATE_START" => ConvertDateTime($date, "YYYY-MM-DD")." 00:00:00",
        //"<=PROPERTY_DATE_START" => ConvertDateTime($date, "YYYY-MM-DD")." 23:59:59",
        //"ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $output = [];

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        //echo '<pre>'.print_r($arFields,1).'</pre>';
        //$arProps = $ob->GetProperties();
        $output[] = $arFields[$propNameMatchID.'_VALUE'];
    }
    return $output;
}

function isPrem($premLimit)
{
    $now = date('d.m.Y');
    $origin = new DateTime($now);
    $target = new DateTime($premLimit);
    $interval = $origin->diff($target);
    return $interval->format('%R%a')+0;
}

function willBePrem($premLimit, $matchTime)
{
    $now = date('d.m.Y', strtotime($matchTime));
    $origin = new DateTime($now);
    $target = new DateTime($premLimit);
    $interval = $origin->diff($target);
    return $interval->format('%R%a')+0;
}

function isCaptainHeader($idUser, $idTeam)
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

/* Status
13 - Unverified /19
14 - Checking / 20
15 - Verified / 21
16 - CheckingNext / 22
17 - Rejected / 23
18 - VerifiedTouchOk  / 24
*/
function updateStatusChekingPubgId($userId, $statusId)
{
  $user = new CUser;
  $fields = Array(
    "UF_PUBG_ID_CHECK" => $statusId,
  );
  $user->Update($userId, $fields);
}

// $action true or false
function updatePubgIdVerified($userId, $action)
{
    $user = new CUser;
    $user->Update($userId, Array("UF_PUBG_ID_VERIFIED" => $action));
}

function addScreenshot($file, $userId, $statusId)
{
  $arFile = array();
  $arFile['name']     = $file['scrinPubg']['name'];
  $arFile['size']     = $file['scrinPubg']['size'];
  $arFile['tmp_name'] = $file['scrinPubg']['tmp_name'];
  $arFile['type']     = $file['scrinPubg']['type'];
  $arFile['del'] = "Y";
  $arFile['old_file'] = "";
  $arFile["MODULE_ID"] = "";
  $fields = Array(
    "WORK_LOGO" => $arFile,
  );
  $user = new CUser;
  $res = $user->Update($userId, $fields);

  if ($res) {
    updateStatusChekingPubgId($userId, $statusId);
    return true;
  } else {
    return false;
  }
}
function addReasonRejected($userId, $reason)
{
  $fields = Array(
    "PERSONAL_NOTES" => $reason,
  );
  $user = new CUser;
  $res = $user->Update($userId, $fields);
}
function addCommentRejected($userId, $comment)
{
  $fields = Array(
    "WORK_NOTES" => $comment,
  );
  $user = new CUser;
  $res = $user->Update($userId, $fields);
}

function existsPubgId($userId, $pubgId)
{
  $filter = array(
    '!ID'=>$userId,
    'UF_PUBG_ID' => $pubgId,
    "ACTIVE" => 'Y',
  );
  $arParams["SELECT"] = array("UF_*");
  $elementsResult = CUser::GetList(($by="ID"), ($order="DESC"), $filter, $arParams);
  $users = [];
  while ($rsUser = $elementsResult->Fetch())
  {
    $users[] = $rsUser;
    //updateUserPrem($rsUser["ID"], 10);
    //echo $rsUser["ID"] . $rsUser["LOGIN"] . " - " . $rsUser["UF_DATE_PREM_EXP"] . "<br>";
  }
  return $users;
}

function updateUser($userId, $fields= [])
{
    $user = new CUser;
    if ($user->Update($userId, $fields)) {
        return true;
    } else {
        return 'Error: ' . $user->LAST_ERROR;
    }
}

function existsNickname($userId, $nickname)
{
    $filter = array(
        '!ID'=>$userId,
        'LOGIN' => $nickname,
        "ACTIVE" => 'Y',
    );
    $arParams["SELECT"] = array("UF_*");
    $elementsResult = CUser::GetList(($by="ID"), ($order="DESC"), $filter, $arParams);
    $users = [];
    while ($rsUser = $elementsResult->Fetch())
    {
        $users[] = $rsUser;
        //updateUserPrem($rsUser["ID"], 10);
        //echo $rsUser["ID"] . $rsUser["LOGIN"] . " - " . $rsUser["UF_DATE_PREM_EXP"] . "<br>";
    }
    return $users;
}

function getListUsersManage($ids = []){
    global $DB;
    $ids = implode(',', $ids);
    $strSql = 'SELECT @rank:=0';
    $rsData = $DB->Query($strSql);
    $strSql = 'SELECT rank, r3.GROUP_ID, r3.LOGIN, r3.PERSONAL_PHOTO, r3.ID, count_matches, total, kills 
FROM (SELECT @rank:=@rank+1 as rank, r2.GROUP_ID, r2.LOGIN, r2.PERSONAL_PHOTO, r2.ID, count_matches, total, kills 
FROM (SELECT g.GROUP_ID, u.LOGIN, u.PERSONAL_PHOTO, u.ID, count_matches, IF(total IS NOT Null,total, 0) + IF(r.UF_RATING IS NOT Null, r.UF_RATING, 300) as total, kills 
                      FROM  b_user as u 
                            LEFT JOIN (SELECT t.USER_ID, count(t.USER_ID) as count_matches, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills FROM b_squad_member_result AS t WHERE t.TYPE_MATCH = 6 GROUP BY t.USER_ID) AS r1 ON r1.USER_ID = u.ID 
                            LEFT JOIN b_uts_user AS r ON r.VALUE_ID = u.ID 
                            INNER JOIN b_user_group AS g ON g.USER_ID = u.ID 
                            AND g.GROUP_ID = 7 
                            ORDER BY total DESC, kills DESC, u.ID ASC) as r2) as r3 
                            WHERE r3.ID IN('.$ids.')';
    $rsData = $DB->Query($strSql);
    $players = [];
    while($row = $rsData->fetch()){
        $players[ $row['ID'] ] = [
            'kills' => $row['kills'],
            'login' => $row['LOGIN'],
            'rank' => $row['rank'],
            'total' => $row['total'],
            'photo' => $row['PERSONAL_PHOTO'],
            'count_matches' => $row["count_matches"],
        ];

        //$players[] = $el;
    }

    return $players;
}


