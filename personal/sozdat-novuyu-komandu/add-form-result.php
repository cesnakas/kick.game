<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("ROBOTS", "noindex, nofollow");
$APPLICATION->SetPageProperty("TITLE", "Обработчик");
$APPLICATION->SetPageProperty("keywords", "Обработчик");
$APPLICATION->SetPageProperty("description", "Обработчик");
$APPLICATION->SetTitle("Обработчик");
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
?>
<?
$userID = CUser::GetID();
if (!empty($_REQUEST['name']) and !empty($_REQUEST['description_team'])) {

  CModule::IncludeModule('iblock');

  //echo 'Вот такие данные мы передали';
  //echo '<pre>';
  //print_r($_POST);
  //echo '<pre>';
  //die();


  //Погнали
  $el = new CIBlockElement;
  $iblock_id = 1;
  //$section_id = false;
  //$section_id[$i] = $_POST['section_id']; //Разделы для добавления

  //Свойства
  $PROP = [];

  $PROP['NAME_TEAM'] = $_POST['name'];
  $PROP['TAG_TEAM'] = $_POST['tag_team'];
  $PROP['LOGO_TEAM'] = $_FILES['image'];
  $PROP['DESCRIPTION_TEAM'] = Array("VALUE" => Array ("TEXT" => $_POST['description_team'], "TYPE" => "html или text"));
  $PROP['AUTHOR'] = $userID;


  //Основные поля элемента
  $fields = array(
    "DATE_CREATE" => date("d.m.Y H:i:s"), //Передаем дата создания
    "CREATED_BY" => $GLOBALS['USER']->GetID(),    //Передаем ID пользователя кто добавляет
    "IBLOCK_SECTION_ID" => false,
    "IBLOCK_ID" => $iblock_id, //ID информационного блока он 24-ый
    "PROPERTY_VALUES" => $PROP, // Передаем массив значении для свойств
    "NAME" => strip_tags($_REQUEST['name']),
    "ACTIVE" => "Y", //поумолчанию делаем активным или ставим N для отключении поумолчанию
    "PREVIEW_TEXT" => strip_tags($_REQUEST['description_team']), //Анонс
    "PREVIEW_PICTURE" => $_FILES['image'], //изображение для анонса
    "DETAIL_TEXT"    => strip_tags($_REQUEST['description_team']),
      "DETAIL_PICTURE" => $_FILES['image'] //изображение для детальной страницы
    );


    //Результат в конце отработки
    if ($ID = $el->Add($fields)) {
      //echo "Команда успешно сохранена id - " . $ID;

      $user = new CUser;
      $fields = Array(
        //"NAME"              => "Сергей",
        "UF_ID_TEAM"        => $ID,
      );
      if ($user->Update($userID, $fields)) {
        //echo 'ID_Team заполнен';
        header('Location: /');
      } else {
        echo 'Error: ' . $user->LAST_ERROR;
      }

    } else {
      echo "Error: ".$el->LAST_ERROR;
    }
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>