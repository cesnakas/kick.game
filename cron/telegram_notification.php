<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_BUFFER_USED", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
set_time_limit(0);

$intervals = array(
    array(0, 1),
    array(1, 4)
);

$text = "Игра №#NUM# начинается через #HOUR# #HOUR_WORD#. Ссылка на трансляцию: #URL# PUBG LOBBY ID : #LOBBY# Количество команд: #TEAM# Пароль: kick";
CustomTelegram::scan($intervals[0], 0, "Kick.game", $text);
//CustomTelegram::scan($intervals[0], 1, "Test Name", $text);
CustomTelegram::scan($intervals[1], 0, "Kick.game", $text);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php"); 
