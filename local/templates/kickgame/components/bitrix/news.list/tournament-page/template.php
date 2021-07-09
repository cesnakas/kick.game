<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);


// получаем текушего пользователя
// дергаем у него поле id команды

$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];
$participantId = $teamID;

$firstGameID = findGame($_GET["tournamentID"]);
$firstGame = getMatchById($firstGameID);

if($firstGame["GAME_MODE"]["VALUE_ENUM_ID"] == 12){
    $participantId = $userID;
}
$totalFree = getParticipationByMatchId($firstGame['ID']);
$totalFree = count($totalFree);
$nextGameID = getNextGame($participantId, $_GET["tournamentID"]);
$nextGame = getMatchById($nextGameID);


function getSquadsForFinals($matchID){
GLOBAL $DB;

    $curMatch = getMatchById($matchID);

$sql="SELECT u.PROPERTY_1 as tag, u.PROPERTY_19 as avatar, u.PROPERTY_21 AS name, u.IBLOCK_ELEMENT_ID as id_team
                                FROM b_iblock_element_prop_s1 as u 
                                INNER JOIN b_iblock_element_prop_s6 as n ON n.PROPERTY_28 = u.IBLOCK_ELEMENT_ID
                                AND n.PROPERTY_27 = " . $matchID;

if($curMatch["GAME_MODE"]["VALUE_ENUM_ID"] == 12){
    $sql="SELECT u.LOGIN as name, u.PERSONAL_PHOTO as avatar, u.ID as id_team
                                FROM b_user as u 
                                INNER JOIN b_iblock_element_prop_s6 as n ON n.PROPERTY_28 = u.ID
                                AND n.PROPERTY_27 = " . $matchID;
}
//dump($sql, 1);
$rsData = $DB->Query($sql);

$arTeams = [];
while($row = $rsData->fetch()) {
    $arTeams[] = $row;
}
return $arTeams;

}

function getFinalsMatches($tournamentID, $stage) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array(
        "IBLOCK_ID" =>3,
        "PROPERTY_PREV_MATCH" => false,
        "PROPERTY_TYPE_MATCH" => 5,
        "PROPERTY_TOURNAMENT" => $tournamentID,
        "PROPERTY_STAGE_TOURNAMENT" => 1,
        //  "<=PROPERTY_DATE_START" => ConvertDateTime($date, "YYYY-MM-DD")." 23:59:59",
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $output = [];

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        //$arProps = $ob->GetProperties();
        $output[] = $arFields;
    }
    return $output;
}

function getFinalsGame($tournamentID){
    GLOBAL $DB;
    $sql = "SELECT m.IBLOCK_ELEMENT_ID as gameID FROM b_iblock_element_prop_s3 as m WHERE m.PROPERTY_3 = ". $tournamentID ." AND m.PROPERTY_22 = 1";
    $rsData = $DB->Query($sql);


    while($el = $rsData->fetch()) {
        $game[] = $el["gameID"];
    }
    return $game;
}
$tournamentId = $_GET["tournamentID"];
$costParticipationChicks = '';
?>
<?php if ($tournamentId) {
    $tournament = getTournamentById($tournamentId);
    $costParticipationChicks = $tournament["COST_PARTICIPATION_CHICKS"]["VALUE"]+0;
}
?>
<script>
    function getPlayers(str) {
        if (str == "") {
            document.getElementById("participants"+str).innerHTML = "";

            return;
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    let response = this.responseText;
                    console.log(str);
                    document.getElementById("participants"+str).innerHTML = response;

                }
            };
            xmlhttp.open("GET","getmatchparticipants.php?q="+str,true);
            xmlhttp.send();
        }
    }

    function getResults(str) {
        if (str == "") {
            document.getElementById("results"+str).innerHTML = "";
            return;
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    let response = this.responseText;
                    console.log(str);
                    document.getElementById("results"+str).innerHTML = response;

                }
            };
            console.log(str);
            xmlhttp.open("GET","getresults.php?q="+str,true);
            xmlhttp.send();
        }
    }
</script>

<?

$matchesArray = [];
foreach($arResult["ITEMS"] as $arItem) {
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    $matchesArray[$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE_ENUM_ID']][substr($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"],0,10)][substr($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"],11)][] = $arItem;
    $stages[$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE_ENUM_ID']] = $arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'];
}


?>

<div class="tournament-schedule-results">
    <ul class="nav nav-pills tournament-schedule-results__big-nav" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#tab1" role="tab" data-toggle="tab"><?=GetMessage('TOUR_SCHEDULE')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#tab2" role="tab" data-toggle="tab"><?=GetMessage('TOUR_RESULTS')?></a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">

        <div role="tabpanel" class="tab-pane fade show active" id="tab1">

            <ul class="nav nav-pills tournament-schedule-results__stage-nav" role="tablist">
                <?php $countStages = 1;
                foreach ($matchesArray as $k => $stage){ ?>
                <li class="nav-item">
                    <a class="nav-link <?= $countStages == 1 ? "active" : "" ?>" href="#tab_schedule_<?= $countStages ?> " role="tab" data-toggle="tab"><?= $k > 2 ? $stages[$k] . GetMessage('TOUR_FINALE') : $stages[$k] ?></a>
                </li>
                <?php
                    $countStages = $countStages + 1;
                } ?>
            </ul>

            <div class="tab-content">
                <?php $countStages = 1;
                foreach ($matchesArray as $k => $stage){ ?>
                <div role="tabpanel" class="tab-pane fade <?php echo $countStages == 1 ? "show active" : "" ?>" id="tab_schedule_<?php echo $countStages ?>">
                    <?php
                    $dates[$countStages] = getStagePeriod($k, $_GET["tournamentID"]);
                    ?>
                    <?php if ($k != 1){ ?>

                    <div class="tournament-schedule-results__overlay" id="scrollToGroups">
                        <?= $dates[$countStages]["min"] . " - " . $dates[$countStages]["max"] ?>
                        <br>
                        <?= $dates[$countStages]["games"] . " " . num_decline($dates[$countStages]["games"], GetMessage('TOUR_GAME_GAMES'), false); ?>
                    </div>
                    <div class="accordion accordion-game" id="gameStage_<?php echo $countStages ?>">
                        <?php $countDate = 1;
                        foreach ($stage as $l => $date){

                        foreach ($date as $m => $time) {

                            foreach ($time as $n => $group) {
                                $matchOccupied[$group["ID"]] = countTeams($group["ID"]);
                                $totalOccupied[$l] += $matchOccupied[$group["ID"]];
                                $counter[$l] += 1;
                            }
                        }
                            ?>
                        <div class="card card-game">
                            <div class="card-header" id="headingGame_<?php echo $countDate ?>_stage_<?php echo $countStages ?>">
                                <h2 class="mb-0">
                                    <button class="accordion-game__heading <?php echo $countDate == 1 ? "": "collapsed";?>" type="button" data-toggle="collapse" data-target="#collapseGame_<?php echo $countDate?>_stage_<?php echo $countStages ?>" aria-expanded="true" aria-controls="collapseGame_<?php echo $countDate?>_stage_<?php echo $countStages ?>">
                                        <?php
                                        // $scheduleDay = formDate($l, 'd MMMM, E');
                                        $scheduleDay = FormatDate('d F, D', MakeTimeStamp($l), time() + CTimeZone::GetOffset() );
                                        echo $scheduleDay ?> <span class="tournament-schedule-results__participants"><i></i> <span class="tournament-schedule-results__participants-cur-count"><?php echo  $totalOccupied[$l] ?></span> / <?php echo  $counter[$l]*$totalFree?></span>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseGame_<?php echo $countDate?>_stage_<?php echo $countStages ?>" class="collapse collapse-game <?php echo $countDate == 1 ? "show": "";?>" aria-labelledby="headingGame_<?php echo $countDate?>_stage_<?php echo $countStages ?>" data-parent="#gameStage_<?php echo $countStages ?>">
                                <div class="card-body card-body__game">
                                    <!--start group -->
                                    <?php $countTime = 1;
                                    foreach ($date as $m => $time){
                                        ?>
                                    <div class="tournament-schedule-results__time"><?php
                                        // $scheduleTime = formDate($m, "HH:mm");
                                        if (LANGUAGE_ID == 'ru') {
                                            //$scheduleTime = FormatDate('H:m', MakeTimeStamp($m), time() + CTimeZone::GetOffset());
                                          $scheduleTime = formDate($m, "HH:mm");
                                        } else {
                                            $scheduleTime = FormatDate('h:m a', MakeTimeStamp($m), time() + CTimeZone::GetOffset());
                                        }
                                        echo $scheduleTime;
                                      ?></div>
                                    <div class="accordion accordion-group" id="game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                        <?php $countGroup = 1;
                                        foreach ($time as $n => $group){ ?>
                                        <div class="card">
                                            <div class="card-header" id="headingGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                <h2 class="mb-0">
                                                    <button class="accordion-group__heading collapsed" value="<?php echo $group["ID"]?>" onclick="getPlayers(this.value)" type="button" data-toggle="collapse" data-target="#collapseGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" aria-expanded="true" aria-controls="collapseGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                        <?=GetMessage('TOUR_GROUP_NO')?><?php echo $countGroup ?>
                                                        <span class="tournament-schedule-results__participants-group"><i></i> <span class="tournament-schedule-results__participants-group-cur-count"><?php echo $matchOccupied[$group["ID"]] ?></span> / <?php echo $totalFree ?></span>
                                                        <span class="accordion-group__progress" style="width:  <?php echo $matchOccupied[$group["ID"]] * 5.555 ?>%;"></span>
                                                    </button>
                                                </h2>
                                            </div>

                                            <div id="collapseGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" class="collapse" aria-labelledby="headingGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" data-parent="#game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                <div class="card-body card-body__group" ">
                                                    <div class="flex-table-tournament">
                                                        <div class="flex-table-tournament--header">
                                                            <div class="flex-table-tournament--categories">
                                                                <span><?=GetMessage('TOUR_TEAM')?></span>
                                                                <span><?=GetMessage('TOUR_RATINGS')?></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-table-tournament--body" id="participants<?php echo $group["ID"]?>">

                                                        </div>
                                                    </div>
                                                <?php
                                                if ((isCaptain($userID, $teamID) || $group["PROPERTIES"]["GAME_MODE"]["VALUE_ENUM_ID"] == 12) && $matchOccupied[$group["ID"]] < $totalFree && strtotime($group["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]) > time() ) { ?>
                                                    <div class="tournament-schedule-results__btn-change-group">
                                                        <?php
                                                        if (!$nextGameID && $countStages == 1){ ?>
                                                        <a href="<?=SITE_DIR?>tournament-page/join-game/?mid=<?php echo $group["ID"]?>" data-matchId="<?php echo $group["ID"]?>" class="btn btn-reg-on-tournament" <?php if($costParticipationChicks > 0) { ?> data-toggle="modal" data-target="#registrationOnTournament"<?php } ?>><?=GetMessage('TOUR_APPLY')?><?php if($costParticipationChicks > 0) { ?> за <?php echo num_decline($costParticipationChicks, "chick, chicks");  } ?></a>

                                                        <?php } else if ($nextGame["STAGE_TOURNAMENT"]["VALUE_ENUM_ID"] == $k && $nextGame["ID"] != $group["ID"]  && strtotime($nextGame["DATE_START"]["VALUE"]) > time()) { ?>
                                                                <form action="#" method="post">
                                                            <input value="<?php echo $group["ID"]?>" type="hidden" name="idMatch">
                                                            <input value="<?=GetMessage('TOUR_CHANGE_GROUP')?>" type="submit" name="changeGame" class="btn">
                                                                </form>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                            <?php
                                            $countGroup = $countGroup + 1;
                                        } ?>
                                    </div>
                                    <!--end group -->
                                        <?php
                                        $countTime = $countTime + 1;
                                    } ?>
                                </div>
                            </div>

                        </div>
                            <?php
                            $countDate = $countDate + 1;
                        } ?>

                    </div>
            <?php } else {

            foreach ($stage as $l => $date){

                foreach ($date as $m => $time) {

                    foreach ($time as $n => $group) {

                        $matchOccupied[$group["ID"]] = countTeams($group["ID"]);
                        $totalOccupied[$l] += $matchOccupied[$group["ID"]];
                        $counter[$l] += 1;
                    }
                }
            } ?>

                <div class="tournament-schedule-results__overlay"><?php echo $dates[$countStages]["min"] . " - " . $dates[$countStages]["max"] ?><br>
                    <?php echo $dates[$countStages]["games"] . " " . num_decline($dates[$countStages]["games"], GetMessage('TOUR_GAME_FINAL'), false); ?> </div>
                <div class="accordion accordion-game" id="gameStage_<?php echo $countStages ?>">
                    <?php $countDate = 1;
                    foreach ($stage as $l => $date){ ?>

                        <?php $countTime = 1;
                        foreach ($date as $m => $time){ ?>
                            <div class="tournament-schedule-results__time">
                                <?php
                                $scheduleTime = formDate($l.$m, "d MMMM yyyy, HH:mm");
                                echo $scheduleTime ?></div>
                            <div class="accordion accordion-group" id="results_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                <?php $countGroup = 1;
                                foreach ($time as $n => $group){ ?>
                                    <div class="card">
                                        <div class="card-header" id="headingGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                            <h2 class="mb-0">
                                                <button class="accordion-group__heading" value="<?php echo $group["ID"]?>" onclick="getPlayers(this.value)" type="button" data-toggle="collapse" data-target="#collapseGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" aria-expanded="true" aria-controls="collapseGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                    <?=GetMessage('TOUR_DAY_NO')?><?php echo $countDate ?>
                                                    <span class="tournament-schedule-results__participants-group"><i></i> <span class="tournament-schedule-results__participants-group-cur-count"><?php echo $matchOccupied[$group["ID"]] ?></span> / <?php echo $totalFree ?></span>
                                                    <span class="accordion-group__progress" style="width: <?php echo $matchOccupied[$group["ID"]] * 5.555 ?>%;"></span>
                                                </button>
                                            </h2>
                                        </div>

                                        <div id="collapseGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" class="collapse" aria-labelledby="headingGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" data-parent="#results_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                            <div class="card-body card-body__group">
                                                <div class="flex-table-tournament">
                                                    <div class="flex-table-tournament--header">
                                                        <div class="flex-table-tournament--categories">
                                                            <span><?=GetMessage('TOUR_TEAM')?></span>
                                                            <span><?=GetMessage('TOUR_RATINGS')?></span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-table-tournament--body" id="participants<?php echo $group["ID"]?>">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $countGroup = $countGroup + 1;
                                } ?>
                            </div>
                            <!--end group -->
                            <?php
                            $countTime = $countTime + 1;
                        } ?>

                        <?php
                        $countDate = $countDate + 1;
                    } ?>

                </div>
            <?php } ?>
                </div>
                    <?php
                    $countStages = $countStages + 1;
                } ?>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="tab2">

            <ul class="nav nav-pills tournament-schedule-results__stage-nav" role="tablist">
                <?php $countStages = 1;
                foreach ($matchesArray as $k => $stage){ ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $countStages == 1 ? "active" : "" ?>" href="#tab_results_<?php echo $countStages ?> " role="tab" data-toggle="tab"><?php echo $k > 2 ? $stages[$k] . GetMessage('TOUR_FINALE') : $stages[$k] ?></a>
                    </li>
                    <?php
                    $countStages = $countStages + 1;
                } ?>
            </ul>

            <div class="tab-content">
                <?php $countStages = 1;
                foreach ($matchesArray as $k => $stage){
                    ?>
                    <div role="tabpanel" class="tab-pane fade <?php echo $countStages == 1 ? "show active" : "" ?>" id="tab_results_<?php echo $countStages ?>">
                        <?php if ($k != 1){ ?>

                            <div class="tournament-schedule-results__overlay"><?php echo $dates[$countStages]["min"] . " - " . $dates[$countStages]["max"] ?><br>
                        <?php echo $dates[$countStages]["games"] . " " . num_decline($dates[$countStages]["games"], GetMessage('TOUR_GAME_GAMES'), false); ?> </div>
                            <div class="accordion accordion-game" id="gameResultsStage_<?php echo $countStages ?>">
                    <?php $countDate = 1;
                    foreach ($stage as $l => $date){?>
                        <div class="card card-game">
                            <div class="card-header" id="headingResultsGame_<?php echo $countDate ?>_stage_<?php echo $countStages ?>">
                                <h2 class="mb-0">
                                    <button class="accordion-game__heading <?php echo $countDate == 1 ? "": "collapsed";?>" type="button" data-toggle="collapse" data-target="#collapseResultsGame_<?php echo $countDate?>_stage_<?php echo $countStages ?>" aria-expanded="true" aria-controls="collapseResultsGame_<?php echo $countDate?>_stage_<?php echo $countStages ?>">
                                        <?php
                                        // $scheduleDay = formDate($l, 'd MMMM, E');
                                        $scheduleDay = FormatDate('d F, D', MakeTimeStamp($l));
                                        echo $scheduleDay ?> <span class="tournament-schedule-results__participants"><i></i> <span class="tournament-schedule-results__participants-cur-count"><?php echo  $totalOccupied[$l]?></span> / <?php echo $counter[$l]*$totalFree ?></span>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseResultsGame_<?php echo $countDate?>_stage_<?php echo $countStages ?>" class="collapse collapse-game <?php echo $countDate == 1 ? "show": "";?>" aria-labelledby="headingResultsGame_<?php echo $countDate?>_stage_<?php echo $countStages ?>" data-parent="#gameResultsStage_<?php echo $countStages ?>">
                                <div class="card-body card-body__game">
                                    <!--start group -->
                                    <?php $countTime = 1;
                                    foreach ($date as $m => $time){ ?>
                                        <div class="tournament-schedule-results__time">
                                            <?php
                                            // $scheduleTime = formDate($m, "HH:mm");
                                            if (LANGUAGE_ID == 'ru') {
                                                $scheduleTime = FormatDate('H:m', MakeTimeStamp($m), time() + CTimeZone::GetOffset());
                                            } else {
                                                $scheduleTime = FormatDate('h:m a', MakeTimeStamp($m), time() + CTimeZone::GetOffset());
                                            }
                                            echo $scheduleTime ?></div>
                                        <div class="accordion accordion-group" id="results_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                            <?php $countGroup = 1;
                                            foreach ($time as $n => $group){ ?>
                                                <div class="card">
                                                    <div class="card-header" id="headingResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                        <h2 class="mb-0">
                                                            <button class="accordion-group__heading" value="<?php echo $group["ID"]?>" onclick="getResults(this.value)" type="button" data-toggle="collapse" data-target="#collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" aria-expanded="true" aria-controls="collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                                <?=GetMessage('TOUR_GROUP_NO')?><?php echo $countGroup ?>
                                                                <span class="tournament-schedule-results__participants-group"><i></i> <span class="tournament-schedule-results__participants-group-cur-count"><?php echo $matchOccupied[$group["ID"]] ?></span> / <?php echo $totalFree ?></span>
                                                                <span class="accordion-group__progress" style="width: <?php echo $matchOccupied[$group["ID"]] * 5.555 ?>%;"></span>
                                                            </button>
                                                        </h2>
                                                    </div>

                                                    <div id="collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" class="collapse" aria-labelledby="headingResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" data-parent="#results_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                        <div class="card-body card-body__group">
                                                            <div class="flex-table-tournament">
                                                                <div class="flex-table-tournament--header">
                                                                    <div class="flex-table-tournament--categories">
                                                                        <span><?=GetMessage('TOUR_TEAM')?></span>
                                                                        <span><?=GetMessage('TOUR_POINTS')?></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-table-tournament--body" id="results<?php echo $group["ID"]; ?>">
                                                                    <?php if (strtotime($group["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]) > time()){ ?>
                                                                        <?=GetMessage('TOUR_NOT_RESULTS')?>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <?php if (strtotime( $group["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]) < time()){ ?>
                                                                <div class="tournament-schedule-results__btn-change-group">
                                                                    <div><a href="#" class="btn-change-big"><?=GetMessage('TOUR_DETAILED_RESULTS')?> <?php if($costParticipationChicks > 0) { ?> за <?php echo $costParticipationChicks; } ?></a></div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                $countGroup = $countGroup + 1;
                                            } ?>
                                        </div>
                                        <!--end group -->
                                        <?php
                                        $countTime = $countTime + 1;
                                    } ?>
                                </div>
                            </div>

                        </div>
                        <?php
                        $countDate = $countDate + 1;
                    } ?>

                </div>

                        <?php } else {
                            $finals = array_reverse(getFinalsGame($_GET["tournamentID"]));
                            ?>
                            <div class="tournament-schedule-results__overlay"><?php echo $dates[$countStages]["min"] . " - " . $dates[$countStages]["max"] ?><br>
                                <?php echo $dates[$countStages]["games"] . " " . num_decline($dates[$countStages]["games"], GetMessage('TOUR_GAME_FINAL'), false); ?> </div>
                            <h1 style="text-align: center;"><?php echo isGameResults($finals[0]) ? "Итоговые" : "Предварительные"?> результаты турнира</h1>
<!--Construction site             Construction site              Construction site-->
                            <div class="container" style="margin-bottom: 5%">
                                <div class="row row-mobile-table">
                        <div style="display: flex;">
                            <div class="table-responsive">
                            <table class="finals-table">
                                <thead>
                                <tr>
                                    <th scope="col" class="mobile-th"><span>Место</span></th>
                                    <th scope="col"><span>Участники</span></th>
                                    <th scope="col" class="mobile-th">Общий счет<br><span>Total PTS</span></th>
                                    <th scope="col" class="mobile-th"><span>Kills PTS</span></th>
                                    <th class="last-th" scope="col"><span>Place PTS</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $finals = getFinalsGame($_GET["tournamentID"]);
                                    if(isGameResults($finals[0])){
                                        $arrTeams = getFinalsFirstTable($finals);
                                    } else {
                                        $arrTeams = getSquadsForFinals($finals[0]);
                                    }

                                    $rank = 0;

                                    foreach ($arrTeams as $team) {
                                        $tag = "";
                                        if(isset($team["tag"])){
                                            $tag = "  [" . $team["tag"] . "]";
                                        }
                                        $rank += 1;
                                        $totalKills = ceil($team["kills"]);
                                        $totalPlace = ceil( $team["total"] - $team["kills"]);
                                        $totalTotal = ceil($team["total"]);
                                        if(!isset($team["total"])){
                                            $rank = "#";
                                            $totalKills = "...";
                                            $totalPlace = "...";
                                            $totalTotal = "...";
                                        }
                                      ?>

                                        <tr>
                                            <td class="mobile-th"><?php echo $rank; ?></td>
                                            <td class="mobile-th">
                                                <div class="team-wrap">
                                                <div class='participants-logo' style='background-image: url("<?php echo CFile::GetPath($team["avatar"]) ?>");'>
                                                </div><a href='<?php echo SITE_DIR; ?>teams/<?php echo $team["id_team"]; ?>/' class='participants-link'><?php echo $team["name"] . $tag; ?></a>
                                                </div>
                                            </td>
                                            <td class="mobile-th"><?php echo $totalTotal; ?></td>
                                            <td class="mobile-th"><?php echo $totalKills; ?></td>
                                            <td class="mobile-th"><?php echo $totalPlace; ?></td>
                                        </tr>

                                    <?php } ?>

                                </tbody>
                            </table>
                            </div>
                            <div class="scroll dragscroll" >
                                <div class="box-wrap" style="display: flex">
                                    <div class="accordion" id="accordionHorizontalExample" style="display: flex"  >

                            <?php
                            $finalGames = getFinalsMatches($_GET["tournamentID"], 1);
                            $matchCount = 0;

                            foreach ($finalGames as $finalGame) {

                                if($finalGame["PROPERTY_8"] == NULL && isGameResults($finalGame["ID"])){
                                $matchCount += 1;

                                $gameResults = getGameResultsTable($finalGame["ID"]);

                                if(!$gameResults["results"]){
                                    $gameKills = "...";
                                    $gamePlace = "...";
                                    $gameTotal = "...";
                                    $gameResults["results"] = $arrTeams;
                                }?>

                                    <div class="table-responsive" style="display: flex;  position: relative; max-height: min-content " >
                                        <table class="finals-table" >
                                            <thead>
                                                <tr>
                                                    <th  scope="col">День <?php echo $matchCount; ?><br><span> Kill PTS</span></th>
                                                    <th  scope="col"><br><span>Place PTS</span></th>
                                                    <th class="last-th" scope="col"><br><span>TOTAL PTS</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($gameResults["results"] as $result) {
                                                    if(isset($result["kills"])){
                                                        $gameKills = ceil($result["kills"]);
                                                        $gamePlace = ceil($result["placement"]);
                                                        $gameTotal = ceil($result["total"]);
                                                    } ?>

                                                    <tr>
                                                        <td><?php echo $gameKills; ?></td>
                                                        <td><?php echo $gamePlace; ?></td>
                                                        <td><?php echo $gameTotal; ?></td>
                                                    </tr>

                                                <?php } ?>
                                            </tbody>
                                         </table>

                                        <div class="horizontal-card" style="display: flex">
                                                <div class="horizontal-card-header" data-toggle="collapse" data-target="#collapse<?php echo $matchCount; ?>" style="display: flex">

                                                        <?php if(isset($result["kills"])){ ?>
                                                            <img class="button-collapse" onclick="rotate(this.id);" id="accordionImg<?php echo $matchCount; ?>" src="<?=SITE_TEMPLATE_PATH;?>/dist/images/accordion.png" alt="logo">
                                                        <?php } ?>

                                            </div>


                    <div id="collapse<?php echo $matchCount; ?>" class="collapse width" data-parent="#accordionHorizontalExample" >
                        <div class="horizontal-card-body" style="display:flex;">
                            <?php $m = 0;

                                while(isset($result["total{$m}"])) { ?>
                                    <div class="table-responsive">
                                    <table class="finals-table">
                                        <thead>
                                            <tr>
                                                <th  scope="col">Карта <?php echo $m + 1; ?><br><span>Kill PTS</span></th>
                                                <th  scope="col"><br><span>Place PTS</span></th>
                                                <th class="last-th" scope="col"><br><span>Total PTS</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($gameResults["results"] as $result) { ?>

                                            <tr>
                                                <td><?php echo ceil($result["kills{$m}"]); ?></td>
                                                <td><?php echo ceil($result["total{$m}"]) - $result["kills{$m}"]; ?></td>
                                                <td><?php echo ceil($result["total{$m}"]); ?></td>
                                            </tr>

                                        <?php } ?>

                                        </tbody>
                                    </table>

                                </div>
<!--                                </div>-->

                        <?php
                                    $m++;
                                } ?>

                                        </div>
                                    </div>
                                </div>

                            </div>
                              <?php }

                        } ?>
                                </div>
                            </div>


                                </div>
                            </div>
                        </div>
                    </div>
<!--Construction site             Construction site               Construction site-->

                            <div class="accordion accordion-game" id="gameResultsStage_<?php echo $countStages ?>">
                                <?php $countDate = 1;
                                foreach ($stage as $l => $date){?>

                                                <?php $countTime = 1;
                                                foreach ($date as $m => $time){ ?>
                                                    <div class="tournament-schedule-results__time">
                                                        <?php
                                                        $scheduleTime = formDate($l.$m, "d MMMM yyyy, HH:mm");
                                                        /*
                                                        if (LANGUAGE_ID == 'ru') {
                                                            $scheduleTime = FormatDate('j M Y, H:m', MakeTimeStamp($l.$m), time() + CTimeZone::GetOffset());
                                                        } else {
                                                            $scheduleTime = FormatDate('M j Y, h:m a', MakeTimeStamp($l.$m), time() + CTimeZone::GetOffset());
                                                        }
                                                        */
                                                        echo $scheduleTime ?></div>
                                                    <div class="accordion accordion-group" id="results_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                        <?php $countGroup = 1;
                                                        foreach ($time as $n => $group){ ?>
                                                            <div class="card">
                                                                <div class="card-header" id="headingResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                                    <h2 class="mb-0">
                                                                        <button class="accordion-group__heading" value="<?php echo $group["ID"]?>" onclick="getResults(this.value)" type="button" data-toggle="collapse" data-target="#collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" aria-expanded="true" aria-controls="collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                                            <?=GetMessage('TOUR_DAY_NO')?><?php echo $countDate ?>
                                                                            <span class="tournament-schedule-results__participants-group"><i></i> <span class="tournament-schedule-results__participants-group-cur-count"><?php echo $matchOccupied[$group["ID"]] ?></span> / <?php echo $totalFree ?></span>
                                                                            <span class="accordion-group__progress" style="width: <?php echo $matchOccupied[$group["ID"]] * 5.555 ?>%;"></span>
                                                                        </button>
                                                                    </h2>
                                                                </div>

                                                                <div id="collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" class="collapse" aria-labelledby="headingResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" data-parent="#results_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                                    <div class="card-body card-body__group">
                                                                        <div class="flex-table-tournament">
                                                                            <div class="flex-table-tournament--header">
                                                                                <div class="flex-table-tournament--categories">
                                                                                    <span><?=GetMessage('TOUR_TEAM')?></span>
                                                                                    <span><?=GetMessage('TOUR_POINTS')?></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex-table-tournament--body" id="results<?php echo $group["ID"]; ?>">
                                                                                <?php if (strtotime($group["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]) > time()){ ?>
                                                                                    <?=GetMessage('TOUR_NOT_RESULTS')?>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <?php

                                                                        if(strtotime( $group["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]) < time()){
                                                                           ?>
                                                                            <div class="tournament-schedule-results__btn-change-group">

                                                                                <div><a href="<?=SITE_DIR?>game-schedule/<?php echo $group["CODE"] ?>/" class="btn-change-big"><?=GetMessage('TOUR_DETAILED_RESULTS')?></a></div>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                            $countGroup = $countGroup + 1;
                                                        } ?>
                                                    </div>
                                                    <!--end group -->
                                                    <?php
                                                    $countTime = $countTime + 1;
                                                } ?>

                                    <?php
                                    $countDate = $countDate + 1;
                                } ?>

                            </div>
                        <?php } ?>
                    </div>
                    <?php
                    $countStages = $countStages + 1;
                } ?>
            </div>
        </div>
    </div>
<!--</div>-->
<script>
  let elBtnRegOnTournament = document.querySelectorAll('.btn-reg-on-tournament');
  let elInputRegOnMatchId = document.querySelector('.regOnMatchId');
  console.log(elBtnRegOnTournament);
  elBtnRegOnTournament.forEach(el => {
    el.addEventListener('click', function () {
      elInputRegOnMatchId.value = this.dataset.matchid
      //console.log(this.dataset.matchid)
    })
  });
</script>






