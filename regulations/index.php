<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Регламент");
?>

    <div class="container">
        <h1 class="text-center"><?=GetMessage('REGULATIONS_TITLE')?></h1>
        <div class="content">
            <p><?=GetMessage('REGULATIONS_TEXT_P1')?></p>
            <p><?=GetMessage('REGULATIONS_TEXT_P2')?></p>
            <p><?=GetMessage('REGULATIONS_TEXT_P3')?></p>
            <p><?=GetMessage('REGULATIONS_TEXT_P4')?></p>
            <div class="policy">
                <ol class="policy-list">
                    <li id="1"><h2 class="policy-list_title"><?=GetMessage('REGULATIONS_TITLE_2_1')?></h2>
                        <ol>
                            <li id="1.1">
                                <p><?=GetMessage('REGULATIONS_ITEM_1_1')?></p>
                                <p><?=GetMessage('REGULATIONS_ITEM_1_1_TEXT')?></p>
                            </li>
                            <li id="2.2">
                                <p><?=GetMessage('REGULATIONS_ITEM_1_2')?></p>
                                <p><?=GetMessage('REGULATIONS_ITEM_1_2_TEXT')?></p>
                            </li>
                        </ol>
                    </li>
                    <li id="2"><h2 class="policy-list_title"><?=GetMessage('REGULATIONS_TITLE_PLAYERS')?></h2>
                        <ol>
                            <li id="2.1">
                                <p><?=GetMessage('REGULATIONS_ITEM_PLAYERS_TEXT')?></p>
                                <h3 class="text-center"><?=GetMessage('REGULATIONS_ITEM_PLAYERS_TITLE')?></h3>
                                <p><?=GetMessage('REGULATIONS_ITEM_PLAYERS_P1')?></p>
                                <p><?=GetMessage('REGULATIONS_ITEM_PLAYERS_P2')?></p>
                                <p><?=GetMessage('REGULATIONS_ITEM_PLAYERS_P3')?></p>
                                <p><?=GetMessage('REGULATIONS_ITEM_PLAYERS_P4')?></p>
                                <p><?=GetMessage('REGULATIONS_ITEM_PLAYERS_P5')?></p>
                                <p><?=GetMessage('REGULATIONS_ITEM_PLAYERS_P6')?></p>
                            </li>
                            <li id="2.2">
                                <p><?=GetMessage('REGULATIONS_TITLE_REG')?></p>
                                <p><?=GetMessage('REGULATIONS_ITEM_REG_P1')?></p>
                                <p><?=GetMessage('REGULATIONS_ITEM_REG_P2')?></p>
                            </li>
                        </ol>
                    </li>
                </ol>
            </div>
        </div>
    </div>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>