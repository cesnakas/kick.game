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
<?php /*<div class="row">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="col-md-12" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
    <div class="card mb-3">

      <div class="card-body">
        <div>
          <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
            <img
              class="preview_picture"
              border="0"
              src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
              width="150px"
              height="150px"
              alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
              title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
            />
          </a>
        </div>
        <h2 class="card-title"><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?> [<?echo $arItem["DISPLAY_PROPERTIES"]['TAG_TEAM']['VALUE']?>]</a></h2>
        <p class="card-text"><?echo $arItem["PREVIEW_TEXT"];?></p>
      </div>
    </div>
	</div>
<?endforeach;?>

</div>
*/?>
    <form action="/teams/" method="get">
    <div class="form-field">
        <label for="nameTeam" class="form-field__label">Название команды</label>
        <input type="text" class="form-field__input" name="nameTeam" value="" autocomplete="off" id="nameTeam" placeholder="Введите название команды">
    </div>
        <button type="submit" class="btn mr-3">Найти</button>
    </form>

<section class="match-participants">
    <div class="container">
        <h2 class="game-schedule__heading text-center">Команды</h2>
        <div class="game-schedule-table">
            <div class="flex-table">
                <div class="flex-table--header bg-blue-lighter">
                    <div class="flex-table--categories">
                        <span>Название</span>
                        <span>Позиция в рейтинге</span>
                        <span>Рейтинг</span>
                        <span>Сумма очков</span>
                        <span>Фраги</span>
                        <span>Награды</span>
                    </div>
                </div>
                <div class="flex-table--body">
                    <?php foreach ($arResult["ITEMS"] as $team) {
                        ?>
                        <div class="flex-table--row">
                <span>
                  <div class="match-participants__team">
                    <div class="match-participants__team-logo" style="background-image: url(<?=$team["PREVIEW_PICTURE"]["SRC"]?>">
                    </div>
                    <a href="<?=$team["DETAIL_PAGE_URL"]?>" class="match-participants__team-link"><?php echo $team['NAME']; ?> [<?php echo $team["DISPLAY_PROPERTIES"]['TAG_TEAM']['VALUE']; ?>]</a>
                  </div>
                </span>
                            <span class="flex-table__param-wrap">
                 <div class="flex-table__param">Позиция в рейтинге</div>
                  80
                </span>
                            <span class="flex-table__param-wrap">
                 <div class="flex-table__param">Рейтинг</div>
                  80
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
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <br />
    <br /><?=$arResult["NAV_STRING"]?>
<?endif;?>