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

if (isset($_POST['UF_DATE_PREM_EXP'])) {
    $days = 14;
    $now = date('d.m.Y');
    $datePremExp = date( 'd.m.Y', strtotime( $now ." +" . $days . "days" ));
    $_POST['UF_DATE_PREM_EXP'] = $datePremExp;
    $_REQUEST['UF_DATE_PREM_EXP'] = $datePremExp;
}
if (!empty($_POST['REGISTER']['PERSONAL_PHONE'])) {
	$_POST['REGISTER']['PERSONAL_PHONE'] = $_POST['full_number'];
	$_REQUEST['REGISTER']['PERSONAL_PHONE'] = $_POST['full_number'];
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");

//die;
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
		"SUCCESS_PAGE" => '/personal/',
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