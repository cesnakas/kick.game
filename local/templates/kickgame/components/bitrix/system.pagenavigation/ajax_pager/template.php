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

if(!$arResult["NavShowAlways"]) {
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false)) {
		return;
	}
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
?>

<?php
$rNum = 3;
if(($arResult["sUrlPath"] == SITE_DIR."game-schedule/") || ($arResult["sUrlPath"] == SITE_DIR."management-games/")){
    $rNum = 5;
}
?>
<?php
if(isPrem($arUser["UF_DATE_PREM_EXP"]) > 0 || $arResult["sUrlPath"] == SITE_DIR."game-schedule/"){
?>

<?if($arResult["bDescPageNumbering"] === true):?>
	<?if ($arResult["NavPageNomer"] > 1):?>
		<a class="btn-italic mt-5" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" id="ajax_next_page"><?=GetMessage("LOAD_MORE")?> <?=$rNum?></a>
	<?endif?>
<?else:?>
	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<a class="btn-italic mt-5" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" id="ajax_next_page"><?=GetMessage('LOAD_MORE')?> <?=$rNum?></a>
	<?endif?>
<?endif?>

<?php } else { ?>
    <?php } ?>
