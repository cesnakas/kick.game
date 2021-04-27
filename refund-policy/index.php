<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Политика возврата средств");
?>

    <div class="container">
        <h1 class="text-center"><?=GetMessage('RP_HEADLINE')?></h1>
        <div class="content">
            <p><?=GetMessage('RP_DATE')?></p>
            <p><?=GetMessage('RP_VERSION')?></p>
            <p><?=GetMessage('RP_SUBTITLE')?></p>
            <div class="policy">
                <ol class="policy-list">
                    <li id="1">
                        <?=GetMessage('RP_LIST_1')?>
                    </li>
                    <li id="2">
                        <?=GetMessage('RP_LIST_2')?>
                    </li>
                    <li id="3">
                        <?=GetMessage('RP_LIST_3')?>
                    </li>
                    <li id="4">
                        <?=GetMessage('RP_LIST_4')?>
                    </li>
                    <li id="5">
                        <?=GetMessage('RP_LIST_5')?>
                    </li>
                    <li id="6">
                        <?=GetMessage('RP_LIST_6')?>
                    </li>
                    <li id="7">
                        <?=GetMessage('RP_LIST_7')?>
                    </li>
                </ol>
            </div>
        </div>
    </div>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>