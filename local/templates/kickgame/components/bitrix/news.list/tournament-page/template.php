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

$nextGameID = getNextGame($teamID, $_GET["tournamentID"]);
$nextGame = getMatchById($nextGameID);

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
    $key = [
        3 => "TEAM_PLACE_03",
        4 => "TEAM_PLACE_04",
        5 => "TEAM_PLACE_05",
        6 => "TEAM_PLACE_06",
        7 => "TEAM_PLACE_07",
        8 => "TEAM_PLACE_08",
        9 => "TEAM_PLACE_09",
        10 => "TEAM_PLACE_10",
        11 => "TEAM_PLACE_11",
        12 => "TEAM_PLACE_12",
        13 => "TEAM_PLACE_13",
        14 => "TEAM_PLACE_14",
        15 => "TEAM_PLACE_15",
        16 => "TEAM_PLACE_16",
        17 => "TEAM_PLACE_17",
        18 => "TEAM_PLACE_18",
        19 => "TEAM_PLACE_19",
        20 => "TEAM_PLACE_20",
    ];
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        foreach ($key as $place=>$name) {
            if ($arProps[$name]['VALUE']+0 > 0) {
                $arrTeams[$place] = $arProps[$name]['VALUE'];
            }
        }
        return $arrTeams;
    }
    return  false;
}

function checkRegistrationTeamOnTournament($idTeam, $idTournament)
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
                "?NAME" => '_GROUP4_STAGE1'
            ),
        ),
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        //$arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $matchesId[] = $arProps["WHICH_MATCH"]['VALUE']+0;
    }
    if (!empty($matchesId)) {
        foreach ($matchesId as $id) {
            if($tmp = getParticipationByMatchId($id)) {
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
function isPlace($idMatch): bool
{
    $qtyPlaces = 18;
    $qtyOccupiedPlaces = getParticipationByMatchId($idMatch);
    if ($qtyPlaces == count($qtyOccupiedPlaces)) {
        return false;
    }
    return true;
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
                    <div class="tournament-schedule-results__overlay">
                        <?= $dates[$countStages]["min"] . " - " . $dates[$countStages]["max"] ?>
                        <br>
                        <?= $dates[$countStages]["games"]*18 . GetMessage('TOUR_SQUADS') . $dates[$countStages]["games"] . " " . num_decline($dates[$countStages]["games"], GetMessage('TOUR_GAME_GAMES'), false); ?>
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
                                        echo $scheduleDay ?> <span class="tournament-schedule-results__participants"><i></i> <span class="tournament-schedule-results__participants-cur-count"><?php echo  $totalOccupied[$l] ?></span> / <?php echo  $counter[$l]*18?></span>
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
                                        $scheduleTime = formDate($m, "HH:mm");
                                        echo $scheduleTime ?></div>
                                    <div class="accordion accordion-group" id="game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                        <?php $countGroup = 1;
                                        foreach ($time as $n => $group){ ?>
                                        <div class="card">
                                            <div class="card-header" id="headingGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                <h2 class="mb-0">
                                                    <button class="accordion-group__heading collapsed" value="<?php echo $group["ID"]?>" onclick="getPlayers(this.value)" type="button" data-toggle="collapse" data-target="#collapseGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" aria-expanded="true" aria-controls="collapseGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                        <?=GetMessage('TOUR_GROUP_NO')?><?php echo $countGroup ?>
                                                        <span class="tournament-schedule-results__participants-group"><i></i> <span class="tournament-schedule-results__participants-group-cur-count"><?php echo $matchOccupied[$group["ID"]] ?></span> / 18</span>
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
                                                if (isCaptain($userID, $teamID) && $matchOccupied[$group["ID"]] < 18 && strtotime($group["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]) > time()) { ?>
                                                    <div class="tournament-schedule-results__btn-change-group">
                                                        <?php if (!$nextGameID && $countStages == 1){ ?>

                                                            <a href="<?=SITE_DIR?>tournament-page/join-game/?mid=<?php echo $group["ID"]?>" class="btn"><?=GetMessage('TOUR_APPLY')?></a>

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
                        <?php echo $dates[$countStages]["games"]*18 . GetMessage('TOUR_SQUADS') . $dates[$countStages]["games"] . " " . num_decline($dates[$countStages]["games"], GetMessage('TOUR_GAME_GAMES'), false); ?> </div>
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
                                        echo $scheduleDay ?> <span class="tournament-schedule-results__participants"><i></i> <span class="tournament-schedule-results__participants-cur-count"><?php echo  $totalOccupied[$l]?></span> / <?php echo $counter[$l]*18 ?></span>
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
                                            $scheduleTime = formDate($m, "HH:mm");
                                            echo $scheduleTime ?></div>
                                        <div class="accordion accordion-group" id="results_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                            <?php $countGroup = 1;
                                            foreach ($time as $n => $group){ ?>
                                                <div class="card">
                                                    <div class="card-header" id="headingResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                        <h2 class="mb-0">
                                                            <button class="accordion-group__heading" value="<?php echo $group["ID"]?>" onclick="getResults(this.value)" type="button" data-toggle="collapse" data-target="#collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" aria-expanded="true" aria-controls="collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                                <?=GetMessage('TOUR_GROUP_NO')?><?php echo $countGroup ?>
                                                                <span class="tournament-schedule-results__participants-group"><i></i> <span class="tournament-schedule-results__participants-group-cur-count"><?php echo $matchOccupied[$group["ID"]] ?></span> / 18</span>
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
                                                                    <div><a href="#" class="btn-change-big"><?=GetMessage('TOUR_DETAILED_RESULTS')?></a></div>
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

                        <?php } else { ?>
                            <div class="tournament-schedule-results__overlay"><?php echo $dates[$countStages]["min"] . " - " . $dates[$countStages]["max"] ?><br>
                                <?php echo "18 команд, " . $dates[$countStages]["games"] . " " . num_decline($dates[$countStages]["games"], "игра, игры, игр", false); ?> </div>
                            <div class="accordion accordion-game" id="gameResultsStage_<?php echo $countStages ?>">
                                <?php $countDate = 1;
                                foreach ($stage as $l => $date){?>

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
                                                                <div class="card-header" id="headingResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                                    <h2 class="mb-0">
                                                                        <button class="accordion-group__heading" value="<?php echo $group["ID"]?>" onclick="getResults(this.value)" type="button" data-toggle="collapse" data-target="#collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" aria-expanded="true" aria-controls="collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                                            День №<?php echo $countDate ?>
                                                                            <span class="tournament-schedule-results__participants-group"><i></i> <span class="tournament-schedule-results__participants-group-cur-count"><?php echo $matchOccupied[$group["ID"]] ?></span> / 18</span>
                                                                            <span class="accordion-group__progress" style="width: <?php echo $matchOccupied[$group["ID"]] * 5.555 ?>%;"></span>
                                                                        </button>
                                                                    </h2>
                                                                </div>

                                                                <div id="collapseResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" class="collapse" aria-labelledby="headingResultsGroup_<?php echo $countGroup ?>_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>" data-parent="#results_game_<?php echo $countDate?>_time_<?php echo $countTime ?>_<?php echo $countStages ?>">
                                                                    <div class="card-body card-body__group">
                                                                        <div class="flex-table-tournament">
                                                                            <div class="flex-table-tournament--header">
                                                                                <div class="flex-table-tournament--categories">
                                                                                    <span>Команда</span>
                                                                                    <span>Очки</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex-table-tournament--body" id="results<?php echo $group["ID"]; ?>">
                                                                                <?php if (strtotime($group["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]) > time()){ ?>
                                                                                    Результаты по этой игре еще не сформированы
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <?php if(strtotime( $group["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]) < time()){ ?>
                                                                            <div class="tournament-schedule-results__btn-change-group">

                                                                                <div><a href="<?=SITE_DIR?>game-schedule/<?php echo $group["CODE"] ?>/" class="btn-change-big">Подробные Результаты</a></div>
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
</div>





