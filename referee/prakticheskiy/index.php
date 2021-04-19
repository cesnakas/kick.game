<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Практический");
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

if (isset($_POST['create_match'])) {

  function makeUID($PROP, $level)
  {
    $str = 'MATCH';
    if ($PROP['TOURNAMENT']) {
      $str.='TOURNAMENT';
    } else {
      $str.='_PRAC';
    }
    $time = new \DateTime($PROP['DATE_START']);
    $str.= '_'.$time->format('d.m.Y-H:i');
    $str.= '_GROUP'.$PROP['STAGE_TOURNAMENT'];
    $str.= '_STAGE'.$level;
    $time = new \DateTime();
    $str.= $time->format('_YmdHis');
    return $str;

    //$PROP['COUTN_TEAMS'] = 4;
    //$PROP['TYPE_MATCH'] = 6; // id свойства
  }
  function createMatchMembersEmpty($idMatch, $code)
    {
        $el = new CIBlockElement;
        $iblock_id = 4;
        $PROP =  array('WHICH_MATCH' => $idMatch);
        $fields = array(
            "DATE_CREATE" => date("d.m.Y H:i:s"), //Передаем дата создания
            "CREATED_BY" => $GLOBALS['USER']->GetID(),    //Передаем ID пользователя кто добавляет
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => $iblock_id, //ID информационного блока он 24-ый
            "PROPERTY_VALUES" => $PROP, // Передаем массив значении для свойств
            "NAME" => 'MEMBERS_'.$code,
            "ACTIVE" => "Y", //поумолчанию делаем активным или ставим N для отключении поумолчанию
        );
        //Результат в конце отработки
        if ($ID = $el->Add($fields)) {
            return $ID;

        } else {
            return "Error: ".$el->LAST_ERROR;
        }
    }
  function createMatchItem($PROP = [], $level = 0)
  {
      $el = new CIBlockElement;
      $iblock_id = 3;
      $params = Array(
          "max_len" => "100", // обрезает символьный код до 100 символов
          "change_case" => "L", // буквы преобразуются к нижнему регистру
          "replace_space" => "-", // меняем пробелы на нижнее подчеркивание
          "replace_other" => "-", // меняем левые символы на нижнее подчеркивание
          "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
          "use_google" => "false", // отключаем использование google
      );
      $code = makeUID($PROP, $level);
      $fields = array(
          "DATE_CREATE" => date("d.m.Y H:i:s"), //Передаем дата создания
          "CREATED_BY" => $GLOBALS['USER']->GetID(),    //Передаем ID пользователя кто добавляет
          "IBLOCK_SECTION_ID" => false,
          "CODE" => CUtil::translit($code, "ru" , $params),
          "IBLOCK_ID" => $iblock_id, //ID информационного блока он 24-ый
          "PROPERTY_VALUES" => $PROP, // Передаем массив значении для свойств
          "NAME" => $code,
          "ACTIVE" => "Y", //поумолчанию делаем активным или ставим N для отключении поумолчанию
          "PREVIEW_TEXT" => strip_tags($_REQUEST['description_team']), //Анонс
          "PREVIEW_PICTURE" => $_FILES['image'], //изображение для анонса
          "DETAIL_TEXT"    => strip_tags($_REQUEST['description_team']),
          //"DETAIL_PICTURE" => $_FILES['image'] //изображение для детальной страницы
      );
      //Результат в конце отработки
      if ($ID = $el->Add($fields)) {
          createMatchMembersEmpty($ID, $code);
          return $ID;

      } else {
          return "Error: ".$el->LAST_ERROR;
      }
  }


    $PROP = [];

    $PROP['TOURNAMENT'] = false;
    $PROP['DATE_START'] = $_POST['date_time_match'];
    $PROP['URL_STREAM'] = false;
    $PROP['PUBG_LOBBY_ID'] = false;
    $PROP['COUTN_TEAMS'] = 4;
    $PROP['PREV_MATCH'] =  false;
    $PROP['STAGE_TOURNAMENT'] = 4; // id свойства
    $PROP['TYPE_MATCH'] = 6; // id свойства

  $countChainMatches = 3;
  $matchDuration = 40; // 30 min
    $resIds = [];
  $resId = createMatchItem($PROP, 1);

  if ($resId+0 > 0 ) {
    $resIds[] = $resId;
    $countChainMatches--;
    $countItems = $countChainMatches;
    do{
      $countItems--;
      $time = new \DateTime($_REQUEST['date_time_match']);
      $time->add(new \DateInterval('PT'.($matchDuration*($countChainMatches-$countItems)).'M'));
      $nextDateTime = $time->format('d.m.Y H:i');
      $PROP['DATE_START'] = $nextDateTime;
      $PROP['PREV_MATCH'] = $resId;
      $resId = createMatchItem($PROP, $countChainMatches-$countItems+1);
        $resIds[] = $resId;

    } while($countItems > 0 && $resId+0 > 0);

  }



}

?>
<div class="container my-5">
  <?php
  if(!empty($resIds)) { ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <h4 class="alert-heading">Well done!</h4>
      <p>Матчи с ID <strong><?php echo implode($resIds, ','); ?></strong> созданы.</p>
      <hr>
      <p><a href="/dashboard/match-chain/?id=<?php echo $resIds[0];?>">Перейти в цепочку матчей</a></p>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  <? } ?>
    <h2>Создать цепочку матчей</h2>
    <div class="container">
        <div class="row">
          <div class="col-6">
            <form action="#" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label for="datetimepicker2" class="form-label">Введите дату и время матча</label>
                <input type='text' class="form-control" name="date_time_match" value="" id="datetimepicker2" />
              </div>

              <button type="submit" name="create_match" class="btn btn-primary">Отправить</button>
            </form>
          </div>
        </div>
    </div>
</div>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>