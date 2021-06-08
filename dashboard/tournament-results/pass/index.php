<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Pass");
CModule::IncludeModule("iblock");

$mId = $_GET["id"]+0;

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

function getChainMatches( $firstMaitchID ){
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

function getIdsPassedTeams( $IDs = array(),$stagePass){
    $arSelect = Array("ID", "PROPERTY_STAGE_PASS");
    $arFilter = Array(
                        "IBLOCK_ID" => 1,
                        "ID" => $IDs,
                        "PROPERTY_STAGE_PASS" => $stagePass,
                        "ACTIVE_DATE" => "Y",
                        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $ids = [];
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $ids[$arFields["ID"]] = array_merge($arFields, $arProps);
    }
    return $ids;
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


//function
$passingTeams = array_flip($_POST["idPass"]);
$arMatch = getMatchById($mId);
$stageKeyPass = $arMatch["TOURNAMENT"]["VALUE"]. "." .$arMatch["STAGE_TOURNAMENT"]["VALUE_ENUM_ID"];

//dump(getMatchById($mId));
?>
<div class="container my-5">
    <h2 class="mb-3"><?php echo $arMatch["NAME"] ?></h2>
    <div class="row align-items-center">
        <div class="col-md-4">
            <h4>Дата и время начала</h4>
            <p><span ><?php echo $arMatch["DATE_START"]["VALUE"] ?></span></p>
        </div>
        <div class="col-md-4">
            <h4>Тип матча</h4>
            <p><span ><?php echo $arMatch["TYPE_MATCH"]["VALUE"] ?></span></p>
        </div>
    </div>

</div>

<?php
$teamIds = getMembersIdsTeamByMatchId($arMatch["ID"]);
$teamIds = array_diff($teamIds, array(''));
$passedTeams = getIdsPassedTeams($teamIds, $stageKeyPass);

if(isset($_POST["btn_give_pass"])){

    foreach($teamIds as $teamId){
        $props["STAGE_PASS"] = "";
        if(isset($passingTeams[$teamId])) {
            $props["STAGE_PASS"] = $stageKeyPass;
        }
        updateTeam($props, $teamId+0);
    }
    LocalRedirect("/dashboard/tournament-results/invite-game/?id=".$mId);
}

if (!empty($teamIds)) {
    $chainMatches = getChainMatches( $arMatch['ID'] );
    //dump( $chainMatches );
    $points = countPointsByMatchesIDs( $chainMatches['chain'] );
    //dump($points);
    $wins =countWWCD($chainMatches['chain']);

    $matchKills = countKillsByMatchesIDs($chainMatches['chain']);

    //dump($wins);
    $titleParticipants = 'Результаты игры';

    //dump($points);

    ?>
        <div class="container">
    <form action="#" method="post">
        <div class="table-responsive">
    <table class="table table-striped table-dark">
        <thead>
        <tr>
            <th scope="col">Пропуск</th>
            <th scope="col">Ранк</th>
            <th scope="col">Комманда</th>
            <th scope="col">WWCD</th>
            <th scope="col">KILL PTS</th>
            <th scope="col">PLACE PTS</th>
            <th scope="col">TOTAL PTS</th>
        </tr>
        </thead>
        <tbody>
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


    sortRank($arrForRank, $matchKills);
    //dump($arrForRank);


    foreach ($arrForRank as $rank => $teamRank) {
        $rank+=1;
        ?>
        <tr>
            <th scope="row">
                <input type="checkbox" <?php if(isset($passedTeams[$teamRank['team']['ID']])) echo "checked" ?>  name="idPass[]" value="<?php echo $teamRank['team']['ID'] ;?>">
            </th>
            <td><?php echo $rank ?></td>
            <td>
                <?php if(isset($teamRank['team'])) { ?>
                    <a href="<?=SITE_DIR?>teams/<?php echo $teamRank['team']['ID'];?>/" class="match-participants__team-link"><?php echo $teamRank['team']['NAME']; ?> [<?php echo $teamRank['team']["TAG_TEAM"]['VALUE']; ?>]</a>
                <?php } else {
                    echo "Команда удалена";
                }?>
            </td>
            <td> <?php echo $teamRank['wwcd'] ?></td>
            <td> <?php echo $teamRank['kills'] ?></td>
            <td> <?php echo $teamRank['place'] ?></td>
            <td> <?php echo $teamRank['total'] ?></td>
        </tr>
    <?php } ?>


        </tbody>
    </table>
        </div>
    <div class="d-flex">
        <button type="submit" class="btn btn-success d-block ml-auto" name="btn_give_pass">Сохранить</button>
    </div>
    </form>
        </div>

<?php  } ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>