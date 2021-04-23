<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if ($arResult["SHOW_SMS_FIELD"] == true) {
    CJSCore::Init('phone_auth');
}
?>
<?php /*
<div class="container">

    <?ShowError($arResult["strProfileError"]);?>
    <?
    if ($arResult['DATA_SAVED'] == 'Y')
        ShowNote(GetMessage('PROFILE_DATA_SAVED'));
    ?>

    <?if($arResult["SHOW_SMS_FIELD"] == true):?>

      <form method="post" action="<?=$arResult["FORM_TARGET"]?>">
          <?=$arResult["BX_SESSION_CHECK"]?>
        <input type="hidden" name="lang" value="<?=LANG?>" />
        <input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
        <input type="hidden" name="SIGNED_DATA" value="<?=htmlspecialcharsbx($arResult["SIGNED_DATA"])?>" />
        <table class="profile-table data-table">
          <tbody>
          <tr>
            <td><?echo GetMessage("main_profile_code")?><span class="starrequired">*</span></td>
            <td><input size="30" type="text" name="SMS_CODE" value="<?=htmlspecialcharsbx($arResult["SMS_CODE"])?>" autocomplete="off" /></td>
          </tr>
          </tbody>
        </table>

        <p><input type="submit" name="code_submit_button" value="<?echo GetMessage("main_profile_send")?>" /></p>

      </form>

      <script>
          new BX.PhoneAuth({
              containerId: 'bx_profile_resend',
              errorContainerId: 'bx_profile_error',
              interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
              data:
                  <?=CUtil::PhpToJSObject([
                      'signedData' => $arResult["SIGNED_DATA"],
                  ])?>,
              onError:
                  function(response)
                  {
                      var errorDiv = BX('bx_profile_error');
                      var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
                      errorNode.innerHTML = '';
                      for(var i = 0; i < response.errors.length; i++)
                      {
                          errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
                      }
                      errorDiv.style.display = '';
                  }
          });
      </script>

      <div id="bx_profile_error" style="display:none"><?ShowError("error")?></div>

      <div id="bx_profile_resend"></div>

    <?else:?>

      <script type="text/javascript">
          <!--
          var opened_sections = [<?
              $arResult["opened"] = $_COOKIE[$arResult["COOKIE_PREFIX"]."_user_profile_open"];
              $arResult["opened"] = preg_replace("/[^a-z0-9_,]/i", "", $arResult["opened"]);
              if ($arResult["opened"] <> '')
              {
                  echo "'".implode("', '", explode(",", $arResult["opened"]))."'";
              }
              else
              {
                  $arResult["opened"] = "reg";
                  echo "'reg'";
              }
              ?>];
          //-->

          var cookie_prefix = '<?=$arResult["COOKIE_PREFIX"]?>';
      </script>

      <form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
          <?=$arResult["BX_SESSION_CHECK"]?>
        <input type="hidden" name="lang" value="<?=LANG?>" />
        <input type="hidden" name="ID" value=<?=$arResult["ID"]?> />


        <?php if($arResult["ID"]>0) { ?>
          <p>Аватарка:</p>
            <?=$arResult["arUser"]["PERSONAL_PHOTO_INPUT"]?>
            <?
            if ($arResult["arUser"]["PERSONAL_PHOTO"] <> '')
            {
                ?>
              <br />
                <?=$arResult["arUser"]["PERSONAL_PHOTO_HTML"]?>
                <?
            }
            ?>
          <br>
          <br>
          <p>Дата регитсрации: <?=$arResult["arUser"]["DATE_REGISTER"]; ?></p>
          <p>Премаккаунт до: <?= !empty($arResult["arUser"]["UF_DATE_PREM_EXP"]) ? $arResult["arUser"]["UF_DATE_PREM_EXP"] : date('d.m.Y', time() - (3600*24)); ?></p>
          <div class="form-group">
            <label>Мое настроение</label>
            <input type="text" class="form-control" name="TITLE" value="<?=$arResult["arUser"]["TITLE"]?>">
          </div>
          <!--<div class="form-group">
            <label>Имя</label>
            <input type="text" class="form-control" maxlength="50" name="NAME" value="<?=$arResult["arUser"]["NAME"]?>">
          </div>
          <div class="form-group">
            <label>Фамилия</label>
            <input type="text" class="form-control" maxlength="50" name="LAST_NAME" value="<?=$arResult["arUser"]["LAST_NAME"]?>">
          </div>
          <div class="form-group">
            <label>Отчество</label>
            <input type="text" class="form-control" maxlength="50" name="SECOND_NAME" value="<?=$arResult["arUser"]["SECOND_NAME"]?>">
          </div>-->
          <div class="form-group">
            <label>PUBG ID</label>
            <input type="text" class="form-control" maxlength="50" name="UF_PUBG_ID" value="<?=$arResult["arUser"]["UF_PUBG_ID"]?>">
          </div>
          <div class="form-group">
            <label>Nic (мин. 3 символа) *</label>
            <input type="text" class="form-control" maxlength="50" name="LOGIN" value="<?=$arResult["arUser"]["LOGIN"]?>">
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" class="form-control" name="EMAIL" value="<?=$arResult["arUser"]["EMAIL"]?>" readonly>
          </div>
            <?if($arResult['CAN_EDIT_PASSWORD']) { ?>
            <p>Пароль должен быть не менее 6 символов длиной.</p>
            <div class="form-group">
              <label>New Password</label>
              <input type="password" class="form-control" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off">
            </div>
            <div class="form-group">
              <label>Confirm Password</label>
              <input type="password" class="form-control" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off">
            </div>
            <?php } ?>
          <div class="form-group">
            <label>Ссылка на vk</label>
            <input type="text" class="form-control" name="UF_LINK_VK" value="<?=$arResult["arUser"]["UF_LINK_VK"]?>">
          </div>
          <div class="form-group">
            <label>Ссылка на fb</label>
            <input type="text" class="form-control" name="UF_LINK_FB" value="<?=$arResult["arUser"]["UF_LINK_FB"]?>">
          </div>
          <div class="form-group">
            <label>Ссылка на Discord</label>
            <input type="text" class="form-control" name="UF_LINK_DISCORD" value="<?=$arResult["arUser"]["UF_LINK_DISCORD"]?>">
          </div>
            <?
            //$APPLICATION->IncludeComponent(
                //'bitrix:main.calendar',
               //'',
                //array(
                    //'SHOW_INPUT' => 'Y',
                    //'FORM_NAME' => 'form1',
                    //'INPUT_NAME' => 'PERSONAL_BIRTHDAY',
                    //'INPUT_VALUE' => $arResult["arUser"]["PERSONAL_BIRTHDAY"],
                    //'SHOW_TIME' => 'N'
               // ),
               // null,
                //array('HIDE_ICONS' => 'Y')
           // );

            //=CalendarDate("PERSONAL_BIRTHDAY", $arResult["arUser"]["PERSONAL_BIRTHDAY"], "form1", "15")
            //?>
        <?php } ?>


        <p>
          <input class="btn btn-success mr-2" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>">&nbsp;&nbsp;<input type="reset" class="btn btn-secondary" value="<?=GetMessage('MAIN_RESET');?>"></p>
      </form>

    <?endif?>

</div>
<?php */ ?>
<?php if (!empty($arResult["strProfileError"])) {
    createSession('save-profile_error', $arResult["strProfileError"]);
    LocalRedirect("/personal/edit/");
}
?>
<?php
if ($arResult['DATA_SAVED'] == 'Y') {
    createSession('save-profile_success', GetMessage('PROFILE_DATA_SAVED'));
    LocalRedirect(SITE_DIR."personal/edit/");
} ?>
<?php
if (isset($_SESSION['save-profile_success'])) { ?>
    <div class="alert-container">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['save-profile_success']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php
    unset($_SESSION['save-profile_success']);
} else if (isset($_SESSION['save-profile_error'])) { ?>
    <div class="alert-container">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['save-profile_error']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php }
unset($_SESSION['save-profile_error']);
?>
<section class="profile">
    <div class="container">
        <div class="layout__content-heading-with-btn-back">
            <a href="<?= SITE_DIR ?>personal/" class="btn-italic-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 11.62">
                    <path d="M955.22,534.89a1,1,0,0,1,0,1.33l-3,3.27h18.2a.94.94,0,0,1,0,1.88h-18.2l3,3.27a1,1,0,0,1,0,1.32.81.81,0,0,1-1.21,0l-4.49-4.88h0a1,1,0,0,1,0-1.33h0l4.49-4.87A.81.81,0,0,1,955.22,534.89Z"
                          transform="translate(-949.26 -534.62)"/>
                </svg>
                Назад
            </a>
            <h1 class="text-center">Редактирование профиля</h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">

                <form method="post" name="form1" action="<?= $arResult["FORM_TARGET"] ?>" enctype="multipart/form-data">
                    <?= $arResult["BX_SESSION_CHECK"] ?>
                    <input type="hidden" name="lang" value="<?= LANG ?>">
                    <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>>
                    <?php if ($arResult["ID"] > 0) { ?>
                    <div class="profile__edit-avatar">
                        <input type="file" class="form-field__input-file inputFileAvatar" name="PERSONAL_PHOTO"
                               id="avatar">
                        <label for="avatar" class="form-field__upload-avatar">
                            <div class="profile__avatar-bg">
                                <div title="Поменять аватар"
                                     class="profile__avatar profile__avatar-edit fileAvatarUploaded"
                                    <?php if (!empty($arResult["arUser"]["PERSONAL_PHOTO"])) { ?>
                                        style="background-image: url(<?php echo CFile::GetPath($arResult["arUser"]["PERSONAL_PHOTO"]); ?>)"
                                    <?php } ?>>
                                    <div class="profile__avatar-edit-icon"></div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="profile-info">
                        <div class="profile-info__edit">
                            <div class="form-field">
                                <label for="edit-nic" class="form-field__label">NickName <span>*</span></label>
                                <input type="text" class="form-field__input" name="LOGIN"
                                       value="<?= $arResult["arUser"]["LOGIN"] ?>" autocomplete="off" id="edit-nic"
                                       placeholder="Введите свой NicName">

                            </div>
                            <div class="form-field">
                                <label for="edit-pubgid" class="form-field__label">PUBG ID <span>*</span></label>
                                <input type="text" class="form-field__input" name="UF_PUBG_ID"
                                       value="<?= htmlspecialchars($arResult["arUser"]["UF_PUBG_ID"]); ?>"
                                       autocomplete="off" id="edit-pubgid" placeholder="Введите свой PUBG ID">
                            </div>
                            <div class="form-field">
                                <label for="edit-subscription" class="form-field__label">Подписка</label>
                                <?php $resultPrem = isPrem($arResult["arUser"]["UF_DATE_PREM_EXP"]);
                                if ($resultPrem <= 0) { ?>
                                    <div class="form-field__with-btn">
                                        <div class="form-field__input-wrap">
                                            <i class="form-field__icon form-field__icon_base"></i>
                                            <input type="text" class="form-field__input" name="authLogin"
                                                   value="Базовая" autocomplete="off" id="edit-subscription">
                                        </div>
                                        <a href="/subscription-plans/" class="btn-italic" target="_blank">Поменять</a>
                                    </div>
                                <?php } else { ?>
                                    <div class="form-field__input-wrap">
                                        <i class="form-field__icon form-field__icon_prem"></i>
                                        <input type="text" class="form-field__input" name="authLogin"
                                               value="Премиум <?php echo num_decline($resultPrem, 'Остался, Осталось, Осталось', false); ?> <?php echo num_decline($resultPrem, 'день, дня, дней'); ?>"
                                               autocomplete="off" id="edit-subscription">
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="form-field">
                                <label for="my-mood" class="form-field__label">Мое настроение</label>
                                <input type="text" class="form-field__input" name="TITLE"
                                       value="<?php echo htmlspecialchars($arResult["arUser"]["TITLE"]) ?>"
                                       autocomplete="off" id="my-mood" placeholder="Какое у Вас настроение">
                            </div>
                            <div class="form-field">
                                <label for="edit-email" class="form-field__label">Email <span>*</span></label>
                                <input type="text" class="form-field__input" name="EMAIL"
                                       value="<?= $arResult["arUser"]["EMAIL"] ?>" autocomplete="off" id="edit-email"
                                       placeholder="Введите свой email" readonly>
                            </div>
                            <div class="form-field">
                                <label for="edit-telegramNic" class="form-field__label">Telegram Nic</label>
                                <input type="text" class="form-field__input" name="UF_TELEGRAM_NIC"
                                       value="<?= htmlspecialchars($arResult["arUser"]["UF_TELEGRAM_NIC"]) ?>"
                                       autocomplete="off" id="edit-telegramNic" placeholder="Введите свой email">
                            </div>
                            <? if ($arResult['CAN_EDIT_PASSWORD']) { ?>
                                <div class="form-field">
                                    <label for="edit-new-pass" class="form-field__label">Новый пароль</label>
                                    <input type="password" class="form-field__input form-field__input_pass"
                                           name="NEW_PASSWORD" value="" autocomplete="off" id="edit-new-pass"
                                           placeholder="Введите новый пароль">
                                    <span class="form-field__eyes"></span>
                                    <span class="form-field__helper">Пароль должен быть не менее 6 символов длиной.</span>
                                </div>
                                <div class="form-field">
                                    <label for="edit-confirm-new-pass" class="form-field__label">Повторить
                                        пароль</label>
                                    <input type="password" class="form-field__input" name="NEW_PASSWORD_CONFIRM"
                                           value="" autocomplete="off" id="edit-confirm-new-pass"
                                           placeholder="Повторить пароль">
                                </div>
                            <?php } ?>
                            <?php } ?>
                            <div class="form-field__button">
                                <button type="submit" name="save" class="btn"
                                        value="<?= (($arResult["ID"] > 0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD")) ?>">
                                    Сохранить
                                </button>
                                <button type="reset" value="<?= GetMessage('MAIN_RESET'); ?>" class="btn">Сбросить
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
