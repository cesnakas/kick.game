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
/*function countTeamsRating(){
  GLOBAL $DB;
  $points = false;
  // m.PROPERTY_23 = 6 prak
  //16 prizeplace
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

$points = countTeamsRating();
//dump( $points ); */

function countAllTeams(){
  GLOBAL $DB;
  $sql = 'SELECT count(u.IBLOCK_ELEMENT_ID) as c 
			FROM  b_iblock_element_prop_s1 AS u
			INNER JOIN b_iblock_element AS a ON a.ID = u.IBLOCK_ELEMENT_ID 
            WHERE a.ACTIVE = "Y"';

  $res = $DB->Query($sql);
  $count = [];
  $count = $res->Fetch();

  return $count['c'];
}

function showTeams(){
    global $DB;
    global $APPLICATION;
    //пагинация
    $count_tema=20; // выводим по 5 Записей на страницу
    //создаем объект пагинации
    $nav = new \Bitrix\Main\UI\PageNavigation("nav-more-teams");
    $nav->allowAllRecords(false)
        ->setPageSize($count_tema)
        ->initFromUri();

    $count_zap=countAllTeams(); // сделать запрос для определения количества всех строк
    // в sql вставляем limit и Offset
    $strSql = 'SELECT a.ACTIVE, u.PROPERTY_19 as avatar, u.PROPERTY_21 AS name, u.IBLOCK_ELEMENT_ID as id_team, IF(total IS NOT Null,total, 0) + IF(u.PROPERTY_31 IS NOT Null, u.PROPERTY_31, 300) as total, kills 
                                FROM b_iblock_element_prop_s1 as u 
                                LEFT JOIN (SELECT t.PROPERTY_15 AS teamID, sum(t.PROPERTY_18) AS total, sum(t.PROPERTY_17) AS kills 
                                FROM b_iblock_element_prop_s5 AS t 
                                INNER JOIN b_iblock_element_prop_s3 AS m ON t.PROPERTY_14 = m.IBLOCK_ELEMENT_ID 
                                WHERE m.PROPERTY_23 = 6 
                                GROUP BY t.PROPERTY_15) AS r1 ON r1.teamID = u.IBLOCK_ELEMENT_ID 
                                INNER JOIN b_iblock_element AS a ON a.ID = u.IBLOCK_ELEMENT_ID 
                                WHERE a.ACTIVE = "Y"
                                ORDER BY total DESC, kills DESC LIMIT '.$nav->getLimit().'  OFFSET '.$nav->getOffset() ; //
    $rsData = $DB->Query($strSql);
    $i = $nav->getOffset();
    while($el = $rsData->fetch()){
        $i+=1;
        showRow($el, $i);
    }

    $nav->setRecordCount($count_zap);
    $APPLICATION->IncludeComponent("bitrix:main.pagenavigation", ".default", Array(
        "NAV_OBJECT" => $nav,
        "SEF_MODE" => "N",
    ),
        false
    );
}

function searchTeams($search = ""){
  global $DB;
    $search = trim(strip_tags($search));

    $search = "WHERE name LIKE '%" . $search . "%' ";

    $strSql = 'SELECT @rank:=0';
    $rsData = $DB->Query($strSql);

  $strSql = 'SELECT rank, avatar, name, id_team, total, kills FROM (SELECT @rank:=@rank+1 as rank, avatar, name, id_team, total, kills FROM (SELECT u.PROPERTY_19 as avatar, u.PROPERTY_21 AS name, u.IBLOCK_ELEMENT_ID as id_team, IF(total IS NOT Null,total, 0) + IF(u.PROPERTY_31 IS NOT Null, u.PROPERTY_31, 300) as total, kills 
                                FROM b_iblock_element_prop_s1 as u 
                                LEFT JOIN (SELECT t.PROPERTY_15 AS teamID, sum(t.PROPERTY_18) AS total, sum(t.PROPERTY_17) AS kills 
                                FROM b_iblock_element_prop_s5 AS t 
                                INNER JOIN b_iblock_element_prop_s3 AS m ON t.PROPERTY_14 = m.IBLOCK_ELEMENT_ID 
                                WHERE m.PROPERTY_23 = 6 
                                GROUP BY t.PROPERTY_15) AS r1 ON r1.teamID = u.IBLOCK_ELEMENT_ID
                                INNER JOIN b_iblock_element AS a ON a.ID = u.IBLOCK_ELEMENT_ID 
                                WHERE a.ACTIVE = "Y"
                                ORDER BY total DESC, kills DESC) as r2) as r3 
                                ' . $search;
  $rsData = $DB->Query($strSql);
  $i = 0;

  while($el = $rsData->fetch()){
    $i+=1;
    showRow($el, $el['rank']);
  }
    if($i == 0){
        echo GetMessage('ALERTS_SEARCH_NOT_FOUND');
    }

}

function showRow($team, $rank){
  $kills = isset($team["kills"]) ? ceil($team["kills"]) : '..'; ?>

  <div class="flex-table--row">
                  <span>
                    <div class="match-participants__team">
                      <div class="match-participants__team-logo" style="background-image: url(<?php echo CFile::GetPath($team["avatar"]); ?>)">
                      </div>
                      <a href="<?=SITE_DIR?>teams/<?php echo $team["id_team"]; ?>/" class="match-participants__team-link"><?php echo $team["name"]; ?></a>
                    </div>
                  </span>
    <span class="flex-table__param-wrap">
                   <div class="flex-table__param"><?=GetMessage('GAME_SCHEDULE_TABLE_POSITION')?></div>
                    <?php
                    echo $rank;
                    ?>
                  </span>
    <span class="flex-table__param-wrap">
                   <div class="flex-table__param"><?=GetMessage('GAME_SCHEDULE_TABLE_RATING')?></div>
                    <?php
                    echo ceil($team["total"]);
                    ?>
                  </span>
    <span class="flex-table__param-wrap">
                   <div class="flex-table__param"><?=GetMessage('GAME_SCHEDULE_TABLE_KILLS')?></div>
                   <?php
                   echo $kills;
                   ?>
                  </span>
  </div>

<?php }

?>
<?php /*
if( count( $points ) && !isset($_GET['teamname']) ){
  $tmp = [];
  foreach( $arResult["ITEMS"] as $v ){
     $tmp[ $v['ID'] ] = $v['NAME'];
  }
  asort( $tmp );

  $places = [];
  /////////////////////////////////
  foreach( $points as $v ){
    $places[$v['teamID']] = empty($arRating[$v['teamID']]) ? 300 + $v['rating'] : $arRating[$v['teamID']] + $v['rating'];
      //dump($places[$v['teamID']]);
    unset( $tmp[$v['teamID']] );
  }
  //dump($points);
  foreach( $tmp as $k=>$v ){
     // dump($points[ $k ]);
    $points[ $k ] = [ 'rating' => 300 ];
    $places[ $k ] = 300;
  }
  //////////////////////////
  arsort($places);

  $pos = 1;
  foreach( $places as $k=>$v ){
    $points[ $k ]['ratingPosition'] = $pos++;
  }
  $sortRes = $places;
  //$sortRes = $sortRes + $tmp;
  foreach( $arResult["ITEMS"] as $v ){
    $sortRes[ $v['ID'] ] = $v;
  }
  $arResult["ITEMS"] = $sortRes;
}

?>
<section class="match-participants bg-blue-lighter">
  <div class="container">
    <!--h2 class="game-schedule__heading text-center">Команды</h2-->
    <div class="game-schedule-table">
      <div class="flex-table">
        <div class="flex-table--header bg-blue-lighter">
          <div class="flex-table--categories">
            <span>Команда</span>
            <span>Позиция в рейтинге</span>
            <span>Рейтинг</span>
            <!--span>Сумма очков</span-->
            <span>Киллы</span>
            <!--span>Награды</span-->
          </div>
        </div>
        <div class="flex-table--body">
            <?php foreach($arResult["ITEMS"] as $arItem) { ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
          <div class="flex-table--row" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                  <span>
                    <div class="match-participants__team">
                      <div class="match-participants__team-logo" style="background-image: url(<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>)">
                      </div>
                      <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="match-participants__team-link"><?=$arItem["NAME"]?></a>
                    </div>
                  </span>
            <span class="flex-table__param-wrap">
                   <div class="flex-table__param">Позиция в рейтинге</div>
                    <?php
                    if( isset($points[ $arItem['ID'] ]) ){
                      echo $points[ $arItem['ID'] ]['ratingPosition'];
                    } else {
                      echo '-';
                    }
                    ?>
                  </span>
            <span class="flex-table__param-wrap">
                   <div class="flex-table__param">Рейтинг</div>
                    <?php
                    if( isset($places[ $arItem['ID'] ]) ){
                      echo $places[ $arItem['ID'] ];
                    } else {
                      if (empty($arItem["PROPERTIES"]['RATING']["VALUE"])) {
                          echo '-';
                      } else {
                          echo $arItem["PROPERTIES"]['RATING']["VALUE"];
                      }
                    }
                    ?>
                  </span>
            <!--span class="flex-table__param-wrap">
                   <div class="flex-table__param">Сумма очков</div>
                   <?php
                    if( isset($points[ $arItem['ID'] ]) ){
                      echo $points[ $arItem['ID'] ]['total'];
                    } else {
                      echo '-';
                    }
                    ?>
                  </span-->
            <span class="flex-table__param-wrap">
                   <div class="flex-table__param">Киллы</div>
                   <?php
                    if( isset($points[ $arItem['ID'] ]['kills']) ){
                      echo $points[ $arItem['ID'] ]['kills'];
                    } else {
                      echo '-';
                    }
                    ?>
                  </span>
            <!--span class="flex-table__param-wrap">
                   <div class="flex-table__param">Награды</div>
                    -
                  </span-->
          </div>
            <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php //echo $arResult["NAV_STRING"]*/ ?>
<section class="match-participants">
  <div class="container">
    <div class="game-schedule-table">
      <div class="flex-table">
        <div class="flex-table--header bg-default">
          <div class="flex-table--categories">
                <span><?= GetMessage('GAME_SCHEDULE_TABLE_TEAM') ?></span>
                <span><?= GetMessage('GAME_SCHEDULE_TABLE_POSITION') ?></span>
                <span><?= GetMessage('GAME_SCHEDULE_TABLE_RATING') ?></span>
                <span><?= GetMessage('GAME_SCHEDULE_TABLE_KILLS') ?></span>
          </div>
        </div>
        <div class="flex-table--body">
         <?php

         if( isset($_GET['teamname']) && $_GET['teamname'] != '' ){
             searchTeams($_GET['teamname']);
         }else{
             showTeams();
         } ?>
        </div>
      </div>
    </div>
  </div>
</section>
