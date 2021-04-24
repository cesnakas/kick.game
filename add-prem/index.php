<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Add Prem");
?>

<?php

function updateUserPrem($userID, $days)
{
    $now = date('d.m.Y');
    $datePremExp = date( 'd.m.Y', strtotime( $now ." +" . $days . "days" ));
    $user = new CUser;
    $fields = array(
        "UF_DATE_PREM_EXP" => $datePremExp,
    );
    if ($user->Update($userID, $fields)) {
        return true;
    }
    return false;
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
    '<UF_DATE_PREM_EXP' => date('01.05.2021'),
    "ACTIVE" => 'Y',
);
$arParams["SELECT"] = array("UF_*");

$elementsResult = CUser::GetList(($by="ID"), ($order="ASC"), $filter, $arParams);
$n = 0;
while ($rsUser = $elementsResult->Fetch())
{
    $n++;
    //updateUserPrem($rsUser["ID"], 10);
    //echo $rsUser["ID"] . $rsUser["LOGIN"] . " - " . $rsUser["UF_DATE_PREM_EXP"] . "<br>";
}
echo $n;
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>