<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Invites");
$asset->addCss(SITE_TEMPLATE_PATH . '/dist/css/datepicker.css');
CModule::IncludeModule("iblock");

function getMatchesByDate($date) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array(
        "IBLOCK_ID" =>3,
        "PROPERTY_PREV_MATCH" => false,
        "PROPERTY_TYPE_MATCH" => 5,
        ">=PROPERTY_DATE_START" => ConvertDateTime($date, "YYYY-MM-DD")." 00:00:00",
        "<=PROPERTY_DATE_START" => ConvertDateTime($date, "YYYY-MM-DD")." 23:59:59",
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $output = [];

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        //$arProps = $ob->GetProperties();
        $output[] = $arFields;
    }
    return $output;
}
?>

    <div class="container">
        <div>
            <h1>Календарь</h1>
            <div >
                <div class="form-group">
                    <div class="row my-5">
                        <div class="col-md-8">
                            <div class="datepicker-here" id="calendarDashboardInvites"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php
                $curDate = $_GET['date'];
                //dump($curDate);


                if($curDate) {
                    $resMatches = getMatchesByDate($curDate);
                    if (!empty($resMatches)) {
                        ?>

                        <h2>Список матчей дня</h2>
                        <ul>
                            <?php

                            foreach ($resMatches as $match) { ;?>

                                <li><a href="/dashboard/invites/invite-game/?id=<?php echo $match['ID']?>"><?php echo $match['NAME']; ?></a></li>
                            <?php } ?>
                        </ul>
                    <?php } else {
                        echo 'Матчи не найдены';
                    }
                } else {
                    echo 'День не выбран';
                }?>
            </div>
        </div>
    </div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>