<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Кабинет пользователя");

if (!empty($teamID)) {
    $arrResultTeam = getTeamById($teamID);
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


// получаем состав команды
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
    return  false;
}
$isCaptain = isCaptain($userID, $teamID);

?>
<?php


//dump($teamID);
$requestTeamID = $arUser['UF_REQUEST_ID_TEAM'];



// получаем список матчей где я участвую
function getSquadsWhereIm($userId)
{
    $arSelect = array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = array(
        "IBLOCK_ID" => 6,
        array(
            "LOGIC" => "OR",
            array(
                "PROPERTY_PLAYER_1" => $userId,
            ),
            array(
                "PROPERTY_PLAYER_2" => $userId,
            ),
            array(
                "PROPERTY_PLAYER_3" => $userId,
            ),
          array(
            "PROPERTY_PLAYER_4" => $userId,
          ),
          array(
            "PROPERTY_PLAYER_5" => $userId,
          ),
          array(
            "PROPERTY_PLAYER_6" => $userId,
          ),
        ),
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $myMatches = [];
    $myMatchesIds = [];
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $myMatches[] = array_merge($arFields, $arProps);

    }
    if (!empty($myMatches)) {
        foreach ($myMatches as $match) {
            $myMatchesIds[] = $match["MATCH_STAGE_ONE"]['VALUE'];
        }
    }

    return $myMatchesIds;
}





// end get list team


if (isset($_POST['join_submit'])) {

    $user = new CUser;
    $fields = array(
        //"NAME"              => "Сергей",
        "UF_REQUEST_ID_TEAM" => $_POST['team_id'],
    );
    if ($user->Update($userID, $fields)) {
        header('Location: /personal/');
        echo 'вы присоединились в команду';
    } else {
        echo 'Error: ' . $user->LAST_ERROR;
    }
}

if (isset($_REQUEST['createTeam']) && check_bitrix_sessid()) {
    //Погнали
    $el = new CIBlockElement;
    //$section_id = false;
    //$section_id[$i] = $_POST['section_id']; //Разделы для добавления
    //Свойства
    $PROP = [];
    $PROP['NAME_TEAM'] = trim(strip_tags($_POST['nameTeam']));
    $PROP['TAG_TEAM'] = trim(strip_tags($_POST['tagTeam']));
    $PROP['LOGO_TEAM'] = $_FILES['logoTeam'];
    $PROP['DESCRIPTION_TEAM'] = Array("VALUE" => Array ("TEXT" =>trim(strip_tags($_POST['descriptionTeam'])), "TYPE" => "html или text"));
    $PROP['AUTHOR'] = $userID;
    $params = Array(
        "max_len" => "100", // обрезает символьный код до 100 символов
        "change_case" => "L", // буквы преобразуются к нижнему регистру
        "replace_space" => "-", // меняем пробелы на нижнее подчеркивание
        "replace_other" => "-", // меняем левые символы на нижнее подчеркивание
        "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
        "use_google" => "false", // отключаем использование google
    );

    //Основные поля элемента
    $fields = array(
        "DATE_CREATE" => date("d.m.Y H:i:s"), //Передаем дата создания
        "CREATED_BY" => $GLOBALS['USER']->GetID(),    //Передаем ID пользователя кто добавляет
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => 1, //ID информационного блока он 24-ый
        "PROPERTY_VALUES" => $PROP, // Передаем массив значении для свойств
        "CODE" => CUtil::translit(trim(strip_tags($_POST['nameTeam'])), "ru" , $params),
        "NAME" => trim(strip_tags($_POST['nameTeam'])),
        "ACTIVE" => "Y", //поумолчанию делаем активным или ставим N для отключении поумолчанию
        "PREVIEW_TEXT" => strip_tags($_REQUEST['description_team']), //Анонс
        "PREVIEW_PICTURE" => $_FILES['logoTeam'], //изображение для анонса
        "DETAIL_TEXT"    => trim(strip_tags($_POST['descriptionTeam'])),
        "DETAIL_PICTURE" => $_FILES['logoTeam'] //изображение для детальной страницы
    );


    //Результат в конце отработки
    if ($ID = $el->Add($fields)) {
        //echo "Команда успешно сохранена id - " . $ID;
        $user = new CUser;
        $fields = Array(
            //"NAME"              => "Сергей",
            "UF_ID_TEAM"        => $ID,
        );
        if ($user->Update($userID, $fields)) {
            createSession('team_success', 'Команда успешно создана');
            LocalRedirect("/personal/");
            //echo 'ID_Team заполнен';
        } else {
            echo 'Error: ' . $user->LAST_ERROR;
        }

    } else {
        //echo "Error: ".$el->LAST_ERROR;
        createSession('team_error', $el->LAST_ERROR);
        LocalRedirect("/personal/");
    }
}

?>
<?php

function getPromo($code){
    $promoCode = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
    if (empty($promoCode)) {
        return false;
    }
    $arSelect = Array('ID', 'NAME', 'DATE_ACTIVE_FROM', 'SORT');// PROPERTY_*
    $arFilter = Array(
        'IBLOCK_ID' => 11,
        'NAME' => $promoCode,
        'ACTIVE_DATE' => 'Y',
        'ACTIVE' => 'Y'
    );
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement()) {
        return $ob->fields;
    }
    return false;
}

function checkUsedPromo($code){
    $promoCode = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
    $userID = $GLOBALS['USER']->GetID();
    $arSelect = Array('ID', 'NAME', 'DATE_CREATE');
    $arFilter = Array(
        'IBLOCK_ID' => 12,
        'NAME' => $promoCode.'___'.$userID,
        'ACTIVE' => 'Y'
    );
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement()) {
        return $ob->fields;
    }
    return false;
}

function addUsedCodeToHistory($code){
    $promoCode = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
    $iblock_id = 12;
    $userID = $GLOBALS['USER']->GetID();
    $bCode = 'use'.$promoCode.'by'.$userID;
    $el = new CIBlockElement;
    $params = Array(
        'max_len' => '100', // обрезает символьный код до 100 символов
        'change_case' => 'L', // буквы преобразуются к нижнему регистру
        'replace_space' => '-', // меняем пробелы на нижнее подчеркивание
        'replace_other' => '-', // меняем левые символы на нижнее подчеркивание
        'delete_repeat_replace' => 'true', // удаляем повторяющиеся нижние подчеркивания
        'use_google' => 'false', // отключаем использование google
    );
    $fields = array(
        'DATE_CREATE' => date('d.m.Y H:i:s'), //Передаем дата создания
        'CREATED_BY' => $userID,    //Передаем ID пользователя кто добавляет
        'IBLOCK_SECTION_ID' => false,
        'CODE' => CUtil::translit($bCode, 'ru' , $params),
        'IBLOCK_ID' => $iblock_id, //ID информационного блока он 24-ый
        'NAME' => $promoCode.'___'.$userID,
        'ACTIVE' => 'Y', //поумолчанию делаем активным или ставим N для отключении поумолчанию
    );
    //Результат в конце отработки
    if ($ID = $el->Add($fields)) {
        return $ID;
    } else {
        return "Error: ".$el->LAST_ERROR;
    }
}

function addDaysToPremlimit( $days ){
    $days += 0;
    $userID = $GLOBALS['USER']->GetID();
    $rsUser = CUser::GetByID($userID);
    $arUser = $rsUser->Fetch();
    $premLimit = $arUser['UF_DATE_PREM_EXP'];
    $now = date('d.m.Y');
    $today = DateTime::createFromFormat('d.m.Y', $now);
    $premDay = DateTime::createFromFormat('d.m.Y', $premLimit);
    if ($premDay < $today) {
        $datePremExp = date( 'd.m.Y', strtotime( $now ." +" . $days . "days" ));
    } else if ($premDay >= $today) {
      $datePremExp = date( 'd.m.Y', strtotime( $premLimit ." +" . $days . "days" ));
    }
    $user = new CUser;
    $fields = array(
        "UF_DATE_PREM_EXP" => $datePremExp,
    );
    if ($user->Update($userID, $fields)) {
        return true;
    }
    // to do добавить к прему пользователю $days
    // если текущий прем < today то today + days
    // если текущий прем >= today то прем + days
   return false;
}

$alertPromoCode = '';
if( isset( $_POST['promocode'] )  && check_bitrix_sessid() ){
    if( $promoCodeItem = getPromo( $_POST['promocode'] ) ){
        $days = $promoCodeItem['SORT'];
        if( $promoCodeUsed = checkUsedPromo( $_POST['promocode'] ) ){
            $alertPromoCode = 'Код уже использовался Вами '.$promoCodeUsed['DATE_CREATE'];
        } else {
            if( addDaysToPremlimit( $days ) ){
                addUsedCodeToHistory( $_POST['promocode'] );
                $alertPromoCode = 'Код активирован. Тебе добавлено '.$days.' дней прем аккаунта';
                createSession('alert_success', $alertPromoCode);
                $alertPromoCode = '';
            } else {
                $alertPromoCode = 'Что-то пошло не так. Свяжись со службой технической поддержки.';
            }
        }
    } else {
        $alertPromoCode = 'Код не существует';
    }
}
if ($alertPromoCode != '') {
    createSession('alert_error', $alertPromoCode);
}
?>
<?php /*
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <div style="width: 150px; height: 150px; border-radius:50%;  background-color: gray; margin: 0 auto;">
            <?php if (!empty($arUser["PERSONAL_PHOTO"])) { ?>
              <img style="border-radius: 50%" src="<?php echo CFile::GetPath($arUser["PERSONAL_PHOTO"]); ?>" alt="">
            <?php } ?>
        </div>
        <div class="my-5 text-center"><a href="/personal/edit/" class="btn btn-info">Редактировать профиль</a></div>
      </div>
      <div class="col-md-6">
        <p>ID: <?php echo $arUser['ID']; ?></p>
        <h1><?= $arUser["LOGIN"] ?></h1>
        <p>Мое настроение: <?= $arUser["TITLE"]; ?></p>
      </div>
      <div class="col-md-3">

        <p><strong>Тип Аккаунта - активный</strong></p>
        <p><strong>Рейтинг - 6</strong></p>
          <?php if (isset($arrResultTeam)) { ?>
            <div>TEAM - <span
                class="badge rounded-pill bg-secondary"><?php echo $arrResultTeam['NAME'] ?> [<?php echo $arrResultTeam['TAG_TEAM']['~VALUE']; ?>]</span>
            </div>
          <?php } else { ?>
            <div class="badge rounded-pill bg-danger color-white">Команада не выбрана</div>
          <?php } ?>
      </div>
    </div>--week*/?>
      <?php /* if(!empty($teamID)) { ?>
    <div class="row py-5 mb-3" style="background-color: #ccc;">
      <div class="col-12">
        <h2>Мои матчи</h2>
          <?php

          // получаем участников матча
          $arSelectMatchParticipants = Array(
              "ID",
              "IBLOCK_ID",
              "NAME",
              "DATE_ACTIVE_FROM",
              //'DETAIL_PAGE_URL',
              //'PROPERTY_*'
              'PROPERTY_TEAM_PLACE_07.NAME',
              'PROPERTY_TEAM_PLACE_07.ID',
              'PROPERTY_TEAM_PLACE_08.NAME',
              'PROPERTY_TEAM_PLACE_08.ID',
              'PROPERTY_TEAM_PLACE_09.NAME',
              'PROPERTY_TEAM_PLACE_09.ID',
              'PROPERTY_TEAM_PLACE_10.NAME',
              'PROPERTY_TEAM_PLACE_10.ID',
              'PROPERTY_TEAM_PLACE_10.ID',
              'PROPERTY_WHICH_MATCH.ID',
              'PROPERTY_WHICH_MATCH.NAME',
              'PROPERTY_WHICH_MATCH.DETAIL_PAGE_URL',
          );

          $arFilterMatchParticipants = Array("IBLOCK_ID"=> 4, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
          $arr = [
              'PROPERTY_TEAM_PLACE_07.ID',
              'PROPERTY_TEAM_PLACE_08.ID',
              'PROPERTY_TEAM_PLACE_09.ID',
              'PROPERTY_TEAM_PLACE_10.ID',
          ];

          // нужно получить team id
          $idTeam = $teamID;

          $arFilterMatchParticipantsExt = array(
              "LOGIC" => "OR",
          );


          foreach ($arr as $v) {
              $arFilterMatchParticipantsExt[] = array($v => $idTeam) ;
          }
          $arFilterMatchParticipants += array($arFilterMatchParticipantsExt);


          $resMatchParticipants = CIBlockElement::GetList(Array(), $arFilterMatchParticipants, false, Array("nPageSize"=>50), $arSelectMatchParticipants);
          $matchesId = [];
          while($ob = $resMatchParticipants->GetNextElement()){
              $arFields = $ob->GetFields();
              $matchesId[]= $arFields['PROPERTY_WHICH_MATCH_ID'];
          }

          function getMatchByIds($iBlockId, $matchesId) {
              $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
              $arFilter = Array("IBLOCK_ID" => $iBlockId, "ID" => $matchesId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
              $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
              while ($ob = $res->GetNextElement()) {
                  $arFields = $ob->GetFields();
                  $arProps = $ob->GetProperties();
                  return array_merge($arFields, $arProps);
              }
              return null;
          }

          // end get list team
          if (!empty($matchesId)) {
              GLOBAL  $arrFilterIdsMatches;
              $curDate = date('Y-m-d H:i:s', time()-3600);
              $arrFilterIdsMatches=Array("ID" => $matchesId, ">=PROPERTY_DATE_START" => $curDate);

              $APPLICATION->IncludeComponent(
                  "bitrix:news.list",
                  "matches_list",
                  Array(
                      "ACTIVE_DATE_FORMAT" => "d.m.Y",
                      "ADD_SECTIONS_CHAIN" => "Y",
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
                      "DETAIL_URL" => "",
                      "DISPLAY_BOTTOM_PAGER" => "Y",
                      "DISPLAY_DATE" => "Y",
                      "DISPLAY_NAME" => "Y",
                      "DISPLAY_PICTURE" => "Y",
                      "DISPLAY_PREVIEW_TEXT" => "Y",
                      "DISPLAY_TOP_PAGER" => "N",
                      "FIELD_CODE" => array("","PROPERTY_TOURNAMENT.NAME","PROPERTY_TOURNAMENT.DETAIL_PICTURE","PROPERTY_TOURNAMENT.DETAIL_PAGE_URL",""),
                      "FILTER_NAME" => "arrFilterIdsMatches",
                      "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                      "IBLOCK_ID" => "3",
                      "IBLOCK_TYPE" => "matches",
                      "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
                      "INCLUDE_SUBSECTIONS" => "Y",
                      "MESSAGE_404" => "",
                      "NEWS_COUNT" => "20",
                      "PAGER_BASE_LINK_ENABLE" => "N",
                      "PAGER_DESC_NUMBERING" => "N",
                      "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                      "PAGER_SHOW_ALL" => "N",
                      "PAGER_SHOW_ALWAYS" => "N",
                      "PAGER_TEMPLATE" => ".default",
                      "PAGER_TITLE" => "Новости",
                      "PARENT_SECTION" => "",
                      "PARENT_SECTION_CODE" => "",
                      "PREVIEW_TRUNCATE_LEN" => "",
                      "PROPERTY_CODE" => array("PUBG_LOBBY_ID","DATE_START","TOURNAMENT",""),
                      "SET_BROWSER_TITLE" => "Y",
                      "SET_LAST_MODIFIED" => "N",
                      "SET_META_DESCRIPTION" => "Y",
                      "SET_META_KEYWORDS" => "Y",
                      "SET_STATUS_404" => "N",
                      "SET_TITLE" => "Y",
                      "SHOW_404" => "N",
                      "SORT_BY1" => "ACTIVE_FROM",
                      "SORT_BY2" => "SORT",
                      "SORT_ORDER1" => "DESC",
                      "SORT_ORDER2" => "ASC",
                      "STRICT_SECTION_CHECK" => "N"
                  )
              );
          }

          ?>
      </div>
    </div>
    <?php } */

      ?>
<?php /*
      <?php
      $myMatchIds = getSquadsWhereIm($userID);

      if (!empty($myMatchIds)) { ?>
        <div class="row py-5 mb-3" style="background-color: #ccc;">
          <div class="container">
            <h2>Матчи где я участвую</h2>
              <?php
              //dump($myMatchIds);
              global $arrFilterIdsMatches;
              $curDate = date('Y-m-d H:i:s', time() - 3600);
              $arrFilterIdsMatches = array("ID" => $myMatchIds, ">=PROPERTY_DATE_START" => $curDate);

              $APPLICATION->IncludeComponent(
                  "bitrix:news.list",
                  "my_matches_list",
                  array(
                      "ACTIVE_DATE_FORMAT" => "d.m.Y",
                      "ADD_SECTIONS_CHAIN" => "Y",
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
                      "DETAIL_URL" => "",
                      "DISPLAY_BOTTOM_PAGER" => "Y",
                      "DISPLAY_DATE" => "Y",
                      "DISPLAY_NAME" => "Y",
                      "DISPLAY_PICTURE" => "Y",
                      "DISPLAY_PREVIEW_TEXT" => "Y",
                      "DISPLAY_TOP_PAGER" => "N",
                      "FIELD_CODE" => array("", "PROPERTY_TOURNAMENT.NAME", "PROPERTY_TOURNAMENT.DETAIL_PICTURE", "PROPERTY_TOURNAMENT.DETAIL_PAGE_URL", ""),
                      "FILTER_NAME" => "arrFilterIdsMatches",
                      "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                      "IBLOCK_ID" => "3",
                      "IBLOCK_TYPE" => "matches",
                      "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
                      "INCLUDE_SUBSECTIONS" => "Y",
                      "MESSAGE_404" => "",
                      "NEWS_COUNT" => "20",
                      "PAGER_BASE_LINK_ENABLE" => "N",
                      "PAGER_DESC_NUMBERING" => "N",
                      "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                      "PAGER_SHOW_ALL" => "N",
                      "PAGER_SHOW_ALWAYS" => "N",
                      "PAGER_TEMPLATE" => ".default",
                      "PAGER_TITLE" => "Новости",
                      "PARENT_SECTION" => "",
                      "PARENT_SECTION_CODE" => "",
                      "PREVIEW_TRUNCATE_LEN" => "",
                      "PROPERTY_CODE" => array("PUBG_LOBBY_ID", "DATE_START", "TOURNAMENT", ""),
                      "SET_BROWSER_TITLE" => "Y",
                      "SET_LAST_MODIFIED" => "N",
                      "SET_META_DESCRIPTION" => "Y",
                      "SET_META_KEYWORDS" => "Y",
                      "SET_STATUS_404" => "N",
                      "SET_TITLE" => "Y",
                      "SHOW_404" => "N",
                      "SORT_BY1" => "ACTIVE_FROM",
                      "SORT_BY2" => "SORT",
                      "SORT_ORDER1" => "DESC",
                      "SORT_ORDER2" => "ASC",
                      "STRICT_SECTION_CHECK" => "N"
                  )
              );

              ?>
          </div>
        </div>
      <?php } ?>

    <div class="row py-5" style="background-color: #ccc;">
      <div class="col-12">
        <h2>Матчи для участия</h2>
          <?

          $curDate = date('Y-m-d H:i:s', time() - 3600);
          global $arrFilterDateTime;
          $arrFilterDateTime = array("ACTIVE" => "Y", ">=PROPERTY_DATE_START" => $curDate, "PROPERTY_PREV_MATCH" => false,);
          //dump($arrFilterDateTime);
          $APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"matches_list", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "Y",
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
		"DETAIL_URL" => "",
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
			3 => "PROPERTY_TOURNAMENT.DETAIL_PAGE_URL",
			4 => "",
		),
		"FILTER_NAME" => "arrFilterDateTime",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "3",
		"IBLOCK_TYPE" => "matches",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"INCLUDE_SUBSECTIONS" => "Y",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "200",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Новости",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "PUBG_LOBBY_ID",
			1 => "DATE_START",
			2 => "TOURNAMENT",
			3 => "",
		),
		"SET_BROWSER_TITLE" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "Y",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "Y",
		"SHOW_404" => "N",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"COMPONENT_TEMPLATE" => "matches_list"
	),
	false
);

          ?>

        <div class="d-flex justify-content-end mt-5">
          <a class="btn btn-info" style="margin-left: auto" href="/matches/">Все матчи</a>
        </div>
      </div>
    </div>
    <div class="row py-5 my-5" style="background-color: #ccc;">

        <?php
        if (!empty($teamID)) { ?>
          <div class="col-md-12">

            <h2>Моя команда <span
                style="color: orangered;"><?php echo $arrResultTeam['NAME'] ?> [<?php echo $arrResultTeam['TAG_TEAM']['~VALUE']; ?>]</span>
            </h2>
            <ul style="list-style: none">
                <?php
                // get list users by id team
                $filter = array("GROUPS_ID" => array(7), ["UF_ID_TEAM" => $teamID]);
                $arParams["SELECT"] = array("UF_*");
                $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
                $imCaptain = false;
                while ($rsUser = $elementsResult->Fetch()) {
                    ?>

                  <li><?php
                      $isCaptain = false;
                      if ($arrResultTeam['AUTHOR']["VALUE"] == $rsUser['ID']) {
                          $isCaptain = true;
                          if ($rsUser['ID'] == CUser::GetID()) {
                              $imCaptain = true;
                          }
                      }
                      echo $isCaptain ? '*captain* <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="yellow" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
              </svg> ' : ''; ?><?php echo $rsUser['LOGIN']; ?></li>
                    <?php
                }
                ?>

            </ul>
              <?php if ($imCaptain) { ?>
                <h2>Управление командой</h2>

                <a href="/management-compositional/" class="btn btn-success">Управление составом</a>
                <a href="/management-games/" class="btn btn-success">Управление играми</a>
              <?php } ?>
          </div>
        <?php } else if (!empty($requestTeamID)) {
            $sendRequestTeam = getTeamById($requestTeamID);
            //dump($sendRequestTeam);
            ?>
          <div class="col-md-12">
            <p>Вы отправили запрос на вступление в команду: </p>
            <div><img src="<?php echo CFile::GetPath($sendRequestTeam["LOGO_TEAM"]["VALUE"]); ?>" alt=""></div>
            <p><?php echo $sendRequestTeam['NAME'] ?></p>
          </div>
        <? } else { ?>
          <div class="col-md-12">
            <h2 class="mb-5">Присоединиться к существующей команде </h2>
              <? $APPLICATION->IncludeComponent(
                  "bitrix:search.title",
                  "search_team",
                  array(
                      "CATEGORY_0" => array(0 => "iblock_teams",),
                      "CATEGORY_0_TITLE" => "",
                      "CATEGORY_0_iblock_teams" => array(0 => "1",),
                      "CHECK_DATES" => "N",
                      "COMPONENT_TEMPLATE" => "bootstrap_v4",
                      "CONTAINER_ID" => "title-search",
                      "CONVERT_CURRENCY" => "N",
                      "INPUT_ID" => "title-search-input",
                      "NUM_CATEGORIES" => "1",
                      "ORDER" => "date",
                      "PAGE" => "#SITE_DIR#search/index.php",
                      "PREVIEW_HEIGHT" => "75",
                      "PREVIEW_TRUNCATE_LEN" => "",
                      "PREVIEW_WIDTH" => "75",
                      "PRICE_CODE" => "",
                      "PRICE_VAT_INCLUDE" => "Y",
                      "SHOW_INPUT" => "Y",
                      "SHOW_OTHERS" => "N",
                      "SHOW_PREVIEW" => "Y",
                      "TEMPLATE_THEME" => "blue",
                      "TOP_COUNT" => "5",
                      "USE_LANGUAGE_GUESS" => "Y"
                  )
              ); ?>
            <h2 class="my-5">Создать новую команду</h2>
            <div class="d-flex flex-column align-items-end">
              <a href="/personal/sozdat-novuyu-komandu/" class="btn btn-info">Create New</a>
            </div>
          </div>
        <?php } ?>
    </div>
    <div class="row py-5" style="background-color: #ccc;">
      <div class="col-12">
        Моя статистика
        <div class="d-flex justify-content-end">
          <a class="btn btn-info" style="margin-left: auto" href="#">Подробнее</a>
        </div>
      </div>
    </div>
  </div>
  week*/?>
  <?php
  if(isset($_SESSION['team_success'])) { ?>
    <div class="alert-container">
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['team_success'];?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
      </div>
    </div>
      <?php
      unset($_SESSION['team_success']);
  } else if(isset($_SESSION['team_error'])){ ?>
    <div class="alert-container">
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo $_SESSION['team_error'];?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
      </div>
    </div>
  <?php }
  unset($_SESSION['team_error']);
  ?>
<?php
if(isset($_SESSION['alert_success'])) { ?>
    <div class="alert-container">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['alert_success'];?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php
    unset($_SESSION['alert_success']);
} else if(isset($_SESSION['alert_error'])){ ?>
    <div class="alert-container">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['alert_error'];?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php }
unset($_SESSION['alert_error']);
?>
  <section class="profile">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-11 col-md-12">
          <div class="profile__avatar-bg">
            <div class="profile__avatar"
                <?php if (!empty($arUser["PERSONAL_PHOTO"])) { ?>
                  style="background-image: url(<?php echo CFile::GetPath($arUser["PERSONAL_PHOTO"]); ?>)"
                <?php } else { ?>
                  style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                <?php } ?>>
              <div class="profile__avatar-rating-bg">
                <div class="profile__avatar-rating">
                    <?php if(!$arUser['UF_RATING']) { ?>
                      300
                    <?php } else { ?>
                        <?php echo $arUser['UF_RATING'];?>
                    <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <div class="profile-info">
            <div class="row profile-info__row align-items-center ">
              <div class="col-md-3 profile-info__item">
                <div class="profile-info__type-account">
                  <?php
                  $resultPrem = isPrem($arUser['UF_DATE_PREM_EXP']);

                  if ($resultPrem <= 0) { ?>
                    <div class="profile-info__type-account-icon profile-info__type-account-icon_base">
                  <i></i>
                </div>
                <div class="profile-info__type-account-description">
                  <div><?=GetMessage('TYPE_ACCOUNT')?></div>
                  <div><a href="<?=SITE_DIR?>subscription-plans/" class="btn-italic"><?=GetMessage('TYPE_ACCOUNT_CHANGE')?></a></div>
                </div>
                  <?php  } else { ?>
                    <div class="profile-info__type-account-icon profile-info__type-account-icon_prem">
                      <i></i>
                    </div>
                    <div class="profile-info__type-account-description">
                      <div><?=GetMessage('TYPE_ACCOUNT_PREMIUM')?></div>
                      <div class="profile-info__day-left"><?php echo num_decline( $resultPrem, 'Остался, Осталось, Осталось', false );?> <?php echo num_decline( $resultPrem, 'день, дня, дней' );?></div>
                    </div>
                  <?php  } ?>
                </div>
              </div>
              <div class="col-md-6 profile-info__item">
                <div class="profile-info__nic">
                  <span><?php echo htmlspecialchars($arUser["LOGIN"]); ?></span>
                  <div class="profile-info__element">
                    <i>
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><defs><style>.cls-1{fill:#100b2e;stroke:#ff0052;stroke-width:2px;}.cls-2{fill:#ff0052;}</style></defs><circle class="cls-1" cx="11" cy="11" r="10"/><path class="cls-2" d="M959.62,535.52a.64.64,0,0,1,1.21,0l.82,2.49a.65.65,0,0,0,.61.44h2.67a.62.62,0,0,1,.37,1.13l-2.16,1.54a.64.64,0,0,0-.23.7l.83,2.5a.63.63,0,0,1-1,.7l-2.16-1.54a.65.65,0,0,0-.75,0L957.69,545a.63.63,0,0,1-1-.7l.82-2.5a.62.62,0,0,0-.23-.7l-2.16-1.54a.62.62,0,0,1,.38-1.13h2.67a.63.63,0,0,0,.6-.44Z" transform="translate(-949.22 -529.43)"/></svg>
                    </i>
                  </div>
                </div>
              </div>
              <div class="col-md-3 profile-info__item text-lg-right text-center">
                <a href="<?=SITE_DIR?>personal/edit/" class="btn__edit">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><path d="M952.88,546.68H953l4.63-.82a.26.26,0,0,0,.15-.07l11.65-11.66a.24.24,0,0,0,.06-.09.18.18,0,0,0,0-.1.2.2,0,0,0,0-.11.24.24,0,0,0-.06-.09l-4.57-4.57a.27.27,0,0,0-.19-.08.28.28,0,0,0-.2.08l-11.65,11.66a.23.23,0,0,0-.08.14l-.81,4.63a.94.94,0,0,0,0,.44,1,1,0,0,0,.24.38A1,1,0,0,0,952.88,546.68Zm1.85-4.8,10-10,2,2-10,10-2.45.43ZM970,549H949.75a.88.88,0,0,0-.88.88v1a.22.22,0,0,0,.22.22h21.56a.22.22,0,0,0,.22-.22v-1A.87.87,0,0,0,970,549Z" transform="translate(-948.87 -529.08)"/></svg>
                  <span><?=GetMessage('PERSONAL_EDIT')?></span>
                </a>
              </div>
            </div>
            <div class="row">
              <div class="col-6 col-md-3">
                <div class="profile-info__next-item">
                  <div>PUBG ID</div>
                  <div>
                    <?php if (empty($arUser['UF_PUBG_ID'])) { ?>
                        <?=GetMessage('PERSONAL_ENTER_ID')?> PUBG ID
                    <?php } else { ?>
                        <?php echo htmlspecialchars($arUser['UF_PUBG_ID'])?>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="profile-info__next-item">
                  <div><?=GetMessage('PERSONAL_MY_MOOD')?></div>
                  <div><?php echo htmlspecialchars($arUser["TITLE"]); ?></div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="profile-info__next-item">
                  <div><?=GetMessage('PERSONAL_REGION')?></div>
                  <div>Введите регион, город</div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="profile-info__next-item">
                  <div><?=GetMessage('PERSONAL_DIVISION')?></div>
                  <div>
                    <div class="profile-info__rating">
                      <span>1</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="profile-info__next-item">
                  <div><?=GetMessage('PERSONAL_LANG')?></div>
                  <div>Русский</div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="profile-info__next-item">
                  <div><?=GetMessage('PERSONAL_ACTIVITY')?></div>
                  <div>с 02:00 до 15:00</div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="profile-info__next-item">
                  <div><?=GetMessage('PERSONAL_DEVICE')?></div>
                  <div>Телефон</div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="profile-info__next-item">
                  <div><?=GetMessage('PERSONAL_TEAM')?></div>
                  <div>
                    <?php if (!empty($teamID)) {
                        echo '<a href="/teams/'.$teamID.'/">';
                        echo htmlspecialchars($arrResultTeam['NAME']) . ' ['. $arrResultTeam['TAG_TEAM']['~VALUE'] . ']';
                        echo '</a>';
                    } else { ?>
                        <?=GetMessage('PERSONAL_TEAM_NO_SELECTED')?>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <?php if (empty($teamID)) { ?>
              <div class="profile-info__wrap-btn-invite">
                <a href="#" class="btn" data-toggle="modal" data-target="#createTeam"><?=GetMessage('PERSONAL_CREATE_TEAM')?></a>
                <a href="<?=SITE_DIR?>teams/" class="btn"><?=GetMessage('PERSONAL_FIND_TEAM')?></a>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php
//require_once($_SERVER["DOCUMENT_ROOT"] . "/personal/mygames.php");


?>
<?php
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
function getChainMatchesByParentId($parentId)
{
    $chainMatches = [];
    $chainMatches[] = $parentId;
    do {
        $match = getMatchByParentId($parentId);

        if ($match != null) {
            $nextMatch = true;
            $chainMatches[] = $match['ID'];
            $parentId = $match['ID'];
        } else {
            $nextMatch = false;
        }


    } while($nextMatch == true);
    return $chainMatches;
}
$myMatchIds = getSquadsWhereIm($userID);

$newMatchIds = [];
foreach ($myMatchIds as $id) {
    $newMatchIds[] = getChainMatchesByParentId($id);
}


function getParticipationByMatchIdMyMatches($idMatch)
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
if (!empty($newMatchIds)) {
    //dump($newMatchIds); ?>

  <section class="game-schedule bg-blue-lighter">
    <div class="container">
      <h2 class="game-schedule__heading text-center"><?=GetMessage('MY_GAMES_HEADLINE')?></h2>
      <div class="game-schedule-table">
        <div class="flex-table">
          <div class="flex-table--header bg-blue-lighter">
            <div class="flex-table--categories">
              <span><?=GetMessage('MY_GAMES_TYPE')?></span>
              <span><?=GetMessage('MY_GAMES_TITLE')?></span>
              <span><?=GetMessage('MY_GAMES_DATE_EVENT')?></span>
              <span><?=GetMessage('MY_GAMES_RATING')?></span>
              <span><?=GetMessage('MY_GAMES_MODE')?></span>
              <span><?=GetMessage('MY_GAMES_COMMENTATOR')?></span>
            </div>
          </div>
          <div class="flex-table--body">
        <?php
        foreach ($newMatchIds as $ids) {
            //dump($myMatchIds);
            global $arrFilterIdsMatches;
            $curDate = date('Y-m-d H:i:s', time() - 7200);
            $arrFilterIdsMatches = array("ID" => $ids, ">=PROPERTY_DATE_START" => $curDate);

            $APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"my_matches_list", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "Y",
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
		"DETAIL_URL" => SITE_DIR.'game-schedule/#ELEMENT_CODE#/',
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
			3 => "PROPERTY_TOURNAMENT.DETAIL_PAGE_URL",
			4 => "PROPERTY_STREAMER.NAME",
			5 => "",
		),
		"FILTER_NAME" => "arrFilterIdsMatches",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "3",
		"IBLOCK_TYPE" => "matches",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"INCLUDE_SUBSECTIONS" => "Y",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "20",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Новости",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "PUBG_LOBBY_ID",
			1 => "DATE_START",
			2 => "COUTN_TEAMS",
			3 => "URL_STREAM",
			4 => "TYPE_MATCH",
			5 => "STAGE_TOURNAMENT",
			6 => "TOURNAMENT",
			7 => "",
		),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "Y",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "N",
		"SHOW_404" => "Y",
		"SORT_BY1" => "PROPERTY_DATE_START",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"COMPONENT_TEMPLATE" => "my_matches_list",
		"FILE_404" => ""
	),
	false
);?>
        <?php } ?>

        </div>
      </div>
    </div>
        <div class="mt-3 text-center">
            <a href="https://t.me/joinchat/3zyL7w5RL7czZmYy" class="btn" target="_blank"><?=GetMessage('MY_GAMES_SUPPORT')?></a>
        </div>
    </div>
  </section>
<?php } ?>
  <section class="promo-code mt-5 <?php if ($resultPrem >= 0) echo 'mb-5'; ?>">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-11 col-md-12">
          <div class="promo-code__wrapper">
            <h3><?=GetMessage('MY_GAMES_PROMO_CODE')?></h3>
            <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
              <?=bitrix_sessid_post()?>
              <div class="form-field">
                <div class="form-field__with-btn">
                  <input type="text" class="form-field__input" name="promocode" value="" autocomplete="off" placeholder="<?=GetMessage('MY_GAMES_PROMO_CODE_PLACEHOLDER')?>">
                  <button class="btn" type="submit" name=""><?=GetMessage('MY_GAMES_PROMO_CODE_BTN')?></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

    <? if ($resultPrem <= 0) { ?>
    <section class="banner">
        <div class="container">
            <div class="banner__bg">
                <div class="banner__content">
                    <h2><?=GetMessage('BANNER_TITLE')?></h2>
                    <div class="banner__content-btn">
                        <a href="<?=SITE_DIR?>subscription-plans/" class="btn"><?=GetMessage('BANNER_BUTTON')?></a>
                    </div>
                </div>
                <div class="banner__img">
                    <img src="<?= SITE_TEMPLATE_PATH; ?>/dist/images/banner-img-2.png" alt="banner">
                </div>
            </div>
        </div>
    </section>
    <? } ?>

  <section class="game-schedule bg-blue-lighter">
    <div class="container">
      <h2 class="game-schedule__heading text-center"><?=GetMessage('GS_HEADER')?></h2>
      <div class="game-schedule-table">
        <div class="flex-table">
          <div class="flex-table--header bg-blue-lighter">
            <div class="flex-table--categories">
                <span><?=GetMessage('MY_GAMES_TYPE')?></span>
                <span><?=GetMessage('MY_GAMES_TITLE')?></span>
                <span><?=GetMessage('MY_GAMES_DATE_EVENT')?></span>
                <span><?=GetMessage('MY_GAMES_RATING')?></span>
                <span><?=GetMessage('MY_GAMES_MODE')?></span>
                <span><?=GetMessage('MY_GAMES_COMMENTATOR')?></span>
            </div>
          </div>
          <div class="flex-table--body">
        <?php
        $curDate = date('Y-m-d H:i:s', time()-3600);
        GLOBAL $arrFilterDateTime;
        $arrFilterDateTime=Array(
            "ACTIVE" => "Y",
            ">=PROPERTY_DATE_START" => $curDate,
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
		"NEWS_COUNT" => "5",
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
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"COMPONENT_TEMPLATE" => "game-schedule",
		"FILE_404" => ""
	),
	false
);
        ?>
          </div>
        </div>
        <div class="game-schedule-table__show-more">
          <div class="mt-3">
            <a href="<?=SITE_DIR?>game-schedule/" class="btn"><?=GetMessage('GS_BTN')?></a>
          </div>
        </div>
      </div>
    </div>
  </section>
    <?php  if (empty($teamID)) { // !empty
      $players = getCoreTeam($teamID);
    $points = countPointsAllUsers();
      // ставим капитана на первое место
      foreach ($players as $k => $player) {
          if ($arrResultTeam['AUTHOR']["VALUE"] == $player['ID']) {
              $players = [$k => $player] + $players;
              break;
          }
      }
      ?>
      <section class="py-10">
    <div class="container">
      <h2 class="core-team__heading">Моя команда</h2>
      <h3 class="core-team__sub-heading"><?php echo $arrResultTeam['NAME'] ?> [<?php echo $arrResultTeam['TAG_TEAM']['~VALUE']; ?>]</h3>
      <div class="core-team__heading-core-team">Основной Состав</div>
      <div class="core-team">
        <div class="flex-table">
          <div class="flex-table--header bg-default">
            <div class="flex-table--categories">
              <span>Игрок</span>
              <span>Количество игр</span>
              <span>Киллы</span>
              <span>Total</span>
              <span>Рейтинг</span>
            </div>
          </div>
          <div class="flex-table--body">
            <?php foreach ($players as $player) {
                $cntMatches = '..';
                $kills = '..';
                $total = '..';
                if( isset($points[$player['ID']]) ){
                    $cntMatches = ceil($points[$player['ID']]['count_matches']);
                    $kills = ceil($points[$player['ID']]['kills']);
                    $total = ceil($points[$player['ID']]['total']);
                }
              ?>
            <div class="flex-table--row">
                <span>
                  <div class="core-team__user">
                    <div class="core-team__user-avatar"
                         <?php if (!empty($player["PERSONAL_PHOTO"])) { ?>
                           style="background-image: url(<?php echo CFile::GetPath($player["PERSONAL_PHOTO"]); ?>)"
                         <?php } else { ?>
                           style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                         <?php } ?>>
                      <?php if ($arrResultTeam['AUTHOR']["VALUE"] == $player['ID']) { ?>
                        <div class="core-team__user-avatar-icon_captain">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                          <circle  cx="11" cy="11" r="10"/>
                          <path d="M682.39,379.09a.65.65,0,0,1,1.22,0l.82,2.5a.65.65,0,0,0,.61.43h2.66a.62.62,0,0,1,.38,1.13l-2.16,1.54a.63.63,0,0,0-.23.71l.82,2.49a.63.63,0,0,1-1,.7l-2.16-1.54a.63.63,0,0,0-.74,0l-2.16,1.54a.63.63,0,0,1-1-.7l.82-2.49a.63.63,0,0,0-.23-.71l-2.16-1.54a.62.62,0,0,1,.38-1.13H681a.65.65,0,0,0,.61-.43Z" transform="translate(-672 -373)"/>
                        </svg>
                      </div>
                      <?php } ?>
                    </div>
                    <a href="/players/<?php echo $player['ID'].'_'.$player['LOGIN'].'/';?>" class="core-team__user-link"><?php echo $player['LOGIN'];?></a>
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
                  <div class="core-team__param">Total</div>
                  <?php echo $total;?>
                </span>
              <span class="core-team__param-wrap">
                  <div class="core-team__param">Рейтинг</div>
                  <?php if(!$player['UF_RATING']) { ?>
                    300
                  <?php } else { ?>
                      <?php echo $player['UF_RATING'];?>
                  <?php } ?>
                </span>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <?php if($isCaptain) { ?>
        <div class="core-team__btn">

          <a href="<?=SITE_DIR?>management-compositional/" class="btn">Управление составом</a>
          <a href="<?=SITE_DIR?>Rmanagement-games/" class="btn">Управление играми</a>
          <!--<a href="#" class="btn__edit mr-3">
           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><path d="M952.88,546.68H953l4.63-.82a.26.26,0,0,0,.15-.07l11.65-11.66a.24.24,0,0,0,.06-.09.18.18,0,0,0,0-.1.2.2,0,0,0,0-.11.24.24,0,0,0-.06-.09l-4.57-4.57a.27.27,0,0,0-.19-.08.28.28,0,0,0-.2.08l-11.65,11.66a.23.23,0,0,0-.08.14l-.81,4.63a.94.94,0,0,0,0,.44,1,1,0,0,0,.24.38A1,1,0,0,0,952.88,546.68Zm1.85-4.8,10-10,2,2-10,10-2.45.43ZM970,549H949.75a.88.88,0,0,0-.88.88v1a.22.22,0,0,0,.22.22h21.56a.22.22,0,0,0,.22-.22v-1A.87.87,0,0,0,970,549Z" transform="translate(-948.87 -529.08)"/></svg>
           <span>Управление составом</span>
         </a>-->
          <!--<a href="#" class="btn__edit">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><path d="M952.88,546.68H953l4.63-.82a.26.26,0,0,0,.15-.07l11.65-11.66a.24.24,0,0,0,.06-.09.18.18,0,0,0,0-.1.2.2,0,0,0,0-.11.24.24,0,0,0-.06-.09l-4.57-4.57a.27.27,0,0,0-.19-.08.28.28,0,0,0-.2.08l-11.65,11.66a.23.23,0,0,0-.08.14l-.81,4.63a.94.94,0,0,0,0,.44,1,1,0,0,0,.24.38A1,1,0,0,0,952.88,546.68Zm1.85-4.8,10-10,2,2-10,10-2.45.43ZM970,549H949.75a.88.88,0,0,0-.88.88v1a.22.22,0,0,0,.22.22h21.56a.22.22,0,0,0,.22-.22v-1A.87.87,0,0,0,970,549Z" transform="translate(-948.87 -529.08)"/></svg>
            <span>Управление играми</span>
          </a>-->
        </div>
      <?php } ?>
    </div>
  </section>
    <?php } ?>

    <?php if (empty($teamID)) { ?>
  <div class="modal fade " id="createTeam" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-modal-close" data-dismiss="modal" aria-label="Close">
            <i></i>
          </button>
        </div>
        <div class="modal-body">
          <h3 class="modal-body__title">Создание команды</h3>
          <form class="form-team" action="<?= POST_FORM_ACTION_URI; ?>" method="post" enctype="multipart/form-data">
              <?=bitrix_sessid_post()?>
            <div class="form-field">
              <label for="nameTeam" class="form-field__label">Название команды</label>
              <input type="text" class="form-field__input" name="nameTeam" value="" autocomplete="off" id="nameTeam" placeholder="Введите название команды">
            </div>
            <div class="form-field">
              <label for="tagTeam" class="form-field__label">Тег команды</label>
              <input type="text" class="form-field__input" name="tagTeam" value="" autocomplete="off" id="tagTeam" placeholder="Введите тег команды">
            </div>
            <!--<div class="form-field">
              <label for="mottoTeam" class="form-field__label">Девиз команды</label>
              <input type="text" class="form-field__input" name="mottoTeam" value="" autocomplete="off" id="mottoTeam" placeholder="Введите девиз команды">
            </div>-->
            <div class="form-field">

              <input type="file" class="form-field__input-file inputFile" data-multiple-caption="выбрано {count} файла(ов)" name="logoTeam"  autocomplete="off"  id="logoTeam" >
              <label for="logoTeam" class="form-field__upload-file">
                <i></i><span>Прикрепить логотип команды</span> <div class="fileUploaded"></div>
              </label>
            </div>
            <div class="form-field">
              <label for="descriptionTeam" class="form-field__label">Описание команды</label>
              <textarea name="descriptionTeam" id="descriptionTeam" class="form-field__textarea" cols="30" rows="3"  placeholder="Введите описание команды"></textarea>
            </div>
            <input type="hidden" name="user_id" value="<?php echo $userID;?>"><br>
            <div class="modal-body__btn">
              <button type="submit" class="btn mr-3" name="createTeam">Создать команду</button>
              <button type="button" class="btn btn_border" data-dismiss="modal">Отмена</button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
    <?php } ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>