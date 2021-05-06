<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
?>
<footer class="footer">
  <div class="footer__info">
            <svg width="154" height="17" fill="currentColor" class="footer__logo">
                <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/img/logo.svg#logo-footer"/>
            </svg>

            <p><?=GetMessage('FOOTER_CONTENT_P_1')?></p>
            <p>
                <?=GetMessage('FOOTER_CONTENT_P_2_1')?>
                <br>
                <?=GetMessage('FOOTER_CONTENT_P_2_2')?>
                <br>
                © KICKGAME ESPORTS, <?=date('Y')?>. <?=GetMessage('FOOTER_CONTENT_P_2_3')?>.
                <br>
          </p>

    <div class="footer__contacts">
      <div class="footer__contact-info">
         <a href="mailto:support@kick.game" type="email" class="footer__contacts-link">support@kick.game</a>
        <a href="tel:+35799934485" type="tel" class="footer__contacts-link">+35799934485</a>
      </div>

    </div>
    <div class="footer__socials">
        <a href="https://vm.tiktok.com/ZSEfnbCf/" target="_blank" class="footer__icons">
      <img
        src="<?php echo SITE_TEMPLATE_PATH;?>/images/tiktok.png" alt="tiktok"
        srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/tiktok.png 1x,<?php echo SITE_TEMPLATE_PATH;?>/images/tiktok@2x.png 2x"
      >
        </a>
        <a href="https://t.me/joinchat/VdYRn7YOnT-ll6ms" target="_blank" class="footer__icons">
        <img
                src="<?php echo SITE_TEMPLATE_PATH;?>/images/telegram.png" alt="instagram"
                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/telegram.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/telegram@2x.png 2x"
        >
        </a>
        <a href="https://www.instagram.com/kickgameleague/ " target="_blank" class="footer__icons">
      <img
        src="<?php echo SITE_TEMPLATE_PATH;?>/images/instagram.png" alt="instagram"
        srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/instagram.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/instagram@2x.png 2x"
      >
        </a>
        <a href="https://youtube.com/c/KICKGAMEeSports" target="_blank" class="footer__icons">
            <img
                    src="<?php echo SITE_TEMPLATE_PATH;?>/images/youtube.png" alt="youtube"
                    srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/youtube.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/youtube@2x.png 2x"
            >
        </a>
        <a href="https://discord.gg/wEVwzumwSQ" target="_blank" class="footer__icons">
      <img
        src="<?php echo SITE_TEMPLATE_PATH;?>/images/discord.png" alt="discord"
        srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/discord.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/discord@2x.png 2x"
      >
        </a>
        <a href="https://vk.com/kick.game" target="_blank" class="footer__icons"">
            <img
                src="<?php echo SITE_TEMPLATE_PATH;?>/images/vk.png" alt="youtube"
                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/vk.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/vk@2x.png 2x"
            >
        </a>
    </div>
  </div>
  <div class="footer__nav">
    <nav class="footer__nav-item">
        <a class="footer__link" href="<?=SITE_DIR?>game-schedule/"><?=GetMessage('FOOTER_NAV_GAME_SCHEDULE')?></a>
        <a class="footer__link" href="<?=SITE_DIR?>personal/"><?=GetMessage('FOOTER_NAV_PERSONAL')?></a>
        <a class="footer__link" href="<?=SITE_DIR?>players/"><?=GetMessage('FOOTER_NAV_PLAYERS')?></a>
        <a class="footer__link" href="<?=SITE_DIR?>teams/"><?=GetMessage('FOOTER_NAV_TEAMS')?></a>
        <a class="footer__link" href="<?=SITE_DIR?>subscription-plans/"><?=GetMessage('FOOTER_NAV_SUBSCRIPTION_PLANS')?></a>
    </nav>
    <nav class="footer__nav-item">
        <a class="footer__link" href="#"><?=GetMessage('FOOTER_NAV_SUBSCRIPTION_RULES')?></a>
        <a class="footer__link" href="<?=SITE_DIR?>regulations/"><?=GetMessage('FOOTER_NAV_TOURNAMENT_REGULATIONS')?></a>
        <a class="footer__link" href="<?=SITE_DIR?>refund-policy/"><?=GetMessage('FOOTER_NAV_REFUND_POLICY')?></a>
        <a class="footer__link" href="<?=SITE_DIR?>privacy-policy/"><?=GetMessage('FOOTER_NAV_PRIVACY_POLICY')?></a>
    </nav>
  </div>
</footer>
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
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-189243091-1');
</script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(72113281, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/72113281" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>