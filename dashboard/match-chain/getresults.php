<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
global $DB;
CModule::IncludeModule('sale');

$q = intval($_GET['q']);

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

function getSquadByIdMatch($idMatch, $idTeam)
{
    $arSelect = Array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "DATE_CREATE",
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

$select = "";
$join = "";
$order = "";

$chainMatches = getChainMatches($q);

foreach ($chainMatches["chain"] as $k => $match ){
    $select = $select.", m{$k}.PROPERTY_17, m{$k}.PROPERTY_18";
    $join = $join." LEFT JOIN b_iblock_element_prop_s5 as m{$k} ON m{$k}.PROPERTY_15 = t.IBLOCK_ELEMENT_ID AND m{$k}.PROPERTY_14 = {$match} ";
    $order =  $order.", m0.PROPERTY_17 DESC";
}

$sql = " SELECT t.PROPERTY_21 as name, t.PROPERTY_19 as avatar, t.PROPERTY_1 as tag, (total - kills) as placement, kills, total, r1.teamID as id_team" . $select . " FROM (SELECT t.PROPERTY_28 as teamID FROM b_iblock_element_prop_s6 as t
INNER JOIN b_iblock_element_prop_s1 as n ON t.PROPERTY_28 = n.IBLOCK_ELEMENT_ID
AND t.PROPERTY_27 = ".$q.") as r1
INNER JOIN (SELECT t.PROPERTY_15 AS teamID
                    , sum(t.PROPERTY_18) AS total
                    , sum(t.PROPERTY_17) AS kills
              FROM b_iblock_element_prop_s5 AS t 
              WHERE t.PROPERTY_14 IN (" . implode(',',$chainMatches["chain"]) . ")
              GROUP BY t.PROPERTY_15) as r2 ON r2.teamID = r1.teamID
INNER JOIN b_iblock_element_prop_s1 as t ON r1.teamID = t.IBLOCK_ELEMENT_ID
" . $join . "
ORDER BY total DESC, kills DESC, m0.PROPERTY_17 DESC, m1.PROPERTY_17 DESC";

$rsData = $DB->Query($sql);

$rank = 0;
while($el = $rsData->fetch()) {

    $rank += 1;
    $strSql = 'SELECT u.IBLOCK_ELEMENT_ID as id_team, IF(total IS NOT Null,total, 0) + IF(u.PROPERTY_31 IS NOT Null, u.PROPERTY_31, 300) as total
                                FROM b_iblock_element_prop_s1 as u 
                                LEFT JOIN (SELECT t.PROPERTY_15 AS teamID, sum(t.PROPERTY_18) AS total, sum(t.PROPERTY_17) AS kills 
                                FROM b_iblock_element_prop_s5 AS t 
                                INNER JOIN b_iblock_element_prop_s3 AS m ON t.PROPERTY_14 = m.IBLOCK_ELEMENT_ID 
                                WHERE m.PROPERTY_23 = 6 
                                GROUP BY t.PROPERTY_15) AS r1 ON r1.teamID = u.IBLOCK_ELEMENT_ID 
                                INNER JOIN b_iblock_element AS a ON a.ID = u.IBLOCK_ELEMENT_ID 
                                WHERE a.ACTIVE = "Y"
                                AND u.IBLOCK_ELEMENT_ID = ' . $el["id_team"];

    $data = $DB->Query($strSql);

    if ($row = $data->fetch()) {
        $rating = ceil($row["total"]);
        $total = ceil($el['total']);
    }
    $prem = "Без подписки";
    $isPrem = wasTeamPrem($el["id_team"], $q);
    if ($isPrem) {
        $prem = "С подпиской";
    }
    $squad = getSquadByIdMatch( $q, $el["id_team"]);

    echo "<tr>
        <td>{$rank}</td>
            <td>{$el['name']}</td>
            <td>{$total}</td>
            <td>{$rating}</td>
            <td>{$prem}</td>
            <td>{$squad['DATE_CREATE']}</td>
            </tr>";
}