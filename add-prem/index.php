<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Add Prem");
?>

<?php

function updateUserPrem($userID, $datePremExp)
{
    $now = date('d.m.Y');
    //$datePremExp = date( 'd.m.Y', strtotime( $now ." +" . $days . "days" ));
    //$datePremExp = date( 'd.m.Y');
    $user = new CUser;
    $fields = array(
        "UF_DATE_PREM_EXP" => $datePremExp,
    );
    if ($user->Update($userID, $fields)) {
        return true;
    }
    return false;
}


function addGroupStandart($userID, $datePrem)
{
    $params = array();
    $groups = CUser::GetUserGroup($userID);
    foreach ($groups as $k => $v)
    {
        $params[] = array("GROUP_ID" => $v);
    }
    dump($params);

    $params[] = array(
      "GROUP_ID" => 10,
      "DATE_ACTIVE_FROM" => date($datePrem),
      "DATE_ACTIVE_TO" => date($datePrem)
    );

    CUser::SetUserGroup($userID, $params);

}

function deleteUserGroup($idGroup, $userId)
{
    $productGroups = array($idGroup);//id группы которую надо убрать
    $groupParams = array();
    $res = CUser::GetUserGroupList($userId);
    while ($group = $res->Fetch())
    {
        if(!in_array($group["GROUP_ID"], $productGroups))
        {
            $groupParams[] = $group;
        }
    }
    CUser::SetUserGroup($userId, $groupParams);
}



//$i = 15; // Сколько дней назад зарегистрировался пользователей

//$s1 = strtotime("-$i day");
//$s2 = strtotime("today");

//echo("от " . date('d.m.Y  H:i:s', $s1)."\n");
//echo("до " . date('d.m.Y  H:i:s', $s2)."\n");

$filter = array(
    //"DATE_REGISTER_1" => date('d.m.Y H:i:s', $s1),
    //"DATE_REGISTER_2" => date('d.m.Y H:i:s', $s2),
    /* date register
    "DATE_REGISTER_1" => date('21.03.2021 23:59:00'),
    "DATE_REGISTER_2" => date('29.03.2021 23:59:00'),
    */
    //'!ID' => implode('|', array(123,803)),
    'ID' => "~".implode("& ~", [
        4812,
        2946,
        2769,
          21415,
          16364,
          2799,
          9329,
          20223,
          16939,
          20772,
          16935,
          20777,
          20774,
          20423,
          20918
      ]),
    '>UF_DATE_PREM_EXP' => date('12.05.2021'),
    "ACTIVE" => 'Y',
);
$arParams["SELECT"] = array("UF_*");

$elementsResult = CUser::GetList(($by="ID"), ($order="ASC"), $filter, $arParams);
$n = 0;
while ($rsUser = $elementsResult->Fetch())
{
    $n++;
    //updateUserPrem($rsUser["ID"], 10);
    //addGroupStandart($rsUser["ID"], 2);
    //addGroupStandart($rsUser["ID"], $rsUser["UF_DATE_PREM_EXP"]);

    //updateUserPrem($rsUser["ID"], 10);
    echo $rsUser["ID"] . $rsUser["LOGIN"] . " - " . $rsUser["UF_DATE_PREM_EXP"] . "<br>";
}
echo $n;
//addGroupStandart(13536, '12.05.2021');
//updateUserPrem(13536, '12.05.2021');
//deleteUserGroup(10, 13537);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>