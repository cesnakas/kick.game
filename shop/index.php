<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");?>
<div class="container">
    <h1 class="text-center">Игровой магазин</h1>
    <div class="row">
        <?$products = CustomChick::getProducts();?>
        <?foreach ($products as $k => $v):?>
            <div class="col-md-6 <?if(!$k || !($k % 4 == 0 || $k % 5 == 0)):?>col-lg-3<?endif;?>">
                <div class="card-chicks-item">
                    <div class="card-chicks-item__img">
                        <?$picture = CFile::ResizeImageGet($v["PREVIEW_PICTURE"], array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, true)["src"];?>
                        <img src="<?= $picture;?>" alt="chick">
                        <div class="card-chicks-item__label">
                            <!--<span><?/*= $v["PROPERTY_CHICK_PRICE_VALUE"]*/?> ₽</span>-->
                            <span>€ <?= $v["PROPERTY_CHICK_PRICE_VALUE"]?></span>
                            <span>1 chick</span>
                        </div>
                    </div>
                    <div class="card-chicks-item__offer">
                        <h2>
                            <a href="javascript:void(0);"
                               class="product"
                               data-amount="<?= $v["PROPERTY_PRICE_VALUE"]?>"
                               data-email="customer@example.com"
                               data-full-name="Customer full name"
                               data-customer-trns="Short description of items/services purchased to display to your customer"
                               data-request-lang="en-GB"
                               data-source-code="5967"
                               data-product-id="<?= $v["ID"]?>">
                                <?= $v["NAME"]?>
                            </a>
                        </h2>
                        <!--<div class="card-chicks-item__price"><?/*= $v["PROPERTY_PRICE_VALUE"]*/?> ₽</div>-->
                        <div class="card-chicks-item__price">€ <?= $v["PROPERTY_PRICE_VALUE"]?></div>
                    </div>
                </div>
            </div>
        <?endforeach;?>
    </div>
</div>
<?
global $USER;
$userId = $USER->GetID();
$user = CUser::GetByID($userId)->Fetch();?>
<script>
    $(function(){
        $('.card-chicks-item .card-chicks-item__offer a').on('click', function(e){
            e.preventDefault();
            let data = {
                action: 'getOrderCode',
                amount: $(this).data('amount') * 100,
                email: $(this).data('email'),
                fullName: $(this).data('full-name'),
                customerTrns: $(this).data('customer-trns'),
                requestLang: $(this).data('request-lang'),
                sourceCode: $(this).data('source-code'),
                tags: [
                    JSON.stringify({
                        id: $(this).data('product-id'),
                        email: '<?= $user["EMAIL"]?>',
                        name: 'Не требуется',
                        phone: '89533921212'
                    })
                ]
            };
            $.ajax({
                type: 'post',
                url: '/ajax/vivapayments.php',
                data: data,
                cache: false,
                async: false,
                success: function (data)
                {
                    let response = JSON.parse(data);
                    if(response.OrderCode)
                    {
                        location.href = "https://www.vivapayments.com/web/checkout?ref=" + response.OrderCode;
                    }
                }
            });
            return false;
        });
    });
</script>
<section class="game-store-adv">
    <div class="container">
        <h2 class="game-store-adv__heading">Что я могу делать за "чики"?</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="game-store-adv__anons">
                    Чики — игровая валюта KICKGAME. Используя её, можно открывать дополнительные возможности для игры на платформе, такие как ранняя регистрация на игры и участие в турнирах.
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="game-store-adv__wrap-adv">
                    <div class="game-store-adv__el game-store-adv__el_top-left"></div>
                    <div class="game-store-adv__el game-store-adv__el_top-right"></div>
                    <div class="game-store-adv__el game-store-adv__el_bottom-left"></div>
                    <div class="game-store-adv__el game-store-adv__el_bottom-right"></div>
                    <div class="row justify-content-center">
                        <div class="col-md-12 col-lg-4">
                            <div class="game-store-adv-item">
                                <div class="game-store-adv-item__icon">
                                    <img src="<?= SITE_TEMPLATE_PATH?>/dist/images/icon-adv-gs-1.svg" alt="">
                                </div>
                                <div class="game-store-adv-item__heading">
                                    Оплачивать раннюю
                                    регистрацию на игры
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4">
                            <div class="game-store-adv-item">
                                <div class="game-store-adv-item__icon">
                                    <img src="<?= SITE_TEMPLATE_PATH?>/dist/images/icon-adv-gs-2.svg" alt="">
                                </div>
                                <div class="game-store-adv-item__heading">
                                    Покупать вход на турниры
                                    и специальные мероприятия
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="game-store-adv__description">
            Итоговая стоимость может незначительно отличаться в зависимости от способа оплаты. Чтобы увидеть итоговую стоимость, нажмите на кнопку выбранного способа оплаты. Более подробную информацию вы найдёте в Юридических документах. KickGame — онлайн-магазин, зарегистрированный по адресу 105, Agion Omologiton Avenue, Никосия 1080, Кипр
        </div>
    </div>
</section>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
