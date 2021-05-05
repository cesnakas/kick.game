<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Расписание игр");
?><?$APPLICATION->IncludeComponent(
	"bitrix:news", 
	"game-schedule-page", 
	array(
		"ADD_ELEMENT_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BROWSER_TITLE" => "NAME",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_FIELD_CODE" => array(
			0 => "",
			1 => "PROPERTY_TOURNAMENT.NAME",
			2 => "PROPERTY_TOURNAMENT.DETAIL_PICTURE",
			3 => "PROPERTY_STREAMER.NAME",
			4 => "",
		),
		"DETAIL_PAGER_SHOW_ALL" => "Y",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_TITLE" => "Страница",
		"DETAIL_PROPERTY_CODE" => array(
			0 => "PUBG_LOBBY_ID",
			1 => "DATE_START",
			2 => "COUTN_TEAMS",
			3 => "STREAMER",
			4 => "MAX_RATING",
			5 => "MIN_RATING",
			6 => "PREV_MATCH",
			7 => "URL_STREAM",
			8 => "TYPE_MATCH",
			9 => "TOURNAMENT",
			10 => "STAGE_TOURNAMENT",
			11 => "",
		),
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FILE_404" => "",
		"FILTER_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_NAME" => "arrFilterDateTime",
		"FILTER_PROPERTY_CODE" => array(
			0 => "COUTN_TEAMS",
			1 => "TYPE_MATCH",
			2 => "",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "3",
		"IBLOCK_TYPE" => "matches",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"LIST_FIELD_CODE" => array(
			0 => "",
			1 => "PROPERTY_TOURNAMENT.NAME",
			2 => "PROPERTY_TOURNAMENT.DETAIL_PICTURE",
			3 => "PROPERTY_STREAMER.NAME",
			4 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "PUBG_LOBBY_ID",
			1 => "DATE_START",
			2 => "COUTN_TEAMS",
			3 => "STREAMER",
			4 => "URL_STREAM",
			5 => "TYPE_MATCH",
			6 => "TOURNAMENT",
			7 => "STAGE_TOURNAMENT",
			8 => "",
		),
		"MESSAGE_404" => "",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"NEWS_COUNT" => "5",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "ajax_pager",
		"PAGER_TITLE" => "Новости",
		"PREVIEW_TRUNCATE_LEN" => "",
		"SEF_FOLDER" => SITE_DIR."game-schedule/",
		"SEF_MODE" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"SHOW_404" => "Y",
		"SORT_BY1" => "PROPERTY_DATE_START",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"USE_CATEGORIES" => "N",
		"USE_FILTER" => "Y",
		"USE_PERMISSIONS" => "N",
		"USE_RATING" => "N",
		"USE_REVIEW" => "N",
		"USE_RSS" => "N",
		"USE_SEARCH" => "N",
		"USE_SHARE" => "N",
		"COMPONENT_TEMPLATE" => "game-schedule-page",
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "",
			"detail" => "#ELEMENT_CODE#/",
		)
	),
	false
);?>

<?php
$resultPrem = isPrem($arUser['UF_DATE_PREM_EXP']);
if (isset($arUser) && $resultPrem <= 0) { ?>
    <section class="banner">
        <div class="container">
            <div class="banner__bg">
                <div class="banner__content">
                  <h2><?=GetMessage('BANNER_TITLE')?></h2>
                    <div class="banner__content-btn">
                        <a href="<?=SITE_DIR?>subscription-plans/" class="btn"><?= GetMessage('BANNER_BUTTON') ?></a>
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