<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
CModule::IncludeModule("iblock");

$subscribes = array();
global $USER;
$userId = $USER->GetID();
$user = CustomSubscribes::getUserTeam($userId);

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

?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>