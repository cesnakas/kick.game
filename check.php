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
        /*//группа в соответсвии с тарифом
        $productGroups = array(); $productGroupId = 0;
        $res = CIBlockElement::GetList(
            array(),
            array(
                "ACTIVE" => "Y",
                "IBLOCK_CODE" => "tovari"
            ),
            false,
            false,
            array(
                "ID",
                "IBLOCK_ID",
                "PROPERTY_USER_GROUP"
            )
        );
        while($element = $res->Fetch())
        {
            if($productId == $element["ID"])
            {
                $productGroupId = intval($element["PROPERTY_USER_GROUP_VALUE"]);
            }
            $productGroups[$element["ID"]] = intval($element["PROPERTY_USER_GROUP_VALUE"]);
        }
        //количество месяцев
        $month = intval(CIBlockElement::GetProperty($iblockId, $productId, array("sort" => "asc"), array("CODE" => "COL"))->Fetch()["VALUE"]);
        if($month)
        {
            $params = array();
            $dateFrom = date("d.m.Y 00:00:00");
            $dateTo = date("d.m.Y 00:00:00", strtotime("+" . $month . " month"));
            //$groups = CUser::GetUserGroup($userId);
            $groups = array();
            $res = CUser::GetUserGroupList($userId);
            while ($group = $res->Fetch())
            {
                $groups[] = $group;
            }
            $remainderDate = 0;
            foreach ($groups as $k => $v)
            {
                if(in_array($v["GROUP_ID"], $productGroups))
                {
                    if($v["DATE_ACTIVE_TO"])
                    {
                        $now = (new DateTime('now'))->getTimestamp();
                        $dateTwo = DateTime::createFromFormat("d.m.Y H:i:s", $v["DATE_ACTIVE_TO"])->getTimestamp();
                        if($now < $dateTwo)
                        {
                            $remainderDate += ($dateTwo - $now);
                        }
                    }
                }
                else
                {
                    $params[] = $v;
                }
            }
            $dateOne = DateTime::createFromFormat("d.m.Y H:i:s", date("d.m.Y 00:00:00", strtotime("+" . $month . " month")))->getTimestamp();
            $dateTo = date('d.m.Y 00:00:00', $dateOne + $remainderDate);
            foreach ($groups as $k => $v)
            {
                if(in_array($v["GROUP_ID"], $productGroups) && $v["GROUP_ID"] != $productGroupId)
                {
                    unset($groups[$k]);
                }
            }
            $params[] = array(
                "GROUP_ID" => $productGroupId,
                "DATE_ACTIVE_FROM" => $dateFrom,
                "DATE_ACTIVE_TO" => $dateTo
            );
            CUser::SetUserGroup($userId, $params);
            $userOb = new CUser;
            $fields = array(
                "UF_PAY_TRANSACION" => $vpResponse->TransactionId,
                "UF_DATE_PREM_EXP" => $dateTo
            );
            $userOb->Update($userId, $fields);
        }*/
        //$subscribes = CustomSubscribes::setUserSubscribeGroup($userId, $productId);
        $teamId = CustomSubscribes::getUserTeam($userId);
        //$teamId = $user["UF_ID_TEAM"];
        if($teamId)
        {
            $users = CustomSubscribes::getCoreTeam($teamId);
            foreach ($users as $k => $v)
            {
                $subscribes = CustomSubscribes::setUserSubscribeGroup($v["ID"], $productId);
            }
        }
        $userOb = new CUser;
        $fields = array(
            "UF_PAY_TRANSACION" => $vpResponse->TransactionId,
        );
        $userOb->Update($userId, $fields);
    }?>
	
<div class="layout__content">
<div class="container">	
	<div class="content" style="text-align:center;">
 <?   if($vpResponse->Success)
    {
?>
    <h1 class="text-center">Успешно</h1>
<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve" width="150" height="150">
<circle style="fill:#25AE88;" cx="25" cy="25" r="25"/>
<polyline style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" points="
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

<?    }
    else
    {?>
		
     <h1 class="text-center">Что-то пошло не так</h1>
<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="150" height="150">
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
		
  <?  }
?>
</div></div></div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>