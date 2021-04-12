<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
?>

<?php if(!CSite::InDir('/personal/auth/')) { ?>
  </div><!-- end layout__content -->
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="footer__logo">
            <a href="/"><img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/logo-footer.svg" alt="kickgame"></a>
          </div>
          <div class="footer__info-about">
            <p>Весь контент, названия игр, торговые наименования и/или коммерческий внешний вид, товарные знаки, произведения искусства и связанные изображения являются товарными знаками и/или материалами, защищенными авторским правом соответствующих правообладателей.</p>
            <p>KICKGAME ESPORTS LIMITED (рег.номер компании HE 416108)<br>
              Адрес: Василий Михайлиди, 21, Лимассол 3026, Кипр <br>
              © KICKGAME ESPORTS, <?php echo date('Y')?>. Все права защищены.<br>
            email: <a href="mailto:support@kick.game">support@kick.game</a><br>
              tel: <a href="tel:+35799934485">+35799934485</a></p>
          </div>
          <div class="social-networks">
            <a class="social-networks__item" href="https://vm.tiktok.com/ZSEfnbCf/" target="_blank">
            </a>
            <a class="social-networks__item" href="https://t.me/joinchat/VdYRn7YOnT-ll6ms" target="_blank">
            </a>
            <a class="social-networks__item" href="https://www.instagram.com/kickgameleague/" target="_blank">
            </a>
            <a class="social-networks__item" href="https://youtube.com/c/KICKGAMEeSports" target="_blank">
            </a>
              <a class="social-networks__item" href="https://discord.gg/wEVwzumwSQ" target="_blank">
              </a>
              <a class="social-networks__item" href="https://vk.com/kick.game" target="_blank">
              </a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-6">
              <ul class="footer-menu">
                <li><a href="/game-schedule/">Расписание</a></li>
                <li><a href="/personal/">Профиль</a></li>
                <li><a href="/teams/">Команды</a></li>
                <li><a href="/players/">Игроки</a></li>
                <li><a href="/subscription-plans/">Подписка </a></li>
              </ul>
            </div>
            <div class="col-md-6">
              <ul class="footer-menu">
                <li><a href="#">Правила подписки</a></li>
                <li><a href="/regulations/">Регламент проведения турниров</a></li>
                <li><a href="/privacy-policy/">Политика конфиденциальности</a></li>
                <li><a href="/terms-conditions/">Пользовательское соглашение</a></li>
                <li><a href="/refund-policy/">Политика возврата средств</a></li>
                  <?if ($USER->IsAuthorized()) { ?>
                  <li><a href="<?=$APPLICATION->GetCurPageParam("logout=yes&".bitrix_sessid_get(), array(
                      "login",
                      "logout",
                      "register",
                      "forgot_password",
                          "change_password"));?>"> Выход</a></li>
                <?php } ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
<?php } ?>
</div><!-- end layout -->
<section id="cookieSection" class="cookie">
  <div class="cookie-wrapper">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8 col-md-12">
          <div class="cookie-content">
            Мы используем файлы cookie для улучшения взаимодействия с пользователем и анализа посещаемости веб-сайта. По этим причинам мы можем передавать данные об использовании вашего сайта нашим партнерам по аналитике. Нажимая «Принять файлы cookie», вы соглашаетесь сохранить на своем устройстве все технологии, описанные в нашей Политике использования файлов cookie . Вы можете изменить настройки файлов cookie в любое время, нажав «<a
              href="#">Настройки</a> файлов cookie».
          </div>
        </div>
        <div class="col-lg-4 col-md-12">
          <div class="cookie__btn">
            <button class="btn-icon btn-icon_manage btn-icon_default mr-1" data-toggle="modal" data-target="#staticBackdrop"><i></i>Настроить</button>
            <button class="btn-icon btn-icon_check cookieAccept"><i></i> Принять</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<div class="modal fade " id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-modal-close" data-dismiss="modal" aria-label="Close">
          <i></i>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn mr-3" data-dismiss="modal">Отключить все</button>
        <button type="button" class="btn cookieAccept" data-dismiss="modal">Принять</button>
      </div>
    </div>
  </div>
</div>
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
    fbq('init', '1484314341910243');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
               src="https://www.facebook.com/tr?id=1484314341910243&ev=PageView&noscript=1"
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