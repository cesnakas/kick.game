<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
?>
<!DOCTYPE html>
<html>
<head>
    <? $APPLICATION->ShowHead(); ?>
    <title><? $APPLICATION->ShowTitle(); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <style>
        img {
            max-width: 100%;
            height: auto;
        }

        .btn-success-new {
            width: 3px;
            height: 5px;
            background: red;
        }
    </style>
    <?php
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/dist/css/bootstrap-datetimepicker.min.css');
    $asset = \Bitrix\Main\Page\Asset::getInstance();


    //$asset->addJs(SITE_TEMPLATE_PATH . '/dist/js/lib.js');
    //$asset->addJs(SITE_TEMPLATE_PATH . '/dist/js/main.js');

    ?>
</head>
<body>

<div id="panel">
    <? $APPLICATION->ShowPanel(); ?>
</div>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="<?= SITE_DIR; ?>">KICKGAME</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <?php if ($USER->IsAuthorized()) { ?>
                <ul class="navbar-nav m-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="<?= SITE_DIR; ?>">Главная <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_DIR; ?>game-schedule/">Расписание</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Рейтинг Команды/Игрока</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Подписка</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            РУС
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/en/">En</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Support
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="<?= SITE_DIR; ?>matches/">Matches</a>
                            <a class="dropdown-item" href="<?= SITE_DIR; ?>teams/">Teams</a>
                            <a class="dropdown-item" href="<?= SITE_DIR; ?>dashboard/">Dashboard</a>
                            <a class="dropdown-item" href="<?= SITE_DIR; ?>referee/">Referee</a>
                        </div>
                    </li>

                </ul>

            <?php } ?>
            <? if ($USER->IsAuthorized()):
                $name = CUser::GetLogin() . ' ';
                /*if (! $name)
                    $name = trim($USER->GetLogin());
                if (strlen($name) > 10)
                    $name = substr($name, 0, 10).' ...';*/
                ?>
                <a href="<?= SITE_DIR; ?>personal/" style="margin-right: 10px"><?= htmlspecialcharsbx($name); ?></a>
                <a href="<?= $APPLICATION->GetCurPageParam("logout=yes&" . bitrix_sessid_get(), array(
                    "login",
                    "logout",
                    "register",
                    "forgot_password",
                    "change_password")); ?>" class="btn btn-info"> Выход</a>
            <? else: ?>
                <div class="ml-auto">
                    <a href="<?= SITE_DIR; ?>personal/auth/?login=yes" class="btn btn-info mr-2">Войти</a> <a
                            href="<?= SITE_DIR; ?>personal/auth/reg.php" class="btn btn-info">Регистрация</a>
                </div>
            <? endif ?>

        </div>
    </div>
</nav>
