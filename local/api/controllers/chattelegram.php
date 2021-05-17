<?php
namespace GipfelOwn\Api;

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('API_PROCESSING', true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Highloadblock;
\CModule::IncludeModule("highloadblock");

define('CHAT_TELEGRAM_HL', 2);
$result = 0;

$idChat = htmlspecialchars(request()->get('id_chat'));
$idUser = htmlspecialchars(request()->get('id_user'));

if(!$idChat || $idUser)
{
    $fields = array(
        "UF_ID_CHAT" => $idChat,
        "UF_ID_USER" => $idUser,
    );

    $hlBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(CHAT_TELEGRAM_HL)->fetch();
    $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlBlock);
    $entityClass = $entity->getDataClass();
    $res = $entityClass::add($fields);
    if($res->isSuccess())
    {
        $result = $res->getId();
    }
}
if($result)
{
    response()->json(array("success" => $result), 200, JSON_UNESCAPED_UNICODE, []);
}
else
{
    response()->json(array("error" => 0), 404, JSON_UNESCAPED_UNICODE, []);
}

/*\CModule::IncludeModule('iblock');

//define('SITE_TEMPLATE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/local/templates/main');
define('SITE_TEMPLATE_ID', 'main');

global $APPLICATION, $USER;
//ToDo: добавить часть про юзера
if (!is_object($USER)) $USER = new \CUser;
if ($USER->IsAuthorized())
	die('Auth panic!'); //есть подозрение - проверяем

$reqPhone = htmlspecialchars(request()->get('phone_number'));
$reqCapWord = htmlspecialchars(request()->get('captcha_word'));
$reqCapCode = htmlspecialchars(request()->get('captcha_code'));


$cpt = new \CCaptcha();

if (!$cpt->CheckCode($reqCapWord, $reqCapCode)) {//Капча не прошла
    $finalResponse = [
        'type' => 'error',
        'message' => 'Captcha error'
    ];
    response()->json($finalResponse, 404, JSON_UNESCAPED_UNICODE, []);
    return;
}

//Капча прошла, продолжаем.
//Восстановим капчу в БД для возможности многократных успешных попыток
\CCaptcha::Add(
    Array(
        "CODE" => $reqCapWord,
        "ID" => $reqCapCode
    )
);


$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

// модифицируем request чтобы передать нужные данные в  компонент
$request->modifyByQueryString("action=getPhoneNumber"); //checkPhoneCode - второй этап
$request->modifyByQueryString("phone=".$reqPhone);
// referer внутри используется, но что писать ему не ясно - на будущее.
//$request->modifyByQueryString("referer=i_dont_know");

$result = $APPLICATION->IncludeComponent(
	"nextype:gipfel.authorize",
	"api_v1",
	array(
		"PHONE_REGISTRATION" => true,
		"PHONE_REQUIRED" => false,
		"EMAIL_REGISTRATION"=> false,
		"EMAIL_REQUIRED" => false,
		"USE_EMAIL_CONFIRMATION" => "N",
		"PHONE_CODE_RESEND_INTERVAL" => 60,
		"RESEND_DELAY" => 60,
		"RETURN_RESULT" => "Y",
	),
	false
);

if ($result['response']['type'] == 'ok') {
    $finalResponse = $result['response'];

    if (SERVER_TYPE === 'dev') {
        $finalResponse['debug'] = $result['debug'];
    }

    $arUser = \CUser::GetByID($result['userId'])->fetch();

    if (!$arUser['UF_REST_API_TOKEN ']) {
        $user = new \CUser();
        $token = helper()->generateToken($arUser['ID'], $arUser['LOGIN']);
        //$expire = settings()->getTokenExpire();
        $user->update($arUser['ID'], [
            settings()->getTokenFieldCode() => $token,
        //  settings()->getTokenExpireFieldCode() => $expire
        ]);
    }

    response()->json($finalResponse, 200, JSON_UNESCAPED_UNICODE, []);
}
else
	response()->json($result['response'], 404, JSON_UNESCAPED_UNICODE, []);*/