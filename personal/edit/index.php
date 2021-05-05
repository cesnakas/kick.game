<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Редактирование пользователя");
?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.profile",
    "edit",
    Array(
        "CHECK_RIGHTS" => "N",
        "COMPONENT_TEMPLATE" => "edit_test",
        "SEND_INFO" => "N",
        "SET_TITLE" => "Y",
        "USER_PROPERTY" => array(),
        "USER_PROPERTY_NAME" => ""
    )
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>