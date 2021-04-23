<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Политика конфиденциальности");
?>

    <div class="container">
        <h1 class="text-center"><?=GetMessage('PP_HEADLINE')?></h1>
        <div class="content">
            <p><?=GetMessage('PP_DATE_UPDATE')?></p>
            <p><?=GetMessage('PP_VERSION')?></p>
            <p><?=GetMessage('PP_ITEM_P_01')?></p>
            <p><?=GetMessage('PP_ITEM_P_02')?></p>
            <p><?=GetMessage('PP_ITEM_P_03')?></p>
            <p><?=GetMessage('PP_ITEM_P_04')?></p>
            <p><?=GetMessage('PP_ITEM_P_05')?></p>
            <div class="policy">
                <ol class="policy-list">
                    <li id=""><h2 class="policy-list_title"><?=GetMessage('PP_LIST_ITEM_01')?></h2>
                        <?=GetMessage('PP_LIST_ITEM_02')?>
                    </li>
                    <li id="2"><h2 class="policy-list_title"><?=GetMessage('PP_LIST_ITEM_TITLE_02')?></h2>
                        <ol>
                            <li id="2.1">
                                <?=GetMessage('PP_LIST_ITEM_02_1')?>
                            </li>
                            <li id="2.2">
                                <?=GetMessage('PP_LIST_ITEM_02_2')?>
                            </li>
                        </ol>
                    </li>
                    <li id="3">
                        <?=GetMessage('PP_LIST_ITEM_03')?>
                    </li>
                    <li id="4">
                        <?=GetMessage('PP_LIST_ITEM_04')?>
                    </li>
                    <li id="5">
                        <?=GetMessage('PP_LIST_ITEM_05')?>
                    </li>
                    <li id="6">
                        <?=GetMessage('PP_LIST_ITEM_06')?>
                    </li>
                    <li id="7">
                        <?=GetMessage('PP_LIST_ITEM_07')?>
                    </li>
                    <li id="8">
                        <?=GetMessage('PP_LIST_ITEM_08')?>
                    </li>
                    <li id="9">
                        <?=GetMessage('PP_LIST_ITEM_09')?>
                    </li>
                </ol>
            </div>
        </div>
    </div>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>