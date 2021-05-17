<?php

$pathControllers = $_SERVER['DOCUMENT_ROOT'] .  '/local/api/controllers';
include(__DIR__ . "/../controllers/controllers_v1.php");

/**
 * @api {post} /user/sendsms/ Запрос на отправку СМС по номеру телефона
 * @apiName SendSms
 * @apiGroup User
 *
 * @apiParam {string} phone_number Номер телефона.
 * @apiParam {string} captcha_word Юзерский воод текста с картинки капчи для проверки.
 * @apiParam {string} captcha_code Код капчи полученный в другом запросе.
 *
 * @apiSuccess (200) {string} type Результат успеха, обычно "ok".
 * @apiSuccess (200) {string} message Детальное описание.
 * @apiSuccess (200) {string} debug Номер записи в БД для отладки.
 *
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *          "type": "ok",
 *          "message": "На номер <b>+79110000000</b> отправлен код"
 *     }
 *
 * @apiError (404) {string} type Результат ошибки, обычно "error".
 * @apiError (404) {string} message Детальное описание ошибки.
 *
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *          "type": "error",
 *          "message": "Указан некорректный номер телефона."
 *     }
 *
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *          "type": "error",
 *          "message": "Captcha error"
 *     }
 */
$GipfelRoutes ['POST']['chattelegram/'] =
     [
        'description' => '',
        'active' => true,
        'controller' => $pathControllers . '/chattelegram.php',
        //'contentType' => 'application/json',
        'parameters' => [
            'id_chat' => [
                'required' => true,
                'type' => 'string',
                'description' => 'Id чата',
            ],
            'id_user' => [
                'required' => true,
                'type' => 'string',
                'description' => 'Id юзера',
            ],
        ],
        'security' => [
             // Настройки авторизации при запросе
             'auth' => [
                 'required' => true,
                 'type' => 'token', // login || token
            ],
        ],
    ];





return $GipfelRoutes;



