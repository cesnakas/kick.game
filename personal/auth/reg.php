<?

if (!empty($_POST['REGISTER']['EMAIL'])) {

	require_once __DIR__ . '/../../vendor/autoload.php';
	$client = new MailchimpMarketing\ApiClient();

	$client->setConfig([
		'apiKey' => '5ae189555b58f6863614f595cdd14823-us7',
		'server' => 'us7',
	]);
	try {
		$response = $client->lists->addListMember("f4db208c5b", [
			"email_address" => $_POST['REGISTER']['EMAIL'],
			"status" => "subscribed",
		]);
	} catch (Exception $e) {
		echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
	}
	//print_r($response);
}


if (!empty($_POST['REGISTER']['PERSONAL_PHONE'])) {
	$_POST['REGISTER']['PERSONAL_PHONE'] = $_POST['full_number'];
	$_REQUEST['REGISTER']['PERSONAL_PHONE'] = $_POST['full_number'];
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");

if (isset($_POST['REGISTER']['LOGIN'])) {
	$_POST['UF_DATE_PREM_EXP'] = new \Bitrix\Main\Type\DateTime();
    $_REQUEST['UF_DATE_PREM_EXP'] = new \Bitrix\Main\Type\DateTime();
}
//die;
$urlRedirect = '/personal/';
if (LANGUAGE_ID == 'en') {
	$urlRedirect = '/en/personal/';
}

$APPLICATION->IncludeComponent(
	"bitrix:main.register", 
	"registration", 
	array(
		"AUTH" => "Y",
		"REQUIRED_FIELDS" => array(
			0 => "EMAIL",
			1 => "PERSONAL_PHONE",
		),
		"SET_TITLE" => "Y",
		"SHOW_FIELDS" => array(
			0 => "EMAIL",
			1 => "PERSONAL_PHONE",
		),
		"SUCCESS_PAGE" => $urlRedirect,
		"USER_PROPERTY" => array(
			0 => "UF_RATING",
		),
		"USER_PROPERTY_NAME" => "",
		"USE_BACKURL" => "Y",
		"COMPONENT_TEMPLATE" => "registration"
	),
	false
);?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>