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