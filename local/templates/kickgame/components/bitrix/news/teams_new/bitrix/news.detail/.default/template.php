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

$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];
$requestTeamID = $arUser['UF_REQUEST_ID_TEAM'];
$chick_user = CustomChick::getUserBudget($arUser["ID"], CustomChick::CHICK_CURRENCY_CODE);
$chick_user=number_format($chick_user, 0, "", "");
$team = getTeamById($teamID);
$balanceTeamChicks = $team['BALANCE']['VALUE']+0;
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['rand']==$_SESSION['rand'] && check_bitrix_sessid()) {
    $quantity = 0;
    if(isset($_POST["quantity"]))
    {
        $quantity   = trim(strip_tags($_POST["quantity"]));
    }
    if(empty($quantity)) {
        createSession('management-players_error', 'Введи значение в поле количество');
        LocalRedirect('');
    } else if($quantity+0 > $chick_user) {
        createSession('management-players_error', 'Недостаточно средств. Пополни личный счет');
        LocalRedirect('');
    }

    // 33 - id chicks
    // 36 - перевод на счет команды
    // 34 приход
    if(addRecordInHLTeamBalanceHistory($arUser, $balanceTeamChicks, $quantity+0, 33, 36, 34))  {
        // спиши с баланса пользователя
        CustomChick::setUserBudget($arUser["ID"], CustomChick::CHICK_CURRENCY_CODE, -$quantity+0);
        // обнови поле баланса
        CIBlockElement::SetPropertyValueCode($teamID+0, "BALANCE", $balanceTeamChicks+$quantity+0);
        $fields = array(
            "UF_TRANSACTION_ID" => '',
            "UF_QUANTITY" => -$quantity+0,
            "UF_OPERATION_NAME" => "Перевод на счет команды",
            "UF_OPERATION_TYPE" => CustomChick::OPERATION_TYPE[0],
            "UF_DATE" => date("d.m.Y H:i:s"),
            "UF_CURRENCY_TYPE" => CustomChick::CHICK_CURRENCY_CODE,
            "UF_USER_ID" => $arUser['ID']
        );
        CustomChick::addTransaction($fields);

        // записываем в сесию для диалогового окна
        createSession('team-balance__success', '<div class="alert-content__heading">Баланс команды пополнен</div>
        <div class="alert-content__description">Вы успешно пополнили счет команды на '.$quantity.' Chicks</div>');

        LocalRedirect(SITE_DIR . "teams/" . $arResult['ID'] . '/');

    }
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
      $row['total'] = empty($arResult['PROPERTIES']['RATING']["VALUE"]) ? $row['total'] + 300 : $row['total'] + ceil($arResult['PROPERTIES']['RATING']["VALUE"]);
      return $row;
    }
    return false;
}

$ratingTeam = getRating($arResult);
/*function countTeamsRating(){
    GLOBAL $DB;
    $points = false;
    $sql = 'SELECT t.PROPERTY_15 AS teamID, sum(t.PROPERTY_18) AS total, sum(t.PROPERTY_17) AS kills
      FROM b_iblock_element_prop_s5 AS t
      INNER JOIN b_iblock_element_prop_s3 AS m ON t.PROPERTY_14 = m.IBLOCK_ELEMENT_ID
      WHERE m.PROPERTY_23 = 6
      GROUP BY t.PROPERTY_15';
    $sql = 'SELECT r.* FROM ('.$sql.') AS r ORDER BY r.total DESC';
    $res = $DB->Query($sql);
    $pos = 1;
    while( $row = $res->Fetch() ) {
        $points[ $row['teamID'] ] = [
            'teamID' => $row['teamID'],
            'kills' => ceil($row['kills']),
            'rating' => ceil($row['total']) + 300,
            'total' => ceil($row['total']-$row['kills']),
        ];
        $points[ $row['teamID'] ]['ratingPosition'] = $pos++;
    }
    return $points;
}
*/
//dump(countTeamsRating());


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
$isCaptain = isCaptain($userID, $arResult['ID']);

if ($_POST["activate"] == "y" && check_bitrix_sessid()) {

    $user = new CUser;
    $fields = array(
        //"NAME"              => "Сергей",
        "UF_REQUEST_ID_TEAM" => trim(strip_tags($_POST['team_id']+0)),
    );
    if ($user->Update($userID, $fields)) {
        createSession('team_success', GetMessage('TMS_TEAM_SUCCESS'));
        LocalRedirect(SITE_DIR . "teams/" . $arResult['ID'] . '/');
    } else {
        echo 'Error: ' . $user->LAST_ERROR;
    }
}
if($_POST["deactivate"] == "y" && intval($_POST["teamId"]) && check_bitrix_sessid())
{
    $user = new CUser;
    $fields = array(
        "UF_REQUEST_ID_TEAM" => "",
    );
    if ($user->Update($userID, $fields))
    {
        createSession('team_success', "Запрос отменен.");
        LocalRedirect(SITE_DIR . "teams/" . $arResult['ID'] . '/');
    }
    else
    {
        echo "Error: " . $user->LAST_ERROR;
    }
}
$requestTeamID = $arUser['UF_REQUEST_ID_TEAM'];

?>
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

} elseif (isset($_SESSION["team_error"])) {?>
    <div class="alert-container">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['team_error'];?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
        <?php unset($_SESSION['team_error']);} ?>
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
<?php if(isset($_SESSION['team-balance__success'])) { ?>
    <div class="alert alert-bottom alert-dismissible fade show" role="alert">
        <div class="alert-content">
            <div class="alert-content__img">
                <img src="<?php echo CFile::GetPath($arUser["PERSONAL_PHOTO"]); ?>" alt="alert">
            </div>
            <div class="alert-content__message">
                <?php echo $_SESSION['team-balance__success']; ?>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
    </div>
<?php }
unset($_SESSION['team-balance__success']);
?>
<section class="team py-8">
  <div class="container">
    <a href="<?= SITE_DIR ?>teams/" class="btn-italic-icon">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 11.62">
        <path d="M955.22,534.89a1,1,0,0,1,0,1.33l-3,3.27h18.2a.94.94,0,0,1,0,1.88h-18.2l3,3.27a1,1,0,0,1,0,1.32.81.81,0,0,1-1.21,0l-4.49-4.88h0a1,1,0,0,1,0-1.33h0l4.49-4.87A.81.81,0,0,1,955.22,534.89Z" transform="translate(-949.26 -534.62)"/>
      </svg> <?= GetMessage('TMS_BACK') ?>
    </a>

    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-12">
        <div class="team__logo-bg">
          <div class="team__logo" style="background-image: url(<?=$arResult["DETAIL_PICTURE"]["SRC"]?>">
            <div class="team__logo-rating-bg">
              <div class="team__logo-rating">
                  <?php echo $ratingTeam != false ? $ratingTeam['total'] : 300;?>
              </div>
            </div>
          </div>
        </div>
        <div class="team-info">
          <h2 class="team-info__name"><?php echo $arResult['PROPERTIES']["NAME_TEAM"]['VALUE']; ?> [<?php echo $arResult['PROPERTIES']["TAG_TEAM"]['VALUE']; ?>]</h2>
          <?
          $teamId = $arResult["ID"];
          $users = getCoreTeam($teamId);
          $userId = $USER->GEtID();
          $dateFinish = "";
          $isMyCommand = false;
          if($users[0]["UF_DATE_PREM_EXP"])
          {
              $dateFinish = $users[0]["UF_DATE_PREM_EXP"];
          }
          foreach ($users as $k => $v)
          {
              if($v["ID"] == $userId)
              {
                  $isMyCommand = true;
                  break;
              }
          }
          ?>
          <?if($isMyCommand):?>
              <?$userProductGroups = CustomSubscribes::getActualUserSubscribeGroup($userId);?>
                <?if(empty($userProductGroups)):?>
                  <div class="date-finish">
                      <?=GetMessage('TEAM_NOT_SUBSCRIBED')?>
                  </div>
                <?else:?>
                  <div class="date-finish">
                      Дата окончания подписки команды: <?= $dateFinish;?>
                  </div>
                <?endif;?>
              <div class="buttons">
                  <a class="btn btn_border extend" href="<?=SITE_DIR?>subscription-plans/">
                      <?=GetMessage('TEAM_BUTTON_EXTEND')?>
                  </a>
                  <a class="conditions" href="#">
                      Условия использования подписки
                  </a>
              </div>
          <?endif;?>
          <div class="team-info__description">
              <?php echo $arResult['PROPERTIES']["DESCRIPTION_TEAM"]['VALUE']["TEXT"]; ?>
          </div>
            <?php if(CUser::IsAuthorized()) { ?>
                <?php if (!empty($requestTeamID)) {
                    $sendRequestTeam = getTeamById($requestTeamID);
                    ?>
                <div class="team-info__description">
                  <br>
                  <p><?=GetMessage('TMS_SEND_REQUEST_TEAM')?></p>
                  <p><?php echo '<a href="'.SITE_DIR.'teams/'.$requestTeamID.'/">'.$sendRequestTeam['NAME'].'</a>'; ?></p>
                  <br/>
                  <form action="<?= POST_FORM_ACTION_URI;?>" method="post">
                    <?=bitrix_sessid_post()?>
                    <input type="hidden" name="teamId" value="<?= $arResult["ID"];?>">
                    <input type="hidden" name="deactivate" value="y">
                    <button class="btn" type="submit" name="join_submit">Отменить заявку</button>
                  </form>
                </div>
                <?php  } else if (!empty($teamID)) {

            ?>
            <div class="team-info__description">
              <br>
                <?php
                  //dump($isCaptain);
               if (!$isCaptain) {
                if ($teamID != $arResult['ID']) { ?>
              <p><?=GetMessage('TMS_TEAM_ALREADY_HAVE')?></p>
              <p><a href="<?=SITE_DIR?>teams/<?php echo $teamID;?>/"><?php echo $team['NAME'] ?></a></p>
                <?php } else { ?>
                    <a href="<?=SITE_DIR?>teams/<?php echo $arResult['ID'];?>/?leaveteam=<?php echo md5(strtotime('now'))?>" class="btn"><?=GetMessage('TMS_TEAM_QUIT')?></a>
                <?php }
                } else {
                    echo GetMessage('TMS_TEAM_CAPITAN');
               }
                ?>
            </div>
          <?php } else { ?>
                <div class="team-info__btn-edit">
                  <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                    <?=bitrix_sessid_post()?>
                    <input type="hidden" name="team_id" value="<?php echo $arResult['ID']?>">
                      <input type="hidden" name="activate" value="y">
                    <button class="btn" type="submit" name="join_submit"><?=GetMessage('TMS_TEAM_SENT_REQUEST')?></button>
                  </form>
                </div>
                <?php } ?>
            <?php } ?>
            <?php if($isMyCommand) { ?>
          <div class="profile-info__line"></div>

          <div class="team-info__balance">
            <div class="profile-info__balance-wrap">
              <div class="profile-info__balance">
                <i></i>
                <div class="profile-info__balance-value">
                  <span><?php echo $team['BALANCE']['VALUE']+0; ?></span>
                  <span>Chicks</span>
                </div>
              </div>
              <!--<div class="profile-info__balance">
                <i></i>
                <div class="profile-info__balance-value">
                  <span>5 015</span>
                  <span>wincoin</span>
                </div>
              </div>-->
            </div>
            <div class="profile-info__balance-top-up">
              <a href="#" class="btn" data-toggle="modal" data-target="#team-info__add_ballance">Пополнить счет</a>
            </div>
          </div>

          <div class="team-info__balance-operation">
            <div class="profile-info__balance-operation">
              <a href="/management-compositional/account-history/" class="btn-italic-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 20"><path d="M694.75,380.63l-6-6.36a.81.81,0,0,0-1.21,0,.93.93,0,0,0-.25.64v2.73h-6a.91.91,0,0,0,0,1.81h6.85a.88.88,0,0,0,.86-.9V377.1l3.93,4.17L689,385.44V384a.88.88,0,0,0-.86-.91h-9.43v-2.73a.88.88,0,0,0-.85-.91.84.84,0,0,0-.61.27l-6,6.36a1,1,0,0,0,0,1.29l6,6.36a.84.84,0,0,0,.61.27.78.78,0,0,0,.33-.07.9.9,0,0,0,.52-.84v-2.73h6a.91.91,0,0,0,0-1.81h-6.85a.88.88,0,0,0-.86.9v1.45l-3.93-4.17,3.93-4.17V384a.88.88,0,0,0,.86.91h9.43v2.73a.88.88,0,0,0,.85.91.84.84,0,0,0,.61-.27l6-6.36A1,1,0,0,0,694.75,380.63Z" transform="translate(-671 -374)"></path></svg>
                История операций
              </a>
              <a href="#" class="btn-italic-icon" data-toggle="modal" data-target="#withdraw">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 20"><path d="M681.61,374.36a10,10,0,0,1,6.23,1.15,10,10,0,0,1-4.27,18.73,10,10,0,0,1-10-6.66,1,1,0,1,1,1.88-.66,8,8,0,1,0,1.9-8.32l0,0-2.8,2.63H678a1,1,0,0,1,0,2h-6a1,1,0,0,1-1-1v-6a1,1,0,0,1,2,0v3.69l2.94-2.77A10,10,0,0,1,681.61,374.36Z" transform="translate(-671 -374.26)"></path></svg>
                Снять со счета
              </a>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
$_SESSION['rand']=rand()." - ".rand()." - ".rand;
?>
    <div class="modal fade" id="team-info__add_ballance" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-modal-close" data-dismiss="modal" aria-label="Close">
                        <i></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 class="modal-body__title">Пополнение счета команды</h3>
                    <div class="modal-body__content mb-3">
                        <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                            <?=bitrix_sessid_post()?>
                            <input type='hidden' name='rand' value='<?=$_SESSION['rand']?>' />
                            <div class="form-field">
                                <label for="qtyChicks" class="form-field__label">Количество:</label>
                                <input type="number" class="form-field__input form-field__input_big"
                                       autocomplete="off" id="qtyChicks"
                                       maxlength="20"
                                       placeholder="13"
                                       name="quantity"
                                       data-max="<?=$chick_user?>"
                                       step="число"
                                       min="1"
                                       max="<?=$chick_user?>>
                  </div>
                  <div class="text-center">
                                <button type="submit" class="btn mt-5 btn-wide" <?php if($chick_user == 0) { ?> disabled<?php } ?>>Пополнить счет</button>
                            </div>
                        </form>
                    </div>

                </div>
                <?php if($chick_user == 0) { ?>
                    <div class="modal-footer__new modal-footer__new_error">
                        <i></i> Недостаточно средств. <a href="/personal/pay/">Пополнить</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php
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
$teamID = $arResult['ID'];
if ( $teamID ) {
    $arrResultTeam = getTeamById($teamID);
    $players = getCoreTeam($teamID);
    //$points = countPointsAllUsers();
   $results = getQtyPlayedGames($arResult['ID']);
   //dump($results);
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
        <h2 class="core-team__heading"><?=GetMessage('TMS_HEADING')?></h2>
      <div class="core-team">
        <div class="flex-table">
          <div class="flex-table--header bg-default">
            <div class="flex-table--categories">
                <span><?=GetMessage('TMS_PLAYER')?></span>
                <span><?=GetMessage('TMS_GAMES')?></span>
                <span><?=GetMessage('TMS_KILLS')?></span>
                <!--<span><?/*=GetMessage('TMS_TOTAL')*/?></span>-->
                <span><?=GetMessage('TMS_RATING')?></span>
            </div>
          </div>
          <div class="flex-table--body">
              <?php foreach ($players as $player) {
                  $cntMatches = '..';
                  $kills = '..';
                  $total = '..';
                  if( isset($results) ){
                      $cntMatches = ceil($results[$player['ID']]['count_matches']);
                      $kills = ceil($results[$player['ID']]['kills']);
                      $total = ceil($results[$player['ID']]['total']);
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
                    <a href="<?=SITE_DIR?>players/<?php echo $player['ID'].'_'.$player['LOGIN'].'/';?>" class="core-team__user-link"><?php echo $player['LOGIN'];?></a>
                  </div>
                </span>
                  <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('TMS_GAMES')?></div>
                  <?php echo $cntMatches;?>
                </span>
                  <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('TMS_KILLS')?></div>
                  <?php echo $kills;?>
                </span>
                  <!--<span class="core-team__param-wrap">
                  <div class="core-team__param">Total</div>
                  <?php// echo $total;?>
                </span>-->
                  <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('TMS_RATING')?></div>
                      <?php echo $total;?>
                </span>
                </div>
              <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>




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
    </style>
  <section class="game-schedule bg-blue-lighter">
    <div class="container">
        <h2 class="game-schedule__heading text-center"><?=GetMessage('TMS_TABLE_HEADING')?></h2>
      <div class="game-schedule-table">
        <div class="flex-table-new">
          <div class="flex-table--header bg-blue-lighter">
            <div class="flex-table--categories">
                <span><?=GetMessage('TMS_TABLE_TYPE')?></span>
                <span><?=GetMessage('TMS_TABLE_TITLE')?></span>
                <span><?=GetMessage('TMS_TABLE_DATE')?></span>
                <span><?=GetMessage('TMS_TABLE_RATING')?></span>
                <span><?=GetMessage('TMS_TABLE_MODE')?></span>
                <span><?=GetMessage('TMS_TABLE_COMMENTATOR')?></span>
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
                      "DETAIL_URL" => SITE_DIR."game-schedule/#ELEMENT_CODE#/",
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
    <h2 class="game-schedule__heading text-center">'.GetMessage('TMS_NOT_STATISTICS').'</h2>
    </div></section>';

} /* end count matches */
?>


<?php
} /* end teamID */
?>