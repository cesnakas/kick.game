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
$datePremExp = $arUser['UF_DATE_PREM_EXP'];
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
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
		<?$APPLICATION->ShowHead();?>
		<title><?$APPLICATION->ShowTitle();?></title>
    <link rel="apple-touch-icon" href="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="mask-icon" href="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/safari-pinned-tab.svg" color="#003982">
    <link rel="icon" href="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/favicon.ico">
        <?php
        $asset = \Bitrix\Main\Page\Asset::getInstance();
        $asset->addCss(SITE_TEMPLATE_PATH. "/dist/css/main.css");
        $asset->addCss(SITE_TEMPLATE_PATH. "/dist/css/schedule.css");
        $asset->addJs(SITE_TEMPLATE_PATH . '/dist/js/lib.js');
        $asset->addJs(SITE_TEMPLATE_PATH . '/dist/js/main.js');
        //$asset->addString("<script  data-skip-moving='true'>alert('1')</script>");

        function getUserRating($userID){

            GLOBAL $DB;

            $sql = 'SELECT u.ID, IF(total IS NOT Null,total, 0) + IF(r.UF_RATING IS NOT Null, r.UF_RATING, 300) as total
      FROM  b_user as u 
            LEFT JOIN (SELECT t.USER_ID, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills FROM b_squad_member_result AS t WHERE t.TYPE_MATCH = 6 GROUP BY t.USER_ID) AS r1 ON r1.USER_ID = u.ID 
            LEFT JOIN b_uts_user AS r ON r.VALUE_ID = u.ID
            WHERE u.ID = '. $userID;

            $res = $DB->Query($sql);
            $players = [];
            while( $row = $res->Fetch() ) {
                $player['total'] = $row['total'];
            }
            return $player["total"];
        }
        ?>

    <meta name="theme-color" content="#003982">

	</head>
    <body class="preload">
    <?$APPLICATION->ShowPanel();?>
    <div class="layout <?php if($userID == 1) echo ' pt-5'?>">
      <?php if(!CSite::InDir('/personal/auth/')) { ?>
      <header class="header">
        <div class="container">
          <nav class="navbar">
            <div class="navbar__logo">
                <a href="<?=SITE_DIR;?>"><img src="<?=SITE_TEMPLATE_PATH;?>/dist/images/logo.svg" alt="logo"></a>
            </div>
            <div class="navbar__burger" id="navbar__burger">
              <button></button>
            </div>
            <div class="navbar-link" id="navbar-link">
              <ul class="navbar__nav">
                <li><a href="<?=SITE_DIR;?>"><?=GetMessage('NAV_HOME')?></a></li>
                <li><a href="<?=SITE_DIR;?>game-schedule/"><?=GetMessage('NAV_TIMETABLE')?></a></li>
                <li><a href="<?=SITE_DIR;?>teams/"><?=GetMessage('NAV_RATINGS')?></a></li>
                <li><a href="<?=SITE_DIR;?>subscription-plans/"><?=GetMessage('NAV_SUBSCRIPTION')?></a></li>
                <?php if ($USER->IsAuthorized()) { ?>
                <li class="nav-link-exit"><a href="<?=$APPLICATION->GetCurPageParam("logout=yes&".bitrix_sessid_get(), array(
                        "login",
                        "logout",
                        "register",
                        "forgot_password",
                        "change_password"));?>" class="color-red "><?=GetMessage('NAV_LOGOUT')?></a></li>
                <?php } ?>
              </ul>
              <ul class="navbar__lang">
                <li class="navbar-dropdown">
                    <a href="javascript:void(0);">
                        <?=GetMessage('NAV_LANG')?>
                    <i class='navbar-dropdown__icon'></i>
                  </a>
                  <ul class="navbar-dropdown__menu navbar-dropdown__menu_lang">
                  <? $le = substr($_SERVER['REQUEST_URI'], 3); ?>
                    <li class="navbar-dropdown__item">
                        <?='<a class="nav__link" href="//'.$_SERVER['SERVER_NAME'].$le.'">РУС</a>';?>
                    </li>
                    <li class="navbar-dropdown__item">
                        <? if (SITE_DIR == '/en/'): ?>
                        <?='<a class="nav__link" href="//'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'">ENG</a>';?>
                        <? else: ?>
                        <?='<a class="nav__link" href="//'.$_SERVER['SERVER_NAME'].'/en'.$_SERVER['REQUEST_URI'].'">ENG</a>';?>
                        <? endif; ?>
                    </li>
                  </ul>
                </li>
              </ul>

            </div>
            <?php if ($USER->IsAuthorized()) {
                ?>
            <div class="navbar-user ">
                <a href="<?=SITE_DIR;?>personal/">
                <div class="navbar-user__avatar"
                     <?php if (!empty($arUser["PERSONAL_PHOTO"])) { ?>
                       style="background-image: url(<?php echo CFile::GetPath($arUser["PERSONAL_PHOTO"]); ?>)"
                     <?php } else { ?>
                       style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
                     <?php } ?>
                >
                  <div class="navbar-user__avatar-rating-bg">
                    <div class="navbar-user__avatar-rating">

                            <?php echo getUserRating($arUser["ID"]);?>

                    </div>
                  </div>
                </div>
              </a>
              <ul class="navbar-user__menu">
                <li class="navbar-dropdown">
                  <a href="<?=SITE_DIR?>personal/"><?php echo htmlspecialchars($arUser["LOGIN"]); ?>
                    <i class='navbar-dropdown__icon'></i>
                  </a>
                  <ul class="navbar-dropdown__menu">
                    <li class="navbar-dropdown__item">
                        <a href="<?=SITE_DIR;?>personal/" class="nav__link"><?=GetMessage('NAV_PERSONAL')?></a>
                    </li>
                    <?php if ($isCaptainHeader) { ?>
                    <li class="navbar-dropdown__item">
                      <a href="<?=SITE_DIR?>management-compositional/" class="nav__link"><?=GetMessage('NAV_MY_TEAM')?></a>
                    </li>
                        <?php if(isPrem($arUser['UF_DATE_PREM_EXP']) > 0 ) { ?>
                    <li class="navbar-dropdown__item">
                      <a href="<?=SITE_DIR?>management-games/" class="nav__link"><?=GetMessage('NAV_GAMES_MANAGEMENT')?></a>
                    </li>
                        <?php }?>
                    <?php }?>
                    <?php if ( CSite::InGroup( array(1,8) ) ) { ?>
                      <li class="navbar-dropdown__item">
                        <a href="<?=SITE_DIR;?>dashboard/" class="nav__link"><?=GetMessage('NAV_DASHBOARD')?></a>
                      </li>
                    <?php } ?>
            <?php if ( CSite::InGroup( array(1) ) ) { ?>
                      <li class="navbar-dropdown__item">
                        <a href="<?=SITE_DIR;?>referee/" class="nav__link"><?=GetMessage('NAV_CREATE_MATCHES')?></a>
                      </li>
                    <?php } ?>
                    <li class="navbar-dropdown__item">
                      <a href="<?=$APPLICATION->GetCurPageParam("logout=yes&".bitrix_sessid_get(), array(
                          "login",
                          "logout",
                          "register",
                          "forgot_password",
                          "change_password"));?>" class="nav__link color-red"><?=GetMessage('NAV_LOGOUT')?></a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
            <?php } else { ?>
            <div class="navbar-no-auth">
              <a class="btn btn_auth btn_border" href="<?=SITE_DIR?>personal/auth/reg.php"><?=GetMessage('NAV_REGISTER')?></a> <a class="btn btn_auth" href="<?=SITE_DIR?>personal/auth/"><?=GetMessage('NAV_LOGIN')?></a>
            </div>
            <div class="navbar-no-auth-mobile">
              <a class="btn-sign-in" href="<?=SITE_DIR?>personal/auth/"><i></i> <?=GetMessage('NAV_LOGIN')?></a>
            </div>
            <?php } ?>
          </nav>
        </div>
      </header>
      <div class="layout__content">
      <?php } ?>