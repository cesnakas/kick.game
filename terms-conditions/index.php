<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Пользовательское соглашение");
?>

    <div class="container">
        <h1 class="text-center"><?=GetMessage('TC_HEADLINE')?></h1>
        <div class="content">
            <p><?=GetMessage('TC_DATE')?></p>
            <p><?=GetMessage('TC_VERSION')?></p>
            <p><?=GetMessage('TC_SUBTITLE')?></p>
            <div class="policy">
                <ol class="policy-list">
                    <li id="1">
                        <?=GetMessage('TC_LIST_01')?>
                    </li>
                    <li id="2">
                        <?=GetMessage('TC_LIST_02')?>
                    </li>
                    <li id="3">
                        <?=GetMessage('TC_LIST_03')?>
                    </li>
                    <li id="4">
                        <?=GetMessage('TC_LIST_04')?>
                    </li>
                    <li id="5">
                        <?=GetMessage('TC_LIST_05')?>
                    </li>
                    <li id="6">
                        <?=GetMessage('TC_LIST_06')?>
                    </li>
                    <li id="7">
                        <?=GetMessage('TC_LIST_07')?>
                    </li>
                    <li id="8">
                        <?=GetMessage('TC_LIST_08')?>
                    </li>
                    <li id="9">
                        <?=GetMessage('TC_LIST_09')?>
                    </li>
                    <li id="10">
                        <?=GetMessage('TC_LIST_10')?>
                    </li>
                    <li id="11">
                        <?=GetMessage('TC_LIST_11')?>
                    </li>
                    <li id="12">
                        <?=GetMessage('TC_LIST_12')?>
                    </li>
                    <li id="13">
                        <?=GetMessage('TC_LIST_13')?>
                    </li>
                </ol>
            </div>
        </div>
    </div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>