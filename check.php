<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
<?php
    // retrieve vivaWalletToken from POST vars
    $vivaWalletToken = $_POST["vivaWalletToken"];

    // check if the token is defined
    if(!isset($vivaWalletToken)){
        echo ("viva wallet token is not defined");
        //and catch errors if it is not...
        return;
    }

    // define vars
    $merchantId = 'e8bd9529-941d-4e8e-8fdd-3e7f74fa50d4';
    $apiKey = '7hTjmY798PfHZeaMCdranuzigW7D2F';

    $url = 'https://www.vivapayments.com/api/transactions';
    $postArgs = 'PaymentToken='.urlencode($vivaWalletToken);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);
    curl_setopt($curl, CURLOPT_USERPWD, $merchantId . ":" . $apiKey);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $vpResponse = curl_exec($curl);
    curl_close($curl);
    $vpResponse = json_decode($vpResponse);
    //////////////////////////////////////////////////////////////////////
    CModule::IncludeModule("iblock");
    global $USER;
    $productId = intval($_REQUEST["data"]["id"]);
    $userId = $USER->GetID();
    //если оплата прошла успешно
    $payTransaction = CUser::GetList(($by=""), ($order=""), array("ID" => $userId), array("SELECT" => array("UF_PAY_TRANSACION")))->Fetch()["UF_PAY_TRANSACION"];

    if($vpResponse->Success && $vpResponse->TransactionId && $payTransaction != $vpResponse->TransactionId && $productId)
    {
        //создадим заказ
        require($_SERVER["DOCUMENT_ROOT"] . "/local/api/order.php");
        $iblockId = CIBlockElement::GetIBlockByID($productId);
        //группа в соответсвии с тарифом
        $productGroup = intval(CIBlockElement::GetProperty($iblockId, $productId, array("sort" => "asc"), array("CODE" => "USER_GROUP"))->Fetch()["VALUE"]);
        //количество месяцев
        $month = intval(CIBlockElement::GetProperty($iblockId, $productId, array("sort" => "asc"), array("CODE" => "COL"))->Fetch()["VALUE"]);
        if($month)
        {
            $params = array();
            $groups = CUser::GetUserGroup($userId);
            foreach ($groups as $k => $v)
            {
                $params[] = array("GROUP_ID" => $v);
            }
            $params[] = array(
                "GROUP_ID" => $productGroup,
                "DATE_ACTIVE_FROM" => date("d.m.Y"),
                "DATE_ACTIVE_TO" => date("d.m.Y", strtotime("+" . $month . " month"))
            );

            CUser::SetUserGroup($userId, $params);
            $userOb = new CUser;
            $fields = array(
                "UF_PAY_TRANSACION" => $vpResponse->TransactionId
            );
            $userOb->Update($userId, $fields);
        }
    }
    if($vpResponse->Success)
    {
        echo "<div>Тариф успешно оплачен.";
    }
    else
    {
        echo "<div>Что-то пошло не так, обратитесь к администрации сайта.";
    }
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>