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

CModule::IncludeModule("iblock");

$arSelect = Array("ID", "NAME");
$arFilter = Array("IBLOCK_CODE" => "harakteristiki", "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, Array("nPageSize"=>500), $arSelect);
while($ob = $res->GetNextElement())
{
    $arFields = $ob->GetFields();
    $elems[$arFields["ID"]] = $arFields["NAME"];
}

$arSelect_price = Array("ID", "NAME","CATALOG_PRICE_1","CATALOG_GROUP_1");
$arFilter_price = Array("IBLOCK_CODE" => "tovari", "ACTIVE"=>"Y");
$res_price = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter_price, false, Array("nPageSize"=>500), $arSelect_price);
while($ob_price = $res_price->GetNextElement())
{
    $arFields_price = $ob_price->GetFields();
    $elems_price[$arFields_price["ID"]] = $arFields_price;
}
?>

<div id="accordion" role="tablist" class="subscription-plans">
    <div class="row">
        <?
        $elms = 0;
        foreach($arResult["ITEMS"] as $arItem):?>

            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            $elms++;
            ?>

        <div class="col-lg-3">
            <div class="card card-custom <? if($elms ==1) {?>active <? } ?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                <div class="card-custom-heading" id="accordionHeading<? echo $arItem['ID'];?>" role="tab">
                    <div class="card-custom-title">
                        <a role="button" data-toggle="collapse" href="#accordionCollapse<? echo $arItem['ID'];?>" aria-controls="accordionCollapse1">
                            <div class="subscription-plans-item">
                                <div class="subscription-plans-item__icon">
                                    <img src="<?=CFile::GetPath($arItem['PROPERTIES']['ICON']['VALUE'])?>" alt="basic">
                                </div>
                                <div class="subscription-plans-item__name">
                                <? if (LANGUAGE_ID == 'ru'): ?>
                                    <? echo $arItem['NAME'];?>
                                <? elseif (LANGUAGE_ID == 'en'): ?>
                                    <?=$arItem['PROPERTIES']['NAME_ENG']['~VALUE'];?>
                                <? endif; ?>
                                </div>
                                <div class="subscription-plans-item__heading">
                                    <? echo $arItem['PROPERTIES']['INFO']['~VALUE'];?>
                                </div>
                                <?
                                if(!empty($arItem['PROPERTIES']['SIGN']['~VALUE'])){?>
                                    <div class="subscription-plans-item__sub-heading">
                                        <? echo $arItem['PROPERTIES']['SIGN']['~VALUE'];?>
                                    </div>
                                <? } ?>
                            </div>
                        </a>
                    </div>
                </div>
                <!--//-->
                <div class="card-custom-collapse collapse <? if($elms == 1) {?>show<? }?>" id="accordionCollapse<? echo $arItem['ID'];?>" role="tabpanel" aria-labelledby="accordionHeading<? echo $arItem['ID'];?>" data-parent="#accordion">
                    <div class="card-custom-body">
                        <?
                        unset($stvalsall);
                        foreach($arItem['PROPERTIES']['CHAR']['VALUE'] as $stvals) {
                            $stvalsall[$stvals] = $stvals;
                        }
                        ?>
                        <ul>
                            <? if (LANGUAGE_ID == 'ru'): ?>

                                <? foreach($elems as $ker => $rvals): ?>
                                    <? if(in_array($ker,$stvalsall)) { ?>
                                        <li><?=$rvals?></li>
                                    <? } else { ?>
                                        <li class="not"><?=$rvals?></li>
                                    <? } ?>
                                <? endforeach; ?>

                            <? elseif (LANGUAGE_ID == 'en'): ?>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_1')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_2')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_3')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_4')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_5')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_6')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_7')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_8')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_9')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_10')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_11')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_12')?></li>
                                <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_13')?></li>
                            <? endif; ?>

                            <?/*
                            foreach($elems as $ker => $rvals) {
                                if(in_array($ker,$stvalsall)) {
                                    echo '<li>'.$rvals.'</li>';
                                } else {
                                    echo '<li class="not">'.$rvals.'</li>';
                                }
                            }
                            */?>

                        </ul>
                    </div>
                </div>
                <!--//-->
                <?
                $sprice = $elems_price[$arItem[ID]]["CATALOG_PRICE_1"];
                if(!empty($sprice) && $sprice >0) {
                    ?>
                    <div class="subscription-plans-item__btn text-center" >
                        <a data-id="" href="<?=SITE_DIR?>order/?orderid=<? echo $arItem[ID];?>" class="btn jsaddtoorder">
                            <?=GetMessage('SUBSCRIPTION_BTN_ORDER')?>
                        </a>
                    </div>
                <? } ?>
                <!--//-->
            </div>
        </div>

        <?endforeach;?>
    </div>
</div>



<?/*

    <?foreach($arResult["ITEMS"] as $arItem):?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>

        <p class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
            <? if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img
                        class="preview_picture"
                        border="0"
                        src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
                        width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
                        height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
                        alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
                        title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
                        style="float:left"
                    /></a>
            <? else: ?>
                <img
                    class="preview_picture"
                    border="0"
                    src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
                    width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
                    height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
                    alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
                    title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
                    style="float:left"
                />
            <? endif; ?>
        <?endif?>
        <?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
            <span class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
        <?endif?>
        <?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
            <?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
                <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a><br />
            <?else:?>
                <b><?echo $arItem["NAME"]?></b><br />
            <?endif;?>
        <?endif;?>
        <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
            <?echo $arItem["PREVIEW_TEXT"];?>
        <?endif;?>
        <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
            <div style="clear:both"></div>
        <?endif?>
        <?foreach($arItem["FIELDS"] as $code=>$value):?>
            <small>
                <?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
            </small><br />
        <?endforeach;?>
        <?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
            <small>
                <?=$arProperty["NAME"]?>:&nbsp;
                <?if(is_array($arProperty["DISPLAY_VALUE"])):?>
                    <?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
                <?else:?>
                    <?=$arProperty["DISPLAY_VALUE"];?>
                <?endif?>
            </small><br />
        <?endforeach;?>
        </p>

    <?endforeach;?>

    </div>
</div>

*/?>