<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
global $DB;
$q = intval($_GET['q']);

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
ORDER BY total DESC, kills DESC, m0.PROPERTY_17 DESC, m1.PROPERTY_17 DESC LIMIT 6";
//echo $sql;
$rsData = $DB->Query($sql);
while($el = $rsData->fetch()) {
    echo "
<div class='flex-table-tournament--row' >
<span>
    <div class='match-participants__team'>
    <div class='match-participants__team-logo' style='background-image: url(".CFile::GetPath($el["avatar"]).");'>
    </div>
    <a href='/teams/{$el["id_team"]}/' class='match-participants__team-link'>{$el["name"]} [{$el["tag"]}] </a>
    </div>
    </span>
    <span>
    ".ceil($el["total"])."
  </span>
  </div>";
}

