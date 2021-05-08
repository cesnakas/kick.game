<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Управление играми");
//#1Вывести список матчей у который parent_id null, все которые о сегодня и дальше
//-название
//-дата
//-тип
?>
<!--#2
1. Кликнуть на матч и его выбрать. /management-games/id матч первого из серии stage1
2. Выводим описание матча(дата и время id) и состав моей команды checkbox и у капитана,
	//3. Выбрать от 2-3 галочек у тех у кого prem>0;
4. появляется кнопка зарегистрироваться на матч
5. клик по кнопке
	5.1 Формирование команды (каждый игрок записывается в инфоблок состав команды) (берем код в название и в сивольльный код.(SQUAD_MATCH_PRAC_20210130-0600_GROUP4_STAGE1_20210120192310) из матча и добавлем )
        в players кладем id игроков, в матч id матча

если success все ok


5.2 получаю по stage1_id, цепочку матчей будет 3 id матчей
5.3 дергаю соответсвующие элементы из инфоблока  участники матчей по пулю wich match на выходе 3 id из участников матчей


5.4 беру запись из участков матчей и смотрю есть ли свободные places? 7,8,9,10 на  получаю place который свободен,
если все заняты(мест нет и удаляю squad(отряд из 5.1))
если есть free place например place 8, берем и заносим id команды в place 8 попадает в каждую запись инфоблока участников матчей из пункта 5.3 -->

  <div class="container">
  <div class="row">
  <div class="col-lg-3 col-md-12">
    <div class="filter-param">
    <h3><?=GetMessage('MG_FILTERS_TITLE')?></h3>
      <form action="" method="get">
        <div class="form-field-wrap">
          <div class="form-field__row">
            <!--<div class="form-field form-field__checkbox form-field__checkbox_filter">
                <input type="checkbox" class="form-field__checkbox " name="arrFilterDateTime_pf[TYPE_MATCH]" <?php if($_GET['arrFilterDateTime_pf']['TYPE_MATCH'] == '') echo 'checked' ?> value="" id="all">
                <label for="all" class="form-field__label form-field__label_filter form-field__label-checkbox">Все</label>
              </div>-->
            <label class="label-checkbox-main">
              <input type="checkbox" name="arrFilterDateTime_pf[TYPE_MATCH]" value="6" <?php if($_GET['arrFilterDateTime_pf']['TYPE_MATCH'] == 6) echo 'checked' ?> >
              <div class="label-checkbox-main__checkmark label-checkbox-main__checkmark_filter"></div>
              <span class="label-checkbox-main__title label-checkbox-main__title_filter"><?=GetMessage('MG_FILTERS_LABEL_PRAKI')?></span>
            </label>
            <label class="label-checkbox-main">
              <input type="checkbox" name="arrFilterDateTime_pf[TYPE_MATCH]" value="5" <?php if($_GET['arrFilterDateTime_pf']['TYPE_MATCH'] == 5) echo 'checked' ?> >
              <div class="label-checkbox-main__checkmark label-checkbox-main__checkmark_filter"></div>
              <span class="label-checkbox-main__title label-checkbox-main__title_filter"><?=GetMessage('MG_FILTERS_LABEL_TOURNAMENTS')?></span>
            </label>
            <!--<div class="form-field form-field__checkbox form-field__checkbox_filter">
              <input type="checkbox" class="form-field__checkbox " name="practical" id="custom">
              <label for="custom" class="form-field__label form-field__label_filter form-field__label-checkbox">Кастомки</label>
            </div>-->
          </div>
          <div class="form-field__row">
            <!--<div class="form-field form-field__checkbox form-field__checkbox_filter">
              <input type="checkbox" class="form-field__checkbox " name="practical" id="solo">
              <label for="solo" class="form-field__label form-field__label_filter form-field__label-checkbox">Соло</label>
            </div>-->
            <label class="label-checkbox-main">
              <input type="checkbox" name="arrFilterDateTime_pf[COUTN_TEAMS]" value="2" <?php if($_GET['arrFilterDateTime_pf']['COUTN_TEAMS'] == 2) echo 'checked' ?> >
              <div class="label-checkbox-main__checkmark label-checkbox-main__checkmark_filter"></div>
              <span class="label-checkbox-main__title label-checkbox-main__title_filter">DUO</span>
            </label>
            <label class="label-checkbox-main">
              <input type="checkbox" name="arrFilterDateTime_pf[COUTN_TEAMS]" value="4" <?php if($_GET['arrFilterDateTime_pf']['COUTN_TEAMS'] == 4) echo 'checked' ?>>
              <div class="label-checkbox-main__checkmark label-checkbox-main__checkmark_filter"></div>
              <span class="label-checkbox-main__title label-checkbox-main__title_filter">SQUAD</span>
            </label>
          </div>
        </div>
        <!--<div class="form-field">
          <label for="search-name" class="form-field__label">Поиск по названию</label>
          <input type="text" class="form-field__input" autocomplete="off" id="search-name" placeholder="Введите назваие матча">
        </div>-->
        <div class="form-field  text-center">
          <input type="hidden" name="set_filter" value="Y" />
          <button class="btn mr-1" type="submit" name="set_filter"><?=GetMessage('MG_FILTERS_BTN_FILTER')?></button>
          <button class="btn" type="submit" name="arrFilterDateTime_pf[TYPE_MATCH]" value=""><?=GetMessage('MG_FILTERS_BTN_RESET')?></button>
        </div>
      </form>
        <?/*if($arParams["USE_FILTER"]=="Y"):?>
              <?$APPLICATION->IncludeComponent(
                  "bitrix:catalog.filter",
                  "",
                  Array(
                      "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                      "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                      "FILTER_NAME" => $arParams["FILTER_NAME"],
                      "FIELD_CODE" => $arParams["FILTER_FIELD_CODE"],
                      "PROPERTY_CODE" => $arParams["FILTER_PROPERTY_CODE"],
                      "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                      "CACHE_TIME" => $arParams["CACHE_TIME"],
                      "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                      "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                  ),
                  $component
              );
              ?>
          <?endif*/?>
    </div>
  </div>
  <div class="col-lg-9 col-md-12">
    <h1 class="game-schedule__heading mt-3"><?=GetMessage('MG_HEADING')?></h1>
  <div class="game-schedule-table">
  <div class="flex-table">
  <div class="flex-table--header bg-default">
    <div class="flex-table--categories">
        <span><?= GetMessage('MG_TYPE') ?></span>
        <span><?= GetMessage('MG_TITLE') ?></span>
        <span><?= GetMessage('MG_DATE_EVENT') ?></span>
        <span><?= GetMessage('MG_RATING') ?></span>
        <span><?= GetMessage('MG_MODE') ?></span>
        <span><?= GetMessage('MG_COMMENTATOR') ?></span>
    </div>
  </div>
  <div class="flex-table--body">
<?

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
//dump($arrFilterDateTime);
if (isset($_GET['arrFilterDateTime_pf']['COUTN_TEAMS'])) {
    //dump($_GET['arrFilterDateTime_pf']);
    $mode = $_GET['arrFilterDateTime_pf']['COUTN_TEAMS']+0;
    if ($mode == 2 || $mode == 4) {
        $arrFilterDateTime["PROPERTY_COUTN_TEAMS"] = $mode;
    }
}
if (isset($_GET['arrFilterDateTime_pf']) && isset($_GET['arrFilterDateTime_pf']['TYPE_MATCH'])) {
    $typeMatch = $_GET['arrFilterDateTime_pf']['TYPE_MATCH']+0;
    if ($typeMatch == 5 || $typeMatch == 6) {
        $arrFilterDateTime["PROPERTY_TYPE_MATCH"] = $typeMatch;
    }
}
$APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"management-games", 
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
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"DISPLAY_TOP_PAGER" => "Y",
		"FIELD_CODE" => array(
			0 => "",
			1 => "PROPERTY_TOURNAMENT.NAME",
			2 => "PROPERTY_TOURNAMENT.DETAIL_PICTURE",
			3 => "PROPERTY_TOURNAMENT.DETAIL_PAGE_URL",
			4 => "PROPERTY_STREAMER.NAME",
			5 => "",
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
		"PAGER_TITLE" => "Новости",
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
		"COMPONENT_TEMPLATE" => "management-games",
		"FILE_404" => ""
	),
	false
);

?>
  </div>
  </div>
  </div>
  </div>
  </div>
  </div>
<?php

$resultPrem = isPrem($arUser['UF_DATE_PREM_EXP']);
if (isset($arUser) && $resultPrem <= 0) { ?>


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
          <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/banner-img-2.png" alt="banner">
        </div>
      </div>
    </div>
  </section>
    <?php } ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>