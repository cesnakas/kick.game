<?php
$arUrlRewrite = array(
    2 =>
        array(
            'CONDITION' => '#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
            'RULE' => 'alias=$1',
            'ID' => NULL,
            'PATH' => '/desktop_app/router.php',
            'SORT' => 100,
        ),
    1 =>
        array(
            'CONDITION' => '#^/video([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
            'RULE' => 'alias=$1&videoconf',
            'ID' => NULL,
            'PATH' => '/desktop_app/router.php',
            'SORT' => 100,
        ),
    7 =>
        array(
            'CONDITION' => '#^/personal/sozdat-novuyu-komandu/#',
            'RULE' => '',
            'ID' => 'bitrix:iblock.element.add.form',
            'PATH' => '/personal/sozdat-novuyu-komandu/index.php',
            'SORT' => 100,
        ),
    4 =>
        array(
            'CONDITION' => '#^\\/?\\/mobileapp/jn\\/(.*)\\/.*#',
            'RULE' => 'componentName=$1',
            'ID' => NULL,
            'PATH' => '/bitrix/services/mobileapp/jn.php',
            'SORT' => 100,
        ),
    6 =>
        array(
            'CONDITION' => '#^/bitrix/services/ymarket/#',
            'RULE' => '',
            'ID' => '',
            'PATH' => '/bitrix/services/ymarket/index.php',
            'SORT' => 100,
        ),
    3 =>
        array(
            'CONDITION' => '#^/online/(/?)([^/]*)#',
            'RULE' => '',
            'ID' => NULL,
            'PATH' => '/desktop_app/router.php',
            'SORT' => 100,
        ),
    0 =>
        array(
            'CONDITION' => '#^/stssync/calendar/#',
            'RULE' => '',
            'ID' => 'bitrix:stssync.server',
            'PATH' => '/bitrix/services/stssync/calendar/index.php',
            'SORT' => 100,
        ),
    14 =>
        array(
            'CONDITION' => '#^(/game-schedule/|/en/game-schedule/)#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/game-schedule/index.php',
            'SORT' => 100,
        ),
    12 =>
        array(
            'CONDITION' => '#^(/tournaments/|/en/tournaments/)#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/tournaments/index.php',
            'SORT' => 100,
        ),
    13 =>
        array(
            'CONDITION' => '#^(/matches/|/en/matches/)#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/matches/index.php',
            'SORT' => 100,
        ),
    9 =>
        array(
            'CONDITION' => '#^(/players/|/en/players/)#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/players/index.php',
            'SORT' => 100,
        ),
    8 =>
        array(
            'CONDITION' => '#^/matchi/#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/matchi/index.php',
            'SORT' => 100,
        ),
    10 =>
        array(
            'CONDITION' => '#^(/teams/|/en/teams/)#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/teams/index.php',
            'SORT' => 100,
        ),
    5 =>
        array(
            'CONDITION' => '#^/rest/#',
            'RULE' => '',
            'ID' => NULL,
            'PATH' => '/bitrix/services/rest/index.php',
            'SORT' => 100,
        ),
);
