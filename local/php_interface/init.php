<?

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
            //тариф Стандарт на 14 дней
            $arGroups[] = array(
                "GROUP_ID" => 10,
                "DATE_ACTIVE_FROM" => date("d.m.Y 00:00:00"),
                "DATE_ACTIVE_TO" => date("d.m.Y 00:00:00", strtotime("+" . 14 . " day"))
            );
            CUser::SetUserGroup($arFields["ID"], $arGroups);
        }
        else
        {
            $arGroups = CUser::GetUserGroup($arFields["ID"]);
            $arGroups[] = 5; //Иначе в группу c ID5
            //тариф Стандарт на 14 дней
            $arGroups[] = array(
                "GROUP_ID" => 10,
                "DATE_ACTIVE_FROM" => date("d.m.Y 00:00:00"),
                "DATE_ACTIVE_TO" => date("d.m.Y 00:00:00", strtotime("+" . 14 . " day"))
            );
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

?>