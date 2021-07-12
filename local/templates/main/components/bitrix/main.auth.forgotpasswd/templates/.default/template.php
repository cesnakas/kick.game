<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

\Bitrix\Main\Page\Asset::getInstance()->addCss(
	'/bitrix/css/main/system.auth/flat/style.css'
);

if ($arResult['AUTHORIZED'])
{
	echo Loc::getMessage('MAIN_AUTH_PWD_SUCCESS');
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


	<?if ($arResult['ERRORS']):?>
	<div class="alert alert-danger">
		<? foreach ($arResult['ERRORS'] as $error)
		{
			echo $error;
		}
		?>
	</div>
	<?elseif ($arResult['SUCCESS']):?>
	<div class="alert alert-success">
		<?= $arResult['SUCCESS'];?>
	</div>
	<?endif;?>

	<h3 class="form-authentication__heading"><?= Loc::getMessage('MAIN_AUTH_PWD_HEADER');?></h3>

	<p class="bx-authform-content-container"><?= Loc::getMessage('MAIN_AUTH_PWD_NOTE');?></p>

	<form name="bform" method="post" target="_top" action="<?= POST_FORM_ACTION_URI;?>">

		<div class="form-field">
			<div class="form-field__label"><?= Loc::getMessage('MAIN_AUTH_PWD_FIELD_LOGIN');?></div>
				<input type="text" class="form-field__input" name="<?= $arResult['FIELDS']['login'];?>" maxlength="255" value="<?= \htmlspecialcharsbx($arResult['LAST_LOGIN']);?>" />

		</div>
        <div class="form-field" style="margin-bottom: 5% !important">
            <span class="login-label"><?= Loc::getMessage('MAIN_AUTH_PWD_OR');?></span>
        </div>
		<div class="form-field">

			<div class="form-field__label"><?= Loc::getMessage('MAIN_AUTH_PWD_FIELD_EMAIL');?></div>

				<input type="text" class="form-field__input" name="<?= $arResult['FIELDS']['email'];?>" maxlength="255" value="" />

		</div>

		<?if ($arResult['CAPTCHA_CODE']):?>
			<input type="hidden" name="captcha_sid" value="<?= \htmlspecialcharsbx($arResult['CAPTCHA_CODE']);?>" />
			<div class="bx-authform-formgroup-container dbg_captha">
				<div class="bx-authform-label-container">
					<?= Loc::getMessage('MAIN_AUTH_PWD_FIELD_CAPTCHA');?>
				</div>
				<div class="bx-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?= \htmlspecialcharsbx($arResult['CAPTCHA_CODE']);?>" width="180" height="40" alt="CAPTCHA" /></div>
				<div class="bx-authform-input-container">
					<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
				</div>
			</div>
		<?endif;?>

        <div class="form-field d-flex justify-content-center">
			<input type="submit" class="btn btn_login" name="<?= $arResult['FIELDS']['action'];?>" value="<?= Loc::getMessage('MAIN_AUTH_PWD_FIELD_SUBMIT');?>" />
		</div>

		<?if ($arResult['AUTH_AUTH_URL'] || $arResult['AUTH_REGISTER_URL']):?>
			<noindex class="text-center" >
			<?if ($arResult['AUTH_AUTH_URL']):?>
				<div class="bx-authform-link-container" style="margin-top: 17%">
					<a href="<?= $arResult['AUTH_AUTH_URL'];?>" class="btn-italic" rel="nofollow">
						<?= Loc::getMessage('MAIN_AUTH_PWD_URL_AUTH_URL');?>
					</a>
				</div>
			<?endif;?>
			<?if ($arResult['AUTH_REGISTER_URL']):?>
				<div class="bx-authform-link-container">
					<a href="<?= $arResult['AUTH_REGISTER_URL'];?>" class="btn-italic" rel="nofollow">
						<?= Loc::getMessage('MAIN_AUTH_PWD_URL_REGISTER_URL');?>
					</a>
				</div>
			<?endif;?>
			</noindex>
		<?endif;?>

	</form>
</div>
        </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	document.bform.<?= $arResult['FIELDS']['login'];?>.focus();
</script>
