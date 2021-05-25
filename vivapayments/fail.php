<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
Оплата не прошла обратитесь к администратору
<?
$t = $_GET["t"];
if(!empty($t))
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.vivapayments.com/api/transactions/' . $t . '/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ZThiZDk1MjktOTQxZC00ZThlLThmZGQtM2U3Zjc0ZmE1MGQ0OjdoVGptWTc5OFBmSFplYU1DZHJhbnV6aWdXN0QyRg=='
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>