я<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Турнирный");
CModule::IncludeModule('iblock');

$success = [];
// собираем все ошибки
$errors = [];
// create tournament

function getMatchesByDate($date, $tournamentID) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array(
        "IBLOCK_ID" =>3,
        "PROPERTY_PREV_MATCH" => false,
        "PROPERTY_TYPE_MATCH" => 5,
        "PROPERTY_TOURNAMENT" => $tournamentID,
        "PROPERTY_DATE_START" => ConvertDateTime($date, "YYYY-MM-DD HH:MI:SS"),
        //  "<=PROPERTY_DATE_START" => ConvertDateTime($date, "YYYY-MM-DD")." 23:59:59",
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $output = [];

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        //$arProps = $ob->GetProperties();
        $output[] = $arFields;
    }
    return $output;
}

function getFinalsMatches($tournamentID, $stage) {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array(
        "IBLOCK_ID" =>3,
        "PROPERTY_PREV_MATCH" => false,
        "PROPERTY_TYPE_MATCH" => 5,
        "PROPERTY_TOURNAMENT" => $tournamentID,
        "PROPERTY_STAGE_TOURNAMENT" => 1,
        //  "<=PROPERTY_DATE_START" => ConvertDateTime($date, "YYYY-MM-DD")." 23:59:59",
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $output = [];

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        //$arProps = $ob->GetProperties();
        $output[] = $arFields;
    }
    return $output;
}

function getTournaments() {
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID" => 7, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $tour = [];
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $tour[] = array_merge($arFields, $arProps);
    }
    return $tour;
}

function getStagesTournament() {
    $res = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC", "ID"=>"ASC"),[
        "IBLOCK_ID" => 3,
        "CODE" => "STAGE_TOURNAMENT"
    ]);
    $stages = [];
    while ($stage = $res->Fetch()){
        $stages[] = $stage["ID"];
    }

    return $stages;
}

function createTournament($PROP = [], $img)
{
    $el = new CIBlockElement;
    $iblock_id = 7;
    $params = Array(
        "max_len" => "100", // обрезает символьный код до 100 символов
        "change_case" => "L", // буквы преобразуются к нижнему регистру
        "replace_space" => "-", // меняем пробелы на нижнее подчеркивание
        "replace_other" => "-", // меняем левые символы на нижнее подчеркивание
        "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
        "use_google" => "false", // отключаем использование google
    );

    $fields = array(
        "DATE_CREATE" => date("d.m.Y H:i:s"), //Передаем дата создания
        "CREATED_BY" => $GLOBALS['USER']->GetID(),    //Передаем ID пользователя кто добавляет
        "IBLOCK_SECTION_ID" => false,
        "CODE" => CUtil::translit($PROP['NAME'], "ru" , $params),
        "IBLOCK_ID" => $iblock_id, //ID информационного блока он 24-ый
        "PROPERTY_VALUES" => $PROP, // Передаем массив значении для свойств
        "NAME" => $PROP['NAME'],
        "ACTIVE" => "Y", //поумолчанию делаем активным или ставим N для отключении поумолчанию
        "PREVIEW_TEXT" => $PROP['ANONS'], //Анонс
        "PREVIEW_PICTURE" => $img, //изображение для анонса
        "DETAIL_TEXT"    => $PROP['DESCRIPTION'],
        "DETAIL_PICTURE" => $img //изображение для детальной страницы
    );
    //Результат в конце отработки
    if ($ID = $el->Add($fields)) {
        //createMatchMembersEmpty($ID, $code);
        return $ID;

    } else {
        //return "Error: ".$el->LAST_ERROR;
        return false;
    }
}

function makeUID($PROP, $level)
{
    $str = 'MATCH';
    if ($PROP['TOURNAMENT']) {
        $str.='_#'. $PROP['TOURNAMENT'] . '_TOURNAMENT';
    } else {
        $str.='_PRAC';
    }
    $time = new \DateTime($PROP['DATE_START']);
    $str.= '_'.$time->format('d.m.Y-H:i');
    $str.= '_GROUP'.$PROP['STAGE_TOURNAMENT'];
    $str.= '_STAGE'.$level;
    $time = new \DateTime();
    $str.= $time->format('_YmdHis');
    $str.= '_'. uniqid();
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

function makeUnicUrlCode($code,$IBlockId)
{
    $separator = '-';
    while(CIBlockElement::GetList(array(), array('IBLOCK_ID' => $IBlockId, '=NAME' => $code))->SelectedRowsCount()) {
       $code .= $separator . rand(1,9);
       $separator = '';
       //dump($code);
    }
    return $code;
}

function createMatchItem($PROP = [], $level = 0)
{
//    $PROP['GROUP'] = 1 + count( getMatchesByDate( $PROP['DATE_START'],  $PROP['TOURNAMENT']));
//    if ($PROP['STAGE_TOURNAMENT'] == 1){
//        $PROP['GROUP'] = 1 + count(getFinalsMatches($PROP['TOURNAMENT'], $PROP['STAGE_TOURNAMENT']));
//    }

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

    $code = makeUnicUrlCode($code, $iblock_id);
    $urlCode = CUtil::translit($code, "ru" , $params);


    $fields = array(
        "DATE_CREATE" => date("d.m.Y H:i:s"), //Передаем дата создания
        "CREATED_BY" => $GLOBALS['USER']->GetID(),    //Передаем ID пользователя кто добавляет
        "IBLOCK_SECTION_ID" => false,
        "CODE" => $urlCode,
        "IBLOCK_ID" => $iblock_id, //ID информационного блока он 24-ый
        "PROPERTY_VALUES" => $PROP, // Передаем массив значении для свойств
        "NAME" => $code,
        "ACTIVE" => "Y", //поумолчанию делаем активным или ставим N для отключении поумолчанию
        //"PREVIEW_TEXT" => strip_tags($_REQUEST['description_team']), //Анонс
        //"PREVIEW_PICTURE" => $_FILES['image'], //изображение для анонса
        //"DETAIL_TEXT"    => strip_tags($_REQUEST['description_team']),
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

function createMatchChain($propsMatch, $countChainMatches, $startDateTime): array
{
    $propsMatch['GROUP'] = 1 + count( getMatchesByDate( $propsMatch['DATE_START'],  $propsMatch['TOURNAMENT']));
    if ($propsMatch['STAGE_TOURNAMENT'] == 1){
        $propsMatch['GROUP'] = 1 + count(getFinalsMatches($propsMatch['TOURNAMENT'], $propsMatch['STAGE_TOURNAMENT']));
    }
    $matchDuration = 40; // 30 min
    $resIds = [];
    //dump($countChainMatches, 1);
    if ($countChainMatches == 1) {
        $resId = createMatchItem($propsMatch, 1);
        $resIds[] = $resId;
    } else {
        $resId = createMatchItem($propsMatch, 1);
        if ($resId+0 > 0 ) {
            $resIds[] = $resId;
            $countChainMatches--;
            $countItems = $countChainMatches;
            do{
                $countItems--;
                $time = new \DateTime($startDateTime);
                $time->add(new \DateInterval('PT'.($matchDuration*($countChainMatches-$countItems)).'M'));
                $nextDateTime = $time->format('d.m.Y H:i');
                $propsMatch['DATE_START'] = $nextDateTime;
                $propsMatch['PREV_MATCH'] = $resId;
                $resId = createMatchItem($propsMatch, $countChainMatches-$countItems+1);
                $resIds[] = $resId;

            } while($countItems > 0 && $resId+0 > 0);

        }
    }
    return $resIds;
}

/**
 * @return string[]
 */
function getModesMatch()
{
  return [
      '4' => 'SQUAD',
      '2' => 'DUO'
  ];
}

function getStageTournament()
{
  return [
    '8' => '1/32',
    '7' => '1/16',
    '4' => '1/8',
    '3' => '1/4',
    '2' => 'Полуфинал',
    '1' => 'Финал',
  ];
}

$stageTournament = getStageTournament();

$modesMatch = getModesMatch();


if (check_bitrix_sessid() && (!empty($_REQUEST["submit"]))){
    $img = $_FILES['image'];
    $prizePlaces = $_POST['prize'];
    //dump($prizePlaces);
    $props = [];
    $props['NAME'] = strip_tags(trim($_POST['name']));
    $props['ANONS'] = strip_tags($_POST['anonsTournament']);
    $props['DESCRIPTION'] = strip_tags($_POST['descriptionTournament']);
    $props['PRIZE_FUND'] = strip_tags($_POST['prize_fund']);
    $props['QUALIFYING_STAGE'] = strip_tags($_POST['stageTournament']);
    $prizePlacesInsert = [];
    foreach ($prizePlaces as $n => $value) {
      if(!empty($value)) {
        $prizePlacesInsert['n'.$n] = [
          "VALUE" => ($n+1). " место ",
          "DESCRIPTION" => $value
        ];
      }
    }
    //dump($prizePlacesInsert,1);
    $props['PRIZE'] = $prizePlacesInsert;
    $props['TEXT_PRIZE'] = $_POST['text_prize'];
    $props['REGULATION'] = $_POST['regulation'];

    if($_POST["idTournament"] != "Укажите из списка"){
        $tournamentId = $_POST["idTournament"] + 0;
    } else {
        $tournamentId = createTournament($props, $img);
    }

    //$tournamentId = 876;
    // add winner next stage

    $chainMatrix = [
        8 => [
            'count' => count($_POST['dateTime'][8]), // колличество дат заполненных
            'chain' => $_POST['chain8'] // $_POST['chain4']
        ],
        7 => [
            'count' => count($_POST['dateTime'][7]), // колличество дат заполненных
            'chain' => $_POST['chain7'] // $_POST['chain4']
        ],
        4 => [
            'count' => count($_POST['dateTime'][4]), // колличество дат заполненных
            'chain' => $_POST['chain4'] // $_POST['chain4']
        ],
        3 => [
            'count' => count($_POST['dateTime'][3]), //  колличество дат заполненных
            'chain' => $_POST['chain3'] // $_POST['chain3']
        ],
        2 => [
            'count' => count($_POST['dateTime'][2]), //  колличество дат заполненных
            'chain' => $_POST['chain2'] // $_POST['chain2']
        ],
        1 => [
            'count' => count($_POST['dateTime'][1]), //  колличество дат заполненных
            'chain' => $_POST['chain1'] // $_POST['chain1']
        ],
    ];
    //dump($chainMatrix, 1);
    $countCreatedMatches = [];


    if ($tournamentId) {
        $stages = getStagesTournament();

        $propsMatch = [];
        $propsMatch['TOURNAMENT'] = $tournamentId;
        $propsMatch['DATE_START'] = $_POST['date_time_match'];
        $propsMatch['URL_STREAM'] = false;
        $propsMatch['PUBG_LOBBY_ID'] = false;
        $propsMatch['COUTN_TEAMS'] = $_POST['modeMatch']+0;
        $propsMatch['PREV_MATCH'] =  false;
        //$propsMatch['STAGE_TOURNAMENT'] = 4; // id свойства
        $propsMatch['TYPE_MATCH'] = 5; // id свойства
        //dump($chainMatrix);
        // блок проверки
        if(isset($_POST['chain8']) && $_POST['chain8']+0) {
            $chainMatrix[8]['chain'] = intval(abs($_POST['chain8']+0));

        }
        if(isset($_POST['chain7']) && $_POST['chain7']+0) {
            $chainMatrix[7]['chain'] = intval(abs($_POST['chain7']+0));

        }
        if(isset($_POST['chain4']) && $_POST['chain4']+0) {
            $chainMatrix[4]['chain'] = intval(abs($_POST['chain4']+0));

        }
        if(isset($_POST['chain3']) && $_POST['chain3']+0) {
            $chainMatrix[3]['chain'] = intval(abs($_POST['chain3']+0));

        }
        if(isset($_POST['chain2']) && $_POST['chain2']+0) {
            $chainMatrix[2]['chain'] = intval(abs($_POST['chain2']+0));

        }
        if(isset($_POST['chain1']) && $_POST['chain1']+0) {
            $chainMatrix[1]['chain'] = intval(abs($_POST['chain1']+0));

        }

        foreach ($chainMatrix as $stage => $v) {
            $datesTime = [];
            //dump($_POST['dateTime'][$stage]);
            foreach ($_POST['dateTime'][$stage] as $position => $postDate) {
                $timestamp = strtotime($postDate);
                if ($timestamp) {
                    $d = date('d.m.Y H:i', $timestamp);
                    $datesTime[] = $d;
                }
            }

            if(count($datesTime)> 0) {
                $chainMatrix[$stage]['dates'] = $datesTime;
                $chainMatrix[$stage]['count'] = count($datesTime);
            } else {
                unset($chainMatrix[$stage]);
            }

        }

        //dump($chainMatrix);

        $timeStep = 90;// min

        $startDateTime = $_POST['date_time_match'];
        foreach ($chainMatrix as $stage => $params) {
            $propsMatch['STAGE_TOURNAMENT'] = $stage;
            $propsMatch['KEY_STAGE_PASS'] = $stage == strip_tags($_POST['stageTournament']) ? "" : $tournamentId . "." . $stages[array_search($stage, $stages)+1];
            $countChainMatches = $params['chain'];
            for($i = 0; $i < $params['count']; $i++) {


                $startDateTime = $params['dates'][$i];
                $propsMatch['DATE_START'] = $startDateTime;

              //dump($stage . ' :' . $i . ' ----' . $startDateTime );

                $resId = createMatchChain($propsMatch, $countChainMatches, $startDateTime);
                //dump($resId);
                //dump($countCreatedMatches);
                $success[] = 'Турнирные матчи успешно созданы';
            }

        }



    } else {
      $errors[] = 'Не удалось создать турнир';
    }

}
$tournaments = getTournaments();
?>
<div class="container my-3">
    <?php
    if(!empty($success)) { ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <h4 class="alert-heading">Well done!</h4>
          <?php foreach ($success as $mes) { ?>
            <p><strong><?php echo $mes; ?></strong></p>
          <?php } ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <? } ?>
    <?php if (!empty($errors)) { ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h4 class="alert-heading">Error!</h4>
          <?php foreach ($errors as $error) { ?>
            <p><strong><?php echo $error; ?></strong></p>
          <?php } ?>
        <hr>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php } ?>
  <h1>Создание сетки турнирных матчей</h1>
  <form name="formAddTournament" action="#" method="POST" enctype="multipart/form-data">
      <?=bitrix_sessid_post()?>
      <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                  <label for="nameTournament">Название турнира</label>
                  <input type="text" name="name" class="form-control" maxlength="255" id="nameTournament" value="">
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                  <label for="exampleFormControlSelect1">Выберите турнир</label>
                  <select class="form-control" id="exampleFormControlSelect1" name="idTournament">
                      <option>Укажите из списка</option>
                      <?php foreach($tournaments as $tournament){ ?>

                          <option value="<?php echo $tournament["ID"]; ?>"><?php echo $tournament["NAME"]; ?></option>

                      <?php } ?>

                  </select>
              </div>
          </div>
      </div>




<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="imgTournament">Картинка турнира</label>
      <input type="file" class="form-control-file" size="30" name="image" id="imgTournament" value="">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label for="modeMatch">Выберите Режим</label>
      <select class="form-control" id="modeMatch" name="modeMatch">
          <?php foreach($modesMatch as $mode => $value){ ?>

            <option value="<?php echo $mode; ?>"><?php echo $value; ?></option>

          <?php } ?>

      </select>
    </div>
  </div>
</div>
    <script>
      document.getElementById('stageTournamentQ').addEventListener('change', function () {
        /*'8' => '1/32',
          '7' => '1/16',
          '4' => '1/8',
          '3' => '1/4',
          '2' => 'Полуфинал',
          '1' => 'Финал',*/
        console.log(this.value)
        if(this.value == 7) {
          document.querySelector('.block-one-32').style.display = 'none';
          document.querySelector('.block-one-16').style.display = 'block';
          document.querySelector('.block-one-8').style.display = 'block';
          document.querySelector('.block-one-4').style.display = 'block';
          document.querySelector('.block-one-2').style.display = 'block';
        } else if(this.value == 8) {
          document.querySelector('.block-one-32').style.display = 'block';
          document.querySelector('.block-one-16').style.display = 'block';
          document.querySelector('.block-one-8').style.display = 'block';
          document.querySelector('.block-one-4').style.display = 'block';
          document.querySelector('.block-one-2').style.display = 'block';
          document.querySelector('.block-final').style.display = 'block';
        } else if(this.value == 4) {
          document.querySelector('.block-one-32').style.display = 'none';
          document.querySelector('.block-one-16').style.display = 'none';
          document.querySelector('.block-one-8').style.display = 'block';
          document.querySelector('.block-one-4').style.display = 'block';
          document.querySelector('.block-one-2').style.display = 'block';
        } else if(this.value == 3) {
          document.querySelector('.block-one-32').style.display = 'none';
          document.querySelector('.block-one-16').style.display = 'none';
          document.querySelector('.block-one-8').style.display = 'none';
          document.querySelector('.block-one-4').style.display = 'block';
          document.querySelector('.block-one-2').style.display = 'block';
        } else if(this.value == 2) {
          document.querySelector('.block-one-32').style.display = 'none';
          document.querySelector('.block-one-16').style.display = 'none';
          document.querySelector('.block-one-8').style.display = 'none';
          document.querySelector('.block-one-4').style.display = 'none';
        }

        //
      });
    </script>
    <div class="form-group">
      <label for="stageTournamentQ">Выберите отборочный этап</label>
      <select class="form-control" id="stageTournamentQ" name="stageTournament">
        <?php foreach($stageTournament as $stage => $value){ ?>

          <option value="<?php echo $stage; ?>"><?php echo $value; ?></option>

        <?php } ?>

      </select>
    </div>
    <div class="form-group">
      <label for="anonsTournament">Анонс турнира</label>
      <textarea name="anonsTournament" class="form-control" id="anonsTournament" rows="3"></textarea>
    </div>
    <div class="form-group">
      <label for="descrTournament">Описание турнира</label>
      <textarea name="descriptionTournament" class="form-control" id="descrTournament" rows="7"></textarea>
    </div>
    <div class="form-group">
      <label>Призовой фонд, укажите только число</label>
        <input type="text" name="prize_fund" class="form-control" maxlength="255" placeholder="3000">
    </div>
    <h2>Призовые места, укажите только число</h2>
    <button class="btn btn-success add_form_field">Добавить призовое место &nbsp;
      <span style="font-size:16px; font-weight:bold;">+ </span>
    </button>
    <div class="row mb-5 prizePlaces">
      <div class="col-md-2">
        <div class="form-group">
          <label>1 место</label>
          <input type="text" name="prize[]" class="form-control" placeholder="500">
        </div>
      </div>

    </div>
    <div class="form-group">
      <label for="text_prize">Текст под призами</label>
      <textarea name="text_prize" class="form-control" id="text_prize" rows="3" placeholder="Победители каждого игрового дня финала получают приз 200 евро."></textarea>
    </div>
    <div class="form-group">
      <label for="regulationTournament">Регламент для турнира</label>
      <textarea name="regulation" class="form-control" id="regulationTournament" rows="7" placeholder="Текст регламента необходимо вставить в формате html, можно заполнить из админки"></textarea>
    </div>


    <div class="p-3 mb-2 bg-warning text-white block-one-32">
      <h2 class="text-center">1/32 </h2>
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label>Укажите количество матчей в игровой сессии</label>
            <input type="text" name="chain8" class="form-control"  value="3">
          </div>
        </div>
        <div class="col-6">
          <div class="wrap-input">
              <?php
              $oneThirtySecond = 216;
              for($i = 1; $i <= $oneThirtySecond; $i++) { ?>
                <div class="form-group" style="position: relative;">
                  <label><?=$i;?> игра укажите дату</label>
                  <input type="text" name="dateTime[8][<?=$i;?>]" class="form-control dashboard-time" value="">
                </div>
              <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <div class="p-3 mb-2 bg-secondary text-white block-one-16">
      <h2 class="text-center">1/16 </h2>
      <div class="row align-items-center">
        <div class="col-6">
          <div class="form-group">
            <label>Укажите количество матчей в игровой сессии</label>
            <input type="text" name="chain7" class="form-control"  value="3">
          </div>
        </div>
        <div class="col-6">
          <div class="wrap-input">
              <?php
              $oneSixTen = 42;
              for($i = 1; $i <= $oneSixTen; $i++) { ?>
                <div class="form-group" style="position: relative;">
                  <label><?=$i;?> игра укажите дату</label>
                  <input type="text" name="dateTime[7][<?=$i;?>]" class="form-control dashboard-time" value="">
                </div>
              <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <div class="p-3 mb-2 bg-dark text-white block-one-8">
      <h2 class="text-center">1/8 </h2>
      <div class="row align-items-center">
        <div class="col-6">
          <div class="form-group">
            <label for="qtyMatches">Укажите количество матчей в игровой сессии</label>
            <input type="text" name="chain4" class="form-control" id="qtyMatches" value="2">
          </div>
        </div>
        <div class="col-6">
          <div class="wrap-input">
            <?php
            $oneEighth = 14;
            for($i = 1; $i <= $oneEighth; $i++) { ?>
              <div class="form-group" style="position: relative;">
                <label><?=$i;?> игра укажите дату</label>
                <input type="text" name="dateTime[4][<?=$i;?>]" class="form-control dashboard-time" value="">
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="p-3 mb-2 bg-secondary text-white block-one-4">
      <h2 class="text-center">1/4 </h2>
      <div class="row align-items-center">
        <div class="col-6">
          <div class="form-group">
            <label>Укажите количество матчей в игровой сессии</label>
            <input type="text" name="chain3" class="form-control" value="1">
          </div>
        </div>
        <div class="col-6">
          <div class="wrap-input">
              <?php
              $oneQuarter = 5;
              for($i = 1; $i <= $oneQuarter; $i++) { ?>
                <div class="form-group" style="position: relative;">
                  <label><?=$i;?> игра укажите дату</label>
                  <input type="text" name="dateTime[3][<?=$i;?>]" class="form-control dashboard-time" value="">
                </div>
              <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="p-3 mb-2 bg-info text-white block-one-2">
      <h2 class="text-center">1/2 </h2>
      <div class="row align-items-center">
        <div class="col-6">
          <div class="form-group">
            <label>Укажите количество матчей в игровой сессии</label>
            <input type="text" name="chain2" class="form-control" value="1">
          </div>
        </div>
        <div class="col-6">
          <div class="wrap-input">
              <?php
              $oneHalf = 2;
              for($i = 1; $i <=  $oneHalf; $i++) { ?>
                <div class="form-group" style="position: relative;">
                  <label><?=$i;?> игра укажите дату</label>
                  <input type="text" name="dateTime[2][<?=$i;?>]" class="form-control dashboard-time" value="">
                </div>
              <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="p-3 mb-2 bg-success text-white block-final">
      <h2 class="text-center">Финал</h2>
      <div class="row align-items-center">
        <div class="col-6">
          <div class="form-group">
            <label>Укажите количество матчей в игровой сессии</label>
            <input type="text" name="chain1" class="form-control"  value="5">
          </div>
        </div>
        <div class="col-6">
          <div class="wrap-input">
              <?php
              $final = 2;
              for($i = 1; $i <=  $final; $i++) { ?>
                <div class="form-group" style="position: relative;">
                  <label><?=$i;?> игра укажите дату</label>
                  <input type="text" name="dateTime[1][<?=$i;?>]" class="form-control dashboard-time" value="">
                </div>
              <?php } ?>
          </div>
        </div>
      </div>
    </div>




    <button type="submit" name="submit" class="btn btn-success" value="submit">Создать турнир</button>
  </form>


</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>