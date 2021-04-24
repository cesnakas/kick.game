<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Оформление подписки");
?>
    <style>
        .textstyle {
            font-size: 15px;
            color: white;
        }

        .textstyle span {
            font-size: 16px;
            text-decoration: underline;
        }


    </style>

<?
global $USER;
if ($USER->IsAuthorized()) {
    $kviuserid = $GLOBALS['USER']->GetID();
    $rsUser = CUser::GetByID($kviuserid);
    $arUser = $rsUser->Fetch();


    $userbe = intval($_GET['orderid']);
    if ($userbe > 0) {


        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");

        $arSelect = array("ID", "NAME", "CATALOG_PRICE_1", "CATALOG_GROUP_1", "PROPERTY_COL");
        $arFilter = array("IBLOCK_CODE" => "tovari", "ID" => $userbe, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(array(), $arFilter, false, array("nTopCount" => 1), $arSelect);
        $counttake = $res->SelectedRowsCount();

        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $datatarrif = $arFields;
        }


        if ($counttake == 0) {
            echo 'Предложение не найдено';
            exit();
        }


    } else {
        echo 'Не правильный запрос';
        exit();
    }
    ?>


    <div class="layout__content layout__content_full">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 col-lg-6">
                    <div class="logo-tagline">
                        <a href="/"><img src="/local/templates/kickgame/dist/images/logo-tagline.svg"
                                         alt="kickgame esports"></a>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6">
                    <div class="form-authentication">
                        <h2 class="form-authentication__heading">Оплатить заявку</h2>
                        <script src="https://www.vivapayments.com/web/checkout/js"></script>
                        <form id="myform" action="/check.php" method="post">
                        <!--<form class="jssetorder" id="jssetorder" method="post" action="/local/api/order.php"
                              name="regform" enctype="multipart/form-data">-->

                            <input type="hidden" value="<? echo $datatarrif[ID]; ?>" name="data[id]">
                            <input type="hidden" value="Не требуется" name="data[name]">
                            <input type="hidden" value="89533921212" name="data[phone]">
                            <input type="hidden" value="<? echo $arUser[EMAIL]; ?>" name="data[email]">


                            <span class="form-field__helper textstyle">
Выбран тариф <span><? echo $datatarrif[NAME]; ?></span> на <? echo $datatarrif[PROPERTY_COL_VALUE]; ?> месяц
</span>

                            <?
                            $tosumm = $datatarrif["CATALOG_PRICE_1"] * $datatarrif[PROPERTY_COL_VALUE];
                            ?>
                            <br>
                            <span class="form-field__helper textstyle">
Сумма <span><? echo $tosumm; ?></span> €
</span>

                            <br><br>
                            <!--
                            <div class="form-field">
                                                  <label for="auth-login" class="form-field__label">NickName</label>
                                                  <input type="text" class="form-field__input" name="REGISTER[LOGIN]" value="" autocomplete="off" id="auth-login" placeholder="Придумайте свой NickName">
                                                  <span class="form-field__helper" style="margin-bottom: -15px; display: block"></span>

                            </div>
                            -->


                            <?
                            $days = 30 * $datatarrif[PROPERTY_COL_VALUE];

                            /*if (!empty($arUser['UF_DATE_PREM_EXP'])) {*/

                                /*$input = $arUser['UF_DATE_PREM_EXP'];
                                $result = date('d.m.Y', strtotime($input . ' + ' . $days . ' days'));*/


                                echo ' <span class="form-field__helper textstyle" >';

                                $userId = $arUser["ID"];
                                $userGroups = CUser::GetUserGroup($userId);
                                $productGroups = array();
                                $productName = "";
                                $productGroup = 0;
                                $res = CIBlockElement::GetList(
                                    array(),
                                    array(
                                        "IBLOCK_CODE" => "tovari"
                                    ),
                                    false,
                                    false,
                                    array(
                                        "ID",
                                        "IBLOCK_ID",
                                        "PROPERTY_USER_GROUP",
                                        "NAME",
                                    )
                                );
                                while($element = $res->Fetch())
                                {
                                    if($element["PROPERTY_USER_GROUP_VALUE"])
                                    {
                                        $productGroups[$element["PROPERTY_USER_GROUP_VALUE"]] = $element["NAME"];
                                    }
                                }
                                foreach ($userGroups as $k => $v)
                                {
                                    if($productGroups[$v])
                                    {
                                        $productName = $productGroups[$v];
                                        $productGroup = $v;
                                        break;
                                    }
                                }
                                $restDays = 0;
                                if($productGroup)
                                {
                                    $res = CUser::GetUserGroupList($userId);
                                    while ($group = $res->Fetch())
                                    {
                                        if($group["GROUP_ID"] == $productGroup)
                                        {
                                            $dateInsert = \DateTime::createFromFormat("d.m.Y H:i:s", $group["DATE_ACTIVE_TO"]);
                                            $dateNow = new \DateTime('now');
                                            $restDays = $dateNow->diff($dateInsert)->days;
                                            break;
                                        }
                                    }
                                }

                                /*$deadline = new DateTime($arUser['UF_DATE_PREM_EXP']);
                                $now = new DateTime();
                                $diff = $deadline->diff($now);*/

                                if (/*$diff->format('%r')*/$restDays) {
                                    //echo $diff->format('Текущая подписка будет завершена через %y лет, %m месяцев, %d дней');
                                    echo("Текущая подписка будет завершена через " . num_decline($restDays, 'день, дня, дней'));
                                } else {
                                    echo 'Прошлая подписка завершена ' . $arUser['UF_DATE_PREM_EXP'];
                                }
                                echo '</span><br><br>';
                            /*} else {*/
                                $input = date("d.m.Y");
                                $result = date('d.m.Y', strtotime($input . ' + ' . $days . ' days'));
                            /*}*/

                            echo '<span class="form-field__helper" >После оплаты подписка будет доступна до ' . $result . '</span><br><br>';
                            ?>


                            <div class="form-field d-flex justify-content-center">
                                <!--<button class="btn" type="submit" name="register_submit_button" value="Оплатить">
                                    Оплатить
                                </button>-->
                                <button type="button"
                                        data-vp-publickey="8KvNFc04zx3/U3LmOSEjpq/z7OFM7iqdJVNcqVsvozQ="
                                        data-vp-baseurl=""
                                        data-vp-lang="en"
                                        data-vp-amount="<?= $tosumm * 100?>"
                                        data-vp-sourcecode="1180"
                                        data-vp-description="Тариф <?= $datatarrif["NAME"];?>"
                                        data-vp-disablewallet="false"
                                        data-vp-expandcard="true">
                                </button>
                            </div>


                            <div class="form-authentication__rules text-center">
                                Оплачивая заявку, я принимаю следующие документы: <a href="/terms-conditions/"
                                                                                     target="_blank">Условия
                                    обслуживания</a> и
                                <a href="/privacy-policy/" target="_blank">Конфиденциальность</a>.
                            </div>

                            <div class="form-authentication__rules text-center">
                                <br>1 месяц равен 30-ти календарным дням.
                            </div>
							
                      <a href="https://www.vivawallet.com/" target="_blank"><img src="/order/viva.png"
                                         alt="vivawallet"></a>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?
} else {
    echo 'Авторизуйтесь для подписки';
}
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>