<?
$cookieToSave = false;
if(isset($_POST['AUTH_ACTION'])) {
    //echo '<pre>' . print_r($_POST, 1) . '</pre>';
    //echo '<pre>' . print_r($_COOKIE, 1) . '</pre>';
    //echo '<pre>' . print_r($_SESSION, 1) . '</pre>';
    $cookieToSave = json_encode($_COOKIE);
    //echo '<pre>' . print_r($cookieToSave, 1) . '</pre>';
    //die;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация");
?><?
$cookieToSave = json_encode($_COOKIE);

if($cookieToSave != false) {
    $userLoginCookies = new \App\UserLoginCookiesTable();
    // add
  $date = new \Bitrix\Main\Type\DateTime;

  $rsUser = CUser::GetByLogin(htmlspecialchars($_POST["USER_LOGIN"]));
  $arUser = $rsUser->Fetch();
  if($arUser['ID']+0 > 0 ) {
    $result = $userLoginCookies::add(array(
        'USER_ID' => $arUser['ID']+0,
        'C_DATE' => $date,
        'CONTENT' => $cookieToSave
    ));
  }
}

$APPLICATION->IncludeComponent("bitrix:main.auth.form", "auth", Array(
	"AUTH_FORGOT_PASSWORD_URL" => SITE_DIR."personal/auth/forgotpassword.php",	// Страница для восстановления пароля
		"AUTH_REGISTER_URL" => SITE_DIR."personal/auth/reg.php",	// Страница для регистрации
		"AUTH_SUCCESS_URL" => SITE_DIR."personal/",	// Страница после успешной авторизации
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>