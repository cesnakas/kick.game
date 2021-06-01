<?php

/**
 * Module REST API
 * 
 * @install  13.05.2021 16:25:48
 * @package  artamonov.rest
 * @author   Компания Webco <hello@webco.io>
 * @website  http://webco.io/
 */
if (Bitrix\Main\Loader::includeModule('artamonov.rest')) \Artamonov\Rest\Foundation\Core::getInstance()->run();


if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/functions.php'))
    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/functions.php';

require_once( $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/autoload.php');
AddEventHandler("main", "OnAfterUserAdd", "OnAfterUserAddHandler");
function OnAfterUserAddHandler(&$arFields)
{
    if($arFields["ID"] > 0)
    {
        if(strlen($arFields["UF_USER_PLATFORM"]) > 0)  //Если поле UF_BAZA заполнено
        {
            $arGroups = CUser::GetUserGroup($arFields["ID"]);
            $arGroups[] = 7; //То добаляем пользователя в группу c ID7
            //тариф Стандарт на 14 дней1
/*             $arGroups[] = array(
                "GROUP_ID" => 10,
                "DATE_ACTIVE_FROM" => date("d.m.Y 00:00:00"),
                "DATE_ACTIVE_TO" => date("d.m.Y 00:00:00", strtotime("+" . 14 . " day"))
            ); */
            CUser::SetUserGroup($arFields["ID"], $arGroups);
        }
        else
        {
            $arGroups = CUser::GetUserGroup($arFields["ID"]);
            $arGroups[] = 5; //Иначе в группу c ID5
            //тариф Стандарт на 14 дней
/*             $arGroups[] = array(
                "GROUP_ID" => 10,
                "DATE_ACTIVE_FROM" => date("d.m.Y 00:00:00"),
                "DATE_ACTIVE_TO" => date("d.m.Y 00:00:00", strtotime("+" . 14 . " day"))
            ); */
            CUser::SetUserGroup($arFields["ID"], $arGroups);
        }
    }
}

/*AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array("MyClass40", "OnBeforeIBlockElementAddHandler"));
class MyClass40
{
  function OnBeforeIBlockElementAddHandler(&$arFields)
  {
    $name = $arFields["NAME"];
    $arParams = array("replace_space"=>"-","replace_other"=>"-");
    $trans = Cutil::translit($name,"ru",$arParams);
    $arFields["CODE"] = $trans;
  }
}*/

// регистрируем обработчик в /bitrix/php_interface/init.php
AddEventHandler("search", "BeforeIndex", "BeforeIndexHandler");
// создаем обработчик события "BeforeIndex"
function BeforeIndexHandler($arFields)
{
    if(!CModule::IncludeModule("iblock")) // подключаем модуль
        return $arFields;
    if($arFields["MODULE_ID"] == "iblock")
    {
        $db_props = CIBlockElement::GetProperty(        // Запросим свойства индексируемого элемента
            $arFields["PARAM2"],         // IBLOCK_ID индексируемого свойства
            $arFields["ITEM_ID"],          // ID индексируемого свойства
            array("sort" => "asc"),         // Сортировка (можно упустить)
            Array("CODE"=>"TAG_TEAM")); // CODE свойства, по которому нужно осуществлять поиск
        if($ar_props = $db_props->Fetch())
            $arFields["TITLE"] .= " [".$ar_props["VALUE"]."]";   // Добавим свойство в конец заголовка индексируемого элемента
    }
    return $arFields; // вернём изменения
}

function dump($var, $die = false, $all = false)
{
    //global $USER;
    //if( ($USER->GetID() == 1) || ($all == true))
    //{
    ?>
    <div style="text-align: left; font-size: 17px"><pre><?var_dump($var)?></pre></div><br>
    <?
    //}
    if($die)
    {
        die;
    }
}

// при выходе делаем редирет на главную
AddEventHandler("main", "OnUserLogout", "debugLogout");
function debugLogout() {
    global $USER, $APPLICATION;
    if($APPLICATION->GetCurDir() != SITE_DIR){
       // LocalRedirect("/?logout=yes");
        LocalRedirect(SITE_DIR."?logout=yes&sessid=".$_SESSION["fixed_session_id"], true);
    }
}

use Bitrix\Highloadblock;
CModule::IncludeModule("highloadblock");
CModule::IncludeModule("iblock");
CModule::IncludeModule("main");

class CustomSubscribes
{
    const PRODUCT_IBLOCK_CODE = "tovari";
    const SUBSCRIBE_USER_GROUP = 10;

    //получить акутальную группу с подпиской пользователя
    public function getActualUserSubscribeGroup($userId)
    {
        $result = array();
        if(intval($userId))
        {
            $productsIds = array(self::SUBSCRIBE_USER_GROUP);
            /*$productsIds = array();
            $products = self::getSubscribes();
            foreach ($products as $k => $v)
            {
                $productsIds[] = intval($v["PROPERTY_USER_GROUP_VALUE"]);
            }*/
            $res = CUser::GetUserGroupList($userId);

            while ($group = $res->Fetch())
            {
              //dump($userId, 1);
              //dump($group, 1);
                //dump($group["GROUP_ID"]);
                //dump(in_array($group["GROUP_ID"], $productsIds));
                if(in_array($group["GROUP_ID"], $productsIds))
                {
                    if($group["DATE_ACTIVE_TO"] && $group["DATE_ACTIVE_FROM"])
                    {
                        $now = (new DateTime('now'))->getTimestamp();

                        $convertDateFrom = ConvertDateTime($group["DATE_ACTIVE_FROM"], "DD.MM.YYYY HH:MI:SS");
                        $convertDateTo = ConvertDateTime($group["DATE_ACTIVE_TO"], "DD.MM.YYYY HH:MI:SS");
                        $dateFrom = MakeTimeStamp($convertDateFrom, "DD.MM.YYYY HH:MI:SS");
                        $dateTo = MakeTimeStamp($convertDateTo, "DD.MM.YYYY HH:MI:SS");

                        if($now >= $dateFrom && $now <= $dateTo)
                        {
                            $result[] = $group;
                        }
                    }
                }
            }
        }
        return $result;
    }
    //получить информацию по подписке
    public function getSubscribeById($productId)
    {
        $result = array();
        if(intval($productId))
        {
            $result = CIBlockElement::GetList(
                array(),
                array(
                    "IBLOCK_CODE" => self::PRODUCT_IBLOCK_CODE,
                    "ID" => $productId
                ),
                false,
                false,
                array(
                    "ID",
                    "IBLOCK_ID",
                    "NAME",
                    //"PROPERTY_USER_GROUP",
                    "PROPERTY_COL",
                )
            )->Fetch();
        }
        return $result;
    }
    //получить доступные подписки
    public function getSubscribes()
    {
        $result = array();
        $res = CIBlockElement::GetList(
            array(),
            array(
                "ACTIVE" => "Y",
                "IBLOCK_CODE" => self::PRODUCT_IBLOCK_CODE
            ),
            false,
            false,
            array(
                "ID",
                "IBLOCK_ID",
                "NAME",
                //"PROPERTY_USER_GROUP",
                "PROPERTY_COL",
            )
        );
        while($element = $res->Fetch())
        {
            $result[] = $element;
        }
        return $result;
    }
    //добавить подписку пользователю и его команде
    public function setUserSubscribeGroup($userId, $productId)
    {
        $result = array();
        if(intval($userId) && intval($productId))
        {
            $product = CustomSubscribes::getSubscribeById($productId);
            //$productGroupId = $product["PROPERTY_USER_GROUP_VALUE"] ? $product["PROPERTY_USER_GROUP_VALUE"] : 0;
            $productDays = $product["PROPERTY_COL_VALUE"] ? $product["PROPERTY_COL_VALUE"] : 0;

            if(/*$productGroupId &&*/ $productDays)
            {
                $remainderDate = 0;
                $userProductGroups = self::getActualUserSubscribeGroup($userId);
                foreach ($userProductGroups as $k => $v)
                {
                    if($v["DATE_ACTIVE_TO"])
                    {
                        $now = (new DateTime('now'))->getTimestamp();
                        $date = DateTime::createFromFormat("d.m.Y H:i:s", $v["DATE_ACTIVE_TO"])->getTimestamp();
                        if($now < $date)
                        {
                            $remainderDate += ($date - $now);
                        }
                    }
                }
                $productGroups = array(self::SUBSCRIBE_USER_GROUP);
                /*$products = CustomSubscribes::getSubscribes();
                foreach ($products as $k => $v)
                {
                    $productGroups[$v["ID"]] =  intval($v["PROPERTY_USER_GROUP_VALUE"]);
                }*/

                $groupParams = array();
                $res = CUser::GetUserGroupList($userId);
                while ($group = $res->Fetch())
                {
                    if(!in_array($group["GROUP_ID"], $productGroups))
                    {
                        $groupParams[] = $group;
                    }
                }

                $dateFrom = date("d.m.Y H:i:s");
                $date = DateTime::createFromFormat("d.m.Y H:i:s", date("d.m.Y H:i:s", strtotime("+" . $productDays . " days")))->getTimestamp();

                $dateTo = date("d.m.Y H:i:s", $date + $remainderDate);

                $result = $groupParams[] = array(
                    "GROUP_ID" => self::SUBSCRIBE_USER_GROUP/*$productGroupId*/,
                    "DATE_ACTIVE_FROM" => $dateFrom,
                    "DATE_ACTIVE_TO" => $dateTo
                );
                CUser::SetUserGroup($userId, $groupParams);

                $userOb = new CUser;
                $fields = array(
                    "UF_DATE_PREM_EXP" => $dateTo
                );
                $userOb->Update($userId, $fields);
            }
        }
        return $result;
    }
    //получить комманду пользователя
    public function getUserTeam($userId)
    {
        $result = false;
        if(intval($userId))
        {
            $result = CUser::GetList(
                $by,
                $order,
                array(
                    "ID" => $userId
                ),
                array(
                    "SELECT" => array("UF_*")
                )
            )->Fetch()["UF_ID_TEAM"];
        }
        return $result;
    }
    //удалить пользователя с команды
    public function deleteUserTeam($userId)
    {
        $result = false;
        if(intval($userId))
        {
            $productGroups = array(self::SUBSCRIBE_USER_GROUP);
            $groupParams = array();
            $res = CUser::GetUserGroupList($userId);
            while ($group = $res->Fetch())
            {
                if(!in_array($group["GROUP_ID"], $productGroups))
                {
                    $groupParams[] = $group;
                }
            }
            CUser::SetUserGroup($userId, $groupParams);
            $user = new CUser;
            $fields = array(
                "UF_ID_TEAM" => "",
                "UF_DATE_PREM_EXP" => ""
            );
            $result = $user->Update($userId, $fields);
        }
        return $result;
    }
    //добавить пользователя в команду
    public function addUserTeam($userId, $teamId)
    {
        $result = false;
        if(intval($userId) && intval($teamId))
        {
            $groupParams = array();
            $productGroups = array(self::SUBSCRIBE_USER_GROUP);
            $res = CUser::GetUserGroupList($userId);
            while ($group = $res->Fetch())
            {
                if(!in_array($group["GROUP_ID"], $productGroups))
                {
                    $groupParams[] = $group;
                }
            }
            $users = self::getCoreTeam($teamId);
            if($users)
            {
                $res = CUser::GetUserGroupList($users[0]["ID"]);
                while ($group = $res->Fetch())
                {
                    if(in_array($group["GROUP_ID"], $productGroups))
                    {
                        $groupParams[] = $group;
                    }
                }
                CUser::SetUserGroup($userId, $groupParams);
                $user = new CUser;
                $fields = array(
                    "UF_ID_TEAM" => $teamId,
                    "UF_DATE_PREM_EXP" => $users[0]["UF_DATE_PREM_EXP"]
                );
                $result = $user->Update($userId, $fields);
            }
        }
        return $result;
    }
    //получить список игроков в команде
    public function getCoreTeam($teamId)
    {
        $result = array();
        if(intval($teamId))
        {
            $res = CUser::GetList(
                ($by = "NAME"),
                ($order = "desc"),
                array(
                    "GROUPS_ID" => array(7),
                    "UF_ID_TEAM" => $teamId
                ),
                array(
                    "SELECT" => array("UF_*")
                )
            );
            while ($user = $res->Fetch())
            {
                $result[] = $user;
            }
        }
        return $result;
    }
}

class CustomTelegram
{
    const MATCHES_IBLOCK_CODE = "matches";
    const MATCHESQUAD_IBLOCK_CODE = "matchsquad";
    const MATCHES_COMMAND_IBLOCK_CODE = "matchparticipants";
    const NOTIFY_HL_ID = 3;
    const CHAT_HL_ID = 2;
    const NOTIFICATION_URL = 'http://178.20.45.4:7070/api/messages';

    //получить ближайший матч
    public function getNearestMatchUsers($startHour, $finishHour)
    {
        $result = array();
        $res = CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_CODE" => self::MATCHES_IBLOCK_CODE,
                array(
                    "LOGIC" => "AND",
                    array(
                        ">=PROPERTY_DATE_START" => date("Y-m-d H:i:s", strtotime("+" . $startHour . " hour"))
                    ),
                    array(
                        "<=PROPERTY_DATE_START" => date("Y-m-d H:i:s", strtotime("+" . $finishHour . " hour"))
                    ),
                )

            ),
            false,
            false,
            array(
                "ID",
                "IBLOCK_ID",
                "NAME",
                "PROPERTY_URL_STREAM",
                "PROPERTY_PUBG_LOBBY_ID",
                "PROPERTY_COUTN_TEAMS",
            )
        );
        while($element = $res->Fetch())
        {
            $result[] = $element;
        }
        return $result;
    }
    //список команд на матч
    public function getMatchTeams($matchId)
    {
        $result = array();
        if(intval($matchId))
        {
            $select = array(
                "ID",
                "IBLOCK_ID",
                "NAME"
            );
            for($i = 3; $i<= 20; $i++)
            {
                $select[] = "PROPERTY_TEAM_PLACE_" . (($i < 10) ? "0" : "") . $i;
            }
            $res = CIBlockElement::GetList(
                array(),
                array(
                    "IBLOCK_CODE" => self::MATCHES_COMMAND_IBLOCK_CODE,
                    "PROPERTY_WHICH_MATCH" => $matchId
                ),
                false,
                false,
                $select
            );
            while($element = $res->Fetch())
            {
                for($i = 3; $i<= 20; $i++)
                {
                    $teamId = $element["PROPERTY_TEAM_PLACE_" . (($i < 10) ? "0" : "") . $i . "_VALUE"];
                    if($teamId)
                    {
                        $result[] = $teamId;
                    }
                }
            }
        }
        return $result;
    }
    //получить игроков в команде
    public function getCoreTeam($teamId)
    {
        $result = array();
        if(intval($teamId))
        {
            $res = CUser::GetList(
                ($by = "NAME"),
                ($order = "desc"),
                array(
                    "GROUPS_ID" => array(7),
                    "UF_ID_TEAM" => $teamId
                ),
                array(
                    "SELECT" => array("UF_*")
                )
            );
            while ($user = $res->Fetch())
            {
                $result[] = $user["ID"];
            }
        }
        return $result;
    }
    //добавить отчет
    function addReport($fields)
    {
        $result = 0;
        if(!empty($fields))
        {
            $hlBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(self::NOTIFY_HL_ID)->fetch();
            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlBlock);
            $entityClass = $entity->getDataClass();
            $res = $entityClass::add($fields);
            if($res->isSuccess())
            {
                $result = $res->getId();
            }
        }
        return $result;
    }
    //обновить отчет
    function updateReport($id, $fields)
    {
        $result = false;
        if(intval($id) && !empty($fields))
        {
            $hlBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(self::NOTIFY_HL_ID)->fetch();
            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlBlock);
            $entityClass = $entity->getDataClass();
            $res = $entityClass::update($id, $fields);
            if($res->isSuccess())
            {
                $result = $res->getId();
            }
        }
        return $result;
    }
    //получить отчет
    function getReport($filter, $select = array("*"))
    {
        $result = array();
        if($filter)
        {
            $hlBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(self::NOTIFY_HL_ID)->fetch();
            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlBlock);
            $entityClass = $entity->getDataClass();
            $res = $entityClass::getList(
                array(
                    "filter" => $filter,
                    "select" => $select
                )
            );
            while($el = $res->Fetch())
            {
                $result[] = $el;
            }
        }
        return $result;
    }
    //получить chatId
    public function getChatId($userId)
    {
        $result = array();
        if(intval($userId))
        {
            $hlBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(self::CHAT_HL_ID)->fetch();
            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlBlock);
            $entityClass = $entity->getDataClass();
            $res = $entityClass::getList(
                array(
                    "filter" => array(
                        "UF_ID_USER" => $userId
                    ),
                    "select" => array(
                        "UF_ID_CHAT"
                    )
                )
            );
            while($chat = $res->fetch())
            {
                $result[] = intval($chat["UF_ID_CHAT"]);
            }
        }
        return $result;
    }
    //отправить уведомления пользователям
    public function sendNotifications($type = "", $chats = [], $sender = "", $text = "")
    {
        $chats = json_encode($chats);
        /*echo("<pre>");var_dump('{
                "type": ' . $type . ',
                "chats": ' . $chats . ',
                "sender": "' . $sender . '",
                "text": "' . $text . '"
            }');echo("</pre>");*/
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::NOTIFICATION_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "type": ' . $type . ',
                "chats": ' . $chats . ',
                "sender": "' . $sender . '",
                "text": "' . $text . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //echo("<pre>");var_dump($httpCode);echo("</pre>");
        curl_close($curl);
        return $response;
    }
    //
    public function scan($hour = array(), $type = 0, $sender = "", $text = "")
    {
        if($hour)
        {
            //получаем матчи
            $matches = self::getNearestMatchUsers($hour[0], $hour[1]);
            foreach ($matches as $match)
            {
                $chats = array();
                $reports = array();
                //получаем комманды
                $commands = self::getMatchTeams($match["ID"]);
                foreach ($commands as $command)
                {
                    //получаем игроков
                    $players = self::getCoreTeam($command);
                    //для каждого игорка
                    foreach ($players as $player)
                    {
                        $filter = array(
                            "UF_MATCH_ID" => $match["ID"],
                            "UF_USER_ID" => $player,
                            "UF_TYPE" => $type,
                            "UF_NOTIFY_NUM" => $hour[1]
                        );
                        $select = array("ID");
                        //проеверям для игрока была ли уже отправка по текущему матчу и текущему временному интервалу
                        $sending = CustomTelegram::getReport($filter, $select);
                        if(!$sending)
                        {
                            //проверяем если ли у игрока chatId
                            $playerChat = self::getChatId($player);
                            if($playerChat)
                            {
                                $chats = array_merge($chats, $playerChat);
                                $reports[] = array(
                                    "UF_MATCH_ID" => $match["ID"],
                                    "UF_USER_ID" => $player,
                                    "UF_TYPE" => $type,
                                    "UF_NOTIFY_NUM" => $hour[1],
                                    "UF_DATE" => date("d.m.Y H:i:s"),
                                );
                            }
                            /*$chatId = self::getChatId($player);
                            if($chatId)
                            {
                                //отправляем уведомление в telegram
                                //self::sendNotifications();
                                $fields = array(
                                    "UF_MATCH_ID" => $match,
                                    "UF_USER_ID" => $player,
                                    "UF_TYPE" => $type,
                                    "UF_NOTIFY_NUM" => $hour[1],
                                    "UF_DATE" => date("d.m.Y H:i:s"),
                                );
                                //добавим инфу об отправке уведомления
                                self::addReport($fields);
                            }*/
                        }
                    }
                }
                if($chats)
                {
                    $chats =  array_unique($chats);
                    //echo("<pre>");var_dump($chats);echo("</pre>");
                    //отправляем уведомление в telegram
                    $text = str_replace(array("#NUM#", "#HOUR#", "#HOUR_WORD#", "#URL#", "#LOBBY#", "#TEAM#"), array($match["ID"], $hour[1], ($hour[1] == 1 ? "час" : ($hour[1] > 4 ? "часов" :"часа")), $match["PROPERTY_URL_STREAM_VALUE"], (trim($match["PROPERTY_PUBG_LOBBY_ID_VALUE"]) ? $match["PROPERTY_PUBG_LOBBY_ID_VALUE"] : "-"), $match["PROPERTY_COUTN_TEAMS_VALUE"]), $text);
                    $rt = self::sendNotifications($type, $chats, $sender, $text);
                    //echo("<pre>");var_dump($rt);echo("</pre>");
                    //добавим инфу об отправке уведомления
                    foreach ($reports as $k => $v)
                    {
                        self::addReport($v);
                    }
                }
            }
        }
    }
}

//AddEventHandler("main", "OnAfterUserAdd", "OnBeforeUserUpdateHandler");
//AddEventHandler("main", "OnBeforeUserUpdate", "OnBeforeUserUpdateHandler");
/*function OnBeforeUserUpdateHandler(&$arFields)
{
    $user = CUser::GetList(
        $by,
        $order,
        array(
            "ID" => $arFields["ID"]
        ),
        array(
            "SELECT" => array("UF_ID_TEAM")
        )
    )->Fetch();
    if($arFields["UF_ID_TEAM"] && empty($user["UF_ID_TEAM"]))
    {
        CustomSubscribes::addUserTeam($arFields["ID"], $user["UF_ID_TEAM"]);
    }
    else if(empty($arFields["UF_ID_TEAM"]) && $user["UF_ID_TEAM"])
    {
        CustomSubscribes::deleteUserTeam($arFields["ID"]);
    }
    return $arFields;
}*/
