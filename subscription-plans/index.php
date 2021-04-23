<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Подписка");
?>

    <div class="container">
        <h1 class="text-center"><?=GetMessage('SP_HEADER')?></h1>
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="subscription-plans__description text-center">
                    <?=GetMessage('SP_HEADER_TEXT')?>
                </div>
            </div>
        </div>
        <div id="accordion" role="tablist" class="subscription-plans">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card card-custom active">
                        <div class="card-custom-heading" id="accordionHeading1" role="tab">
                            <div class="card-custom-title">
                                <a role="button" data-toggle="collapse" href="#accordionCollapse1"
                                   aria-controls="accordionCollapse1">
                                    <div class="subscription-plans-item">
                                        <div class="subscription-plans-item__icon">
                                            <img src="<?= SITE_TEMPLATE_PATH; ?>/dist/images/plan-basic.svg" alt="basic">
                                        </div>
                                        <div class="subscription-plans-item__name">
                                            <?=GetMessage('SP_SUBSCRIPTION_PLAN_BASIC')?>
                                        </div>
                                        <div class="subscription-plans-item__heading">
                                            <?=GetMessage('SP_SUBSCRIPTION_PLAN_BASIC_PRICE')?>
                                        </div>
                                        <div class="subscription-plans-item__sub-heading">

                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="card-custom-collapse collapse show" id="accordionCollapse1" role="tabpanel"
                             aria-labelledby="accordionHeading1" data-parent="#accordion">
                            <div class="card-custom-body">
                                <ul>
                                    <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_1')?></li>
                                    <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_2')?></li>
                                    <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_3')?></li>
                                    <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_4')?></li>
                                    <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_5')?></li>
                                    <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_6')?></li>
                                    <li><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_7')?></li>
                                    <li class="not"><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_8')?></li>
                                    <li class="not"><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_9')?></li>
                                    <li class="not"><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_10')?></li>
                                    <li class="not"><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_11')?></li>
                                    <li class="not"><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_12')?></li>
                                    <li class="not"><?=GetMessage('SP_SUBSCRIPTION_PLAN_ITEM_13')?></li>
                                </ul>
                                <div class="subscription-plans-item__btn text-center" style="opacity: 0;">
                                    <a href="#" class="btn"><?=GetMessage('SP_SUBSCRIPTION_PLAN_BUY')?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card card-custom">
                        <div class="card-custom-heading" id="accordionHeading2" role="tab">
                            <div class="card-custom-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#accordionCollapse2"
                                   aria-controls="accordionCollapse2">
                                    <div class="subscription-plans-item">
                                        <div class="subscription-plans-item__icon">
                                            <img src="<?php echo SITE_TEMPLATE_PATH; ?>/dist/images/plan-standart.svg"
                                                 alt="basic">
                                        </div>
                                        <div class="subscription-plans-item__name">
                                            <?=GetMessage('SP_SUBSCRIPTION_PLAN_STANDARD')?>
                                        </div>
                                        <div class="subscription-plans-item__heading">
                                            € 3,99<span>/<?=GetMessage('SP_SUBSCRIPTION_PLAN_MONTH')?></span>
                                        </div>
                                        <div class="subscription-plans-item__sub-heading">

                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="card-custom-collapse collapse" id="accordionCollapse2" data-parent="#accordion"
                             role="tabpanel" aria-labelledby="accordionHeading2">
                            <div class="card-custom-body">
                                <ul>
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
                                </ul>
                                <div class="subscription-plans-item__btn text-center">
                                    <a href="#" class="btn"><?=GetMessage('SP_SUBSCRIPTION_PLAN_BUY')?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card card-custom">
                        <div class="card-custom-heading" id="accordionHeading3" role="tab">
                            <div class="card-custom-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#accordionCollapse3"
                                   aria-controls="accordionCollapse3">
                                    <div class="subscription-plans-item">
                                        <div class="subscription-plans-item__icon">
                                            <img src="<?php echo SITE_TEMPLATE_PATH; ?>/dist/images/plan-premium.svg"
                                                 alt="basic">
                                        </div>
                                        <div class="subscription-plans-item__name">
                                            <?=GetMessage('SP_SUBSCRIPTION_PLAN_PREMIUM')?>
                                        </div>
                                        <div class="subscription-plans-item__heading">
                                            € 3,59<span>/<?=GetMessage('SP_SUBSCRIPTION_PLAN_MONTH')?></span>
                                        </div>
                                        <div class="subscription-plans-item__sub-heading">
                                            <?=GetMessage('SP_SUBSCRIPTION_PLAN_PREMIUM_INFO')?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="card-custom-collapse collapse" id="accordionCollapse3" data-parent="#accordion"
                             role="tabpanel" aria-labelledby="accordionHeading3">
                            <div class="card-custom-body">
                                <ul>
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
                                </ul>
                                <div class="subscription-plans-item__btn text-center">
                                    <a href="#" class="btn"><?=GetMessage('SP_SUBSCRIPTION_PLAN_BUY')?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card card-custom">
                        <div class="card-custom-heading" id="accordionHeading4" role="tab">
                            <div class="card-custom-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#accordionCollapse4"
                                   aria-controls="accordionCollapse4">
                                    <div class="subscription-plans-item">
                                        <div class="subscription-plans-item__icon">
                                            <img src="<?php echo SITE_TEMPLATE_PATH; ?>/dist/images/plan-elit.svg"
                                                 alt="basic">
                                        </div>
                                        <div class="subscription-plans-item__name">
                                            <?=GetMessage('SP_SUBSCRIPTION_PLAN_ELITE')?>
                                        </div>
                                        <div class="subscription-plans-item__heading">
                                            € 3,39<span>/<?=GetMessage('SP_SUBSCRIPTION_PLAN_MONTH')?></span>
                                        </div>
                                        <div class="subscription-plans-item__sub-heading">
                                            <?=GetMessage('SP_SUBSCRIPTION_PLAN_ELITE_INFO')?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="card-custom-collapse collapse" id="accordionCollapse4" data-parent="#accordion"
                             role="tabpanel" aria-labelledby="accordionHeading4">
                            <div class="card-custom-body">
                                <ul>
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
                                </ul>
                                <div class="subscription-plans-item__btn text-center">
                                    <a href="#" class="btn"><?=GetMessage('SP_SUBSCRIPTION_PLAN_BUY')?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>