<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оплата завершена");
?><?
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

global $USER;
if ($USER->IsAuthorized()) {
    $kviuserid = $GLOBALS['USER']->GetID();
    $rsUser = CUser::GetByID($kviuserid);
    $arUser = $rsUser->Fetch();



    if(empty($_GET[gw_uniq_id])) { exit(); }



    $arFilter = Array("USER_ID" => $USER->GetID());
    $rsSales = CSaleOrder::GetList(array("ID" => "DESC"), $arFilter,false,Array("nPageSize"=>1),Array("ID","STATUS_ID","DATE_INSERT","PRICE","CANCELED"));
    while ($arSales = $rsSales->Fetch())
    {

        echo $arSales[ID].'<br><br>';
        $datauser = orredinfos($arSales[ID]);

        print_r();

        $gnx = $_GET[gw_uniq_id];
        $gnx2 = $datauser[PROPID][4][VALUE];

        if($gnx ==$gnx2) {

        } else {
            echo GetMessage('ERROR_CONTACT_DEVELOPERS');
            exit();
        }

        $arBasketItems = array();



        $dbBasketItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "LID" => SITE_ID,
                "ORDER_ID" => $arSales[ID]
            )
        );
        $kol = 0;
        while ($arItems = $dbBasketItems->Fetch())
        {
//   $kol += $arItems['QUANTITY'];
            $prods[] = $arItems;
            $getprod[$arItems[PRODUCT_ID]] = $arItems[PRODUCT_ID];
        }

//print_r($getprod);

////////////////////////   //////////////////////////

        $arSelect = Array("ID", "NAME","CATALOG_PRICE_1","CATALOG_GROUP_1","PROPERTY_COL");
        $arFilter = Array("IBLOCK_ID"=>13, "ID"=>$getprod, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array ("nTopCount" => 1), $arSelect);
        $counttake = $res->SelectedRowsCount();

        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $datatarrif = $arFields;
        }

        $days = 30 * $datatarrif[PROPERTY_COL_VALUE];

        if(!empty($arUser['UF_DATE_PREM_EXP'])) {

            $input = $arUser['UF_DATE_PREM_EXP'];
            $result = date('d.m.Y',strtotime($input.' + '.$days.' days'));


//echo ' <span class="form-field__helper textstyle" >';
            $deadline = new DateTime($arUser['UF_DATE_PREM_EXP']);
            $now = new DateTime();
            $diff = $deadline->diff($now);
            if($diff->format('%r')) {
//   echo $diff->format('Текущая подписка будет завершена через %y лет, %m месяцев, %d дней');
            } else {
//   echo 'Прошлая подписка завершена '.$arUser['UF_DATE_PREM_EXP'];
            }
//echo '</span><br><br>';
        }
        else {
            $input = date("d.m.Y");
            $result = date('d.m.Y',strtotime($input.' + '.$days.' days'));
        }

///echo '<span class="form-field__helper" >После оплаты подписка будет доступна до '.$result.'</span><br><br>';

        $userbb = new CUser;
        $fieldsbb = array(
            "UF_DATE_PREM_EXP" => $result,
        );
        if ($userbb->Update($USER->GetID(), $fieldsbb)) {

            echo GetMessage('SUBSCRIPTION_RENEWED');

            $arFields1 = array(
                "ORDER_ID" => $arSales[ID],     /////////ид заказа
                "ORDER_PROPS_ID" => 4,  ////////// ид свойства
                "NAME" => "Номер оплаты",
                "CODE" => "EXT_PAY",
                "VALUE" => ""
            );

            if($datauser[PROPID][4][ID] >0) {
                CSaleOrderPropsValue::Update($datauser[PROPID][4][ID], $arFields1);

            }

        }

///////////////////////     ///////////////////

    }


}

function basketitm($orderid) {


    $dbBasketItems = CSaleBasket::GetList(
        array(
            "NAME" => "ASC",
            "ID" => "ASC"
        ),
        array(
            "LID" => SITE_ID,
            "ORDER_ID" => $orderid
        )
    );
    $kol = 0;
    while ($arItems = $dbBasketItems->Fetch())
    {
        $kol += $arItems['QUANTITY'];
        $prods[$arItems[PRODUCT_ID]] = $arItems;
        $getprod[$arItems[PRODUCT_ID]] = $arItems[PRODUCT_ID];
    }

    $arSelectr = Array("ID", "NAME", "DETAIL_PAGE_URL","PREVIEW_PICTURE","PROPERTY_SAKURA1");
    $arFilterr = Array("IBLOCK_ID"=>15, "ID"=>$getprod,  "ACTIVE"=>"Y");
    $resr = CIBlockElement::GetList(Array(), $arFilterr, false, Array("nPageSize"=>150), $arSelectr);
    while($obr = $resr->GetNextElement())
    {
        $arFieldsr = $obr->GetFields();
        $prinf[$arFieldsr[ID]] = $arFieldsr;
    }


    foreach($prods as $key => $prods2) {

        $databask[] = array(
            'Цена' => $prods2['PRICE'],
            'Артикул' => $prinf[$key]["PROPERTY_SAKURA1_VALUE"],
            'Количество' => $prods2['QUANTITY'],

        );

//$databask[$key][ID] = $key;

    }



    return $databask;



}




function orredinfos($idorder) {
    if (!($arOrderingo = CSaleOrder::GetByID($idorder)))
    {
        echo GetMessage('ORDER_WIDTH') . $idorder . GetMessage('ORDER_NOT_FOUNT');
    }
    else {
        $arPropsord = array();
        $resord = CSaleOrderPropsValue::GetOrderProps($idorder);
        while ($propord = $resord->Fetch()) {
            $dataorder[PROP][$propord['CODE']] = $propord;
            $dataorder[PROPID][$propord[ORDER_PROPS_ID]] = $propord;

        }

        if ($arOrderingo['PAY_SYSTEM_ID']) {
            $dataorder[PAYSYSTEM] = CSalePaySystem::GetByID($arOrderingo['PAY_SYSTEM_ID']);
        }
        if ($arOrderingo['DELIVERY_ID']) {
            $dataorder[DELIVERY] = CSaleDelivery::GetByID($arOrderingo['DELIVERY_ID']);
        }

        $dataorder[ORDER] = $arOrderingo;


        $arAddresssitlegal ="";
        $arLocationsitlegal = CSaleLocation::GetByID($arPropsord['LOCATION']['VALUE']);
        if (strlen($arLocationsitlegal['COUNTRY_NAME'])) {
            $arAddresssitlegal .= $arLocationsitlegal['COUNTRY_NAME'].", ";
        }
        if (strlen($arLocationsitlegal['REGION_NAME'])) {
            $arAddresssitlegal .= $arLocationsitlegal['REGION_NAME']." ";
        }
        if (strlen($arLocationsitlegal['CITY_NAME'])) {
            $arAddresssitlegal .= $arLocationsitlegal['CITY_NAME']." ";
        }
        return $dataorder;


    }

}


?>





<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>