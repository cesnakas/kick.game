<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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
                            <a role="button" data-toggle="collapse"  href="#accordionCollapse<? echo $arItem['ID'];?>" aria-controls="accordionCollapse1">
                                <div class="subscription-plans-item">
                                    <div class="subscription-plans-item__icon">
                                        <img src="<?=CFile::GetPath($arItem['PROPERTIES']['ICON']['VALUE'])?>" alt="basic">
                                    </div>
                                    <div class="subscription-plans-item__name">
                                        <? echo $arItem['NAME'];?>
                                    </div>
                                    <div class="subscription-plans-item__heading">
                                        <? echo $arItem['PROPERTIES']['INFO']['~VALUE'];?>
                                    </div>
                                    <div class="subscription-plans-item__sub-heading">
                                        <? echo $arItem['PROPERTIES']['SIGN']['~VALUE'];?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="card-custom-collapse collapse <? if($elms ==1) {?>show<? }?>" id="accordionCollapse<? echo $arItem['ID'];?>" role="tabpanel" aria-labelledby="accordionHeading<? echo $arItem['ID'];?>" data-parent="#accordion">
                        <div class="card-custom-body">
                            <?
                            unset($stvalsall);
                            foreach($arItem['PROPERTIES']['CHAR']['VALUE'] as $stvals) {
                                $stvalsall[$stvals] = $stvals;
                            }
                            ?>
                            <ul>
                                <?
                                foreach($elems as $ker => $rvals) {
                                    if(in_array($ker,$stvalsall)) {
                                        echo '<li>'.$rvals.'</li>';
                                    } else {
                                        echo '<li class="not">'.$rvals.'</li>';
                                    }
                                }
                                ?>

                            </ul>
                            <?
                            $sprice = $elems_price[$arItem[ID]]["CATALOG_PRICE_1"];

                            if(!empty($sprice) && $sprice >0) {
                                ?>
                                <div class="subscription-plans-item__btn text-center" >
                                    <a data-id="" href="/order/?orderid=<? echo $arItem[ID];?>" class="btn jsaddtoorder">Купить подписку</a>
                                </div>
                            <? } ?>

                        </div>
                    </div>
                </div>
            </div>

        <?endforeach;?>
    </div>
</div>