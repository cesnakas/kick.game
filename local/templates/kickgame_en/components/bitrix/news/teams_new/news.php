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

?>
<div class="container">
  <h1 class="text-center">Рейтинг команд</h1>
  <div class="row justify-content-center">
    <div class="col-lg-10 col-md-12">
      <div class="layout__content-anons text-center">
        Рейтинги KICKGAME - это система квалификации команд и игроков, которая позволяет устраивать игры с равными соперниками. Ты совершенствуешь свои навыки, получаешь опыт - и это не остаётся незамеченным. Твой навык имеет определяющее значение в победах на турнирах.
      </div>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-11 col-md-12">
      <div class="rating">
        <form action="/teams/" method="get">
          <div class="form-field">
            <label for="search-team" class="form-field__label">Название команды</label>
            <div class="form-field__with-btn">
			<?php
			$teamname = '';
			if( isset($_GET['teamname']) && $_GET['teamname'] != '' ){
				$teamname = htmlspecialchars($_GET['teamname'], ENT_QUOTES, 'UTF-8');
			}
			?>
              <input type="text" class="form-field__input" name="teamname" value="<?php echo $teamname;?>" autocomplete="off" id="search-team" placeholder="Введите тег или название команды">
              <button class="btn" type="submit" name="">Найти команду</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?if($arParams["USE_SEARCH"]=="Y"):?>
<?=GetMessage("SEARCH_LABEL")?><?$APPLICATION->IncludeComponent(
	"bitrix:search.form",
	"flat",
	Array(
		"PAGE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["search"]
	),
	$component
);?>
<br />
<?endif?>
<?if($arParams["USE_FILTER"]=="Y"):?>
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
<?endif?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-10 col-md-12">
      <div class="layout__content-anons text-center">
		<a class="btn btn_auth btn_border">Список команд</a>
		<a class="btn btn_auth " href="/players/">Список игроков</a>
      </div>
    </div>
  </div>
</div>

<?

global $arrFilter;
$arrFilter = Array();
$teamname = '';
if( isset($_GET['teamname']) && $_GET['teamname'] != '' ){
	$teamname = htmlspecialchars($_GET['teamname'], ENT_QUOTES, 'UTF-8');
	$arrFilter = Array(
	   'NAME' => '%'.$teamname.'%',
	   //"ACTIVE"  =>  "Y"
	   //"ID" => "310"
	   );
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
		//"FILTER_NAME" => $arParams["FILTER_NAME"],
		"FILTER_NAME" => "arrFilter",
		"HIDE_LINK_WHEN_NO_DETAIL" => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
	),
	$component
);?>
<?php

$resultPrem = isPrem($arUser['UF_DATE_PREM_EXP']);
if (isset($arUser) && $resultPrem <= 0) { ?>
<section class="banner">
  <div class="container">
    <div class="banner__bg">
      <div class="banner__content">
        <h2>15 праков в день<br>
          Турниры на 100€ и 1000€
        </h2>
        <div class="banner__content-btn">
          <a href="/subscription-plans/" class="btn">Купить подписку</a>
        </div>
      </div>
      <div class="banner__img banner__img_girl">
        <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/banner-girl.png" alt="banner">
      </div>
    </div>
  </div>
</section>
<?php } ?>
