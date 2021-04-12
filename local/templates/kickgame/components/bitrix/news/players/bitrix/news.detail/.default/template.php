<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];

if (isset($_POST['join_submit'])) {

    $user = new CUser;
    $fields = array(
        //"NAME"              => "Сергей",
        "UF_REQUEST_ID_TEAM" => $_POST['team_id'],
    );
    if ($user->Update($userID, $fields)) {
        echo 'вы успешно отправили запрос в команду';
    } else {
        echo 'Error: ' . $user->LAST_ERROR;
    }
}

?>

<section class="team py-8">
  <div class="container">
    <a href="javascript:history.back()" class="btn-italic-icon">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 11.62">
        <path d="M955.22,534.89a1,1,0,0,1,0,1.33l-3,3.27h18.2a.94.94,0,0,1,0,1.88h-18.2l3,3.27a1,1,0,0,1,0,1.32.81.81,0,0,1-1.21,0l-4.49-4.88h0a1,1,0,0,1,0-1.33h0l4.49-4.87A.81.81,0,0,1,955.22,534.89Z" transform="translate(-949.26 -534.62)"/>
      </svg> Назад
    </a>
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-12">
        <div class="team__logo-bg">
          <div class="team__logo" style="background-image: url(<?=$arResult["DETAIL_PICTURE"]["SRC"]?>">
            <div class="team__logo-rating-bg">
              <div class="team__logo-rating">3.00</div>
            </div>
          </div>
        </div>
        <div class="team-info">
          <h2 class="team-info__name"><?php echo $arResult['PROPERTIES']["NAME_TEAM"]['VALUE']; ?> [<?php echo $arResult['PROPERTIES']["TAG_TEAM"]['VALUE']; ?>]</h2>
          <div class="team-info__description">
              <?php echo $arResult['PROPERTIES']["DESCRIPTION_TEAM"]['VALUE']["TEXT"]; ?>
          </div>
          <div class="team-info__btn-edit">
            <form action="" method="post">
              <input type="hidden" name="team_id" value="<?php echo $arResult['ID']?>">
            <button class="btn" name="join_submit">Отправить запрос</button>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>