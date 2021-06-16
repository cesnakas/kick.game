<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
//dump(CUser::IsAuthorized());
//if(!CUser::IsAuthorized()) {
    //LocalRedirect("/");
//}
$bIsMainPage = $APPLICATION->GetCurPage() == SITE_DIR;
$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];

Global $intlFormatter;
$intlFormatter = new IntlDateFormatter('ru_RU', IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
?>
<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID;?>">
<head>

    <script data-skip-moving='true'>
    !function (w, d, t) {
        w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
        ttq.load('C1ESPP48PMMOGUUN3PJ0');
        ttq.page();
    }(window, document, 'ttq');
    </script>

    <script>
    !function (w, d, t) {
        w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
        ttq.load('C1ESPP48PMMOGUUN3PJ0');
        ttq.page();
    }(window, document, 'ttq');
    </script>

<?php
    $APPLICATION->ShowHead();
    use Bitrix\Main\Page\Asset;
    $asset = \Bitrix\Main\Page\Asset::getInstance();
    // meta
    $asset->addString('<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">');
    $asset->addString('<meta http-equiv="X-UA-Compatible" content="ie=edge">');
    // facebook
    $asset->addString('<meta name="facebook-domain-verification" content="y03q78g42fjekeh46ywam7t16oi08r" />');
    $asset->addString('<meta name="theme-color" content="#003982">');
    // css & js
    $asset->addCss(SITE_TEMPLATE_PATH.'/style.css');
    $asset->addJs(SITE_TEMPLATE_PATH.'/script.js');
?>

    <title><?$APPLICATION->ShowTitle();?></title>

    <link rel="apple-touch-icon" href="<?=SITE_TEMPLATE_PATH;?>/images/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="<?=SITE_TEMPLATE_PATH;?>/images/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="<?=SITE_TEMPLATE_PATH;?>/images/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="mask-icon" href="<?=SITE_TEMPLATE_PATH;?>/images/safari-pinned-tab.svg" color="#003982">
    <link rel="icon" href="<?=SITE_TEMPLATE_PATH;?>/images/favicon.ico">

</head>
<body>

    <?$APPLICATION->ShowPanel();?>

    <header class="header">
        <div class="header__nav">

            <div class="header__logo">
                <svg width="154" height="17" fill="currentColor">
                    <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/img/logo.svg#logo"/>
                </svg>
            </div>

        <nav class="header__links">
          <a href="" class="back-link js-back">
            <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46 36"><g filter="url(#filter0_d)"><path d="M17.959 10.464a.999.999 0 01-.003 1.325l-3.015 3.274h18.2c.474 0 .859.42.859.937 0 .518-.385.938-.86.938h-18.2l3.016 3.273a.999.999 0 01.003 1.325.81.81 0 01-1.216.003l-4.49-4.875a1 1 0 01-.001-1.328l4.491-4.876a.81.81 0 011.216.004z" fill="#FFE500"/></g><defs><filter id="filter0_d" x="-1" y="-6" width="48" height="48" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="2"/><feGaussianBlur stdDeviation="6"/><feColorMatrix values="0 0 0 0 0.811765 0 0 0 0 0.72549 0 0 0 0 0.0352941 0 0 0 1 0"/><feBlend in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter></defs></svg>
            <?=GetMessage('NAV_BACK')?>
          </a>
            <a href="<?=SITE_DIR;?>" class="link"><?=GetMessage('NAV_HOME')?></a>
            <a href="<?=SITE_DIR;?>game-schedule/" class="link"><?=GetMessage('NAV_GAME_SCHEDULE')?></a>
            <a href="<?=SITE_DIR;?>teams/" class="link"><?=GetMessage('NAV_TEAMS')?></a>
            <a href="<?=SITE_DIR;?>subscription-plans/" class="link"><?=GetMessage('NAV_SUBSCRIPTION_PLANS')?></a>
          <div class="lang-selector js-lang">
                    <svg width="10" height="10" fill="currentColor">
                        <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/img/icons.svg#lang-selector"/>
                    </svg>

                    <? if (LANGUAGE_ID == 'ru'): ?>
                        <span class="lang-selector__label js-lang-label">RU</span>
                        <svg width="13" height="13" fill="currentColor">
                            <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/img/icons.svg#flag-rus"/>
                        </svg>
                    <? elseif (LANGUAGE_ID == 'en'): ?>
                        <span class="lang-selector__label js-lang-label">EN</span>
                        <svg width="13" height="13" fill="currentColor">
                            <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/img/icons.svg#flag-eng"/>
                        </svg>
                    <? endif; ?>

                <div class="lang-selector__menu">
                    <a class="lang-selector__menu-item <?=(LANGUAGE_ID == 'ru') ? 'lang-selector__menu-item--active' : '';?>" href="/">
                        <?=GetMessage('NAV_LANG_RU')?>
                        <svg width="13" height="13">
                            <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/img/icons.svg#flag-rus"/>
                        </svg>
                    </a>
                    <a class="lang-selector__menu-item <?=(LANGUAGE_ID == 'en') ? 'lang-selector__menu-item--active' : '';?>" href="/en/">
                        <?=GetMessage('NAV_LANG_EN')?>
                        <svg width="13" height="13">
                            <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/img/icons.svg#flag-eng"/>
                        </svg>
                    </a>
                </div>
            </div>

                <? if (!$USER->IsAuthorized()): ?>
                    <a href="<?=SITE_DIR;?>personal/auth/reg.php"
                       class="button button--small"><?=GetMessage('NAV_REGISTER')?></a>
                    <a href="<?=SITE_DIR;?>personal/auth/" class="link"><?=GetMessage('NAV_LOGIN')?></a>
                <? else: ?>
                    <a href="<?= SITE_DIR ?>personal/" class="link"><?=GetMessage('NAV_PERSONAL')?></a>
                <? endif; ?>

        </nav>
        <button type="button" class="menu-button js-toggle-menu">
          <img
            width="60"
            src="<?php echo SITE_TEMPLATE_PATH;?>/images/menu-button.png" alt="menu-button"
            srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/menu-button.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/menu-button@2x.png 2x"
          >
        </button>
            <? if ($USER->IsAuthorized()) { ?>
                <a href="<?=SITE_DIR?>personal/" type="button" class="menu-button">
              <img
                width="60"
                src="<?php echo SITE_TEMPLATE_PATH;?>/images/icon-profile.png" alt="icon-profile"
                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/icon-profile.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/icon-profile@2x.png 2x"
              >
            </a>
          <?php } ?>


      </div>
      <div class="header__cta cta">
        <h1 class="title">KICKGAME</h1>
        <span class="subtitle"><?=GetMessage('MAIN_SUBTITLE')?></span>
        <div class="cta__features">
          <div class="cta__feature">
            <img
              src="<?php echo SITE_TEMPLATE_PATH;?>/images/duo.png" alt="duo"
              srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/duo.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/duo@2x.png 2x"
            >
                <?=GetMessage('MAIN_DUO')?>
          </div>
          <div class="cta__feature">
            <img
              src="<?php echo SITE_TEMPLATE_PATH;?>/images/squad.png" alt="squad"
              srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/squad.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/squad@2x.png 2x"
            >
                    <?=GetMessage('MAIN_SQUAD')?>
          </div>
          <div class="cta__feature">
            <img
              src="<?php echo SITE_TEMPLATE_PATH;?>/images/customs.png" alt="customs"
              srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/customs.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/customs@2x.png 2x"
            >
                    <?=GetMessage('MAIN_CUSTOMS')?>
          </div>
          <div class="cta__feature">
            <img
              src="<?php echo SITE_TEMPLATE_PATH;?>/images/free.png" alt="free"
              srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/free.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/free@2x.png 2x"
            >
                    <?=GetMessage('MAIN_FREE')?>
          </div>
        </div>
        <div class="cta__action">
            <?php if (!$USER->IsAuthorized()) { ?>
                    <a href="<?=SITE_DIR?>personal/auth/reg.php" class="button button"><?=GetMessage('MAIN_BTN_ACTION')?></a>
            <?php } else { ?>
                    <a href="<?=SITE_DIR?>personal/" class="button button"><?=GetMessage('MAIN_BTN_ACTION_LOGIN')?></a>
            <?php } ?>
                <?=GetMessage('MAIN_WAY')?>
        </div>
        <div class="cta__scroll">
          <svg xmlns="http://www.w3.org/2000/svg" width="25" viewBox="0 0 512 512"><path d="M256 0C156.595 0 75.726 82.14 75.726 183.099v145.807C75.726 429.865 156.595 512 256 512c99.399 0 180.274-81.886 180.274-182.534V183.099C436.274 82.14 355.399 0 256 0zm146.366 329.466c0 81.954-65.656 148.627-146.366 148.627-80.705 0-146.366-66.927-146.366-149.192V183.099c0-82.265 65.661-149.192 146.366-149.192 80.711 0 146.366 66.927 146.366 149.192v146.367z"/><path d="M256 140.15c-9.364 0-16.954 7.59-16.954 16.954v59.338c0 9.364 7.59 16.954 16.954 16.954s16.954-7.59 16.954-16.954v-59.338c0-9.364-7.59-16.954-16.954-16.954z"/></svg>
        </div>
      </div>
    </header>