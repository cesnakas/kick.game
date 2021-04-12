<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

// \Bitrix\Main\Page\Asset::getInstance()->addCss('/bitrix/css/main/system.auth/flat/style.css');

if ($arResult['AUTHORIZED'])
{
	echo Loc::getMessage('MAIN_AUTH_FORM_SUCCESS');
	return;
}
?>
  <div class="layout__content layout__content_full">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-12 col-lg-6">
          <div class="logo-tagline">
            <a href="/"><img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/logo-tagline.svg" alt="kickgame esports"></a>
          </div>
        </div>
        <div class="col-md-12 col-lg-6">
          <div class="form-authentication">
            <h2 class="form-authentication__heading">Вход</h2>
              <?if ($arResult['ERRORS']):?>
                <div class="alert alert-danger">
                    <? foreach ($arResult['ERRORS'] as $error)
                    {
                        echo $error;
                    }
                    ?>
                </div>
              <?endif;?>
            <form name="<?= $arResult['FORM_ID'];?>" method="post" action="<?= POST_FORM_ACTION_URI;?>">
              <div class="form-field">
                <label for="auth-login" class="form-field__label">NickName</label>
                <input type="text" class="form-field__input" name="<?= $arResult['FIELDS']['login'];?>" maxlength="255" value="<?= \htmlspecialcharsbx($arResult['LAST_LOGIN']);?>" autocomplete="off" id="auth-login" placeholder="Введите свой NickName">
              </div>
              <div class="form-field">
                <label for="auth-pass" class="form-field__label">Пароль</label>
                  <?if ($arResult['SECURE_AUTH']):?>
                    <div class="bx-authform-psw-protected" id="bx_auth_secure" style="display:none">
                      <div class="bx-authform-psw-protected-desc"><span></span>
                          <?= Loc::getMessage('MAIN_AUTH_FORM_SECURE_NOTE');?>
                      </div>
                    </div>
                    <script type="text/javascript">
                        document.getElementById('bx_auth_secure').style.display = '';
                    </script>
                  <?endif?>
                <input type="password" class="form-field__input " name="<?= $arResult['FIELDS']['password'];?>" maxlength="255"  autocomplete="off" id="auth-pass" placeholder="Введите свой пароль">

              </div>

              <?if ($arResult['STORE_PASSWORD'] == 'Y'):?>
                <div class="form-field">
                  <label class="label-checkbox-main">
                    <input type="checkbox" name="<?= $arResult['FIELDS']['remember'];?>" checked value="Y">
                    <div class="label-checkbox-main__checkmark"></div>
                    <span class="label-checkbox-main__title">Запомнить меня</span>
                  </label>
                </div>
              <?endif?>
              <div class="form-field text-center">
                <a class="form-authentication__forgot-pass" href="<?= $arResult['AUTH_FORGOT_PASSWORD_URL'];?>">Забыли пароль?</a>
              </div>
              <div class="form-field d-flex justify-content-center">
                <button class="btn btn_login" type="submit" name="<?= $arResult['FIELDS']['action'];?>" value="<?= Loc::getMessage('MAIN_AUTH_FORM_FIELD_SUBMIT');?>">Вход</button>
              </div>
              <div class="form-authentication__already text-center">У вас нет аккаунта?</div>
              <div class="form-field text-center">
                <a class="form-authentication__forgot-pass" href="<?= $arResult['AUTH_REGISTER_URL'];?>">Создать аккаунт</a>
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
<?php /*
<div class="container">



	<h3 class="bx-title"><?= Loc::getMessage('MAIN_AUTH_FORM_HEADER');?></h3>

	<?if ($arResult['AUTH_SERVICES']):?>
		<?$APPLICATION->IncludeComponent('bitrix:socserv.auth.form',
			'flat',
			array(
				'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
				'AUTH_URL' => $arResult['CURR_URI']
	   		),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
		?>
		<hr class="bxe-light">
	<?endif?>

	<form name="<?= $arResult['FORM_ID'];?>" method="post" target="_top" action="<?= POST_FORM_ACTION_URI;?>">

		<div class="bx-authform-formgroup-container">
			<div class="bx-authform-label-container"><?= Loc::getMessage('MAIN_AUTH_FORM_FIELD_LOGIN');?></div>
			<div class="bx-authform-input-container">
				<input type="text" name="<?= $arResult['FIELDS']['login'];?>" maxlength="255" value="<?= \htmlspecialcharsbx($arResult['LAST_LOGIN']);?>" />
			</div>
		</div>

		<div class="bx-authform-formgroup-container">
			<div class="bx-authform-label-container"><?= Loc::getMessage('MAIN_AUTH_FORM_FIELD_PASS');?></div>
			<div class="bx-authform-input-container">
				<?if ($arResult['SECURE_AUTH']):?>
					<div class="bx-authform-psw-protected" id="bx_auth_secure" style="display:none">
						<div class="bx-authform-psw-protected-desc"><span></span>
							<?= Loc::getMessage('MAIN_AUTH_FORM_SECURE_NOTE');?>
						</div>
					</div>
					<script type="text/javascript">
						document.getElementById('bx_auth_secure').style.display = '';
					</script>
				<?endif?>
				<input type="password" name="<?= $arResult['FIELDS']['password'];?>" maxlength="255" autocomplete="off" />
			</div>
		</div>

		<?if ($arResult['CAPTCHA_CODE']):?>
			<input type="hidden" name="captcha_sid" value="<?= \htmlspecialcharsbx($arResult['CAPTCHA_CODE']);?>" />
			<div class="bx-authform-formgroup-container dbg_captha">
				<div class="bx-authform-label-container">
					<?= Loc::getMessage('MAIN_AUTH_FORM_FIELD_CAPTCHA');?>
				</div>
				<div class="bx-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?= \htmlspecialcharsbx($arResult['CAPTCHA_CODE']);?>" width="180" height="40" alt="CAPTCHA" /></div>
				<div class="bx-authform-input-container">
					<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
				</div>
			</div>
		<?endif;?>

		<?if ($arResult['STORE_PASSWORD'] == 'Y'):?>
			<div class="bx-authform-formgroup-container">
				<div class="checkbox">
					<label class="bx-filter-param-label">
						<input type="checkbox" id="USER_REMEMBER" name="<?= $arResult['FIELDS']['remember'];?>" value="Y" />
						<span class="bx-filter-param-text"><?= Loc::getMessage('MAIN_AUTH_FORM_FIELD_REMEMBER');?></span>
					</label>
				</div>
			</div>
		<?endif?>

		<div class="bx-authform-formgroup-container">
			<input type="submit" class="btn btn-primary" name="<?= $arResult['FIELDS']['action'];?>" value="<?= Loc::getMessage('MAIN_AUTH_FORM_FIELD_SUBMIT');?>" />
		</div>

		<?if ($arResult['AUTH_FORGOT_PASSWORD_URL'] || $arResult['AUTH_REGISTER_URL']):?>
			<hr class="bxe-light">
			<noindex>
			<?if ($arResult['AUTH_FORGOT_PASSWORD_URL']):?>
				<div class="bx-authform-link-container">
					<a href="<?= $arResult['AUTH_FORGOT_PASSWORD_URL'];?>" rel="nofollow">
						<?= Loc::getMessage('MAIN_AUTH_FORM_URL_FORGOT_PASSWORD');?>
					</a>
				</div>
			<?endif;?>
			<?if ($arResult['AUTH_REGISTER_URL']):?>
				<div class="bx-authform-link-container">
					<a href="<?= $arResult['AUTH_REGISTER_URL'];?>" rel="nofollow">
						<?= Loc::getMessage('MAIN_AUTH_FORM_URL_REGISTER_URL');?>
					</a>
				</div>
			<?endif;?>
			</noindex>
		<?endif;?>

	</form>
</div>
 */?>
<script type="text/javascript">
	<?if ($arResult['LAST_LOGIN'] != ''):?>
	try{document.<?= $arResult['FORM_ID'];?>.USER_PASSWORD.focus();}catch(e){}
	<?else:?>
	try{document.<?= $arResult['FORM_ID'];?>.USER_LOGIN.focus();}catch(e){}
	<?endif?>
</script>
