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

if (isset($_POST['join_submit']) && check_bitrix_sessid()) {

    $user = new CUser;
    $fields = array(
        //"NAME"              => "Сергей",
        "UF_REQUEST_ID_TEAM" => trim(strip_tags($_POST['team_id']+0)),
    );
    if ($user->Update($userID, $fields)) {
        createSession('team_success', GetMessge('TMS_TEAM_SUCCESS'));
        LocalRedirect(SITE_DIR . "teams/" . $arResult['ID'] . '/');
    } else {
        echo 'Error: ' . $user->LAST_ERROR;
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
                </div>
                <?php  } else if (!empty($teamID)) {
              $team = getTeamById($teamID);
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
                                        <button class="btn" type="submit" name="join_submit"><?=GetMessage('TMS_TEAM_SENT_REQUEST')?></button>
                  </form>
                </div>
                <?php } ?>
            <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
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