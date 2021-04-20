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
// проверка команды на участие
// проверка команды на участие
$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];
?>

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<?/*<div class="col-md-12" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
    <!--<div>
      Турнир <?php //echo $arItem["DISPLAY_PROPERTIES"]["TOURNAMENT"]["DISPLAY_VALUE"]; ?>
        <?php
        //var_dump($arItem["DISPLAY_PROPERTIES"]["TOURNAMENT"]["DISPLAY_VALUE"]);
        //var_dump($arItem);
        ?>
    </div>-->

    <div class="my-3">
      <img  src="<?php echo CFile::GetPath($arItem["~PROPERTY_TOURNAMENT_DETAIL_PICTURE"]); ?>" alt="">
    </div>
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<div class="text-center"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" ><img
						class="preview_picture"
						border="0"
            width="50px"
						src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
						alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
						title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
						style="border-radius: 50%; margin: 0 auto"
						/></a>
        </div>
			<?else:?>
				<img
					class="preview_picture"
					src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
					width="50px"
					alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
					title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
					style="border-radius: 50%"
					/>
			<?endif;?>
		<?endif?>
		<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
			<span class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
		<?endif?>

        <h2>#<span class="badge bg-secondary text-white"><?php echo $arItem['ID'];?></span></h2>
				<div><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a></div>
      
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<? //echo $arItem["PREVIEW_TEXT"];?>
		<?endif;?>
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<div style="clear:both"></div>
		<?endif?>


    <div>

        <?php

        /*if ($arr = ParseDateTime($arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"], "DD.MM.YYYY HH:MI"))
        {
            echo "День:    ".$arr["DD"]."<br>";    // День: 21
            echo "Месяц:   ".$arr["MM"]."<br>";    // Месяц: 1
            echo "Год:     ".$arr["YYYY"]."<br>";  // Год: 2004
            echo "Часы:    ".$arr["HH"]."<br>";    // Часы: 23
            echo "Минуты:  ".$arr["MI"]."<br>";    // Минуты: 44
            echo "Секунды: ".$arr["SS"]."<br>";    // Секунды: 15
        }
        else echo "Ошибка!";*/?>
<?php /*
        $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
        echo $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["NAME"] . ' <span class="badge rounded-pill bg-success">' . $dateTime[0] . ' в ' . $dateTime[1] . '</span>';?>


    </div>

	</div>*/?>
  <div class="flex-table--row" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <span>
          <div class="game-schedule__type-game">
            <?php if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 6) { ?>
              <div class="game-schedule__icon-type-game game-schedule__icon-type-game_prac">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.6 22.8"><path d="M963.34,529.28h-7.7l-4.4,12.48h6.6v8.32l11-13.52h-7.7Z" transform="translate(-950.24 -528.28)"/></svg>
              </div>
              <div class="color-practical"><?php echo $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["~VALUE"]; ?></div>
            <?php } elseif($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) { ?>
              <div class="game-schedule__icon-type-game game-schedule__icon-type-game_tournament">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.67 21.2">
                <path d="M676.21,374.4H689v7.68a6.41,6.41,0,0,1-6.4,6.4h0a6.4,6.4,0,0,1-6.4-6.4Z" transform="translate(-671.27 -373.4)"/>
                <path d="M689,377h1.3a2.42,2.42,0,0,1,2.57,2.83c-.42,1.8-1.43,3.86-3.87,4.21" transform="translate(-671.27 -373.4)"/>
                <path d="M676.21,377H674.9a2.42,2.42,0,0,0-2.57,2.83c.42,1.8,1.44,3.86,3.88,4.21" transform="translate(-671.27 -373.4)"/>
                <path d="M682.61,388.48v5.12" transform="translate(-671.27 -373.4)"/>
                <path d="M678.77,393.6h7.68" transform="translate(-671.27 -373.4)"/>
              </svg>
            </div>
              <div class="color-tournament"><?php echo $arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["~VALUE"]; ?></div>
            <?php } ?>
              <?php
              if($tmp = getParticipationByMatchIdMyMatches($arItem["ID"])) {
                  $tmp = array_flip($tmp);
                  if (isset($tmp[$teamID])) { ?>
                    <div class="game-schedule__participation-label">Слот № <?php echo $tmp[$teamID];?></div>
                  <?php }
              }
              ?>
          </div>
        </span>
    <span>
    <?php if (!empty($arItem["PROPERTIES"]["PUBG_LOBBY_ID"]["VALUE"])) { ?>
        LobbyId: <?php echo $arItem["PROPERTIES"]["PUBG_LOBBY_ID"]["VALUE"]; ?><br>
        Пароль: kick<br>
    <?php } ?>

          <a href="<?php echo $arItem["DETAIL_PAGE_URL"];?>" class="game-schedule__link">
            <?php
            $name = 'KICKGAME Scrims';//У меня нет названия';

            if ($arItem["DISPLAY_PROPERTIES"]['TYPE_MATCH']["VALUE_ENUM_ID"] == 5) {
                $name = $arItem["PROPERTY_TOURNAMENT_NAME"] . ' (' .$arItem["PROPERTIES"]["STAGE_TOURNAMENT"]['VALUE'] . ')';
            }
            echo $name;

            ?>
          </a>
        </span>
    <span class="game-schedule__param-wrap">
          <div class="game-schedule__param">Дата проведения</div>
          <?php
          $dateTime = explode(' ', $arItem["DISPLAY_PROPERTIES"]["DATE_START"]["VALUE"]);
          echo $dateTime[0] . ' в ' . substr($dateTime[1], 0, 5); ?>
        </span>
    <span class="game-schedule__param-wrap">
          <div class="game-schedule__param">Рейтинг</div>
          3.0
        </span>
    <span class="game-schedule__param-wrap">
            <div class="game-schedule__param">Режим</div>
            <div class="game-schedule__mode">
              <i></i>
              <div>x<?php echo $arItem["PROPERTIES"]["COUTN_TEAMS"]["VALUE"]; ?></div>
            </div>
        </span>
    <span class="game-schedule__param-wrap">
          <div class="game-schedule__param">Комментатор</div>
          <?php if (!empty($arItem["PROPERTY_STREAMER_NAME"])) { ?>
              <?php echo $arItem["PROPERTY_STREAMER_NAME"]; ?>
          <?php } else { ?>
            -
          <?php } ?>
        </span>
  </div>
<?endforeach;?>

<<<<<<< Updated upstream
=======
<?php if(!empty($arResult["ITEMS"])){ ?>
<div class="mt-3">
    <a href="https://t.me/joinchat/3zyL7w5RL7czZmYy" class="btn">Поддержка</a>
</div>
<?php } ?>
>>>>>>> Stashed changes

