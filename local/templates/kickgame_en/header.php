<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();
use Bitrix\Main\Page\Asset;
//dump(CUser::IsAuthorized());
//if(!CUser::IsAuthorized()) {
    //LocalRedirect("/");
//}
$bIsMainPage = $APPLICATION->GetCurPage() == SITE_DIR;
$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];
$isCaptainHeader = isCaptainHeader($userID, $teamID);
?>
<!doctype html>
<html lang="<?=LANGUAGE_ID;?>">
<head>

    <script data-skip-moving='true'>
      !function (w, d, t) {
          w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};


          ttq.load('C1ESPP48PMMOGUUN3PJ0');
          ttq.page();
      }(window, document, 'ttq');
  </script>

    <?
    $APPLICATION->ShowHead();
    $asset = \Bitrix\Main\Page\Asset::getInstance();
    // meta
    $asset->addString('<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, shrink-to-fit=no, viewport-fit=cover, user-scalable=0">');
    $asset->addString('<meta http-equiv="X-UA-Compatible" content="ie=edge">');
    $asset->addString('<meta name="theme-color" content="#003982">');
    // css & js
    $asset->addCss(SITE_TEMPLATE_PATH.'/dist/css/main.css');
    $asset->addJs(SITE_TEMPLATE_PATH.'/dist/js/lib.js');
    $asset->addJs(SITE_TEMPLATE_PATH.'/dist/js/main.js');
    // $asset->addString("<script  data-skip-moving='true'>alert('1')</script>");
    ?>

    <title><?$APPLICATION->ShowTitle();?></title>

    <link rel="apple-touch-icon" href="<?=SITE_TEMPLATE_PATH;?>/dist/images/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="<?=SITE_TEMPLATE_PATH;?>/dist/images/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="<?=SITE_TEMPLATE_PATH;?>/dist/images/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="mask-icon" href="<?=SITE_TEMPLATE_PATH;?>/dist/images/safari-pinned-tab.svg" color="#003982">
    <link rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH;?>/dist/images/favicon.ico">

</head>
<body class="preload">

    <?$APPLICATION->ShowPanel();?>

    <div class="layout <?php if($userID == 1) echo ' pt-5'?>">
      <?php if(!CSite::InDir('/personal/auth/')) { ?>
      <header class="header">
        <div class="container">
          <nav class="navbar">
            <div class="navbar__logo">
              <a href="/"><img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/logo.svg" alt="logo"></a>
            </div>
            <div class="navbar__burger" id="navbar__burger">
              <button></button>
            </div>
            <div class="navbar-link" id="navbar-link">
              <ul class="navbar__nav">
                <li><a href="/">Главная</a></li>
                <li><a href="<?=SITE_DIR;?>game-schedule/">Расписание</a></li>
                <li><a href="/teams/">Рейтинги</a></li>
                <li><a href="/subscription-plans/">Подписка</a></li>
                <?php if ($USER->IsAuthorized()) { ?>
                <li class="nav-link-exit"><a href="<?=$APPLICATION->GetCurPageParam("logout=yes&".bitrix_sessid_get(), array(
                        "login",
                        "logout",
                        "register",
                        "forgot_password",
                        "change_password"));?>" class="color-red ">Выход</a></li>
                <?php } ?>
              </ul>
              <ul class="navbar__lang">
                <li class="navbar-dropdown">
                  <a href="#">Рус
                    <i class='navbar-dropdown__icon'></i>
                  </a>
                  <ul class="navbar-dropdown__menu navbar-dropdown__menu_lang">

                    <li class="navbar-dropdown__item">
                      <a href="/" class="nav__link">РУС</a>
                    </li>

                    <li class="navbar-dropdown__item">
                      <a href="/en/" class="nav__link">ENG</a>
                    </li>

                  </ul>
                </li>
              </ul>

            </div>
            <?php if ($USER->IsAuthorized()) { ?>
            <div class="navbar-user ">
              <a href="/personal/">
                <div class="navbar-user__avatar"
                     <?php if (!empty($arUser["PERSONAL_PHOTO"])) { ?>
                       style="background-image: url(<?php echo CFile::GetPath($arUser["PERSONAL_PHOTO"]); ?>)"
                     <?php } else { ?>
                       style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                     <?php } ?>
                >
                  <div class="navbar-user__avatar-rating-bg">
                    <div class="navbar-user__avatar-rating">
                        <?php if(!$arUser['UF_RATING']) { ?>
                          300
                        <?php } else { ?>
                            <?php echo $arUser['UF_RATING'];?>
                        <?php } ?>
                    </div>
                  </div>
                </div>
              </a>
              <ul class="navbar-user__menu">
                <li class="navbar-dropdown">
                  <a href="/personal/"><?php echo htmlspecialchars($arUser["LOGIN"]); ?>
                    <i class='navbar-dropdown__icon'></i>
                  </a>
                  <ul class="navbar-dropdown__menu">
                    <li class="navbar-dropdown__item">
                      <a href="/personal/" class="nav__link">Профиль</a>
                    </li>
                    <?php if ($isCaptainHeader) { ?>
                    <li class="navbar-dropdown__item">
                      <a href="/management-compositional/" class="nav__link">Моя команда</a>
                    </li>
                    <li class="navbar-dropdown__item">
                      <a href="/management-games/" class="nav__link">Управление играми</a>
                    </li>
                    <?php }?>
                    <?php if ( CSite::InGroup( array(1,8) ) ) { ?>
                      <li class="navbar-dropdown__item">
                        <a href="/dashboard/" class="nav__link">Dashboard</a>
                      </li>
                      <li class="navbar-dropdown__item">
                        <a href="/referee/" class="nav__link">Create matches</a>
                      </li>
                    <?php } ?>
                    <li class="navbar-dropdown__item">
                      <a href="<?=$APPLICATION->GetCurPageParam("logout=yes&".bitrix_sessid_get(), array(
                          "login",
                          "logout",
                          "register",
                          "forgot_password",
                          "change_password"));?>" class="nav__link color-red">Выход</a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
            <?php } else { ?>
            <div class="navbar-no-auth">
              <a class="btn btn_auth btn_border" href="/personal/auth/reg.php">Регистрация</a> <a class="btn btn_auth" href="/personal/auth/">Вход</a>
            </div>
            <div class="navbar-no-auth-mobile">
              <a class="btn-sign-in" href="/personal/auth/"><i></i> Вход</a>
            </div>
            <?php } ?>
          </nav>
        </div>
      </header>
      <div class="layout__content">
      <?php } ?>