<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>

<script src="https://www.vivapayments.com/web/checkout/js"></script>

<form id="myform" action="/success.php" method="post">
    <button type="button"
      data-vp-publickey="8KvNFc04zx3/U3LmOSEjpq/z7OFM7iqdJVNcqVsvozQ="
      data-vp-baseurl=""
      data-vp-lang="en"
      data-vp-amount="10000"
      data-vp-sourcecode="1180"
      data-vp-description="My product"
      data-vp-disablewallet="false"
      data-vp-expandcard="true">
    </button>
</form>


<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://demo-api.vivapayments.com/nativecheckout/v2/chargetokens',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
"Cvc": "111",
"Amount": 1000,
"Number": "5239290700000101",
"Holdername": "Joe Blogs",
"ExpirationYear": 2030,
"ExpirationMonth": 10,
"SessionRedirectUrl": "www.example.com"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Bearer [access_token]'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

?>
<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://www.vivapayments.com/api/orders',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "amount": 10000,
    "email": "customer11@example.com",
    "fullName": "432432432e",
    "customerTrns": "Short description of items/services purchased to display to your customer",
    "requestLang": "en",
    "sourceCode": "1180"

}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Basic [Base64-encoded credentials]'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>







<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>