<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
global $DB;
$q = intval($_GET['q']);



$sql="SELECT a.ACTIVE, u.PROPERTY_1 as tag, u.PROPERTY_19 as avatar, u.PROPERTY_21 AS name, u.IBLOCK_ELEMENT_ID as id_team, IF(total IS NOT Null,total, 0) + IF(u.PROPERTY_31 IS NOT Null, u.PROPERTY_31, 300) as total
                                FROM b_iblock_element_prop_s1 as u 
                                LEFT JOIN (SELECT t.PROPERTY_15 AS teamID, sum(t.PROPERTY_18) AS total, sum(t.PROPERTY_17) AS kills 
                                FROM b_iblock_element_prop_s5 AS t 
                                INNER JOIN b_iblock_element_prop_s3 AS m ON t.PROPERTY_14 = m.IBLOCK_ELEMENT_ID 
                                WHERE m.PROPERTY_23 = 6 
                                GROUP BY t.PROPERTY_15) AS r1 ON r1.teamID = u.IBLOCK_ELEMENT_ID 
                                INNER JOIN b_iblock_element AS a ON a.ID = u.IBLOCK_ELEMENT_ID
                                INNER JOIN b_iblock_element_prop_s6 as n ON n.PROPERTY_28 = u.IBLOCK_ELEMENT_ID
                                WHERE a.ACTIVE = 'Y'
                                AND n.PROPERTY_27 =" . $q;

$rsData = $DB->Query($sql);


while($el = $rsData->fetch()) {
    echo "
<div class='flex-table-tournament--row' >
<span>
    <div class='match-participants__team'>
    <div class='match-participants__team-logo' style='background-image: url(".SITE_TEMPLATE_PATH."/dist/images/{$el["avatar"]}.jpg)'>
    </div>
    <a href='#{$el["id_team"]}' class='match-participants__team-link'>{$el["name"]} [{$el["tag"]}] </a>
    </div>
    </span>
    <span>
    ".ceil($el["total"])."
  </span>
  </div>";
}

