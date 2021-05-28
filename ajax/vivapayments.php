<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$action = $_POST["action"];
unset($_POST["action"]);
if($action == "getOrderCode")
{
    $data = json_encode($_POST);
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.vivapayments.com/api/orders',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ZThiZDk1MjktOTQxZC00ZThlLThmZGQtM2U3Zjc0ZmE1MGQ0OjdoVGptWTc5OFBmSFplYU1DZHJhbnV6aWdXN0QyRg==',
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}


