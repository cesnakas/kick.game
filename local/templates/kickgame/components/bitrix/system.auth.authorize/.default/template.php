<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

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
            <h2 class="form-authentication__heading"><?=GetMessage('AUTH_FORM_TITLE')?></h2>
              <?
              ShowMessage($arParams["~AUTH_RESULT"]);
              ShowMessage($arResult['ERROR_MESSAGE']);
              ?>
            <form name="form_auth" method="post" action="<?=$arResult["AUTH_URL"]?>">
              <input type="hidden" name="AUTH_FORM" value="Y" />
              <input type="hidden" name="TYPE" value="AUTH" />
                <?if ($arResult["BACKURL"] <> ''):?>
                  <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
                <?endif?>
                <?foreach ($arResult["POST"] as $key => $value):?>
                  <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
                <?endforeach?>
              <div class="form-field">
                <label for="auth-login" class="form-field__label"><?=GetMessage('AUTH_FORM_NICKNAME_LABEL')?></label>
                <input type="text" class="form-field__input" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" autocomplete="off" id="auth-login" placeholder="<?=GetMessage('AUTH_FORM_NICKNAME_PLACEHOLDER')?>">
              </div>
              <div class="form-field">
                <label for="auth-pass" class="form-field__label"><?=GetMessage('AUTH_FORM_PASSWORD_LABEL')?></label>
                <input type="password" class="form-field__input " name="USER_PASSWORD" maxlength="255" autocomplete="off" id="auth-pass" placeholder="<?=GetMessage('AUTH_FORM_PASSWORD_PLACEHOLDER')?>">
              </div>
                <?if ($arResult['STORE_PASSWORD'] == 'Y'):?>
                  <div class="form-field">
                    <label class="label-checkbox-main">
                      <input type="checkbox" name="USER_REMEMBER" checked value="Y">
                      <div class="label-checkbox-main__checkmark"></div>
                        <span class="label-checkbox-main__title"><?=GetMessage('AUTH_FORM_REMEMBER_ME')?></span>
                    </label>
                  </div>
                <?endif?>

              <div class="form-field text-center">
                <a class="form-authentication__forgot-pass" href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"];?>"><?=GetMessage('AUTH_FORGOT_PASSWORD')?></a>
              </div>
              <div class="form-field d-flex justify-content-center">
                <button class="btn btn_login"
                        type="submit"
                        name="Login" value="<?=GetMessage("AUTH_FORM_FIELD_SUBMIT")?>">
                    <?=GetMessage('AUTH_FORM_LOGIN')?>
                </button>
              </div>
                <div class="form-authentication__already text-center"><?=GetMessage('AUTH_FORM_ACCOUNT')?></div>
              <div class="form-field text-center">
                <a class="form-authentication__forgot-pass" href="<?= (LANGUAGE_ID == 'ru') ? $arResult['AUTH_REGISTER_URL'] : SITE_DIR.'personal/auth/reg.php';?>">
                    <?=GetMessage('AUTH_FORM_ACCOUNT_CREATE')?>
                </a>
              </div>
            </form>
            <!--<div class="form-authentication__login-social-network">
              <div class="form-authentication__login-social-network-heading">Войти с помощью:</div>
              <div class="social-networks">
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
          </div>
        </div>
      </div>
    </div>
  </div>
<?php/*
<div class="container my-5">
<?if($arResult["AUTH_SERVICES"]):?>
	<div class="bx-auth-title"><?echo GetMessage("AUTH_TITLE")?></div>
<?endif?>
	<div class="bx-auth-note"><?=GetMessage("AUTH_PLEASE_AUTH")?></div>

	<form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">

		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="AUTH" />
		<?if ($arResult["BACKURL"] <> ''):?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?endif?>
		<?foreach ($arResult["POST"] as $key => $value):?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
		<?endforeach?>

		<table class="bx-auth-table">
			<tr>
				<td class="bx-auth-label"><?=GetMessage("AUTH_LOGIN")?></td>
				<td><input class="bx-auth-input form-control" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" /></td>
			</tr>
			<tr>
				<td class="bx-auth-label"><?=GetMessage("AUTH_PASSWORD")?></td>
				<td><input class="bx-auth-input form-control" type="password" name="USER_PASSWORD" maxlength="255" autocomplete="off" />
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
				</td>
			</tr>
			<?if($arResult["CAPTCHA_CODE"]):?>
				<tr>
					<td></td>
					<td><input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></td>
				</tr>
				<tr>
					<td class="bx-auth-label"><?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:</td>
					<td><input class="bx-auth-input form-control" type="text" name="captcha_word" maxlength="50" value="" size="15" autocomplete="off" /></td>
				</tr>
			<?endif;?>
<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
			<tr>
				<td></td>
				<td><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" /><label for="USER_REMEMBER">&nbsp;<?=GetMessage("AUTH_REMEMBER_ME")?></label></td>
			</tr>
<?endif?>
			<tr>
				<td></td>
				<td class="authorize-submit-cell"><input type="submit" class="btn btn-primary" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>" /></td>
			</tr>
		</table>

<?if ($arParams["NOT_SHOW_LINKS"] != "Y"):?>
		<noindex>
			<p>
				<a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a>
			</p>
		</noindex>
<?endif?>

<?if($arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y"):?>
		<noindex>
			<p>
				<a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER")?></a><br />
				<?=GetMessage("AUTH_FIRST_ONE")?>
			</p>
		</noindex>
<?endif?>

	</form>


<script type="text/javascript">
<?if ($arResult["LAST_LOGIN"] <> ''):?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?else:?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?endif?>
</script>

<?if($arResult["AUTH_SERVICES"]):?>
<?
$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
	array(
		"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
		"CURRENT_SERVICE" => $arResult["CURRENT_SERVICE"],
		"AUTH_URL" => $arResult["AUTH_URL"],
		"POST" => $arResult["POST"],
		"SHOW_TITLES" => $arResult["FOR_INTRANET"]?'N':'Y',
		"FOR_SPLIT" => $arResult["FOR_INTRANET"]?'Y':'N',
		"AUTH_LINE" => $arResult["FOR_INTRANET"]?'N':'Y',
	),
	$component,
	array("HIDE_ICONS"=>"Y")
);
?>
<?endif?>

</div>
*/?>