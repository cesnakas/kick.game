<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
<?
CModule::IncludeModule("iblock");
global $USER;
$t = $_GET["t"];

if(!empty($t))
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.vivapayments.com/api/transactions/' . $t . '/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ZThiZDk1MjktOTQxZC00ZThlLThmZGQtM2U3Zjc0ZmE1MGQ0OjdoVGptWTc5OFBmSFplYU1DZHJhbnV6aWdXN0QyRg=='
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $vpResponse = json_decode($response, true);

    $tags = json_decode($vpResponse["Transactions"][0]["Order"]["Tags"][0], true);
    $productId = intval($tags["id"]);
    /*$productId = 36061;
    $transactionId = "34assdsdas-asda-das-d-a-ssd--asdasd";*/
    $userId = $USER->GetID();
    \Bitrix\Main\Diag\Debug::writeToFile("**********************************************", "-------", "/log.txt");
    \Bitrix\Main\Diag\Debug::writeToFile($vpResponse, "-------", "/log.txt");
    \Bitrix\Main\Diag\Debug::writeToFile(date("Y-m-d"), "-------", "/log.txt");
    \Bitrix\Main\Diag\Debug::writeToFile($userId, "-------", "/log.txt");
    $transactionId = $vpResponse["Transactions"][0]["TransactionId"];
    $filter = array("UF_TRANSACTION_ID" => $transactionId);
    \Bitrix\Main\Diag\Debug::writeToFile($transactionId, "-------", "/log.txt");
    $payTransaction = CustomChick::getTransaction("StoreTransaction", $filter)["TRANSACTIONS"];
    \Bitrix\Main\Diag\Debug::writeToFile($payTransaction, "-------", "/log.txt");
    \Bitrix\Main\Diag\Debug::writeToFile("**********************************************", "-------", "/log.txt");
    if ($vpResponse["Success"] && $transactionId && !$payTransaction && $productId)
    {
        $product = CustomChick::getProduct($productId);
        CustomChick::setUserBudget($userId, CustomChick::CHICK_CURRENCY_CODE, $product["PROPERTY_CHICK_COUNT_VALUE"]);
        $fields = array(
            "UF_TRANSACTION_ID" => $transactionId,
            "UF_QUANTITY" => $product["PROPERTY_CHICK_COUNT_VALUE"],
            "UF_OPERATION_NAME" => "Покупка товара " . $product["NAME"],
            "UF_OPERATION_TYPE" => CustomChick::OPERATION_TYPE[1],
            "UF_DATE" => date("d.m.Y H:i:s"),
            "UF_CURRENCY_TYPE" => CustomChick::CHICK_CURRENCY_CODE,
            "UF_USER_ID" => $userId
        );
        CustomChick::addTransaction($fields);
    }
}
?>
<div class="layout__content">
        <div class="container">
            <div class="content" style="text-align:center;">
                <? if ($t && $vpResponse["Success"]) {
                    ?>
                    <h1 class="text-center">Успешно</h1>
                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve" width="150"
                         height="150">
                    <circle style="fill:#25AE88;" cx="25" cy="25" r="25"/>
                        <polyline
                                style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;"
                                points="
                        38,15 22,33 12,25 "/>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                </svg>
                    <p>Ваш платеж успешно прошел, <a href="/personal/">перейти в профиль</a></p>

                <? } else {
                    ?>

                    <h1 class="text-center">Что-то пошло не так</h1>
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"
                         width="150" height="150">
                    <path style="fill:#EC5565;" d="M512,256.006C512,397.402,397.394,512.004,256.004,512C114.606,512.004,0,397.402,0,256.006
                        C-0.007,114.61,114.606,0,256.004,0C397.394,0,512,114.614,512,256.006z"/>
                        <path style="fill:#D94453;" d="M512,256.005c0-14.762-1.318-29.207-3.716-43.285c-0.244-0.201-101.229-101.269-101.493-101.534
                        c-0.264-0.265-0.6-0.389-0.898-0.596c-0.208-0.297-0.332-0.633-0.596-0.898c-0.265-0.266-0.601-0.388-0.898-0.596
                        c-0.208-0.297-0.332-0.633-0.596-0.898c-2.435-2.435-6.381-2.435-8.816,0L256,247.184c0,0-137.491-137.491-137.493-137.492
                        l-1.493-1.493c-2.435-2.435-6.38-2.435-8.816,0c-2.436,2.435-2.435,6.38,0,8.816c0,0,32.873,32.873,32.874,32.875l85.175,85.175
                        L247.184,256L108.198,394.985c-2.435,2.435-2.435,6.381,0,8.816c0.265,0.265,0.601,0.389,0.898,0.596
                        c0.207,0.297,102.81,102.9,103.107,103.107c0.179,0.258,0.317,0.535,0.518,0.781c14.077,2.398,28.523,3.715,43.282,3.715
                        C397.394,512.004,512,397.401,512,256.005z"/>
                        <path style="fill:#F4F6F9;" d="M264.816,256l138.986-138.986c2.435-2.435,2.435-6.381,0-8.816c-2.435-2.435-6.381-2.435-8.816,0
                        L256,247.184L117.014,108.198c-2.435-2.435-6.381-2.435-8.816,0s-2.435,6.381,0,8.816L247.184,256L108.198,394.986
                        c-2.435,2.435-2.435,6.381,0,8.816c1.218,1.218,2.813,1.826,4.407,1.826c1.595,0,3.19-0.609,4.407-1.826L256,264.816
                        l138.986,138.986c1.218,1.218,2.813,1.826,4.407,1.826s3.19-0.609,4.407-1.826c2.435-2.435,2.435-6.381,0-8.816L264.816,256z"/>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                        <g>
                        </g>
                </svg>
                    <p>Свяжитесь с администраций <a href="mailto:support@kick.game">написать на почту</a></p>
                <? }
                ?>
            </div>
        </div>
    </div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>