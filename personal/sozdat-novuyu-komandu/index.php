<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Создать новую команду");
//Подключаем модуль инфоблоков
CModule::IncludeModule('iblock');
$IBLOCK_ID = 1; //ИД инфоблока с которым работаем
?>
<?php
$userID = CUser::GetID();
//var_dump($userID);
//$rsUser = CUser::GetByID(1);
//$arUser = $rsUser->Fetch();
//$teamID = $arUser['UF_ID_TEAM'];
//echo '<pre>';
//var_dump( $arUser);
//echo '</pre>';
?>
    <div class="container">
        <form name="iblock_add" action="<?=SITE_DIR?>personal/sozdat-novuyu-komandu/add-form-result.php" method="POST"
              enctype="multipart/form-data">

            <div class="form-group">
                <label for="nameTeam">Название команды</label>
                <input type="text" name="name" class="form-control" maxlength="255" id="nameTeam" value="">
            </div>
            <div class="form-group">
                <label for="tagTeam">Тег команды</label>
                <input type="text" name="tag_team" class="form-control" maxlength="255" id="tagTeam" value="">
            </div>
            <div class="form-group">
                <label for="logoTeam">Логотип команды</label>
                <input type="file" class="form-control-file" size="30" name="image" id="logoTeam" value="">
            </div>
            <div class="form-group">
                <label for="descrTeam">Описание команды</label>
                <textarea name="description_team" class="form-control" id="descrTeam" rows="3"></textarea>
            </div>


            <input type="hidden" size="30" name="user_id" value="<?php echo $userID; ?>"><br>


            <!--Выпадающий список не множественный
            <select name='selector'>
              <option value='#'>Выберите из списка</option>
              <option value="60">1</option>
              <option value="61">2</option>
            </select>-->

            <?php /*Выбор раздела- множественный
    <select name='section_id[]' multiple>
      <option value='#'>Выберите из списка или начните вводить название</option>
      <?
      $arFilter = array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y', "DEPTH_LEVEL" => "2");
      $arSelect = array('ID', 'NAME');
      $rsSection = CIBlockSection::GetTreeList($arFilter, $arSelect);
      while ($arSection = $rsSection->Fetch()) {
        ?>
        <option value="<?= $arSection['ID']; ?>"><?= $arSection['NAME']; ?></option>
      <?}?>
    </select>

    Чекбокс
    <label><input type="checkbox" name="chek_box" value="47"> Рассрочка </label>

    Произвольный файл
    <input type="file" size="30" name="file_pol" value="">*/ ?>

            <!--Привязка к подразделам конкретного раздела другого мнфоблока  чекбоксы-->
            <? /*
    $rsParentSection = CIBlockSection::GetByID(5741);
    if ($arParentSection = $rsParentSection->GetNext()) {
      $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'], '>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'], '<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'], '>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']);
      $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter);
      while ($arSect = $rsSect->GetNext()) {
        ?>
        <label><input name='service_dop[]' type="checkbox" value="<?= $arSect['ID']; ?>"> <?= $arSect['NAME']; ?></label>
      <?}}*/ ?>

            <button type="submit" class="btn btn-primary">Отправить</button>

        </form>
    </div>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>