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
<section class="match-participants bg-blue-lighter">
  <div class="container">
    <h2 class="game-schedule__heading text-center">Команды</h2>
    <div class="game-schedule-table">
      <div class="flex-table">
        <div class="flex-table--header bg-blue-lighter">
          <div class="flex-table--categories">
            <span>Команда</span>
            <span>Позиция в рейтинге</span>
            <span>Рейтинг</span>
            <span>Сумма очков</span>
            <span>Фраги</span>
            <span>Награды</span>
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
                    80
                  </span>
            <span class="flex-table__param-wrap">
                   <div class="flex-table__param">Рейтинг</div>
                    3.00
                  </span>
            <span class="flex-table__param-wrap">
                   <div class="flex-table__param">Сумма очков</div>
                    80
                  </span>
            <span class="flex-table__param-wrap">
                   <div class="flex-table__param">Фраги</div>
                    80
                  </span>
            <span class="flex-table__param-wrap">
                   <div class="flex-table__param">Награды</div>
                    80
                  </span>
          </div>
            <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php //echo $arResult["NAV_STRING"]?>
