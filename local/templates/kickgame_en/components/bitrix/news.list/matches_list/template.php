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
?>
<div class="row justify-content-center">

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="col-md-12" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
    <!--<div>
      Турнир <?php //echo $arItem["DISPLAY_PROPERTIES"]["TOURNAMENT"]["DISPLAY_VALUE"]; ?>
        <?php
        //var_dump($arItem["DISPLAY_PROPERTIES"]["TOURNAMENT"]["DISPLAY_VALUE"]);
        //var_dump($arItem);
        ?>
    </div>-->
    <div class="my-3">
      <img  src="<?php echo CFile::GetPath($arItem["~PROPERTY_TOURNAMENT_DETAIL_PICTURE"]); ?>" alt="">
    </div>
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<div class="text-center"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" ><img
						class="preview_picture"
						border="0"
            width="50px"
						src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
						alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
						title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
						style="border-radius: 50%; margin: 0 auto"
						/></a>
        </div>
			<?else:?>
				<img
					class="preview_picture"
					src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
					width="50px"
					alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
					title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
					style="border-radius: 50%"
					/>
			<?endif;?>
		<?endif?>
		<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
			<span class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
		<?endif?>
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<div><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a></div>
			<?else:?>
				<b><?echo $arItem["NAME"]?></b><br />
			<?endif;?>
		<?endif;?>
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<? //echo $arItem["PREVIEW_TEXT"];?>
		<?endif;?>
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<div style="clear:both"></div>
		<?endif?>


    <div>

        <?php

        /*if ($arr = ParseDateTime($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], "DD.MM.YYYY HH:MI"))
        {
            echo "День:    ".$arr["DD"]."<br>";    // День: 21
            echo "Месяц:   ".$arr["MM"]."<br>";    // Месяц: 1
            echo "Год:     ".$arr["YYYY"]."<br>";  // Год: 2004
            echo "Часы:    ".$arr["HH"]."<br>";    // Часы: 23
            echo "Минуты:  ".$arr["MI"]."<br>";    // Минуты: 44
            echo "Секунды: ".$arr["SS"]."<br>";    // Секунды: 15
        }
        else echo "Ошибка!";*/

        $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
        echo $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["NAME"] . ' <span class="badge rounded-pill bg-success">' . $dateTime[0] . ' в ' . $dateTime[1] . '</span>';?>


    </div>

	</div>
<?endforeach;?>
</div>
