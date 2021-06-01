<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
CModule::IncludeModule("iblock");

/*$subscribes = array();
global $USER;
$userId = $USER->GetID();
$user = CustomSubscribes::getUserTeam($userId);*/

/*$teamId = 3914/*$user["UF_ID_TEAM"]*/;
/*if($teamId)
{
    $users = CustomSubscribes::getCoreTeam($teamId);
    foreach ($users as $k => $v)
    {
        echo("<pre>");var_dump($v["ID"]);echo("</pre>");
        //$subscribes = CustomSubscribes::setUserSubscribeGroup($v["ID"], 5407);
    }
}*/
//CustomSubscribes::deleteUserTeam(1);
//CustomSubscribes::addUserTeam(1, 2711);





/*$matches = CustomTelegram::getNearestMatchUsers(1);
echo("<pre>");var_dump($matches);echo("</pre>");

$matches = CustomTelegram::getMatchTeams(35972);
echo("<pre>");var_dump($matches);echo("</pre>");*/

/*$intervals = array(
    array(0, 1),
    array(1, 4)
);
CustomTelegram::scan($intervals[0], 1, "Mike", "Hi");*/
//CustomTelegram::scan($intervals[1], 1, "Hi");

/*$rt = CustomTelegram::sendNotifications(1, array(2, 3, 4), "Name", "Text");
echo("<pre>");var_dump($rt);echo("</pre>");*/

/*$rt = CustomTelegram::getChatId(10);
echo("<pre>");var_dump($rt);echo("</pre>");*/

/*$rt = "asd #df# asd #fg# asd as ddasasd";
$fr = str_replace(array("#df#", "#fg#"), array("1"), $rt);
echo("<pre>");var_dump($fr);echo("</pre>");*/

?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>