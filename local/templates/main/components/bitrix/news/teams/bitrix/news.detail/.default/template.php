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
?>

<div class="news-detail">
	<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<img
			class="detail_picture"
			border="0"
			src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
			width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
			height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
			alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
			title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
			/>
	<?endif?>
	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<span class="news-date-time"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
	<?endif;?>
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<h3><?=$arResult["NAME"]?></h3>
	<?endif;?>
	<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
		<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></p>
	<?endif;?>
	<?if($arResult["NAV_RESULT"]):?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
		<?echo $arResult["NAV_TEXT"];?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
	<?elseif($arResult["DETAIL_TEXT"] <> ''):?>
		<?echo $arResult["DETAIL_TEXT"];?>
	<?else:?>
		<?echo $arResult["PREVIEW_TEXT"];?>
	<?endif?>
	<div style="clear:both"></div>
	<br />
	<?foreach($arResult["FIELDS"] as $code=>$value):
		if ('PREVIEW_PICTURE' == $code || 'DETAIL_PICTURE' == $code)
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?
			if (!empty($value) && is_array($value))
			{
				?><img border="0" src="<?=$value["SRC"]?>" width="<?=$value["WIDTH"]?>" height="<?=$value["HEIGHT"]?>"><?
			}
		}
		else
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?><?
		}
		?><br />
	<?endforeach;
	foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>

		<?=$arProperty["NAME"]?>:&nbsp;
		<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
			<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
		<?else:?>
			<?=$arProperty["DISPLAY_VALUE"];?>
		<?endif?>
		<br />
	<?endforeach;
	if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
	{
		?>
		<div class="news-detail-share">
			<noindex>
			<?
			$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
					"HANDLERS" => $arParams["SHARE_HANDLERS"],
					"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
					"PAGE_TITLE" => $arResult["~NAME"],
					"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
					"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
					"HIDE" => $arParams["SHARE_HIDE"],
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);
			?>
			</noindex>
		</div>
		<?
	}
	?>
</div>

<div class="container">
    <a href="javascript:history.back()" class="btn-italic-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 11.62">
            <path d="M955.22,534.89a1,1,0,0,1,0,1.33l-3,3.27h18.2a.94.94,0,0,1,0,1.88h-18.2l3,3.27a1,1,0,0,1,0,1.32.81.81,0,0,1-1.21,0l-4.49-4.88h0a1,1,0,0,1,0-1.33h0l4.49-4.87A.81.81,0,0,1,955.22,534.89Z" transform="translate(-949.26 -534.62)"/>
        </svg> Назад
    </a>
    <section class="game">
        <div class="row align-items-center justify-content-lg-center">
            <div class="col-lg-6">
                <div class="game__block">
                    <div class="game__block-img" style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/profile-avatar.jpg)">
                        <div class="game__block-img-rating-bg">
                            <div class="game__block-img-rating">3.00</div>
                        </div>
                    </div>
                    <h1><?php
                        $name = 'У меня нет названия';

                        if ($arResult["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                            $name = $arResult["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arResult["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')';
                        }
                        echo $name;

                        ?></h1>
                    <?php if ($arResult["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
                        <div class="game__block-type"><i></i> Практическая игра</div>
                    <?php } elseif($arResult["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
                        <div class="game__block-type game__block-type_tournament"><i></i> Турнирная игра</div>
                    <?php } ?>
                    <form action="/teams/?ELEMENT_ID=<?php echo $arResult['ID'];?>" method="post">

                        <input type="hidden" name="team_id" value="<?php echo $arResult['ID'];?>">
                        <button type="submit" name="join_submit" class="btn">Подать заявку</button>
                    </form>

                        <div class="game__block-call">
                            <a href="#" class="btn-italic">Связаться с модератором</a>
                        </div>

                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-6 col-xl-4">
                        <div class="info-item">
                            <div>Дата проведения</div>
                            <div>
                                <?php
                                $dateTime = explode(' ', $arResult["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
                                echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-4">
                        <div class="info-item">
                            <div>Количество матчей</div>
                            <div>3 (2 часа)</div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-4">
                        <div class="info-item">
                            <div>Комментатор</div>
                            <div>
                                <?php if (!empty($arResult["PROPERTY_STREAMER_NAME"])) { ?>
                                    <?php echo $arResult["PROPERTY_STREAMER_NAME"]; ?>
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-4">
                        <div class="info-item">
                            <div>Ссылка на трансляцию</div>
                            <div>
                                <?php if (!empty($arResult["PROPERTIES"]["URL_STREAM"]['VALUE'])) { ?>
                                    <a href="<?php echo $arResult["PROPERTIES"]["URL_STREAM"]['VALUE'];?>" target="_blank" class="btn-blue">В эфире</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-4">
                        <div class="info-item">
                            <div>Режим игры</div>
                            <div>
                      <span class="info-item__mode-block">
                        <!--<span class="info-item__mode-description">Сквад</span>-->
                        <div class="info-item__mode">
                          <i></i>
                          <div>x<?php echo $arResult["PROPERTIES"]["COUTN_TEAMS"]["VALUE"];?></div>
                        </div>
                      </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>