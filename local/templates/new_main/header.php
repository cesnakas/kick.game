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
                    <svg width="30" height="22" fill="currentColor">
                        <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/img/icons.svg#arrow-back"/>
                    </svg>
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
                    <a href="<?=SITE_DIR;?>personal/auth/reg.php" class="button button--small"><?=GetMessage('NAV_REGISTER')?></a>
                    <a href="<?=SITE_DIR;?>personal/auth/" class="link"><?=GetMessage('NAV_LOGIN')?></a>
                <? else: ?>
                    <a href="<?= SITE_DIR ?>personal/" class="link"><?=GetMessage('NAV_PERSONAL')?></a>
                <? endif; ?>
            </nav>
            <button type="button" class="menu-button js-toggle-menu">
                <img
                    width="60"
                    src="<?=SITE_TEMPLATE_PATH;?>/images/menu-button.png" alt="menu-button"
                    srcset="<?=SITE_TEMPLATE_PATH;?>/images/menu-button.png 1x, <?=SITE_TEMPLATE_PATH;?>/images/menu-button@2x.png 2x"
                />
            </button>
            <? if ($USER->IsAuthorized()): ?>
            <a href="<?=SITE_DIR?>personal/" type="button" class="menu-button">
                <img
                    width="60"
                    src="<?=SITE_TEMPLATE_PATH;?>/images/icon-profile.png" alt="icon-profile"
                    srcset="<?=SITE_TEMPLATE_PATH;?>/images/icon-profile.png 1x, <?=SITE_TEMPLATE_PATH;?>/images/icon-profile@2x.png 2x"
                />
            </a>
            <? endif; ?>
        </div>
        <div class="header__cta cta">
            <h1 class="title">KICKGAME</h1>
            <span class="subtitle"><?=GetMessage('MAIN_SUBTITLE')?></span>
            <div class="cta__features">
                <div class="cta__feature">
                    <img
                        src="<?=SITE_TEMPLATE_PATH;?>/images/duo.png" alt="duo"
                        srcset="<?=SITE_TEMPLATE_PATH;?>/images/duo.png 1x, <?=SITE_TEMPLATE_PATH;?>/images/duo@2x.png 2x"
                    />
                    <?=GetMessage('MAIN_DUO')?>
                </div>
                <div class="cta__feature">
                    <img
                        src="<?=SITE_TEMPLATE_PATH;?>/images/squad.png" alt="squad"
                        srcset="<?=SITE_TEMPLATE_PATH;?>/images/squad.png 1x, <?=SITE_TEMPLATE_PATH;?>/images/squad@2x.png 2x"
                    />
                    <?=GetMessage('MAIN_SQUAD')?>
                </div>
                <div class="cta__feature">
                    <img
                        src="<?=SITE_TEMPLATE_PATH;?>/images/customs.png" alt="customs"
                        srcset="<?=SITE_TEMPLATE_PATH;?>/images/customs.png 1x, <?=SITE_TEMPLATE_PATH;?>/images/customs@2x.png 2x"
                    />
                    <?=GetMessage('MAIN_CUSTOMS')?>
                </div>
                <div class="cta__feature">
                    <img
                        src="<?=SITE_TEMPLATE_PATH;?>/images/free.png" alt="free"
                        srcset="<?=SITE_TEMPLATE_PATH;?>/images/free.png 1x, <?=SITE_TEMPLATE_PATH;?>/images/free@2x.png 2x"
                    />
                    <?=GetMessage('MAIN_FREE')?>
                </div>
            </div>
            <div class="cta__action">
            <? if (!$USER->IsAuthorized()): ?>
                <a href="<?=SITE_DIR?>personal/auth/reg.php" class="button button"><?=GetMessage('MAIN_BTN_ACTION')?></a>
            <? else: ?>
                <a href="<?=SITE_DIR?>personal/" class="button button"><?=GetMessage('MAIN_BTN_ACTION_LOGIN')?></a>
            <? endif; ?>
                <?=GetMessage('MAIN_WAY')?>
            </div>
            <div class="cta__scroll">
                <svg width="25" height="25">
                    <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/img/icons.svg#mouse-scroll"/>
                </svg>
            </div>
        </div>
    </header>
