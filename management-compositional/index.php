<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Управление составом");

$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];
$redirectUrlAction = false;
$alertManagementTeam = '';


function getRating($arResult)
{
    global $DB;
    $sql = 'SELECT t.PROPERTY_15 AS teamID, sum(t.PROPERTY_18) AS total, sum(t.PROPERTY_17) AS kills
      FROM b_iblock_element_prop_s5 AS t 
      INNER JOIN b_iblock_element_prop_s3 AS m ON t.PROPERTY_14 = m.IBLOCK_ELEMENT_ID
      WHERE m.PROPERTY_23 = 6 AND t.PROPERTY_15 = '.$arResult['ID'].' 
      GROUP BY t.PROPERTY_15';
    $res = $DB->Query($sql);
    if( $row = $res->Fetch()  ) {
        //$row['total'] = empty($arResult['PROPERTIES']['RATING']) ? $row['total']+0 + 300 :$row['total']+0 + $arResult['PROPERTIES']['RATING']+0;
        //if()
        $row['total'] = empty($arResult['RATING']["VALUE"]) ? $row['total'] + 300 : $row['total'] + ceil($arResult['RATING']["VALUE"]);
        return $row['total'];
    }
    return false;
}

function getRecruitsRating($recruitsIds){

    GLOBAL $DB;
    $sql = 'SELECT g.GROUP_ID, u.LOGIN, u.PERSONAL_PHOTO, u.ID, IF(total IS NOT Null,total, 0) + IF(r.UF_RATING IS NOT Null, r.UF_RATING, 300) as total, kills 
      FROM  b_user as u 
            LEFT JOIN (SELECT t.USER_ID, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills FROM b_squad_member_result AS t WHERE t.TYPE_MATCH = 6 GROUP BY t.USER_ID) AS r1 ON r1.USER_ID = u.ID 
            LEFT JOIN b_uts_user AS r ON r.VALUE_ID = u.ID
            INNER JOIN b_user_group AS g ON g.USER_ID = u.ID 
            AND g.GROUP_ID = 7
            WHERE u.ID IN('.implode(",",$recruitsIds).')
            ORDER BY total DESC, kills DESC';

    $res = $DB->Query($sql);
    $players = [];
    while( $row = $res->Fetch() ) {
        $players[ $row['ID'] ] = [ 'kills' => $row['kills'],
            'total' => $row['total'],
            'ID' => $row['ID'],
            'login' => $row['LOGIN'],
            'count_matches' => '',
            'photo' => $row['PERSONAL_PHOTO']
        ];
    }
    return $players;
}

function getQtyPlayedGames($teamId)
{
    GLOBAL $DB;
    $sql = 'SELECT g.GROUP_ID, u.LOGIN, u.PERSONAL_PHOTO, u.ID, IF(total IS NOT Null,total, 0) + IF(r.UF_RATING IS NOT Null, r.UF_RATING, 300) as total, kills 
      FROM  b_user as u 
            LEFT JOIN (SELECT t.USER_ID, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills FROM b_squad_member_result AS t WHERE t.TYPE_MATCH = 6 GROUP BY t.USER_ID) AS r1 ON r1.USER_ID = u.ID 
            LEFT JOIN b_uts_user AS r ON r.VALUE_ID = u.ID
            INNER JOIN b_user_group AS g ON g.USER_ID = u.ID 
            AND g.GROUP_ID = 7
            WHERE r.UF_ID_TEAM = '.$teamId;

    $res = $DB->Query($sql);
    $players = [];
    while( $row = $res->Fetch() ) {
        $players[ $row['ID'] ] = [ 'kills' => $row['kills'],
            'total' => $row['total'],
            'ID' => $row['ID'],
            'login' => $row['LOGIN'],
            'count_matches' => '',
            'photo' => $row['PERSONAL_PHOTO']
        ];
    }

    $sql = 'SELECT t.USER_ID, count(DISTINCT r1.id1) as count_matches  FROM b_squad_member_result as t
        INNER JOIN 
        (SELECT m.IBLOCK_ELEMENT_ID as id1, m2.IBLOCK_ELEMENT_ID as id2, m3.IBLOCK_ELEMENT_ID as id3 FROM b_iblock_element_prop_s3 as m
        INNER JOIN b_iblock_element_prop_s3 as m2 ON m2.PROPERTY_8 = m.IBLOCK_ELEMENT_ID
        INNER JOIN b_iblock_element_prop_s3 as m3 ON m3.PROPERTY_8 = m2.IBLOCK_ELEMENT_ID
        WHERE m.PROPERTY_8 IS NULL) as r1 ON t.MATCH_ID IN(r1.id1, r1.id2, r1.id3)
        INNER JOIN b_uts_user as u ON u.VALUE_ID = t.USER_ID
        AND u.UF_ID_TEAM = '.$teamId.' 
        GROUP BY t.USER_ID';

    $res = $DB->Query($sql);

    while( $row = $res->Fetch() ) {
        $players[ $row['USER_ID'] ]['count_matches'] = $row['count_matches'];
    }

    return $players;

}

function countPointsByUserID( $userID ){
    GLOBAL $DB;
    $userID += 0;
    if( $userID ){
        $sql = 'SELECT  sum(t.TOTAL) AS total, sum(t.KILLS) AS kills
				FROM b_squad_member_result AS t 
				WHERE t.USER_ID = '.$userID.' AND t.TYPE_MATCH = 6
				GROUP BY t.USER_ID';
        $res = $DB->Query($sql);
        if( $row = $res->Fetch() ) {
            $points = [ 'kills' => $row['kills'], 'total' => $row['total'] ];
            return $points;
        }
    }
    return false;
}

function countPointsAllUsers(){
    GLOBAL $DB;
    $sql = 'SELECT t.USER_ID, count(t.USER_ID) as count_matches, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills
			FROM b_squad_member_result AS t 
			WHERE t.TYPE_MATCH = 6
			GROUP BY t.USER_ID';
    $res = $DB->Query($sql);
    $points = [];
    while( $row = $res->Fetch() ) {
        $points[ $row['USER_ID'] ] = [ 'kills' => $row['kills'], 'total' => $row['total'], 'count_matches' => $row['count_matches'] ];
    }
    return $points;
}

function updateTeam($props = [], $idTeam)
{
    $el = new CIBlockElement;
    $params = Array(
        "max_len" => "100", // обрезает символьный код до 100 символов
        "change_case" => "L", // буквы преобразуются к нижнему регистру
        "replace_space" => "-", // меняем пробелы на нижнее подчеркивание
        "replace_other" => "-", // меняем левые символы на нижнее подчеркивание
        "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
        "use_google" => "false", // отключаем использование google
    );
    $arLoadProductArray = Array(
        "MODIFIED_BY"    => $GLOBALS['USER']->GetID(), // элемент изменен текущим пользователем
        "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
        //"PROPERTY_VALUES"=> $props,
        "NAME"           => trim(strip_tags($_REQUEST['nameTeam'])),
        "CODE"           => CUtil::translit(trim(strip_tags($_POST['nameTeam'])), "ru" , $params),
        "PREVIEW_TEXT"   => trim(strip_tags($_REQUEST['descriptionTeam'])),
        "DETAIL_TEXT"    => trim(strip_tags($_REQUEST['descriptionTeam'])),
        "DETAIL_PICTURE" => $_FILES['logoTeam'],
        "PREVIEW_PICTURE" => $_FILES['logoTeam'],
    );


    if ($el->Update($idTeam, $arLoadProductArray)) {
        CIBlockElement::SetPropertyValuesEx($idTeam, 1, $props);
        return true;
    } else {
        return false;
    }

}

function getUserById($id)
{
        $filter = Array("GROUPS_ID" => Array(7), 'ID' => $id);
        $arParams["SELECT"] = array("UF_*");
        $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
        $output = false;
        if ($rsUser = $elementsResult->Fetch())
        {
            $output = $rsUser;
        }
        return $output;

}

function updateCaptain($props = [], $idTeam)
{
    dump($idTeam);
    CIBlockElement::SetPropertyValuesEx($idTeam, 1, $props, false);
}

function deactivateTeam($idTeam)
{
    $el = new CIBlockElement;

    $arLoadProductArray = array(
        "ACTIVE" => "N"
    );
    $el->Update($idTeam, $arLoadProductArray);

}

if (isset($_REQUEST['updateTeam']) && check_bitrix_sessid()) {

    $PROP = [];
    $PROP['NAME_TEAM'] = trim(strip_tags($_POST['nameTeam']));
    $PROP['TAG_TEAM'] = trim(strip_tags($_POST['tagTeam']));

    if(is_uploaded_file($_FILES['logoTeam']['tmp_name'])) {
        $PROP['LOGO_TEAM'] = $_FILES['logoTeam'];
    }
    $PROP['DESCRIPTION_TEAM'] = Array("VALUE" => Array ("TEXT" =>trim(strip_tags($_POST['descriptionTeam'])), "TYPE" => "html или text"));
    $teamIdPost = $_POST['team_id']+0;
    if($teamID == $teamIdPost && !empty($_POST['nameTeam'])) {
        if(updateTeam($PROP, $teamIdPost)) {
            createSession('management-players_success', 'Твоя команда успешно обновлена');
        } else {
            createSession('management-players_error', 'Команда с таким именем уже существует');
        }

    } else {
        $alertUpdateTeam = 'Ошибка, команда не обновлена';
        createSession('management-players_error', $alertUpdateTeam);
    }
    $redirectUrlAction = '/management-compositional/';

}

if(check_bitrix_sessid() && isset($_REQUEST['btn_chg_cap'])) {
    if (count($_POST['delete_player_from_team'])==1) {
        $teamIdPost = $_POST['team_id']+0;
        $userIds = $_POST['delete_player_from_team'];
        foreach ($userIds as $userId) {
            $PROP["AUTHOR"] = $userId+0;
            updateCaptain($PROP, $teamID+0);
        }
        createSession('team_success', 'Ты успешно передал права капитана');
        LocalRedirect("/personal/");
    } else {
        $alertManagementSquad = 'Ошибка при выборе нового капитана';
        createSession('management-players_error', $alertManagementSquad);
        $redirectUrlAction = '/management-compositional/';
    }
}


if(check_bitrix_sessid() && isset($_REQUEST['btn_drop_team'])) {
    if (!empty($teamID)) {
        $coreTeam = getCoreTeam($teamID);
        $recruits = getRecruitTeam($teamID);
        $squadsCount = count(getSquadByIdTeam($teamID));
    }
    if(count($coreTeam) == 1 && $squadsCount == 0){


    foreach ($recruits as $recruit){
        updateFieldUserbyId($recruit["ID"]+0, $fields= array("UF_REQUEST_ID_TEAM" => null));
    }

        updateFieldUserbyId($userID, $fields= array("UF_ID_TEAM" => null));
        $PROP["AUTHOR"] = "";
        updateCaptain($PROP, $teamID);
        deactivateTeam($teamID);

    createSession('team_success', 'Ты успешно распустил свой состав');
    LocalRedirect("/personal/");
    } else {
        $br = "";
        if(count($coreTeam) > 1){
            $alertManagementSquad = 'Ты не можешь распустить команду пока не удалил всех игроков';
            $br = "<br>";
        }

        if($squadsCount > 0){
            $alertManagementSquad = $alertManagementSquad . $br .'Ты не можешь распустить команду пока не снял ee с предстоящих матчей';
        }


        createSession('management-players_error', $alertManagementSquad);
        $redirectUrlAction = '/management-compositional/';
    }

}

if(check_bitrix_sessid() && isset($_REQUEST['btn_delete'])) {

    if (!empty($_POST['delete_player_from_team'])) {
        $userIds = $_POST['delete_player_from_team'];
        $success = [];
        $alerts = [];
        dump( $teamID);

        foreach ($userIds as $userId) {
            $user = getUserById($userId);
            if(count(getSquadByIdPlayer($teamID, $userId)) == 0){
                updateFieldUserbyId($userId+0, $fields= array("UF_ID_TEAM" => null));
                $success[] = $user["LOGIN"];
            } else {
                $alerts[] = $user["LOGIN"];
            }
        }

        $br = "";
        if(count($success)>0){
        foreach ($success as $succ){
            $successManagementTeam = $successManagementTeam . $br . "<h style='color: #FFE500;'>". $succ . "</h>" . ' успешно удален из команды';
            $br = "<br>";
        }
        createSession('management-players_success', $successManagementTeam);
        }
        $br = "";
        if(count($alerts)>0) {
            foreach ($alerts as $alert) {
                $alertManagementTeam = $alertManagementTeam . $br . "<h style='color: #ffe500;'>" . $alert . "</h>" . ' записан на предстоящий матч. Снимите его с участия прежде чем удалять его из команды';
                $br = "<br>";
            }
            createSession('management-players_error', $alertManagementTeam);
        }
        $redirectUrlAction = '/management-compositional/';
    }
}

if (check_bitrix_sessid() && isset($_REQUEST['btn_accept'])) {

    if (!empty($_POST['accept_in_team'])) {
        $userIds = $_POST['accept_in_team'];
        if (count($userIds) > 1) {
            $alertManagementTeam = 'Игроки успешно приняты в команду';
        } else {
            $alertManagementTeam = 'Игрок успешно принят в команду';
        }
        foreach ($userIds as $userId) {
            updateFieldUserbyId($userId+0, $fields= array("UF_REQUEST_ID_TEAM" => null, "UF_ID_TEAM" => $teamID));
        }

        createSession('management-players_success', $alertManagementTeam);
        $redirectUrlAction = '/management-compositional/';
    }

} else if(check_bitrix_sessid() && isset($_REQUEST['btn_reject'])){
    if (!empty($_POST['accept_in_team'])) {
        $userIds = $_POST['accept_in_team'];
        if (count($userIds) > 1) {
            $alertManagementTeam = 'Запросы игроков успешно отклонены';
        } else {
            $alertManagementTeam = 'Запрос игрока успешно отклонен';
        }
        foreach ($userIds as $userId) {
            updateFieldUserbyId($userId+0, $fields= array("UF_REQUEST_ID_TEAM" => null));
        }
        createSession('management-players_success', $alertManagementTeam);
        $redirectUrlAction = '/management-compositional/';
    }
}

function updateFieldUserbyId($userId, $fields= [])
{
    $user = new CUser;
    if ($user->Update($userId, $fields)) {
        return true;
    } else {
        return 'Error: ' . $user->LAST_ERROR;
    }
}

function getSquadByIdTeam($idTeam)
{
    GLOBAL $DB;
    $sql = 'SELECT s.PROPERTY_27, s.PROPERTY_28, m.PROPERTY_4 FROM b_iblock_element_prop_s6 as s  
                    INNER JOIN b_iblock_element_prop_s3 as m ON m.IBLOCK_ELEMENT_ID = s.PROPERTY_27
                    WHERE s.PROPERTY_28 ='.$idTeam.
        ' AND UNIX_TIMESTAMP(m.PROPERTY_4) > UNIX_TIMESTAMP(CURTIME())';

    $res = $DB->Query($sql);
    $players = [];
    while( $row = $res->Fetch() ) {
        $players[] = $row;
    }
    return $players;
}

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
    return  false;
}

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

function getRecruitTeam($teamID)
{
    $filter = Array("GROUPS_ID" => Array(7), ["UF_REQUEST_ID_TEAM" => $teamID]);
    $arParams["FIELDS"] = array("ID");
    $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
    $output = [];
    while ($rsUser = $elementsResult->Fetch())
    {
        $output[] = $rsUser["ID"];
    }
    return $output;
}

$isCaptain = isCaptain($userID, $teamID);
if(!$isCaptain) {
    LocalRedirect("/personal/");
}

if (!empty($teamID)) {

    $resTeam = getTeamById($teamID);
    $coreTeam = getCoreTeam($teamID);

    if ($redirectUrlAction != false) {
        LocalRedirect($redirectUrlAction);
    }

    // капитан или нет

    /*if(!$isCaptain) {
        LocalRedirect("/personal/");
    }*/
?>
  <?php /* ?>
<div class="container my-5">

    <?php if ($resTeam['AUTHOR']["VALUE"] == $userID) {?>
        <h2>Основной состав</h2>
    <?php if (!empty($coreTeam)) {?>
    <form action="#" method="post">
        <table class="table table-striped table-dark">
            <thead>
            <tr>
                <th scope="col">выбор</th>
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
                    <?php if(!($resTeam['AUTHOR']["VALUE"] == $player['ID'])) { ?>
                        <input type="checkbox"  name="delete_player_from_team[]" value="<?php echo $player['ID']?>">
                    <?php } ?>
                </th>
                <td><?php echo $player['LOGIN']?></td>
                <td><?php echo !$player["UF_PLAYED_GAMES"] ? '0' : $player["UF_PLAYED_GAMES"];?></td>
                <td><?php echo !$player["UF_FRAGS"] ? '0' : $player["UF_FRAGS"]; ?></td>
                <td><?php echo !$player["UF_RATING"] ? '0' : $player["UF_RATING"];?></td>
              <td><?php echo !empty($player['UF_DATE_PREM_EXP']) ? $player['UF_DATE_PREM_EXP'] : date('d.m.Y', time()-(3600*24));?></td>
            </tr>

        <?php } ?>

            </tbody>
        </table>
        <button type="submit" class="btn btn-danger d-block ml-auto" name="btn_delete">Выгнать из команды</button>
    </form>
    <?php } ?>
    <?php if(!empty($recruits)) { ?>
        <h2>Рекруты</h2>
        <form action="#" method="post" class="my-5">
            <table class="table table-striped table-dark">
                <thead>
                <tr>
                    <th scope="col">выбор</th>
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


                foreach ($recruits as $recruit)  {

                    ?>
                    <tr>
                        <th scope="row">
                            <input type="checkbox"  name="accept_in_team[]" value="<?php echo $recruit['ID']?>">
                        </th>
                        <th scope="row">
                            <?php echo $recruit['ID']?>
                        </th>
                        <td><?php echo $recruit['LOGIN']?></td>
                        <td><?php echo !$recruit["UF_PLAYED_GAMES"] ? '0' : $recruit["UF_PLAYED_GAMES"];?></td>
                        <td><?php echo !$recruit["UF_FRAGS"] ? '0' : $recruit["UF_FRAGS"]; ?></td>
                        <td><?php echo !$recruit["UF_RATING"] ? '0' : $recruit["UF_RATING"];?></td>
                        <td><?php echo !empty($recruit['UF_DATE_PREM_EXP']) ? $recruit['UF_DATE_PREM_EXP'] : date('d.m.Y', time()-(3600*24));?></td>
                    </tr>

                <?php } ?>

                </tbody>
            </table>
            <div class="d-inline ml-auto">
                <button type="submit" class="btn btn-success" name="btn_accept">Принять</button>
                <button type="submit" class="btn btn-danger" name="btn_reject">Отклонить</button>
            </div>

        </form>
    <?php } ?>
    <?php } ?>
  <!--Вывожу капитана на первое место<br>
  Таблица игроков<br>
  <h2>Основной состав</h2>
  // собираем по id team
  // у капитана нет checkbox
  checkbox, nick, UF_PLAYED_GAMES, UF_FRAGS, UF_RATING, (UF_DATE_PREM_EXP - today) 0 днях.<br>
  <button>Выгнать из команды</button>
  <hr>
  Таблица рекруты
  // собираем по полю request-id-team
  checkbox, nick, UF_PLAYED_GAMES, UF_FRAGS, UF_RATING, (UF_DATE_PREM_EXP - today) 0 днях.<br>
  <button>Принять</button>
  <button>Отклонить</button>-->
</div> <?php */
    //dump($resTeam);

    ?>
    <?php

    if(isset($_SESSION['management-players_success'])) { ?>
        <div class="alert-container" style="position: relative !important; margin-bottom: 7px;">
            <div class="alert alert-success alert-dismissible fade show"  role="alert">
                <?php echo $_SESSION['management-players_success'];?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php
    }
    if(isset($_SESSION['management-players_error'])){ ?>
        <div class="alert-container" style="position: relative !important;">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['management-players_error'];?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php }
    unset($_SESSION['management-players_success']);
    unset($_SESSION['management-players_error']);
    ?>
    <section class="team py-8">
        <div class="container">
            <h1 class="text-center">Моя команда</h1>
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-12">
                    <div class="team__logo-bg">
                        <div class="team__logo" style="background-image: url(<?php echo CFile::GetPath($resTeam["LOGO_TEAM"]['VALUE']); ?>">
                            <div class="team__logo-rating-bg">
                                <div class="team__logo-rating">

                                    <?php
                                    $ratingTeam = getRating($resTeam);
                                    echo $ratingTeam != false ? $ratingTeam : 300;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="team-info">
                        <h2 class="team-info__name"><?php echo $resTeam["NAME_TEAM"]['VALUE']; ?> [<?php echo $resTeam["TAG_TEAM"]['VALUE']; ?>]</h2>
                        <div class="team-info__description">
                            <?php echo $resTeam["DESCRIPTION_TEAM"]['VALUE']["TEXT"]; ?>
                        </div>
                        <div class="team-info__btn-edit">
                            <a href="#" class="btn__edit" data-toggle="modal" data-target="#editTeam">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><path d="M952.88,546.68H953l4.63-.82a.26.26,0,0,0,.15-.07l11.65-11.66a.24.24,0,0,0,.06-.09.18.18,0,0,0,0-.1.2.2,0,0,0,0-.11.24.24,0,0,0-.06-.09l-4.57-4.57a.27.27,0,0,0-.19-.08.28.28,0,0,0-.2.08l-11.65,11.66a.23.23,0,0,0-.08.14l-.81,4.63a.94.94,0,0,0,0,.44,1,1,0,0,0,.24.38A1,1,0,0,0,952.88,546.68Zm1.85-4.8,10-10,2,2-10,10-2.45.43ZM970,549H949.75a.88.88,0,0,0-.88.88v1a.22.22,0,0,0,.22.22h21.56a.22.22,0,0,0,.22-.22v-1A.87.87,0,0,0,970,549Z" transform="translate(-948.87 -529.08)"/></svg>
                                <span>Редактировать</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal fade " id="editTeam" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-modal-close" data-dismiss="modal" aria-label="Close">
                                <i></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h3 class="modal-body__title">Редактирование команды</h3>
                            <form action="<?= POST_FORM_ACTION_URI; ?>" method="post" enctype="multipart/form-data">
                                <?=bitrix_sessid_post()?>
                                <div class="form-field">
                                    <label for="nameTeam" class="form-field__label">Название команды</label>
                                    <input type="text" class="form-field__input" name="nameTeam" value="<?php echo $resTeam["NAME_TEAM"]['VALUE']; ?>" autocomplete="off" id="nameTeam" placeholder="Введите название команды">
                                </div>
                                <div class="form-field">
                                    <label for="tagTeam" class="form-field__label">Тег команды</label>
                                    <input type="text" class="form-field__input" name="tagTeam" value="<?php echo $resTeam["TAG_TEAM"]['VALUE']; ?>" autocomplete="off" id="tagTeam" placeholder="Введите тег команды">
                                </div>
                                <!--<div class="form-field">
                                  <label for="mottoTeam" class="form-field__label">Девиз команды</label>
                                  <input type="text" class="form-field__input" name="mottoTeam" value="" autocomplete="off" id="mottoTeam" placeholder="Введите девиз команды">
                                </div>-->
                                <div class="form-field">

                                    <input type="file" class="form-field__input-file inputFile" data-multiple-caption="выбрано {count} файла(ов)" name="logoTeam"  autocomplete="off"  id="logoTeam" >
                                    <label for="logoTeam" class="form-field__upload-file">
                                        <i></i><span>Прикрепить логотип команды</span> <div class="fileUploaded" style="background-image: url(<?php echo CFile::GetPath($resTeam["LOGO_TEAM"]['VALUE']); ?>)"></div>
                                    </label>
                                </div>
                                <div class="form-field">
                                    <label for="descriptionTeam" class="form-field__label">Описание команды</label>
                                    <textarea name="descriptionTeam" id="descriptionTeam" class="form-field__textarea" cols="30" rows="3"  placeholder="Введите описание команды"><?php echo $resTeam["DESCRIPTION_TEAM"]['VALUE']["TEXT"]; ?></textarea>
                                </div>
                                <input type="hidden" name="team_id" value="<?php echo $resTeam['ID'];?>"><br>
                                <div class="modal-body__btn">
                                    <button type="submit" class="btn mr-3" name="updateTeam">Изменить команду</button>
                                    <button type="button" class="btn btn_border" data-dismiss="modal">Отмена</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="pb-8">
        <div class="container">
            <h2 class="core-team__heading">Основной Состав</h2>
            <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                <?=bitrix_sessid_post()?>
                <div class="core-team">
                    <div class="flex-table">
                        <div class="flex-table--header bg-default">
                            <div class="flex-table--categories">
                                <span>Игрок</span>
                                <span>Количество игр</span>
                                <span>Киллы</span>
                                <span>Рейтинг</span>
                            </div>
                        </div>
                        <div class="flex-table--body">
                            <?php
                            $players = getQtyPlayedGames($teamID);

                            if($rec = getRecruitTeam($teamID)){
                                $recruits = getRecruitsRating($rec);
                            }

                            //$points = countPointsAllUsers();
                            // ставим капитана на первое место
                            foreach ($players as $k => $player) {
                                if ($resTeam['AUTHOR']["VALUE"] == $player['ID']) {
                                    $players = [$k => $player] + $players;
                                    break;
                                }
                            }
                            ?>
                            <?php foreach ($players as $player) {
                                $cntMatches = '..';
                                $kills = '..';
                                $total = ceil($player['total']);
                                if( isset($player['kills']) ){
                                    $cntMatches = ceil($player['count_matches']);
                                    $kills = ceil($player['kills']);

                                }
                                ?>
                                <div class="flex-table--row">
                    <span>
                      <div class="core-team__user">
                        <?php if (!($resTeam['AUTHOR']["VALUE"] == $player['ID'])) { ?>
                            <label class="label-checkbox">
                            <input type="checkbox" name="delete_player_from_team[]" value="<?php echo $player['ID']?>">
                            <div class="label-checkbox__checkmark"></div>
                          </label>
                        <?php } ?>
                        <div class="core-team__user-avatar"
                             <?php if (!empty($player["photo"])) { ?>
                                 style="background-image: url(<?php echo CFile::GetPath($player["photo"]); ?>)"
                             <?php } else { ?>
                                 style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                             <?php } ?>>
                          <?php if ($resTeam['AUTHOR']["VALUE"] == $player['ID']) { ?>
                              <div class="core-team__user-avatar-icon_captain">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                <circle  cx="11" cy="11" r="10"/>
                                <path d="M682.39,379.09a.65.65,0,0,1,1.22,0l.82,2.5a.65.65,0,0,0,.61.43h2.66a.62.62,0,0,1,.38,1.13l-2.16,1.54a.63.63,0,0,0-.23.71l.82,2.49a.63.63,0,0,1-1,.7l-2.16-1.54a.63.63,0,0,0-.74,0l-2.16,1.54a.63.63,0,0,1-1-.7l.82-2.49a.63.63,0,0,0-.23-.71l-2.16-1.54a.62.62,0,0,1,.38-1.13H681a.65.65,0,0,0,.61-.43Z" transform="translate(-672 -373)"/>
                              </svg>
                            </div>
                          <?php } ?>
                        </div>
                        <a href="/players/<?php echo $player['ID'].'_'.$player['login'].'/';?>" class="core-team__user-link"><?php echo $player['login']?></a>
                      </div>
                    </span>
                                    <span class="core-team__param-wrap">
                      <div class="core-team__param">Количество игр</div>
                      <?php echo $cntMatches;?>
                    </span>
                                    <span class="core-team__param-wrap">
                      <div class="core-team__param">Киллы</div>
                      <?php echo $kills;?>
                    </span>
                                    <span class="core-team__param-wrap">
                      <div class="core-team__param">Рейтинг</div>

                          <?php echo $total;?>
                    </span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content: space-around">
                <?php if(sizeof($players) > 1) { ?>


                        <div class="core-team__btn">
                            <button type="submit" class="btn-icon btn-icon_red btn-icon_close-red" name="btn_delete"><i></i> Удалить из команды</button>
                        </div>

                        <div class="core-team__btn">
                            <button type="submit" class="btn-icon btn-icon_check" name="btn_chg_cap"><i></i> Передать права капитана</button>
                        </div>





                <?php } ?>
                <div class="core-team__btn">
                    <button type="submit" onclick="if (!confirm('Ты собираешься удалить команду, ты уверен?')) return false" class="btn-icon btn-icon_red btn-icon_close-red" name="btn_drop_team"><i></i> Распустить состав</button>
                </div>
                    </div>
            </form>
        </div>
    </section>
    <?php if(!empty($recruits)) { ?>
        <section class="py-8 bg-blue-lighter">
            <div class="container">
                <h2 class="core-team__heading">Запросы игроков <span class="core-team__heading-badge"><?php echo count($recruits); ?></span></h2>
                <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                    <?=bitrix_sessid_post()?>
                    <div class="core-team">
                        <div class="flex-table">
                            <div class="flex-table--header bg-blue-lighter">
                                <div class="flex-table--categories">
                                    <span>Игрок</span>
                                    <span>Количество игр</span>
                                    <span>Киллы</span>
                                    <span>Рейтинг</span>
                                </div>
                            </div>
                            <div class="flex-table--body">
                                <?php foreach ($recruits as $recruit)  {
                                    $cntMatches = '..';
                                    $kills = '..';
                                    $total = ceil($recruit['total']);
                                    if( isset($recruit['kills']) ){
                                        $cntMatches = ceil($recruit['count_matches']);
                                        $kills = ceil($recruit['kills']);
                                    }
                                    ?>

                                    <div class="flex-table--row">
                    <span>
                      <div class="core-team__user">
                        <label class="label-checkbox">
                          <input type="checkbox" name="accept_in_team[]" value="<?php echo $recruit['ID']?>">
                          <div class="label-checkbox__checkmark"></div>
                        </label>
                        <div class="core-team__user-avatar"
                             <?php if (!empty($recruit["photo"])) { ?>
                                 style="background-image: url(<?php echo CFile::GetPath($recruit["photo"]); ?>)"
                             <?php } else { ?>
                                 style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                             <?php } ?>>
                        </div>
                        <a href="/players/<?php echo $recruit['ID'].'_'.$recruit['login'].'/';?>" class="core-team__user-link"><?php echo $recruit['login'];?></a>
                      </div>
                    </span>
                                        <span class="core-team__param-wrap">
                      <div class="core-team__param">Количество игр</div>
                      <?php echo $cntMatches;?>
                    </span>
                                        <span class="core-team__param-wrap">
                      <div class="core-team__param">Киллы</div>
                      <?php echo $kills;?>
                    </span>
                                        <span class="core-team__param-wrap">
                      <div class="core-team__param">Рейтинг</div>
                      <?php echo $total;?>
                    </span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="core-team__btn">
                        <!--<a href="#" class="btn__edit mr-3">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><path d="M952.88,546.68H953l4.63-.82a.26.26,0,0,0,.15-.07l11.65-11.66a.24.24,0,0,0,.06-.09.18.18,0,0,0,0-.1.2.2,0,0,0,0-.11.24.24,0,0,0-.06-.09l-4.57-4.57a.27.27,0,0,0-.19-.08.28.28,0,0,0-.2.08l-11.65,11.66a.23.23,0,0,0-.08.14l-.81,4.63a.94.94,0,0,0,0,.44,1,1,0,0,0,.24.38A1,1,0,0,0,952.88,546.68Zm1.85-4.8,10-10,2,2-10,10-2.45.43ZM970,549H949.75a.88.88,0,0,0-.88.88v1a.22.22,0,0,0,.22.22h21.56a.22.22,0,0,0,.22-.22v-1A.87.87,0,0,0,970,549Z" transform="translate(-948.87 -529.08)"/></svg>
                          <span>Управление составом</span>
                        </a>-->
                        <button type="submit" class="btn-icon btn-icon_check mr-1" name="btn_accept"><i></i> Принять</button>
                        <button type="submit" class="btn-icon btn-icon_red btn-icon_close-red" name="btn_reject"><i></i> Отклонить</button>
                        <!--<a href="#" class="btn__edit">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><path d="M952.88,546.68H953l4.63-.82a.26.26,0,0,0,.15-.07l11.65-11.66a.24.24,0,0,0,.06-.09.18.18,0,0,0,0-.1.2.2,0,0,0,0-.11.24.24,0,0,0-.06-.09l-4.57-4.57a.27.27,0,0,0-.19-.08.28.28,0,0,0-.2.08l-11.65,11.66a.23.23,0,0,0-.08.14l-.81,4.63a.94.94,0,0,0,0,.44,1,1,0,0,0,.24.38A1,1,0,0,0,952.88,546.68Zm1.85-4.8,10-10,2,2-10,10-2.45.43ZM970,549H949.75a.88.88,0,0,0-.88.88v1a.22.22,0,0,0,.22.22h21.56a.22.22,0,0,0,.22-.22v-1A.87.87,0,0,0,970,549Z" transform="translate(-948.87 -529.08)"/></svg>
                          <span>Управление играми</span>
                        </a>-->
                    </div>
                </form>
            </div>
        </section>
    <?php } ?>
    <?php } ?>

<?php
    $matches = getMatchesByTeam($teamID);
    //echo '<pre>'.print_r($matches,1).'</pre>';
    $orfilerMatches = ['LOGIC' => 'OR'];
    foreach( $matches as $matchID ){
    $orfilerMatches[] = [ 'ID' => $matchID ];
    }

    if( count($matches) ){

    ?>
    <style>
        .flex-table-new--body .btn-italic {
            text-align:center;
            display: block;
        }
        .game-schedule {
            padding-top: 0;
            padding-bottom: 30px;
        }
    </style>
    <section class="game-schedule bg-blue-lighter">
        <div class="container py-8">
            <h2 class="game-schedule__heading text-center">Игры команды</h2>
            <div class="game-schedule-table">
                <div class="flex-table-new">
                    <div class="flex-table--header bg-blue-lighter">
                        <div class="flex-table--categories">
                            <span>Тип игры</span>
                            <span>Название</span>
                            <span>Дата проведения</span>
                            <span>Рейтинг</span>
                            <span>Режим</span>
                            <span>Комментатор</span>
                        </div>
                    </div>
                    <div class="flex-table-new--body">
                        <?php
                        GLOBAL $arrFilterDateTime;
                        $arrFilterDateTime=Array(
                            $orfilerMatches,
                            //'ID' => 2224,
                            "ACTIVE" => "Y",
                            //">=PROPERTY_DATE_START" => date('Y-m-d H:i:s', time()-3600),
                            "PROPERTY_PREV_MATCH" => false,
                            //"PROPERTY_STAGE_TOURNAMENT" => 4,
                            //"!=PROPERTY_TOURNAMENT" => false, // турниры
                            //"=PROPERTY_TOURNAMENT" => false, // праки
                        );
                        $APPLICATION->IncludeComponent(
                            "bitrix:news.list",
                            "game-schedule",
                            array(
                                "ACTIVE_DATE_FORMAT" => "d.m.Y",
                                "ADD_SECTIONS_CHAIN" => "N",
                                "AJAX_MODE" => "N",
                                "AJAX_OPTION_ADDITIONAL" => "",
                                "AJAX_OPTION_HISTORY" => "N",
                                "AJAX_OPTION_JUMP" => "N",
                                "AJAX_OPTION_STYLE" => "Y",
                                "CACHE_FILTER" => "N",
                                "CACHE_GROUPS" => "Y",
                                "CACHE_TIME" => "36000000",
                                "CACHE_TYPE" => "A",
                                "CHECK_DATES" => "Y",
                                "DETAIL_URL" => "/game-schedule/#ELEMENT_CODE#/",
                                "DISPLAY_BOTTOM_PAGER" => "Y",
                                "DISPLAY_DATE" => "Y",
                                "DISPLAY_NAME" => "Y",
                                "DISPLAY_PICTURE" => "Y",
                                "DISPLAY_PREVIEW_TEXT" => "Y",
                                "DISPLAY_TOP_PAGER" => "N",
                                "FIELD_CODE" => array(
                                    0 => "",
                                    1 => "PROPERTY_TOURNAMENT.NAME",
                                    2 => "PROPERTY_TOURNAMENT.DETAIL_PICTURE",
                                    3 => "PROPERTY_STREAMER.NAME",
                                    4 => "",
                                ),
                                "FILTER_NAME" => "arrFilterDateTime",
                                "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                                "IBLOCK_ID" => "3",
                                "IBLOCK_TYPE" => "matches",
                                "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                                "INCLUDE_SUBSECTIONS" => "Y",
                                "MESSAGE_404" => "",
                                "NEWS_COUNT" => "3",
                                "PAGER_BASE_LINK_ENABLE" => "N",
                                "PAGER_DESC_NUMBERING" => "N",
                                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                                "PAGER_SHOW_ALL" => "N",
                                "PAGER_SHOW_ALWAYS" => "N",
                                "PAGER_TEMPLATE" => "ajax_pager",
                                "PAGER_TITLE" => "Расписание игр",
                                "PARENT_SECTION" => "",
                                "PARENT_SECTION_CODE" => "",
                                "PREVIEW_TRUNCATE_LEN" => "",
                                "PROPERTY_CODE" => array(
                                    0 => "PUBG_LOBBY_ID",
                                    1 => "DATE_START",
                                    2 => "TYPE_MATCH",
                                    3 => "TOURNAMENT",
                                    4 => "",
                                ),
                                "SET_BROWSER_TITLE" => "N",
                                "SET_LAST_MODIFIED" => "N",
                                "SET_META_DESCRIPTION" => "N",
                                "SET_META_KEYWORDS" => "N",
                                "SET_STATUS_404" => "Y",
                                "SET_TITLE" => "N",
                                "SHOW_404" => "Y",
                                "SORT_BY1" => "PROPERTY_DATE_START",
                                "SORT_BY2" => "SORT",
                                "SORT_ORDER1" => "DESC",
                                "SORT_ORDER2" => "DESC",
                                "STRICT_SECTION_CHECK" => "N",
                                "COMPONENT_TEMPLATE" => "game-schedule",
                                "FILE_404" => ""
                            ),
                            false
                        );
                        ?>
                    </div>
                </div>
                <!--div class="game-schedule-table__show-more">
                  <div class="mt-3">
                    <a href="/game-schedule/" class="btn">Поиск матча</a>
                  </div>
                </div-->
            </div>
        </div>
    </section>
<?php } else {

    echo '<section class="game-schedule bg-blue-lighter"><div class="container">
    <h2 class="game-schedule__heading text-center">Команда еще не имеет статистики по играм </h2>
    </div></section>';

} /* end count matches */
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>