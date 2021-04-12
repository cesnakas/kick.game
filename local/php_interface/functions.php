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

function isPrem($premLimit)
{
    $now = date('d.m.Y');
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