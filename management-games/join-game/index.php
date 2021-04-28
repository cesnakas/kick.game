<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Зарегистрироваться на игру");
CModule::IncludeModule("iblock");
$lang = 'RU';
$messages = [
    'RU' => [
        'UPDATE_SQUAD_SUCCESS' => 'Состав команды на игру успешно обновлен',
        'YOU_ALREADY_ADDED_ON_TOURNAMENT' => 'Ты уже зарегестрирован на этот турнир',
        'GO_TO_MATCH' => 'Перейти к матчу'
    ],
    'EN' => [
        'UPDATE_SQUAD_SUCCESS' => 'Squad members changed',
    ]
];
// получаем текушего пользователя
// дергаем у него поле id команды

$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];

// id матча который пришел
$mId = $_GET['mid'] + 0;

// собираем все ошибки
$errors = [];
function countPointsByUserID($userID)
{
    global $DB;
    $userID += 0;
    if ($userID) {
        $sql = 'SELECT  sum(t.TOTAL) AS total, sum(t.KILLS) AS kills
				FROM b_squad_member_result AS t 
				WHERE t.USER_ID = ' . $userID . ' AND t.TYPE_MATCH = 6
				GROUP BY t.USER_ID';
        $res = $DB->Query($sql);
        if ($row = $res->Fetch()) {
            $points = ['kills' => $row['kills'], 'total' => $row['total']];
            return $points;
        }
    }
    return false;
}

function countPointsAllUsers()
{
    global $DB;
    $sql = 'SELECT t.USER_ID, count(t.USER_ID) as count_matches, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills
			FROM b_squad_member_result AS t 
			WHERE t.TYPE_MATCH = 6
			GROUP BY t.USER_ID';
    $res = $DB->Query($sql);
    $points = [];
    while ($row = $res->Fetch()) {
        $points[$row['USER_ID']] = ['kills' => $row['kills'], 'total' => $row['total'], 'count_matches' => $row['count_matches']];
    }
    return $points;
}

// проверяем, есть ли результаты
function getResultByMatchTeam($matchId, $teamId)
{
    $arSelect = array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = array("IBLOCK_ID" => 5, "PROPERTY_WHICH_MATCH" => $matchId, "PROPERTY_WHICH_TEAM" => $teamId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

// получаем матч по id
function getMatchById($matchId)
{
    $arSelect = array("ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_STREAMER.NAME",
        "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = array("IBLOCK_ID" => 3, "ID" => $matchId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

// получаем турнир по id
function getTournamentById($tournamentId)
{
    $arSelect = array("ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PREVIEW_TEXT",
        "PREVIEW_PICTURE",
        "DETAIL_TEXT",
        "PROPERTY_*"
    );//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = array("IBLOCK_ID" => 7, "ID" => $tournamentId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

// получаем состав команды
function getCoreTeam($teamID)
{
    $filter = array("GROUPS_ID" => array(7), ["UF_ID_TEAM" => $teamID]);
    $arParams["SELECT"] = array("UF_*");
    $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
    $output = [];
    while ($rsUser = $elementsResult->Fetch()) {
        $output[] = $rsUser;
    }
    return $output;
}


//проверка на капитана
function isCaptain($idUser, $idTeam)
{
    if ($idTeam) {
        $resTeam = getTeamById($idTeam);
        if ($resTeam['AUTHOR']["VALUE"] == $idUser) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

// получаем цепочку матчей по id родителя
function getMatchByParentId($parentId)
{
    $arSelect = array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = array("IBLOCK_ID" => 3, "PROPERTY_PREV_MATCH" => $parentId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        return array_merge($arFields, $arProps);
    }
    return null;
}

// созданеие записи с участниками
function createSquad($props = [], $code)
{
    $el = new CIBlockElement;
    $iblock_id = 6;
    $params = array(
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
        "CODE" => CUtil::translit($code, "ru", $params),
        "IBLOCK_ID" => $iblock_id, //ID информационного блока он 24-ый
        "PROPERTY_VALUES" => $props, // Передаем массив значении для свойств
        "NAME" => $code,
        "ACTIVE" => "Y", //поумолчанию делаем активным или ставим N для отключении поумолчанию
    );
    //Результат в конце отработки
    if ($ID = $el->Add($fields)) {
        return $ID;
    } else {
        return "Error: " . $el->LAST_ERROR;
    }
}

function updateSquad($props = [], $idMatch)
{
    $squadRes = getSquadByIdMatch($idMatch, $props['TEAM_ID']);

    $squadId = $squadRes['ID'] + 0;
    //dump($squadId);
    //dump($props);
    CIBlockElement::SetPropertyValues($squadId, 6, $props, false);
    //header('Location: /management-games/join-game/?mid='.$idMatch);
}

// получаем участников матчей по полю which_match передавая id
function getMembersByMatchId($matchId)
{
    $arSelect = array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = array(
        "IBLOCK_ID" => 4,
        "PROPERTY_WHICH_MATCH" => $matchId,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $output = [];

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        //dump($arProps);
        foreach ($arProps as $k => $v) {
            $arFields[$k] = $v['VALUE'];
        }
        $output[] = $arFields;
    }
    return $output;
}


function getPlacesKeys()
{
    return [
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
}

// проверка команды на участие
function getParticipationByMatchId($idMatch)
{
    $arSelect = array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_*",
    );
    $arFilter = array(
        "IBLOCK_ID" => 4,
        "PROPERTY_WHICH_MATCH" => $idMatch,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $arrTeams = [];
    $key = getPlacesKeys();
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        foreach ($key as $place => $name) {
            //dump($arProps[$name]['VALUE']);
            if ($arProps[$name]['VALUE'] + 0 > 0) {
                $arrTeams[$place] = $arProps[$name]['VALUE'];
            }
        }
        return $arrTeams;
    }
    return false;
}

function getPropertyPlace($idMatch)
{
    $arSelect = array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_*",
    );
    $arFilter = array(
        "IBLOCK_ID" => 4,
        "PROPERTY_WHICH_MATCH" => $idMatch,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $arrTeams = [];
    $key = getPlacesKeys();
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        foreach ($key as $place => $name) {
            if ($arProps[$name]['VALUE'] + 0 > 0) {
                $arrTeams[$name] = $arProps[$name]['VALUE'];
            }
        }
        return $arrTeams;
    }
    return false;
}

// получаем участников команды
function getSquadByIdMatch($idMatch, $idTeam)
{
    $arSelect = array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_*",
    );
    $arFilter = array(
        "IBLOCK_ID" => 6,
        "PROPERTY_MATCH_STAGE_ONE" => $idMatch,
        "PROPERTY_TEAM_ID" => $idTeam,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $squad = [];
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $squad = array_merge($arFields, $arProps);
        return $squad;
    }
    return false;
}

// получаем участников команды
function getPlayersSquadByIdMatch($idMatch, $teamId)
{
    //dump($teamId);
    $coreTeam = getCoreTeam($teamId);
    foreach ($coreTeam as $key => $val) {
        $coreTeam[$key] = $val['ID'];
    }
    //dump($coreTeam);
    $arSelect = array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_*",
    );
    $arFilter = array(
        "IBLOCK_ID" => 6,
        "PROPERTY_MATCH_STAGE_ONE" => $idMatch,
        "PROPERTY_TEAM_ID" => $teamId,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );

    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $arrPlayers = [];
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $arrPlayers[] = $arProps["PLAYER_1"]["VALUE"] + 0;
        $arrPlayers[] = $arProps["PLAYER_2"]["VALUE"] + 0;
        $arrPlayers[] = $arProps["PLAYER_3"]["VALUE"] + 0;
        $arrPlayers[] = $arProps["PLAYER_4"]["VALUE"] + 0;
        $arrPlayers[] = $arProps["PLAYER_5"]["VALUE"] + 0;
        $arrPlayers[] = $arProps["PLAYER_6"]["VALUE"] + 0;
        $arrPlayers = array_flip($arrPlayers);
        unset($arrPlayers[0]);
        $arrPlayers = array_flip($arrPlayers);
        //dump($arrPlayers);
        return $arrPlayers;
    }
    return false;
}

//
function isTournament($idMatch)
{
    $match = getMatchById($idMatch);

    if ($match) {
        $idTournament = $match['TOURNAMENT']['VALUE'] + 0;
        if ($idTournament > 0) {
            return $idTournament;
        }
        return false;
    }
    return null;
}


//
function checkRegistrationTeamOnTournament($idTeam, $idTournament)
{
    $matchesId = [];
    $idRegistrationMatch = false;
    $arSelect = array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = array("IBLOCK_ID" => 4,
        //"?NAME" => '#'.$idTournament.'_TOURNAMENT',
        //"?NAME" => '_GROUP4_STAGE1',
        array(
            "LOGIC" => "AND",
            array(
                "?NAME" => '#' . $idTournament . '_TOURNAMENT'
            ),
            array(
                "?NAME" => '_STAGE1'
            )
        ),
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        //$arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $matchesId[] = $arProps["WHICH_MATCH"]['VALUE'] + 0;
    }
    //dump($matchesId);
    if (!empty($matchesId)) {
        foreach ($matchesId as $id) {
            if ($tmp = getParticipationByMatchId($id)) {
                // dump($tmp);
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
function isPlace($idMatch)
{
    $qtyPlaces = 18;
    $qtyOccupiedPlaces = getParticipationByMatchId($idMatch);
    if ($qtyPlaces == count($qtyOccupiedPlaces)) {
        return false;
    }
    return true;
}

function updateMembers($props = [], $id)
{
    CIBlockElement::SetPropertyValuesEx($id, 4, $props);
}

$redirectUrlAction = false;
$actionErrorText = false;
$actionSuccessText = false;
// текущий матч
$curMatch = getMatchById($mId);

// собираем цепочку матчей
$chainMatches = [];
$chainMatches[] = $curMatch['ID'];
$idCurMatch = $mId;


$isPlace = isPlace($curMatch['ID']);


do {
    $match = getMatchByParentId($idCurMatch);
    if ($match != null) {
        $nextMatch = true;
        $chainMatches[] = $match['ID'];
        $idCurMatch = $match['ID'];
    } else {
        $nextMatch = false;
    }
} while ($nextMatch == true);


// проверка на участие в игре
$placeName = '';
$star = '';
$squadMembers = [];

if ($tmp = getParticipationByMatchId($mId)) {
    $tmp = array_flip($tmp);
    if (isset($tmp[$teamID])) {

        $star = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="orangered" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
              </svg>';
        $placeName = '<span class="badge badge-danger">Ваше место в матче № ' . $tmp[$teamID] . '</span>';

        if ($squadMembers = getPlayersSquadByIdMatch($mId, $teamID)) {
            $squadMembers = array_flip($squadMembers);
        }
    }

}
//dump($squadMembers);


// формирование команды
$squad = [];
$squadId = null;
$playerKeys = [
    'PLAYER_1',
    'PLAYER_2',
    'PLAYER_3',
    'PLAYER_4',
    'PLAYER_5',
    'PLAYER_6',
];
$minLimPlayers = 4;
$maxLimPlayers = 6;
$alertManagementSquad = '';
if (check_bitrix_sessid() && isset($_REQUEST['btn_create_squad'])) {
    if (!empty($_POST['squad'])) {
        $squad = $_POST['squad'];
        $props = [];
        $tmp = [];
        foreach ($playerKeys as $key => $name) {
            $props[$name] = $squad[$key] + 0;
            if ($props[$name] > 0) {
                $tmp[] = $key;
            }
        }
        $props['MATCH_STAGE_ONE'] = $mId;
        $props['TEAM_ID'] = $teamID;
        $code = 'SQUAD_' . $curMatch['NAME'];

        if (count($tmp) >= $minLimPlayers && count($tmp) <= $maxLimPlayers) {
            if (isset($_POST['update_squad']) || $placeName != '') {
                updateSquad($props, $mId);
                createSession('management-games_success', $messages[$lang]['UPDATE_SQUAD_SUCCESS']);
                $redirectUrlAction = SITE_DIR . "management-games/join-game/?mid=" . $mId;
            } else {
                if ($idTournament = isTournament($mId)) {

                    $registeredMatchId = checkRegistrationTeamOnTournament($teamID, $idTournament);
                    //dump($registeredMatchId, 1);
                    if ($registeredMatchId > 0) {

                        $alertManagementSquad = $messages[$lang]['YOU_ALREADY_ADDED_ON_TOURNAMENT'] . '<br>
                                <a href="/management-games/join-game/?mid=' . $registeredMatchId . '">' . $messages[$lang]['GO_TO_MATCH'] . '</a>';
                        createSession('management-games_error', $alertManagementSquad);
                        //LocalRedirect("/management-games/join-game/?mid=".$mId);
                    } else {
                        if ($isPlace) {
                            $squadId = createSquad($props, $code);
                            $alertManagementSquad = 'Ты успешно сформировал команду на игру';
                            createSession('management-games_success', $alertManagementSquad);
                            //LocalRedirect("/management-games/join-game/?mid=".$mId);
                        } else {
                            $alertManagementSquad = 'Ты не успел, мест нет';
                            createSession('management-games_error', $alertManagementSquad);
                            //LocalRedirect("/management-games/join-game/?mid=".$mId);
                        }
                    }
                } else {
                    if ($isPlace) {
                        $squadId = createSquad($props, $code);
                        //dump($squadId);

                        $alertManagementSquad = 'Ты успешно сформировал команду';
                        //dump($alertManagementSquad);
                        createSession('management-games_success', $alertManagementSquad);
                        // not working
                        // header('Location: /management-games/join-game/?mid='.$mId);
                        //LocalRedirect("/management-games/join-game/?mid=".$mId);
                    } else {
                        $alertManagementSquad = 'Ты не успел, мест нет';
                        createSession('management-games_error', $alertManagementSquad);
                        //LocalRedirect("/management-games/join-game/?mid=".$mId);
                    }
                }
                $redirectUrlAction = "/management-games/join-game/?mid=" . $mId;
                //LocalRedirect("/management-games/join-game/?mid=".$mId);
            }
        } else {
            $alertManagementSquad = 'Состав команды выбран не полностью, должно быть от ' . $minLimPlayers . ' до ' . $maxLimPlayers . ' участников';
            createSession('management-games_error', $alertManagementSquad);
            //LocalRedirect("/management-games/join-game/?mid=".$mId);
            $redirectUrlAction = "/management-games/join-game/?mid=" . $mId;
        }

    }
}


if (check_bitrix_sessid() && !empty($_REQUEST['removeTeam'])) {

    $results = getResultByMatchTeam($mId, $teamID);

    $now = date('d.m.Y H:i:s');
    $dateStartMatch = $curMatch["DATE_START"]['VALUE'];

    $dateA = DateTime::createFromFormat('d.m.Y H:i:s', $now);
    $dateB = DateTime::createFromFormat('d.m.Y H:i:s', $dateStartMatch);

    if ($results) {
        $alertManagementSquad = 'По матчу уже сформированы результаты';
        createSession('management-games_error', $alertManagementSquad);
        $redirectUrlAction = '/management-games/join-game/?mid=' . $mId;
    } else if ($dateA > $dateB) {
        $alertManagementSquad = 'Матч уже прошел';
        createSession('management-games_error', $alertManagementSquad);
        $redirectUrlAction = '/management-games/join-game/?mid=' . $mId;
    } else {
        // получаем squad
        $curSquadId = getSquadByIdMatch($mId, $teamID);
        // получили место
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
                // удаляем squd
                if (!empty($curSquadId)) {
                    CIBlockElement::Delete($curSquadId['ID']);
                    $alertManagementSquad = 'Ты успешно снял команду с игры';
                    createSession('management-games_success', $alertManagementSquad);
                    $redirectUrlAction = '/management-games/join-game/?mid=' . $mId;
                }
            }


        }
    }


}

// todo not working
if ($squadId) {
//dump($squadId);
// получаем участников матчей
    $resMembersMatches = getMembersByMatchId($chainMatches);
    $membersMatches = [];

    if ($resMembersMatches) {
        foreach ($resMembersMatches as $membersMatch) {

            $membersMatches[] = $membersMatch['ID'];
        }
    }

//dump($membersMatches);
    $match = getMembersByMatchId($curMatch['ID']);
    $match = $match[0];
//dump($match);

    $propertiesCases = getPlacesKeys();

    $emptyPlace = false;
    foreach ($propertiesCases as $case) {
        if ($match[$case] + 0 == 0) {
            $emptyPlace = $case;
            break;
        }
    }


//dump($emptyPlace);
// сделать проверку, что моей команды еще нет в участниках матча, если моя команду существует в этом матче, то $emptyplace = falce

    if ($emptyPlace != false) {
        foreach ($membersMatches as $membersMatchId) {
            CIBlockElement::SetPropertyValues($membersMatchId, 4, $teamID, $emptyPlace);
        }
    } else {
        CIBlockElement::Delete($squadId);
        $alertManagementSquad = 'Разместить команду не удалось';
        createSession('management-games_success', $alertManagementSquad);
        $redirectUrlAction = '/management-games/join-game/?mid=' . $mId;
    }

}


// капитан или нет
$isCaptain = isCaptain($userID, $teamID);
if (!$isCaptain) {
    LocalRedirect("/personal/");
}
$resTeam = getTeamById($teamID);
// получаем список команды
$coreTeam = getCoreTeam($teamID);
// ставим капитана на первое место
foreach ($coreTeam as $k => $player) {
    if ($resTeam['AUTHOR']["VALUE"] == $player['ID']) {
        $coreTeam = [$k => $player] + $coreTeam;
        break;
    }
}
?>
<?php /*
    <div class="container my-5">
        <?php if (!empty($errors)) { ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Error!</h4>
            <?php foreach ($errors as $error) { ?>
            <p><strong><?php echo $error; ?></strong></p>
            <?php } ?>
            <hr>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php } ?>
        <?php if (isset($squadId) && $squadId > 0) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">Well done!</h4>
                <p>Команда с ID <strong><?php echo $squadId; ?></strong> успешно сформирована.</p>
                <hr>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>
        <div class="my-5"><a href="/management-games/">Вернуться к списку матчей</a></div>

        <h2>#<span class="badge bg-secondary text-white"><?php echo $curMatch["ID"];?></span></h2>
        <?=$placeName;?>
        <div class="mb-2">Дата и время старта: <span class="badge bg-primary text-white"><?php echo $curMatch["DATE_START"]['VALUE'];?></span></div>
        <h2><?=$star;?><?php echo $curMatch['NAME']?></h2>
        <div class="my-2">Тип матча: <span class="badge bg-primary text-white"><?php echo $curMatch['TYPE_MATCH']['VALUE']?></span></div>


<?php if ($isCaptain) {?>
    <?php if (!empty($coreTeam)) {?>
        <h2 class="mt-5 mb-3">Состав команды</h2>
        <form action="#" method="post">
            <table class="table table-striped table-dark ">
                <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">#</th>
                    <th scope="col">Nick</th>
                    <th scope="col">Сыграно игр</th>
                    <th scope="col">Фраги</th>
                    <th scope="col">Рейтинг</th>
                    <th scope="col">Премиум</th>
                </tr>
                </thead>
                <tbody>
                <?php

                foreach ($coreTeam as $player)  {

                    ?>
                    <tr>
                        <th scope="row">
                                <input type="checkbox"  name="squad[]" value="<?php echo $player['ID']?>"
                                <?php if(isset($squadMembers[$player['ID']])) echo 'checked';?>
                                >
                        </th>
                        <td><?php echo $player['ID']?></td>
                        <td><?php echo $player['LOGIN']?></td>
                        <td><?php echo !$player["UF_PLAYED_GAMES"] ? '0' : $player["UF_PLAYED_GAMES"];?></td>
                        <td><?php echo !$player["UF_FRAGS"] ? '0' : $player["UF_FRAGS"]; ?></td>
                        <td><?php echo !$player["UF_RATING"] ? '0' : $player["UF_RATING"];?></td>
                        <td><?php echo $player['UF_DATE_PREM_EXP']?></td>
                    </tr>

                <?php } ?>

                </tbody>
            </table>
          <?php
            $btnTitle = 'Сформировать команду';
            if (count($squadMembers) > 0) {
              $btnTitle = 'Обновить команду';
              echo '<input type="hidden" name="update_squad" value="1">';
            }
          ?>
<div class="d-flex">
      <?php if (!empty($squadMembers)) { ?>

        <button type="submit" class="btn btn-danger" name="removeTeam" value="1">Снять команду с матча</button>
      <?php } ?>
      <button type="submit" class="btn btn-success d-block ml-auto" name="btn_create_squad"><?=$btnTitle;?></button>
</div>
        </form>
    <?php } ?>
<?php } ?>

    </div>*/
$tournamentId = isTournament($curMatch['ID']);
//dump($curMatch);
if ($redirectUrlAction != false) {
    LocalRedirect($redirectUrlAction);
}
?>
<?php
if (isset($_SESSION['management-games_success'])) { ?>
    <div class="alert-container">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['management-games_success']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php
    unset($_SESSION['management-games_success']);
} else if (isset($_SESSION['management-games_error'])) { ?>
    <div class="alert-container">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['management-games_error']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php }
unset($_SESSION['management-games_error']);
?>
    <div class="container">
        <a href="javascript:history.back()" class="btn-italic-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 11.62">
                <path d="M955.22,534.89a1,1,0,0,1,0,1.33l-3,3.27h18.2a.94.94,0,0,1,0,1.88h-18.2l3,3.27a1,1,0,0,1,0,1.32.81.81,0,0,1-1.21,0l-4.49-4.88h0a1,1,0,0,1,0-1.33h0l4.49-4.87A.81.81,0,0,1,955.22,534.89Z"
                      transform="translate(-949.26 -534.62)"/>
            </svg>
            <?=GetMessage('JG_BACK')?>
        </a>
        <section class="game">
            <div class="row align-items-center justify-content-lg-center">
                <div class="col-lg-6">
                    <div class="game__block">
                        <div class="game__block-img"
                            <?php if ($tournamentId) {
                                $tournament = getTournamentById($tournamentId);
                                ?>
                                style="background-image: url(<?php echo CFile::GetPath($tournament["PREVIEW_PICTURE"]); ?>"
                            <?php } else { ?>
                                style="background-image: url(<?php echo SITE_TEMPLATE_PATH; ?>/dist/images/profile-avatar.jpg)"
                            <?php } ?>>
                            <div class="game__block-img-rating-bg">
                                <div class="game__block-img-rating">3.00</div>
                            </div>
                        </div>
                        <?php
                        if ($tmp = getParticipationByMatchId($curMatch["ID"])) {
                            $tmp = array_flip($tmp);
                            if (isset($tmp[$teamID])) { ?>
                                <div class="game__participation-label">Слот № <?php echo $tmp[$teamID]; ?></div>
                            <?php }

                        }
                        ?>
                        <h1>
                            <?php
                            $name = 'KICKGAME Scrims';
                            if ($tournamentId) {

                                $name = $tournament['NAME'] . ' (' . $curMatch["STAGE_TOURNAMENT"]['VALUE'] . ')';
                            }
                            echo $name;

                            ?>
                        </h1>
                        <?php if ($curMatch['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
                            <div class="game__block-type"><i></i> <?=GetMessage('JG_PRACTICAL_GAME')?></div>
                        <?php } elseif ($curMatch['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
                            <div class="game__block-type game__block-type_tournament"><i></i> <?=GetMessage('JG_TOURNAMENT_GAME')?></div>
                        <?php } ?>
                        <div class="game__block-call">
                            <a href="#" class="btn-italic"><?=GetMessage('JG_CONTACT_MODERATOR')?></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-6 col-xl-4">
                            <div class="info-item">
                                <div><?=GetMessage('JG_DATE_EVENT')?></div>
                                <div>
                                    <?php
                                    $dateTime = explode(' ', $curMatch["DATE_START"]['VALUE']);
                                    echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-4">
                            <div class="info-item">
                                <div><?=GetMessage('JG_TOTAL_MATCHES')?></div>
                                <div><?=GetMessage('JG_TIME')?></div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-4">
                            <div class="info-item">
                                <div><div><?=GetMessage('JG_COMMENTATOR')?></div></div>
                                <div>
                                    <?php if (!empty($curMatch["PROPERTY_STREAMER_NAME"])) {
                                        echo $curMatch["PROPERTY_STREAMER_NAME"];
                                    } else {
                                        echo '-';
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-4">
                            <div class="info-item">
                                <div><?=GetMessage('JG_LINK')?></div>
                                <div>
                                    <?php if (!empty($curMatch["URL_STREAM"]['VALUE'])) { ?>
                                        <a href="<?php echo $curMatch["URL_STREAM"]['VALUE']; ?>" target="_blank"
                                           class="btn-blue"><?=GetMessage('JG_ONAIR')?></a>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-4">
                            <div class="info-item">
                                <div><?=GetMessage('JG_GAME_MODE')?></div>
                                <div>
                      <span class="info-item__mode-block">
                        <span class="info-item__mode-description"><?=GetMessage('JG_SQUAD')?></span>
                        <div class="info-item__mode">
                          <i></i>
                          <div>x<?php echo $curMatch["COUTN_TEAMS"]['VALUE']; ?></div>
                        </div>
                      </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-4">
                            <div class="info-item">
                                <div><?=GetMessage('JG_SEATS')?></div>
                                <div>
                                    <?php if (!isPlace($curMatch['ID'])) { ?>
                                        <?=GetMessage('JG_NO_SEATS')?>
                                    <?php } else {
                                        $qtyOccupiedPlaces = getParticipationByMatchId($curMatch['ID']);
                                        echo 'Занято ' . count($qtyOccupiedPlaces) . ' из 18 мест';
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php if (!empty($coreTeam)) {
    $points = countPointsAllUsers();
    ?>
    <section class="pb-8">
        <div class="container">
            <h2 class="core-team__heading"><?=GetMessage('JG_CORE_TEAM')?></h2>
            <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                <?= bitrix_sessid_post() ?>
                <div class="core-team">
                    <div class="flex-table">
                        <div class="flex-table--header bg-default">
                            <div class="flex-table--categories">
                  <span>
                    <div style="display: flex; align-items: center">
                    <label class="label-checkbox">
                      <input type="checkbox" id="c-all">
                      <div class="label-checkbox__checkmark"></div>
                    </label>
                      <div><?=GetMessage('JG_TABLE_PLAYER')?></div>
                  </div>

                  </span>
                                <span><?=GetMessage('JG_TABLE_NUMBER_GAME')?></span>
                                <span><?=GetMessage('JG_TABLE_KILLS')?></span>
                                <span><?=GetMessage('JG_TABLE_TOTAL')?></span>
                                <span><?=GetMessage('JG_TABLE_RATING')?></span>
                                <span><?=GetMessage('JG_TABLE_SUBSCRIPTION')?></span>
                            </div>
                        </div>
                        <div class="flex-table--body">
                            <?php foreach ($coreTeam as $player) {
                                $cntMatches = '..';
                                $kills = '..';
                                $total = '..';
                                if (isset($points[$player['ID']])) {
                                    $cntMatches = ceil($points[$player['ID']]['count_matches']);
                                    $kills = ceil($points[$player['ID']]['kills']);
                                    $total = ceil($points[$player['ID']]['total']);
                                }
                                ?>
                                <div class="flex-table--row">
                <span>
                  <div class="core-team__user">
                    <label class="label-checkbox">
                      <input type="checkbox" name="squad[]" value="<?php echo $player['ID'] ?>"
                      <?php if (isset($squadMembers[$player['ID']])) echo 'checked'; ?>>
                      <div class="label-checkbox__checkmark"></div>
                    </label>
                    <div class="core-team__user-avatar"
                         <?php if (!empty($player["PERSONAL_PHOTO"])) { ?>
                             style="background-image: url(<?php echo CFile::GetPath($player["PERSONAL_PHOTO"]); ?>)"
                         <?php } else { ?>
                             style="background-image: url(<?php echo SITE_TEMPLATE_PATH; ?>/dist/images/default-avatar.svg)"
                         <?php } ?>>
                      <?php if ($resTeam['AUTHOR']["VALUE"] == $player['ID']) { ?>
                          <div class="core-team__user-avatar-icon_captain">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                <circle cx="11" cy="11" r="10"/>
                                <path d="M682.39,379.09a.65.65,0,0,1,1.22,0l.82,2.5a.65.65,0,0,0,.61.43h2.66a.62.62,0,0,1,.38,1.13l-2.16,1.54a.63.63,0,0,0-.23.71l.82,2.49a.63.63,0,0,1-1,.7l-2.16-1.54a.63.63,0,0,0-.74,0l-2.16,1.54a.63.63,0,0,1-1-.7l.82-2.49a.63.63,0,0,0-.23-.71l-2.16-1.54a.62.62,0,0,1,.38-1.13H681a.65.65,0,0,0,.61-.43Z"
                                      transform="translate(-672 -373)"/>
                              </svg>
                            </div>
                      <?php } ?>
                    </div>
                    <a href="<?=SITE_DIR?>/players/<?php echo $player['ID'] . '_' . $player['LOGIN'] . '/'; ?>"
                       class="core-team__user-link"><?php echo $player['LOGIN'] ?></a>
                  </div>
                </span>
                                    <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('JG_TABLE_NUMBER_GAME')?></div>
                  <?php echo $cntMatches; ?>
                </span>
                                    <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('JG_TABLE_KILLS')?></div>
                  <?php echo $kills; ?>
                </span>
                                    <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('JG_TABLE_TOTAL')?></div>
                  <?php echo $total; ?>
                </span>
                                    <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('JG_TABLE_RATING')?></div>
                  <?php if (!$player['UF_RATING']) { ?>
                      300
                  <?php } else { ?>
                      <?php echo $player['UF_RATING']; ?>
                  <?php } ?>
                </span>
                                    <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('JG_TABLE_SUBSCRIPTION')?></div>
                    <?php $resultPrem = isPrem($player['UF_DATE_PREM_EXP']); ?>
                                        <? if ($resultPrem <= 0) { ?>
                                            <?=GetMessage('JG_TABLE_NO_SUBSCRIPTION')?>
                                        <? } else { ?>

                                            <?= num_decline($resultPrem, GetMessage('JG_TABLE_SUBSCRIPTION_REMAINED'), false); ?> <?= num_decline($resultPrem, GetMessage('JG_TABLE_SUBSCRIPTION_DAYS')); ?>

                                        <? } ?>
                </span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="core-team__btn-flex">
                    <?php if (!empty($squadMembers)) { ?>
                        <button type="submit" name="removeTeam" value="1"
                                class="btn-icon btn-icon_red btn-icon_close-red mr-1"><i></i> <?=GetMessage('JG_BTN_REMOVE')?>
                        </button>
                    <?php } ?>

                    <?php
                    $btnTitle = GetMessage('JG_BTN_CREATE');
                    if (count($squadMembers) > 0) {
                        $btnTitle = GetMessage('JG_BTN_UPDATE');
                        echo '<input type="hidden" name="update_squad" value="1">';
                    }
                    ?>
                    <button type="submit" class="btn-icon btn-icon_check" name="btn_create_squad">
                        <i></i> <?php echo $btnTitle; ?></button>
                </div>
            </form>
        </div>
    </section>
<?php } ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>