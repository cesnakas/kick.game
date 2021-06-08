<?php
//$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
$_SERVER["DOCUMENT_ROOT"] = "/var/www/www-root/data/www/kickgame.vung.ru";
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

//$text = "Игра №#NUM# начинается через #HOUR# #HOUR_WORD#. Ссылка на трансляцию: #URL# PUBG LOBBY ID : #LOBBY# Количество команд: #TEAM# Пароль: kick";
$text = "Ваша команда зарегистрирована на #NAME# в #TIME#.\\nВаш Слот: #SLOT#\\nLobby ID:  за 10 минут до старта\\nPassword: kick\\n\\nВыдержка из правил:\\n1. Ракетницы запрещены\\n2. Софт запрещён\\n3. Эмуляторы запрещены\\n4. Неявка на событие - штраф/бан\\n\\nПроблема / срочный вопрос?\\nПиши в <a href='https://t.me/joinchat/4FQ3rXG9lcU3N2My'>чат поддержки</a>";
CustomTelegram::scan($intervals[0], 0, "Kick.game", $text);
//CustomTelegram::scan($intervals[0], 1, "Test Name", $text);
CustomTelegram::scan($intervals[1], 0, "Kick.game", $text);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php"); 
