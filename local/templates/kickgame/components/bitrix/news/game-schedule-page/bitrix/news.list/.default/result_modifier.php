<?php
// Функция для получения количества элементов в указанной рубрике

if(array_key_exists("IS_AJAX", $_REQUEST) && $_REQUEST["IS_AJAX"] == "Y")
{
    $APPLICATION->RestartBuffer();
}
/*
foreach ($arResult['ITEMS'] as $key => $item) {

    /*if (!empty($item['PREVIEW_PICTURE']['SRC'])) {

        $resizeImg = CFile::ResizeImageGet(
            $item['PREVIEW_PICTURE'],
            [
                'width'  => 300,
                'height' => 300,
            ]
        );

        if (!empty($resizeImg['src'])) {
            $resizeImg = array_change_key_case($resizeImg, CASE_UPPER);
            $arResult['ITEMS'][$key]['PREVIEW_PICTURE'] = $resizeImg;
        }
    }*/

    //if($item["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] ==)
//}
