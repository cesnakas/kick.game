<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("invite-game");
CModule::IncludeModule("iblock");

$mId = $_GET["id"]+0;
if ($mId == 0) LocalRedirect(SITE_DIR."dashboard/invites/");
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


function getPlayersSquadByIdMatch($idMatch, $teamId)
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

function updateMembers($props = [], $id)
{
    CIBlockElement::SetPropertyValuesEx($id, 4, $props);
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

function removeMatchMember($matchID, $teamID){

    $idNextMatch = $matchID;
    $chainMatches[] = $matchID;

    do {
        $match = getMatchByParentId( $idNextMatch);
        if ($match != null) {
            $nextMatch = true;
            $chainMatches[] = $match['ID'];
            $idNextMatch = $match['ID'];
        } else {
            $nextMatch = false;
        }
    } while($nextMatch == true);

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
        }


    }
}

function addInvite($matchIDs, $matchName, $teamID, $squad){

    $firstMatchId = $matchIDs[0];

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

    $props = [];
    $tmp = [];

    foreach ($playerKeys as $key => $name) {
        $props[$name] = $squad[$key]+0;
        if ($props[$name] > 0) {
            $tmp[] = $key;
        }
    }

    $props['MATCH_STAGE_ONE'] = $firstMatchId;
    $props['TEAM_ID'] = $teamID;
    $code = 'SQUAD_' . $matchName;

    $squadId = createSquad($props, $code);

    if ($squadId) {

// получаем участников матчей
        $resMembersMatches = getMembersByMatchId($matchIDs);
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

//function
$arMatch = getMatchById($mId);
$teams = getAllTeams();
//dump(getMatchById($mId));
?>
    <script>
        function listPlayers(str) {
            if (str == "") {
                document.getElementById("playersList").innerHTML = "";
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        let response = this.responseText;
                        document.getElementById("playersList").innerHTML = response;
                    }
                };
                xmlhttp.open("GET","getinviteform.php?teamID="+str,true);
                xmlhttp.send();
            }
        }
    </script>
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
<div class="container my-5">
    <form action='#' method='post'>
        <label for="teams">Выберите команду</label>
        <input class ="my-5" list="teams" name="teams" onchange="listPlayers(this.value)">
        <datalist id="teams">
            <?php foreach ($teams as $team){ ?>
                <option  value="<?php echo $team["teamID"] ?>" label="<?php echo $team["teamID"] . " - " . $team["name"] ?>">
            <?php } ?>

        </datalist>
        <div id="playersList"></div>
    </form>
</div>
<?php


if(isset($_POST["btn_invite"])){

    if (!empty($_POST['formedSquad'])) {
        $idNextMatch = $mId;
        $chainMatches[] = $mId;

        do {
            $match = getMatchByParentId( $idNextMatch);
            if ($match != null) {
                $nextMatch = true;
                $chainMatches[] = $match['ID'];
                $idNextMatch = $match['ID'];
            } else {
                $nextMatch = false;
            }
        } while($nextMatch == true);


        addInvite($chainMatches, $arMatch["NAME"], $_POST["teams"], $_POST['formedSquad']);

 //addMatchMember($_POST["idMatch"], $teamID);
}
}


if(isset($_POST["btn_delete"])){

    foreach ($_POST["squads"] as $teamSquad){

        if($teamSquad){
            $curSquadId = getSquadByIdMatch($mId, $teamSquad);
            CIBlockElement::Delete($curSquadId['ID']);
            removeMatchMember($mId, $teamSquad);
        }
    }

}

$members = getMembersListByMatchId($arMatch["ID"]);

if (!empty($members)) {
    ?>
        <div class="container">

    <form action="#" method="post">
        <div class="table-responsive">
    <table class="table table-striped table table-hover ">
        <thead class ="table-primary">
        <tr>
            <th style ="width:10%" scope="col">Удалить</th>
            <th style ="width:14%" scope="col">Слот</th>
            <th scope="col">Команда</th>

        </tr>
        </thead>
        <tbody>
        <div id="accordion">
    <?php

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

    $arrForRank = [];
    $n = 0;
    foreach ($tmpKeys as $key) {
        $teamId = $members["TEAM_PLACE_$key"]['VALUE'];
        $team = null;
        if ($teamId) $team = getTeamById($teamId);

        $arrForRank[$n] = [
            'slot' => "Слот N°{$key}",
            'team' => $team
        ];
        $n = $n+1;
    }

    foreach ($arrForRank as $rank => $teamRank) {
        $rank+=1;
        ?>
    <div class="card" style="border: none;">

        <tr>
            <th scope="row">
                <input <?php if(!isset($teamRank['team']['ID'])) echo "hidden" ?> type="checkbox" name="squads[]" value="<?php echo $teamRank['team']['ID'] ;?>">
            </th>
            <td>
                <?php echo $teamRank['slot'] ;?>
            </td>
            <td>
                <?php if(isset($teamRank['team']['ID'])) {
                        $arrSquad = getUsers(getPlayersSquadByIdMatch($mId, $teamRank['team']['ID']));
                    ?>
                    <div id="heading<?php echo $rank ;?>">
                        <p style="color: #007bff;" data-toggle="collapse" data-target="#collapse<?php echo $rank ;?>" aria-expanded="true" aria-controls="collapse<?php echo $rank ;?>">
                            <?php echo $teamRank['team']['NAME']; ?> [<?php echo $teamRank['team']["TAG_TEAM"]['VALUE']; ?>]
                        </p>
                     </div>

                <?php } else {
                    echo "Свободное место";
                 } ?>

                <div id="collapse<?php echo $rank ;?>" class="collapse" aria-labelledby="heading<?php echo $rank ;?>" data-parent="#accordion">
                    <div class="card-body">


                        <div class="table-responsive">
                            <table class="table table-striped table-sm table-hover ">
                                <thead class ="table-primary">
                                <tr>
                                    <th scope="col">Никнейм</th>
                                    <th scope="col">Электронная почта</th>
                                </tr>
                                </thead>
                                <tbody>
                    <?php foreach ($arrSquad as $player) {
                       // dump($player);
                       ?>
                                <tr>
                                    <td><?php echo $player["LOGIN"] ;?></td>
                                    <td><?php echo $player["EMAIL"] ;?></td>
                                </tr>
                  <?php  } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </td>



        </tr>
    </div>

    <?php } ?>

        </div>
        </tbody>
    </table>

    <div class="d-flex">
        <button type="submit" class="btn btn-success d-block ml-auto" name="btn_delete">Удалить команду</button>
    </div>
        </div>
    </form>
        </div>


<?php  } ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>