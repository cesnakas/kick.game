<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
?>

<? if (!CSite::InDir('/personal/auth/')) { ?>
    </div><!-- end layout__content -->

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="footer__logo">
                        <a href="/">
                            <img src="<?=SITE_TEMPLATE_PATH;?>/dist/images/logo-footer.svg" alt="kickgame">
                        </a>
                    </div>
                    <div class="footer__info-about">

                        <p><?=GetMessage('FOOTER_CONTENT_P_1')?></p>

                        <p>
                            <?=GetMessage('FOOTER_CONTENT_P_2_1')?>
                            <br>
                            <?=GetMessage('FOOTER_CONTENT_P_2_2')?>
                            <br>
                            © KICKGAME ESPORTS, <?= date('Y') ?>. <?=GetMessage('FOOTER_CONTENT_P_2_3')?>.
                            <br>
                            email: <a href="mailto:support@kick.game">support@kick.game</a><br>
                            tel: <a href="tel:+35799934485">+35799934485</a>
                        </p>
                    </div>
                    <div class="social-networks">
                        <a class="social-networks__item" href="https://vm.tiktok.com/ZSEfnbCf/" target="_blank"></a>
                        <a class="social-networks__item" href="https://t.me/joinchat/VdYRn7YOnT-ll6ms" target="_blank"></a>
                        <a class="social-networks__item" href="https://www.instagram.com/kickgameleague/" target="_blank"></a>
                        <a class="social-networks__item" href="https://youtube.com/c/KICKGAMEeSports" target="_blank"></a>
                        <a class="social-networks__item" href="https://discord.gg/wEVwzumwSQ" target="_blank"></a>
                        <a class="social-networks__item" href="https://vk.com/kick.game" target="_blank"></a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="footer-menu">
                                <li><a href="<?=SITE_DIR?>game-schedule/"><?=GetMessage('FOOTER_NAV_GAME_SCHEDULE')?></a></li>
                                <li><a href="<?=SITE_DIR?>personal/"><?=GetMessage('FOOTER_NAV_PERSONAL')?></a></li>
                                <li><a href="<?=SITE_DIR?>teams/"><?=GetMessage('FOOTER_NAV_TEAMS')?></a></li>
                                <li><a href="<?=SITE_DIR?>players/"><?=GetMessage('FOOTER_NAV_PLAYERS')?></a></li>
                                <li><a href="<?=SITE_DIR?>subscription-plans/"><?=GetMessage('FOOTER_NAV_SUBSCRIPTION_PLANS')?></a></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="footer-menu">
                                <li><a href="#"><?=GetMessage('FOOTER_NAV_SUBSCRIPTION_RULES')?></a></li>
                                <li><a href="<?=SITE_DIR?>regulations/"><?=GetMessage('FOOTER_NAV_TOURNAMENT_REGULATIONS')?></a></li>
                                <li><a href="<?=SITE_DIR?>privacy-policy/"><?=GetMessage('FOOTER_NAV_PRIVACY_POLICY')?></a></li>
                                <li><a href="<?=SITE_DIR?>terms-conditions/"><?=GetMessage('FOOTER_NAV_USER_AGREEMENT')?></a></li>
                                <li><a href="<?=SITE_DIR?>refund-policy/"><?=GetMessage('FOOTER_NAV_REFUND_POLICY')?></a></li>
                                <? if ($USER->IsAuthorized()) { ?>
                                    <li>
                                        <a href="<?= $APPLICATION->GetCurPageParam("logout=yes&" . bitrix_sessid_get(), array(
                                            "login",
                                            "logout",
                                            "register",
                                            "forgot_password",
                                            "change_password")); ?>"
                                        ><?=GetMessage('FOOTER_NAV_LOGOUT')?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<? } ?>

</div><!-- end layout -->
<section id="cookieSection" class="cookie">
    <div class="cookie-wrapper">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-12">
                    <div class="cookie-content">
                        <?=GetMessage('FOOTER_COOKIE')?>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="cookie__btn">
                        <button class="btn-icon btn-icon_manage btn-icon_default mr-1" data-toggle="modal" data-target="#staticBackdrop">
                            <i></i> <?=GetMessage('FOOTER_COOKIE_SETTINGS')?>
                        </button>
                        <button class="btn-icon btn-icon_check cookieAccept">
                            <i></i> <?=GetMessage('FOOTER_COOKIE_ACCEPT')?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="expirePrem" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-modal-close" data-dismiss="modal" aria-label="Close">
                    <i></i>
                </button>
            </div>
            <div class="modal-body">
                <h3 class="modal-body__title"><?=GetMessage('MODAL_TITLE')?></h3>
                <div class="subscription-plans">
                    <div class="subscription-plans__description subscription-plans__description_popup text-center">
                        <?=GetMessage('MODAL_HEADER_TEXT')?>
                    </div>
                    <ul>
                        <li class="not"><?=GetMessage('MODAL_ITEM_01')?></li>
                        <li class="not"><?=GetMessage('MODAL_ITEM_02')?></li>
                        <li class="not"><?=GetMessage('MODAL_ITEM_03')?></li>
                        <li class="not"><?=GetMessage('MODAL_ITEM_04')?></li>
                        <li class="not"><?=GetMessage('MODAL_ITEM_05')?></li>
                        <li class="not"><?=GetMessage('MODAL_ITEM_06')?></li>
                    </ul>
                    <div class="subscription-plans__description subscription-plans__description_popup text-center">
                        <?=GetMessage('MODAL_FOOTER_TEXT')?>
                    </div>
                    <div class="subscription-plans-item__btn text-center">
                        <a href="<?=SITE_DIR?>subscription-plans/" class="btn"><?=GetMessage('MODAL_BUTTON')?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($USER->IsAuthorized()) {
    $resultPrem = isPrem($arUser['UF_DATE_PREM_EXP']); ?>
  <?php   if ($resultPrem <= 0) { ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!sessionStorage.getItem('shown-modal')){
            setTimeout(function () {
                $('#expirePrem').modal('show');
            }, 3000);
            sessionStorage.setItem('shown-modal', 'true');
        }
    });
</script>
      <?php } ?>
<?php } ?>
<!-- Facebook Pixel Code -->
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '191446898308524');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
               src="https://www.facebook.com/tr?id=191446898308524&ev=PageView&noscript=1"
    /></noscript>
<!-- End Facebook Pixel Code -->
<!-- Start of  Zendesk Widget script -->
<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=adaefb0f-5b54-4b83-a508-c98c16194250"> </script>
<!-- End of  Zendesk Widget script -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-189243091-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-189243091-1');
    </script>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (m, e, t, r, i, k, a) {
            m[i] = m[i] || function () {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(72113281, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/72113281" style="position:absolute; left:-9999px;" alt=""/></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->

</body>
</html>