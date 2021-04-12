<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
//dump(CUser::IsAuthorized());
//if(!CUser::IsAuthorized()) {
    //LocalRedirect("/");
//}
$bIsMainPage = $APPLICATION->GetCurPage() == SITE_DIR;
$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];
?>
<!doctype html>
<html lang="ru">
<head>
  <script data-skip-moving='true'>
      !function (w, d, t) {
          w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
          ttq.load('C1ESPP48PMMOGUUN3PJ0');
          ttq.page();
      }(window, document, 'ttq');
  </script>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
		<?$APPLICATION->ShowHead();?>
		<title><?$APPLICATION->ShowTitle();?></title>
    <link rel="apple-touch-icon" href="<?php echo SITE_TEMPLATE_PATH;?>/images/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="<?php echo SITE_TEMPLATE_PATH;?>/images/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="<?php echo SITE_TEMPLATE_PATH;?>/images/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="mask-icon" href="<?php echo SITE_TEMPLATE_PATH;?>/images/safari-pinned-tab.svg" color="#003982">
    <link rel="icon" href="<?php echo SITE_TEMPLATE_PATH;?>/images/favicon.ico">
        <?php
        $asset = \Bitrix\Main\Page\Asset::getInstance();

        $asset->addCss(SITE_TEMPLATE_PATH. "/style.css");
        $asset->addJs(SITE_TEMPLATE_PATH . '/script.js');
        //Asset::getInstance()->addString("<link rel='shortcut icon' href='/local/images/favicon.ico' />");
        ?>

    <meta name="theme-color" content="#003982">
    <script>
        !function (w, d, t) {
            w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
            ttq.load('C1ESPP48PMMOGUUN3PJ0');
            ttq.page();
        }(window, document, 'ttq');
    </script>
	</head>
    <body>
    <?$APPLICATION->ShowPanel();?>
    <header class="header">
      <div class="header__nav">
        <div class="header__logo">
          <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 154 17"><path d="M21.423 15.927V1.057h3.425v14.87h-3.425zM43.804 15.494c-.447.172-.912.322-1.397.449-.478.127-.989.224-1.518.284-.53.067-1.103.097-1.716.097-1.294 0-2.48-.165-3.565-.486-1.084-.321-2.022-.815-2.806-1.465a6.593 6.593 0 01-1.83-2.445c-.434-.98-.658-2.116-.658-3.424 0-1.308.217-2.444.657-3.424.44-.979 1.046-1.794 1.83-2.444.785-.65 1.723-1.144 2.807-1.466C36.692.85 37.885.685 39.173.685c.613 0 1.18.03 1.716.097.53.067 1.04.157 1.518.284.478.127.944.276 1.397.448.446.172.892.367 1.339.583v3.611c-.35-.224-.72-.448-1.11-.665a10.077 10.077 0 00-1.282-.598 9.457 9.457 0 00-1.543-.434 9.727 9.727 0 00-1.894-.164c-1.078 0-1.971.127-2.685.389-.708.261-1.276.605-1.703 1.039-.428.433-.721.927-.893 1.487a5.865 5.865 0 00-.262 1.735c0 .396.038.785.109 1.173a4.006 4.006 0 001.046 2.041c.274.292.618.539 1.033.748.414.21.893.374 1.448.493.548.12 1.186.18 1.907.18.701 0 1.333-.053 1.894-.157a9.658 9.658 0 001.543-.419 8.351 8.351 0 001.282-.59c.39-.225.76-.449 1.11-.673v3.61c-.44.217-.887.412-1.34.591zM79.985.677c1.243 0 2.398.127 3.47.374a16.94 16.94 0 012.94.956v3.634c-.351-.232-.747-.456-1.187-.673-.44-.217-.918-.404-1.435-.576a12.847 12.847 0 00-1.664-.403 10.699 10.699 0 00-1.863-.15c-.81 0-1.518.067-2.124.202a5.961 5.961 0 00-1.55.553c-.433.232-.784.5-1.058.807a4.01 4.01 0 00-.657.98c-.16.344-.268.703-.332 1.061-.064.36-.095.71-.095 1.047 0 .299.025.62.076.964s.153.696.293 1.04c.147.343.345.68.606.994.262.314.594.598 1.008.845.415.246.919.44 1.499.59.58.15 1.282.224 2.085.224.409 0 .76-.015 1.06-.045.299-.03.573-.067.828-.12.249-.051.485-.111.702-.186.217-.075.446-.15.676-.224v-1.794h-3.84v-3.26h7.265v7.596c-.453.179-.963.343-1.53.493-.562.15-1.142.277-1.748.381-.6.105-1.2.187-1.805.247-.606.06-1.174.09-1.71.09-.829 0-1.626-.068-2.391-.195a10.016 10.016 0 01-2.137-.598 7.795 7.795 0 01-1.811-1.032 6.277 6.277 0 01-1.397-1.495 6.994 6.994 0 01-.893-1.988c-.21-.748-.319-1.585-.319-2.52 0-.92.109-1.756.332-2.511.223-.748.53-1.413.925-1.989a6.414 6.414 0 011.428-1.495 7.964 7.964 0 011.837-1.032c.67-.269 1.384-.47 2.143-.598a14.756 14.756 0 012.373-.194zM100.865 4.736L96.12 15.927H92.3l6.64-14.87h3.819l6.64 14.87h-3.821l-4.713-11.191zM131.364 15.927V5.483l-4.706 10.444h-3.285l-4.707-10.444v10.444h-3.425V1.057h4.656l5.121 11.252 5.122-11.251h4.637v14.869h-3.413zM144.542 12.75v-2.9h8.846V6.68h-12.361v9.247H154V12.75h-9.458zm9.324-8.523v-3.17h-12.839v3.17h12.839zM6.512 8.19l8.54-7.132H10.46L3.444 6.724 0 9.505v6.422h3.444V9.744l7.182 6.183h4.993L6.512 8.19zM3.444 1.058H0v4.037h3.444V1.058zM58.07 8.19l8.54-7.132h-4.592l-7.015 5.666-3.444 2.781v6.422h3.444V9.744l7.181 6.183h4.994L58.07 8.19zM55.003 1.058h-3.444v4.037h3.444V1.058z" fill="#FFE500"/></svg>
        </div>
        <nav class="header__links">
          <a href="" class="back-link js-back">
            <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46 36"><g filter="url(#filter0_d)"><path d="M17.959 10.464a.999.999 0 01-.003 1.325l-3.015 3.274h18.2c.474 0 .859.42.859.937 0 .518-.385.938-.86.938h-18.2l3.016 3.273a.999.999 0 01.003 1.325.81.81 0 01-1.216.003l-4.49-4.875a1 1 0 01-.001-1.328l4.491-4.876a.81.81 0 011.216.004z" fill="#FFE500"/></g><defs><filter id="filter0_d" x="-1" y="-6" width="48" height="48" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="2"/><feGaussianBlur stdDeviation="6"/><feColorMatrix values="0 0 0 0 0.811765 0 0 0 0 0.72549 0 0 0 0 0.0352941 0 0 0 1 0"/><feBlend in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter></defs></svg>
            Назад
          </a>
          <a href="/" class="link">Главная</a>
          <a href="/game-schedule/" class="link">Расписание</a>
          <a href="/teams/" class="link">Рейтинг</a>
          <a href="/subscription-plans/" class="link">Подписка</a>
          <div class="lang-selector js-lang">
            <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 8 8"><path d="M0 4h8L4 8 0 4z" fill="#fff"/></svg>
            <span class="lang-selector__label js-lang-label">
            РУС
          </span>
            <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 32 28"><g><path fill="#0039A6" d="M6 11.5h20v5H6z"/><path d="M6 7.5a1 1 0 011-1h18a1 1 0 011 1v4H6v-4z" fill="#fff"/><path d="M6 16.5h20v4a1 1 0 01-1 1H7a1 1 0 01-1-1v-4z" fill="#E62D3B"/></g></svg>
            <div class="lang-selector__menu">
              <div class="lang-selector__menu-item lang-selector__menu-item--active">
                РУС
                <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 32 28"><g><path fill="#0039A6" d="M6 11.5h20v5H6z"/><path d="M6 7.5a1 1 0 011-1h18a1 1 0 011 1v4H6v-4z" fill="#fff"/><path d="M6 16.5h20v4a1 1 0 01-1 1H7a1 1 0 01-1-1v-4z" fill="#E62D3B"/></g></svg>
              </div>
              <div class="lang-selector__menu-item">
                ENG
                <svg fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" viewBox="0 0 32 28"><g ><path fill="url(#pattern0)" d="M6 6.5h20v15H6z"/></g><defs><pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1"><use xlink:href="#image0" transform="matrix(.015 0 0 .02 -.25 0)"/></pattern><image id="image0" width="100" height="50" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAAAyCAMAAACd646MAAAApVBMVEUAJH1Vbaj///9WbqnPFCvi5vDbTl/zxMoCJn5UbKjU2ulXb6nk6PHl6fLwtLsBJX7T2enbT2DTKD1ZcKpbcqvW3OrY3uuOnsWPn8bfYXHfYnLhbXzhbnzhb33ib32Qn8aRoMcDJ3/niJTniZTojpnoj5rpkZzpk57SJTrwtr3wt77TJjz319v32Nz88vP98/T99PX99fb+9/j++Pj++fr++vvTJzwm3ws1AAABN0lEQVR42u3Xt27FMAxGYYqW2+3pvffe8/6PlqvBGQQaJ1yCIPCZf+DbBEqqWrs21hrpL6hq/E5VW+nvZH1TH8+vY6qqJUZgAGFCdYkAAwgTS+QzAgMIEgl5vgQGECQSMlJgAGFCF9KMgQGEiTAXIYYRJgQYRphghhEmmAEECAdjI0y4GBNBws0YCBJ+psiRAgk/M8mR6c+JYFRsl/p2c7ibOnvQrgx5n+3ni3KrCEaimI14+kdI/IUGZEAG5I8gwwPpQ1qj4niqXfenO6mD248MyRd7s1edHIXWqOcy76qrmFq9etEyR8p8dXfxBL81IBahyJEwHgHjJebWSdQA4yXs4w4YL2EjwHgJGwHGQQDCDBOIMMMEI8wwwQgzRDDCjBDBCDNCBCPMCBGMMCNEMMKMAMEIMytfZZZd/8K7Cz4AAAAASUVORK5CYII="/></defs></svg>
              </div>
            </div>
          </div>
            <?php if (!$USER->IsAuthorized()) { ?>
              <a href="/personal/auth/reg.php" class="button button--small">Регистрация</a>
              <a href="/personal/auth/" class="link">Войти</a>
            <?php } else { ?>
              <a href="/personal/" class="link">Профиль</a>
            <?php } ?>

        </nav>
        <button type="button" class="menu-button js-toggle-menu">
          <img
            width="60"
            src="<?php echo SITE_TEMPLATE_PATH;?>/images/menu-button.png" alt="menu-button"
            srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/menu-button.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/menu-button@2x.png 2x"
          >
        </button>
          <?php if ($USER->IsAuthorized()) { ?>
            <a href="/personal/" type="button" class="menu-button">
              <img
                width="60"
                src="<?php echo SITE_TEMPLATE_PATH;?>/images/icon-profile.png" alt="icon-profile"
                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/icon-profile.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/icon-profile@2x.png 2x"
              >
            </a>
          <?php } ?>


      </div>
      <div class="header__cta cta">
        <h1 class="title"">KICKGAME</h1>
        <span class="subtitle">Твой пропуск в киберспорт, пабгер</span>
        <div class="cta__features">
          <div class="cta__feature">
            <img
              src="<?php echo SITE_TEMPLATE_PATH;?>/images/duo.png" alt="duo"
              srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/duo.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/duo@2x.png 2x"
            >
            Турниры дуо на 100&euro; каждую неделю
          </div>
          <div class="cta__feature">
            <img
              src="<?php echo SITE_TEMPLATE_PATH;?>/images/squad.png" alt="squad"
              srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/squad.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/squad@2x.png 2x"
            >
            Турниры для сквадов на 1000&euro; каждый месяц
          </div>
          <div class="cta__feature">
            <img
              src="<?php echo SITE_TEMPLATE_PATH;?>/images/customs.png" alt="customs"
              srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/customs.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/customs@2x.png 2x"
            >
            Кастомки с призами каждую неделю
          </div>
          <div class="cta__feature">
            <img
              src="<?php echo SITE_TEMPLATE_PATH;?>/images/free.png" alt="free"
              srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/free.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/free@2x.png 2x"
            >
            14 дней, чтобы попробовать все плюшки бесплатно
          </div>
        </div>
        <div class="cta__action">
            <?php if (!$USER->IsAuthorized()) { ?>
              <a href="/personal/auth/reg.php" class="button button">регистрация</a>
            <?php } else { ?>
              <a href="/personal/" class="button button">Войти</a>
            <?php } ?>
          Начни путь к победе сегодня!
        </div>
        <div class="cta__scroll">
          <svg xmlns="http://www.w3.org/2000/svg" width="25" viewBox="0 0 512 512"><path d="M256 0C156.595 0 75.726 82.14 75.726 183.099v145.807C75.726 429.865 156.595 512 256 512c99.399 0 180.274-81.886 180.274-182.534V183.099C436.274 82.14 355.399 0 256 0zm146.366 329.466c0 81.954-65.656 148.627-146.366 148.627-80.705 0-146.366-66.927-146.366-149.192V183.099c0-82.265 65.661-149.192 146.366-149.192 80.711 0 146.366 66.927 146.366 149.192v146.367z"/><path d="M256 140.15c-9.364 0-16.954 7.59-16.954 16.954v59.338c0 9.364 7.59 16.954 16.954 16.954s16.954-7.59 16.954-16.954v-59.338c0-9.364-7.59-16.954-16.954-16.954z"/></svg>
        </div>
      </div>
    </header>