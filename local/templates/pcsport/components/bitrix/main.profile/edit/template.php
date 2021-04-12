<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if($arResult["SHOW_SMS_FIELD"] == true)
{
	CJSCore::Init('phone_auth');
}
?>

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
            <?/*
            $APPLICATION->IncludeComponent(
                'bitrix:main.calendar',
                '',
                array(
                    'SHOW_INPUT' => 'Y',
                    'FORM_NAME' => 'form1',
                    'INPUT_NAME' => 'PERSONAL_BIRTHDAY',
                    'INPUT_VALUE' => $arResult["arUser"]["PERSONAL_BIRTHDAY"],
                    'SHOW_TIME' => 'N'
                ),
                null,
                array('HIDE_ICONS' => 'Y')
            );

            //=CalendarDate("PERSONAL_BIRTHDAY", $arResult["arUser"]["PERSONAL_BIRTHDAY"], "form1", "15")
            */?>
        <?php } ?>


        <p>
          <input class="btn btn-success mr-2" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>">&nbsp;&nbsp;<input type="reset" class="btn btn-secondary" value="<?=GetMessage('MAIN_RESET');?>"></p>
      </form>

    <?endif?>

</div>
