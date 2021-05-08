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
//dump($_GET['arrFilterDateTime_pf']['TYPE_MATCH']);
?>
<style>
    .flex-table-new--body .btn-italic{
    text-align:center;
        display: block;
    }
</style>
<div class="container">
  <div class="row">
    <div class="col-lg-3 col-md-12">
      <div class="filter-param">
        <h3><?= GetMessage('FILTER_TITLE') ?></h3>
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
                <span class="label-checkbox-main__title label-checkbox-main__title_filter"><?= GetMessage('FILTER_INPUT_PRACTICAL') ?></span>
              </label>
              <label class="label-checkbox-main">
                <input type="checkbox" name="arrFilterDateTime_pf[TYPE_MATCH]" value="5" <?php if($_GET['arrFilterDateTime_pf']['TYPE_MATCH'] == 5) echo 'checked' ?> >
                <div class="label-checkbox-main__checkmark label-checkbox-main__checkmark_filter"></div>
                <span class="label-checkbox-main__title label-checkbox-main__title_filter"><?= GetMessage('FILTER_INPUT_TOURNAMENT') ?></span>
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
            <button class="btn mr-1" type="submit" name="set_filter"><?= GetMessage('FILTER_BTN_FILTER') ?></button>
            <button class="btn" type="submit" name="arrFilterDateTime_pf[TYPE_MATCH]" value=""><?= GetMessage('FILTER_BTN_RESET') ?></button>
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
      <h1 class="game-schedule__heading mt-3"><?= GetMessage('GS_HEADER') ?></h1>
<div class="new-game-schedule-table">
  <div class="flex-table-new">
    <div class="flex-table--header bg-default">
      <div class="flex-table--categories">
          <span><?= GetMessage('GS_TYPE') ?></span>
          <span><?= GetMessage('GS_TITLE') ?></span>
          <span><?= GetMessage('GS_DATE_EVENT') ?></span>
          <span><?= GetMessage('GS_RATING') ?></span>
          <span><?= GetMessage('GS_MODE') ?></span>
          <span><?= GetMessage('GS_COMMENTATOR') ?></span>
          <span><?=GetMessage('GS_SEATS')?></span>
      </div>
    </div>
    <div class="flex-table-new--body">
<?php
$curDate = date('Y-m-d H:i:s', time()+7200);
$finalsDate = date('Y-m-d H:i:s', time()-(3600*24));
GLOBAL $arrFilterDateTime;
$arrFilterDateTime=Array(
    "ACTIVE" => "Y",
    array(
      "LOGIC" => "OR",
        array(
            "LOGIC" => "AND",
      array("PROPERTY_GROUP" => "A"),
            ">=PROPERTY_DATE_START" => $curDate,),

        array(
            "LOGIC" => "AND",
      array("PROPERTY_TYPE_MATCH" => 5),
            ">=PROPERTY_DATE_START" => $curDate,),

        array(
            "LOGIC" => "AND",
            array("PROPERTY_STAGE_TOURNAMENT" => 1),
            array(">=PROPERTY_DATE_START" => $finalsDate),
        )
    ),

    "PROPERTY_PREV_MATCH" => false,
    //"PROPERTY_TYPE_MATCH" => 5
    //"PROPERTY_STAGE_TOURNAMENT" => 4,
    //"!=PROPERTY_TOURNAMENT" => false, // турниры
    //"=PROPERTY_TOURNAMENT" => false, // праки
);
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
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"NEWS_COUNT" => $arParams["NEWS_COUNT"],
		"SORT_BY1" => $arParams["SORT_BY1"],
		"SORT_ORDER1" => $arParams["SORT_ORDER1"],
		"SORT_BY2" => $arParams["SORT_BY2"],
		"SORT_ORDER2" => $arParams["SORT_ORDER2"],
		"FIELD_CODE" => $arParams["LIST_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"MESSAGE_404" => $arParams["MESSAGE_404"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SHOW_404" => $arParams["SHOW_404"],
		"FILE_404" => $arParams["FILE_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
		"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
		"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
		"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
		"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
		"PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
		"ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
		"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"HIDE_LINK_WHEN_NO_DETAIL" => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
	),
	$component
);?>
    </div>
  </div>
</div>
    </div>
  </div>
</div>