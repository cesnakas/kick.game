<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Редактирование пользователя");
if(!empty($_POST["UF_PUBG_ID"])) {
    $pubgId = trim(strip_tags($_POST["UF_PUBG_ID"]));
    $existsPubgid = existsPubgId($userID, $pubgId);
    if(!empty($existsPubgid)) {
        $alertPubgIdError = 'Такой pubg id существует, попробуй еще';
        createSession('exists_pubgId_error', $alertPubgIdError);
        LocalRedirect('');
    }
}
?>
<?php
if(isset($_SESSION['exists_pubgId_error'])){ ?>
    <div class="alert-container" style="position: relative !important;">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['exists_pubgId_error'];?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php }
unset($_SESSION['exists_pubgId_error']);
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