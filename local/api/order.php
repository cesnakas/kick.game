<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");
global $APPLICATION;
global $USER;
use Bitrix\Main,
    Bitrix\Main\Localization\Loc as Loc,
    Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Catalog\Discount,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem,
    Bitrix\Sale,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Order,
    Bitrix\Sale\DiscountCouponsManager,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Main\Context;

$siteId = \Bitrix\Main\Context::getCurrent()->getSite();

$currencyCode = CurrencyManager::getBaseCurrency();


function randomNumber($length) {
    $result = '';

    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }

    return $result;
}



function updateOrderProperty($params = [])
{
    if ($params && !empty($params['order']) && !empty($params['code'])) {

        $order = $params['order'];
        $code  = $params['code'];
        $value = !empty($params['value']) ? $params['value'] : '';

        if (CModule::IncludeModule('sale')) {

            $prop = Bitrix\Sale\Internals\OrderPropsValueTable::getList([
                'filter' => [
                    'ORDER_ID' => $order,
                    'CODE'     => $code
                ]
            ])->Fetch();

            if ($prop) {

                return CSaleOrderPropsValue::Update($prop['ID'], [
                    'VALUE' => $value
                ]);
            }
        }
    }

    return false;
}


function addOrderProperty($params = [])
{
    if ($params && !empty($params['order']) && !empty($params['code'])) {

        $order = $params['order'];
        $code  = $params['code'];
        $value = !empty($params['value']) ? $params['value'] : '';

        if (CModule::IncludeModule('sale')) {

            if ($prop = CSaleOrderProps::GetList([], ['CODE' => $code])->Fetch()) {

                return CSaleOrderPropsValue::Add([
                    'NAME'           => $prop['NAME'],
                    'CODE'           => $prop['CODE'],
                    'ORDER_PROPS_ID' => $prop['ID'],
                    'ORDER_ID'       => $order,
                    'VALUE'          => $value
                ]);
            }
        }
    }

    return false;
}

function executeHookLog($data, $title = '') {
    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n"; $log .= print_r($data, 1);
    $log .= "\n------------------------\n";

    file_put_contents($_SERVER["DOCUMENT_ROOT"].'/log.txt', $log, FILE_APPEND); return true;
}


executeHookLog($_REQUEST);

$phone = strip_tags($_POST[data][phone]);


$phone = preg_replace("/[^0-9]/", '', $phone);

if(strlen($phone) ==11) {} else {

    $check=1;
    $datas[result] = false;
    $datas[errors] = "11 символов должно быть в телефоне";
    echo json_encode($datas);
    exit();

}



if ($USER->IsAuthorized())
{
    $kviuserid = $GLOBALS['USER']->GetID(); // ID пользователя
} else {


    $rsUser = CUser::GetByLogin($phone);
    if($arUser = $rsUser->Fetch())
    {
        $idtoset = $arUser[ID];
        $kviuserid = $arUser[ID];

    }

    if($idtoset >0) {  } else {
        $regrund = randomNumber(10);
        $user_add = new CUser;
        $arFields_add = Array(
            "NAME"              => $_POST[data][name],
            "EMAIL"             => $phone."@kickgame.link",
            "LOGIN"             => $phone,
            "LID"               => "ru",
            "ACTIVE"            => "Y",
            "GROUP_ID"          => array(5),
            "PASSWORD"          => $regrund,
            "CONFIRM_PASSWORD"  => $regrund,
        );

        $kviuserid = $user_add->Add($arFields_add);

        $USER->Logout();

    }

    if (intval($kviuserid) > 0) {

    }
    else {

        $datas[result] = false;
        $datas[errors] = $user_add->LAST_ERROR;
        echo json_encode($datas);
        exit();


    }



}
$personTypeId = 1; // тип пользователя






// проверка на число
$arranums = array(
    "delivery",
    "payment",
);

/// определение свойств  страница заказа - свойства админки
$dataprops_order_get = array(
    'FIO' => 'name',
    'EMAIL' => 'email',
    'PHONE' => 'phone',
);

$dataprops_order_set = array(
    'FIO',
    'EMAIL',
    'PHONE',
);



foreach($_POST[data] as $mykey => $myvalue) {
    $orderdata[$mykey] = strip_tags($myvalue);
}

executeHookLog($orderdata);





foreach($orderdata as $mykey  => $myvalue) {
    if(in_array($mykey,$arranums)) {
        $orderdata[$mykey] = intval($myvalue);
    }
}

$orderdata[delivery] = 2;
$orderdata[payment] =  2;
$orderdata[email] = $phone."@kickgame.link";



// Создаем заказ и привязываем корзину, перерасчет происходит автоматически
$order = \Bitrix\Sale\Order::create('s1', $kviuserid);  // ID пользователя
$order->setPersonTypeId($personTypeId); // тип пользователя



$basket = Basket::create($siteId);
$item = $basket->createItem('catalog', $orderdata[id]);
$item->setFields(array(
    'QUANTITY' => 1,
    'CURRENCY' => $currencyCode,
    'LID' => $siteId,
    'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider',
));
$order->setBasket($basket);




// Создание отгрузки
$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem(
    \Bitrix\Sale\Delivery\Services\Manager::getObjectById($orderdata[delivery])
);
$shipmentItemCollection = $shipment->getShipmentItemCollection();



// Создание оплаты
$paymentCollection = $order->getPaymentCollection();
$payment = $paymentCollection->createItem(
    \Bitrix\Sale\PaySystem\Manager::getObjectById($orderdata[payment])
);
$payment->setField("SUM", $order->getPrice());
$payment->setField("CURRENCY", $order->getCurrency());


/*
$propertyValue = $propertyCollection->createItem([
    'ID' => 1,
    'NAME' => 'Ф.И.О.',
    'TYPE' => 'STRING',
    'CODE' => 'FIO',
]);
$propertyValue->setField('VALUE', $orderdata[name]);

$propertyCollection = $order->getPropertyCollection();

    foreach($dataprops_order_get as $keyprop => $isvalprop) {
    $orderdata[$keyprop] = trim($orderdata[$keyprop]);
        if(strlen($orderdata[$keyprop] >0) && !empty($keyprop)) {
            $Property[$keyprop] = getPropertyByCode($propertyCollection, $isvalprop);
            $Property[$keyprop]->setValue($orderdata[$keyprop]);
        }
    }


*/


$order->doFinalAction(true);














// Coхраняем заказ


//$order->setField("STATUS_ID", "OK");


$result = $order->save();
$orderId = $order->getId();


executeHookLog("orderm- ".$orderId);

foreach($dataprops_order_set as $dataprops_order_set2) {

    $id = addOrderProperty([
        'order' => $orderId,
        'code'  => $dataprops_order_set2,
        'value' => $orderdata[$dataprops_order_get[$dataprops_order_set2]]
    ]);

    $id2 = updateOrderProperty([
        'order' => $orderId,
        'code'  => $dataprops_order_set2,
        'value' => $orderdata[$dataprops_order_get[$dataprops_order_set2]]
    ]);



}



if (!$result->isSuccess()) {

    $datas[result] = false;
    $datas[errors] = $result->getError();
    //echo json_encode($datas);
    exit();


} else {
    $datas[result] = true;
    $datas[id] = $orderId;
    //echo json_encode($datas);

}



?>




