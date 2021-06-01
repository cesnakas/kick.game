<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if($arResult["SHOW_SMS_FIELD"] == true)
{
	CJSCore::Init('phone_auth');
}
?>
<?php if($USER->IsAuthorized()) { ?>

  <p class="text-center"><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>
  <p class="text-center"><?= GetMessage('MAIN_REGISTER_LINK_BEFORE') ?> <a  href="<?=SITE_DIR?>personal/"><?= GetMessage("MAIN_REGISTER_LINK") ?></a>.</p>

<?php } else { ?>
  <div class="layout__content layout__content_full">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-12 col-lg-6">
          <div class="logo-tagline">
            <a href="<?=SITE_DIR?>"><img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/logo-tagline.svg" alt="kickgame esports"></a>
          </div>
        </div>
        <div class="col-md-12 col-lg-6">
          <div class="form-authentication">
            <h2 class="form-authentication__heading"><?= GetMessage("AUTH_REGISTER") ?></h2>
              <?php if (count($arResult["ERRORS"]) > 0): /* сообщения об ошибках при заполнении формы */ ?>
                  <?php
                  foreach ($arResult["ERRORS"] as $key => $error) {
                      if (intval($key) == 0 && $key !== 0) {
                          $arResult["ERRORS"][$key] = str_replace(
                              "#FIELD_NAME#",
                              '«'.GetMessage('MAIN_REGISTER_'.$key).'»',
                              $error
                          );
                      }
                  }
                  ShowError(implode("<br />", $arResult["ERRORS"]));
                  ?>
              <?php elseif ($arResult["USE_EMAIL_CONFIRMATION"] === "Y"): ?>
                <p><?= GetMessage('MAIN_REGISTER_EMAIL_HELP'); /* будет отправлено письмо для подтверждения */ ?></p>
              <?php endif; ?>
            <!--<div class="form-authentication__login-social-network_sign-in">
              <div class="form-authentication__login-social-network-heading">Регистрация с помощью Соц Сетей</div>
              <div class="social-networks justify-content-center">
                <a class="social-networks__item" href="#">
                </a>
                <a class="social-networks__item" href="#">
                </a>
                <a class="social-networks__item" href="#">
                </a>
                <a class="social-networks__item" href="#">
                </a>
              </div>
            </div>-->
            <form method="post" action="<?= POST_FORM_ACTION_URI; ?>" name="regform" enctype="multipart/form-data">
                <?php if ($arResult["BACKURL"] <> ''): ?>
                  <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"]; ?>" />
                <?php endif; ?>
                    <div class="form-field">
                      <label for="auth-login" class="form-field__label"><?= GetMessage('MAIN_REGISTER_LOGIN') ?></label><div class="form-field__question">? <span class="tooltiptext"><?=GetMessage('MAIN_INPUT_TOOLTIP')?></span></div>
                      <input type="text" class="form-field__input" name="REGISTER[LOGIN]" value="<?= $arResult["VALUES"]['LOGIN'] ?>" autocomplete="off" id="auth-login" placeholder="<?= GetMessage('MAIN_REGISTER_LOGIN_PLACEHOLDER') ?>">
                      <span class="form-field__helper" style="margin-bottom: -15px; display: block"><?= GetMessage('MAIN_REGISTER_LOGIN_HELPER') ?></span>
                    </div>
                    <div class="form-field">
                      <label for="auth-email" class="form-field__label"><?=Getmessage('MAIN_REGISTER_EMAIL')?></label>
                      <input type="email" class="form-field__input" name="REGISTER[EMAIL]" value="<?= $arResult["VALUES"]['EMAIL'] ?>" autocomplete="off" id="auth-email" placeholder="<?= GetMessage('MAIN_REGISTER_EMAIL_PLACEHOLDER') ?>">
                    </div>
                    <div> <label for="auth-phone" class="form-field__label"><?=GetMessage('MAIN_REGISTER_PERSONAL_PHONE')?></label></div>
                    <input type="tel" class="form-field__input" name="REGISTER[PERSONAL_PHONE]" value="<?= $arResult["VALUES"]['PERSONAL_PHONE'] ?>" autocomplete="off" id="auth-phone">
                    <div class="form-field">
                      <label for="auth-pass" class="form-field__label"><?= GetMessage('MAIN_REGISTER_PASSWORD') ?></label>
                      <input type="password" class="form-field__input form-field__input_pass" name="REGISTER[PASSWORD]" value="<?= $arResult["VALUES"]['PASSWORD'] ?>" autocomplete="off" id="auth-pass" placeholder="<?= GetMessage('MAIN_REGISTER_PASSWORD_PLACEHOLDER') ?>">
                      <span class="form-field__eyes"></span>
                    </div>
                    <div class="form-field">
                      <label for="auth-pass-repeat" class="form-field__label"><?= GetMessage('MAIN_REGISTER_CONFIRM_PASSWORD') ?></label>
                      <input type="password" class="form-field__input" name="REGISTER[CONFIRM_PASSWORD]" value="<?= $arResult["VALUES"]['CONFIRM_PASSWORD'] ?>" autocomplete="off" id="auth-pass-repeat" placeholder="<?= GetMessage('MAIN_REGISTER_CONFIRM_PASSWORD_PLACEHOLDER') ?>">
                    </div>
                  <!--<input type="hidden" name="UF_DATE_PREM_EXP" value="<?php echo date('d.m.Y');?>">-->
                <?
                if ($arResult["USE_CAPTCHA"] == "Y"):?>
                  <div class="form-field">
                      <div class="bx-captcha">
                        <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                        <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>"
                             width="180" height="40" alt="<?=GetMessage("AUTH_CAPTCHA_LOADING") ?>"/>
                      </div>
                      <a href="<?= $APPLICATION->GetCurPage() ?>?reload_captcha=yes"
                         class="reload-captcha"
                         title="<?=GetMessage('AUTH_RELOAD_CAPTCHA_TITLE')?>"><i class="uk-icon-refresh uk-link-muted"></i></a>
                      <input type="text" name="captcha_word" maxlength="50" value=""
                             class="form-field__input"
                             placeholder="<?= GetMessage("REGISTER_CAPTCHA_PROMT") ?>">

                  </div>
                <?endif;?>
              <div class="form-field d-flex justify-content-center">
                <button class="btn" id=reg-btn type="submit" name="register_submit_button" value="<?= GetMessage("AUTH_REGISTER") ?>"><?= GetMessage("AUTH_REGISTER") ?></button>
              </div>
              <div class="form-authentication__rules text-center"><?= GetMessage('MAIN_REGISTER_ACCEPT') ?> <a href="<?=SITE_DIR?>terms-conditions/" target="_blank"><?= GetMessage('MAIN_REGISTER_ACCEPT_SERVICE') ?></a> и
                <a href="<?=SITE_DIR?>privacy-policy/" target="_blank"><?= GetMessage('MAIN_REGISTER_ACCEPT_PRIVACY') ?></a>.</div>
              <div class="form-authentication__already text-center"><?= GetMessage('MAIN_REGISTER_ACCEPT_ACCOUNT') ?></div>
              <div class="form-field text-center">
                <a class="form-authentication__forgot-pass" href="<?=SITE_DIR?>personal/auth/"><?= GetMessage('MAIN_REGISTER_ACCOUNT_ENTER') ?></a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<?php /*<div class="container my-3">

<?if($USER->IsAuthorized()):?>

<p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

<?else:?>
<?
if (count($arResult["ERRORS"]) > 0):
	foreach ($arResult["ERRORS"] as $key => $error)
		if (intval($key) == 0 && $key !== 0) 
			$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

	ShowError(implode("<br />", $arResult["ERRORS"]));

elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):
?>
<p><?echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT")?></p>
<?endif?>

<?if($arResult["SHOW_SMS_FIELD"] == true):?>

<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform">
<?
if($arResult["BACKURL"] <> ''):
?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?
endif;
?>
<input type="hidden" name="SIGNED_DATA" value="<?=htmlspecialcharsbx($arResult["SIGNED_DATA"])?>" />
<table>
	<tbody>
		<tr>
			<td><?echo GetMessage("main_register_sms")?><span class="starrequired">*</span></td>
			<td><input size="30" type="text" name="SMS_CODE" value="<?=htmlspecialcharsbx($arResult["SMS_CODE"])?>" autocomplete="off" /></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td></td>
			<td><input type="submit" name="code_submit_button" value="<?echo GetMessage("main_register_sms_send")?>" /></td>
		</tr>
	</tfoot>
</table>
</form>

<script>
new BX.PhoneAuth({
	containerId: 'bx_register_resend',
	errorContainerId: 'bx_register_error',
	interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
	data:
		<?=CUtil::PhpToJSObject([
			'signedData' => $arResult["SIGNED_DATA"],
		])?>,
	onError:
		function(response)
		{
			var errorDiv = BX('bx_register_error');
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

<div id="bx_register_error" style="display:none"><?ShowError("error")?></div>

<div id="bx_register_resend"></div>

<?else:?>

<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
<?
if($arResult["BACKURL"] <> ''):
?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?
endif;
?>

<table>
	<thead>
		<tr>
			<td colspan="2"><b><?=GetMessage("AUTH_REGISTER")?></b></td>
		</tr>
	</thead>
	<tbody>
<?foreach ($arResult["SHOW_FIELDS"] as $FIELD):?>
	<?if($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true):?>
		<tr>
			<td><?echo GetMessage("main_profile_time_zones_auto")?><?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><span class="starrequired">*</span><?endif?></td>
			<td>
				<select name="REGISTER[AUTO_TIME_ZONE]" onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled=(this.value != 'N')">
					<option value=""><?echo GetMessage("main_profile_time_zones_auto_def")?></option>
					<option value="Y"<?=$arResult["VALUES"][$FIELD] == "Y" ? " selected=\"selected\"" : ""?>><?echo GetMessage("main_profile_time_zones_auto_yes")?></option>
					<option value="N"<?=$arResult["VALUES"][$FIELD] == "N" ? " selected=\"selected\"" : ""?>><?echo GetMessage("main_profile_time_zones_auto_no")?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("main_profile_time_zones_zones")?></td>
			<td>
				<select name="REGISTER[TIME_ZONE]"<?if(!isset($_REQUEST["REGISTER"]["TIME_ZONE"])) echo 'disabled="disabled"'?>>
		<?foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name):?>
					<option value="<?=htmlspecialcharsbx($tz)?>"<?=$arResult["VALUES"]["TIME_ZONE"] == $tz ? " selected=\"selected\"" : ""?>><?=htmlspecialcharsbx($tz_name)?></option>
		<?endforeach?>
				</select>
			</td>
		</tr>
	<?else:?>
		<tr>
			<td><?=GetMessage("REGISTER_FIELD_".$FIELD)?>:<?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><span class="starrequired">*</span><?endif?></td>
			<td><?
	switch ($FIELD)
	{
		case "PASSWORD":
			?><input size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="bx-auth-input" />
<?if($arResult["SECURE_AUTH"]):?>
				<span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
					<div class="bx-auth-secure-icon"></div>
				</span>
				<noscript>
				<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
					<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
				</span>
				</noscript>
<script type="text/javascript">
document.getElementById('bx_auth_secure').style.display = 'inline-block';
</script>
<?endif?>
<?
			break;
		case "CONFIRM_PASSWORD":
			?><input size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" /><?
			break;

		case "PERSONAL_GENDER":
			?><select name="REGISTER[<?=$FIELD?>]">
				<option value=""><?=GetMessage("USER_DONT_KNOW")?></option>
				<option value="M"<?=$arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_MALE")?></option>
				<option value="F"<?=$arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_FEMALE")?></option>
			</select><?
			break;

		case "PERSONAL_COUNTRY":
		case "WORK_COUNTRY":
			?><select name="REGISTER[<?=$FIELD?>]"><?
			foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value)
			{
				?><option value="<?=$value?>"<?if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<?endif?>><?=$arResult["COUNTRIES"]["reference"][$key]?></option>
			<?
			}
			?></select><?
			break;

		case "PERSONAL_PHOTO":
		case "WORK_LOGO":
			?><input size="30" type="file" name="REGISTER_FILES_<?=$FIELD?>" /><?
			break;

		case "PERSONAL_NOTES":
		case "WORK_NOTES":
			?><textarea cols="30" rows="5" name="REGISTER[<?=$FIELD?>]"><?=$arResult["VALUES"][$FIELD]?></textarea><?
			break;
		default:
			if ($FIELD == "PERSONAL_BIRTHDAY"):?><small><?=$arResult["DATE_FORMAT"]?></small><br /><?endif;
			?><input size="30" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" /><?
				if ($FIELD == "PERSONAL_BIRTHDAY")
					$APPLICATION->IncludeComponent(
						'bitrix:main.calendar',
						'',
						array(
							'SHOW_INPUT' => 'N',
							'FORM_NAME' => 'regform',
							'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
							'SHOW_TIME' => 'N'
						),
						null,
						array("HIDE_ICONS"=>"Y")
					);
				?><?
	}?></td>
		</tr>
	<?endif?>
<?endforeach?>
<?// ********************* User properties ***************************************************?>
<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
	<tr><td colspan="2"><?=trim($arParams["USER_PROPERTY_NAME"]) <> '' ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></td></tr>
	<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
	<tr><td><?=$arUserField["EDIT_FORM_LABEL"]?>:<?if ($arUserField["MANDATORY"]=="Y"):?><span class="starrequired">*</span><?endif;?></td><td>
			<?$APPLICATION->IncludeComponent(
				"bitrix:system.field.edit",
				$arUserField["USER_TYPE"]["USER_TYPE_ID"],
				array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "regform"), null, array("HIDE_ICONS"=>"Y"));?></td></tr>
	<?endforeach;?>
<?endif;?>
<?// ******************** /User properties ***************************************************?>
<?
///Captcha
if ($arResult["USE_CAPTCHA"] == "Y")
{
	?>
		<tr>
			<td colspan="2"><b><?=GetMessage("REGISTER_CAPTCHA_TITLE")?></b></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("REGISTER_CAPTCHA_PROMT")?>:<span class="starrequired">*</span></td>
			<td><input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" /></td>
		</tr>
	<?
}
//!CAPTCHA
?>
	</tbody>
	<tfoot>
		<tr>
			<td></td>
			<td><input type="submit" name="register_submit_button" value="<?=GetMessage("AUTH_REGISTER")?>" /></td>
		</tr>
	</tfoot>
</table>
</form>

<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>

<?endif //$arResult["SHOW_SMS_FIELD"] == true ?>

<p><span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p>

<?endif?>
</div>?>
 */?>