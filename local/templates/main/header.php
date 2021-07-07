<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
//
function getUserRating($userID) {
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
<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID;?>">
<head>

<?php
    $APPLICATION->ShowHead();
    use Bitrix\Main\Page\Asset;
    $asset = \Bitrix\Main\Page\Asset::getInstance();
    $asset->addString('<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">');
    $asset->addString('<meta http-equiv="X-UA-Compatible" content="ie=edge">');
    // jquery
    $asset->addJs('https://code.jquery.com/jquery-3.6.0.min.js');
    // custom
    $asset->addCss(SITE_TEMPLATE_PATH.'/dist/css/main.css');
    $asset->addJs(SITE_TEMPLATE_PATH.'/dist/js/main.js');
?>

    <title><?$APPLICATION->ShowTitle();?></title>

</head>
<body>

    <?$APPLICATION->ShowPanel();?>

    <header class="navbar navbar-expand-lg navbar-dark fixed-top" <?=($USER->IsAdmin()) ? 'style="top:auto;"' : '';?>>
        <div class="container">

            <? if($USER->IsAuthorized()): ?>
            <a class="d-lg-none" href="#">
                <div class="nav__user-userpic">
                <? if (!empty($arUser['PERSONAL_PHOTO'])): ?>
                    <?= CFile::ShowImage($arUser['PERSONAL_PHOTO'],40,40,'alt="userpic"', false); ?>
                <? else: ?>
                    <svg width="40" height="40" role="img" aria-label="userpic">
                        <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/dist/img/icons.svg#user-sm"/>
                    </svg>
                <? endif; ?>
                    <span class="nav__user-stat">
                        <?=getUserRating($arUser["ID"]);?>
                    </span>
                </div>
            </a>
            <? endif; ?>

            <a class="navbar-brand" href="<?=SITE_DIR?>~index.php">
                <svg width="154" height="17" fill="#FFE500" role="img" aria-label="KICKGAME">
                    <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/dist/img/logo.svg#logo"/>
                </svg>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <svg width="40" height="10" class="mb-1" aria-hidden="true">
                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#menu"/>
                </svg>
            </button>

            <nav class="collapse navbar-collapse" id="navbarSupportedContent" role="navigation">
                <ul class="navbar-nav align-items-lg-center text-center navbar-nav-scroll">
                    <li class="nav-item d-lg-none">
                        <button class="btn btn-warning px-3">RU</button>
                        <button class="btn btn-outline-warning px-3">EN</button>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/~index.php">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/game-schedule/~index.php">Расписание</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Рейтинги</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Игровой магазин</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Подписка</a>
                    </li>
                    <li class="nav-item dropdown d-none d-lg-block">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            RU
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="#">EN</a></li>
                            <li><a class="dropdown-item" href="#">RU</a></li>
                        </ul>
                    </li>
                    <? if($USER->IsAuthorized()): ?>
                    <li class="nav-item d-lg-none">
                        <a class="nav-link" href="#">Профиль игрока</a>
                    </li>
                    <li class="nav-item d-lg-none">
                        <a class="nav-link" href="#">Команда</a>
                    </li>
                    <li class="nav-item d-lg-none">
                        <a class="nav-link" href="#">Выйти</a>
                    </li>
                    <? else: ?>
                    <li class="nav-item d-lg-none">
                        <div class="d-grid gap-3 gx-3">
                            <a class="btn btn-warning" href="#" role="button">Вход</a>
                            <a class="btn btn-outline-warning" href="#" role="button">Регистрация</a>
                        </div>
                    </li>
                    <? endif; ?>
                    <li class="nav-item d-flex d-lg-none justify-content-center my-3">
                        <a class="d-inline-block mx-2 p-2 text-decoration-none" href="https://vm.tiktok.com/ZSEfnbCf/" target="_blank">
                            <svg width="24" height="24" fill="currentColor" role="img" aria-label="TikTok">
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#social-tiktok"/>
                            </svg>
                        </a>
                        <a class="d-inline-block mx-2 p-2 text-decoration-none" href="#">
                            <svg width="24" height="24" fill="currentColor" role="img" aria-label="Telegram">
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#social-telegram"/>
                            </svg>
                        </a>
                        <a class="d-inline-block mx-2 p-2 text-decoration-none" href="https://www.instagram.com/kickgameleague/" target="_blank">
                            <svg width="24" height="24" fill="currentColor" role="img" aria-label="Instagram">
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#social-instagram"/>
                            </svg>
                        </a>
                        <a class="d-inline-block mx-2 p-2 text-decoration-none" href="#">
                            <svg width="24" height="24" fill="currentColor" role="img" aria-label="YouTube">
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#social-youtube"/>
                            </svg>
                        </a>
                        <a class="d-inline-block mx-2 p-2 text-decoration-none" href="#">
                            <svg width="24" height="24" fill="currentColor" role="img" aria-label="Discord">
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#social-discord"/>
                            </svg>
                        </a>
                        <a class="d-block mx-2 p-2 text-decoration-none" href="https://vk.com/kick.game" target="_blank">
                            <svg width="24" height="24" fill="currentColor" role="img" aria-label="VKontakte">
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#social-vkontakte"/>
                            </svg>
                        </a>
                    </li>
                    <? if(CSite::InDir('/~index.php')): ?>
                        <? if($USER->IsAuthorized()): ?>
                        <li class="nav-item dropdown d-none d-lg-flex">
                            <a class="nav-link dropdown-toggle nav__user-link" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="nav__user-userpic">
                                <? if (!empty($arUser['PERSONAL_PHOTO'])): ?>
                                    <?= CFile::ShowImage($arUser['PERSONAL_PHOTO'],56,56,'alt="user"', false); ?>
                                <? else: ?>
                                    <svg width="56" height="56">
                                        <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/dist/img/icons.svg#user-md"/>
                                    </svg>
                                <? endif; ?>
                                    <span class="nav__user-stat">
                                        <?=getUserRating($arUser["ID"]);?>
                                    </span>
                                </div>
                                <span class="d-none d-xl-block ms-2"><?= htmlspecialchars($arUser['LOGIN']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                                <li><a class="dropdown-item" href="#">Профиль пользователя</a></li>
                                <li><a class="dropdown-item" href="#">Моя команда</a></li>
                                <li><a class="dropdown-item" href="#">Dashboard</a></li>
                                <li><a class="dropdown-item" href="#">Creat Matches</a></li>
                                <li><a class="dropdown-item" href="#">Logout</a></li>
                            </ul>
                        </li>
                        <? else: ?>
                        <li class="nav-item d-none d-lg-flex">
                            <a class="ms-3 btn btn-outline-warning" href="#" role="button">Регистрация</a>
                            <a class="ms-3 btn btn-warning" href="#" role="button">Войти</a>
                        </li>
                        <? endif; ?>
                    <? endif; ?>
                </ul>
            </nav>

            <? if(!CSite::InDir('/~index.php')): ?>
                <? if($USER->IsAuthorized()): ?>
                <div class="navbar-nav dropdown d-none d-lg-flex">
                    <a class="nav-link dropdown-toggle nav__user-link" href="javascript:void(0);" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="nav__user-userpic">
                        <? if (!empty($arUser['PERSONAL_PHOTO'])): ?>
                            <?= CFile::ShowImage($arUser['PERSONAL_PHOTO'],56,56,'alt="user"', false); ?>
                        <? else: ?>
                            <svg width="56" height="56">
                                <use xlink:href="<?=SITE_TEMPLATE_PATH;?>/dist/img/icons.svg#user-md"/>
                            </svg>
                        <? endif; ?>
                            <span class="nav__user-stat">
                                <?=getUserRating($arUser["ID"]);?>
                            </span>
                        </div>
                        <span class="d-none d-xl-block ms-2"><?= htmlspecialchars($arUser['LOGIN']); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="#">Профиль пользователя</a></li>
                        <li><a class="dropdown-item" href="#">Моя команда</a></li>
                        <li><a class="dropdown-item" href="#">Dashboard</a></li>
                        <li><a class="dropdown-item" href="#">Creat Matches</a></li>
                        <li><a class="dropdown-item" href="#">Выйти</a></li>
                    </ul>
                </div>
                <? else: ?>
                <div class="navbar-nav d-none d-lg-flex">
                    <a class="btn btn-outline-warning" href="#" role="button">Регистрация</a>
                    <a class="ms-3 btn btn-warning" href="#" role="button">Войти</a>
                </div>
                <? endif; ?>
            <? endif; ?>

        </div>
    </header>

    <main>
