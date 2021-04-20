<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetPageProperty("TITLE", "KICKGAME");
$APPLICATION->SetTitle("Главная");

//AddMessage2Log("Произвольный текст сообщения", "my_module_id");

//echo '<!-- '.$_SERVER['DOCUMENT_ROOT'].' -->';
//$myreslt = new \App\MemberResultTable();


// add
/*
$result = $myreslt::add(array(
    'USER_ID' => '5',
    'MATCH_ID' => '3',
    'TOTAL' => '4',
    'KILLS' => '2',
    'PLACE' => '',
));

*/
// update
/*
$result = $myreslt::update(3, array(
    'PLACE' => '66',
));*/

// delete
/*
$result = $myreslt::delete(4);
*/
/*
if ($result->isSuccess())
{
    $id = $result->getId();
    dump($id);
}*/

//$result = $myreslt::getByPrimary(3)
//->fetchObject();

//dump($result['USER_ID']);
/*
?>
<div class="container">
  <h1>Hello, Log in or Sign Up!</h1>
    <?

    $curDate = date('Y-m-d H:i:s', time()-3600);
    GLOBAL $arrFilterDateTime;
    $arrFilterDateTime=Array(
        "ACTIVE" => "Y",
        ">=PROPERTY_DATE_START" => $curDate,
        "PROPERTY_PREV_MATCH" => false,
        //"PROPERTY_STAGE_TOURNAMENT" => 4,
        //"!=PROPERTY_TOURNAMENT" => false, // турниры
        //"=PROPERTY_TOURNAMENT" => false, // праки
    );
    //dump($arrFilterDateTime);
    $APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"game-schedule",
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array(
			0 => "",
			1 => "PROPERTY_TOURNAMENT.NAME",
			2 => "PROPERTY_TOURNAMENT.DETAIL_PICTURE",
			3 => "PROPERTY_STREAMER.NAME",
			4 => "",
		),
		"FILTER_NAME" => "arrFilterDateTime",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "3",
		"IBLOCK_TYPE" => "matches",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"INCLUDE_SUBSECTIONS" => "Y",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "10",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Расписание игр",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "PUBG_LOBBY_ID",
			1 => "DATE_START",
			2 => "TYPE_MATCH",
			3 => "TOURNAMENT",
			4 => "",
		),
		"SET_BROWSER_TITLE" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "Y",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "Y",
		"SHOW_404" => "N",
		"SORT_BY1" => "PROPERTY_DATE_START",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"COMPONENT_TEMPLATE" => "game-schedule"
	),
	false
);

    ?>
</div>
<?php */?>
    <!--<div class="layout__content layout__content_full">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 ">
                    <h1 class="text-center">Скоро открытие</h1>
                </div>
            </div>
        </div>
    </div>--><?php /* ?>
  <section class="slider">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-12 col-lg-6 order-2 order-lg-1">
          <h2 class="slider-item__title">
            KICKGAME</h2>
          <div class="slider-item__slogan">Твой пропуск в киберспорт, пабгер</div>
          <div class="slider-item-adv__wrap">
            <div class="row">
              <div class="col-md-6">
                <div class="slider-item-adv">
                  <div class="slider-item-adv__icon">
                    DUO
                  </div>
                  <div>Турниры дуо на 100€
                    каждую неделю </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="slider-item-adv">
                  <div class="slider-item-adv__icon slider-item-adv__icon_pink">
                    SQU<br>
                    AD
                  </div>
                  <div>Турниры для сквадов
                    на 1000€ каждый месяц</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="slider-item-adv">
                  <div class="slider-item-adv__icon slider-item-adv__icon_orange">
                    <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/slider-item-adv-custom.svg" alt="">
                  </div>
                  <div>
                    Кастомки с призами
                    каждую неделю
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="slider-item-adv">
                  <div class="slider-item-adv__icon slider-item-adv__icon__red">
                    <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/slider-item-adv-calendar.svg" alt="">
                  </div>
                  <div>
                    14 дней, чтобы попробовать
                    все плюшки бесплатно
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="slider-item__btn">
            <a href="/personal/auth/reg.php" class="btn btn-big">Регистрация</a>
            <div class="slider-item__btn__action">Начни путь к победе сегодня!</div>
          </div>
        </div>
        <div class="col-md-12 col-lg-6 order-1 order-lg-2">
          <div class="slider-item__img">
            <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/slider-4.png" alt="slider1">
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="banner banner_main">
    <div class="container">
      <div class="banner__bg banner__bg_main">
        <div class="banner__content">
          <div class="row align-items-center">
            <div class="col-md-12 col-lg-6">
              <h2 class="banner__heading-main">Турнир по PUBG Mobile на 1000€ для команд — регистрация открыта</h2>
              <p class="banner__description">Количество мест ограничено. Зарегистрируйся на платформе KICKGAME, чтобы подать заявку на участие сегодня :3</p>
            </div>
            <div class="col-md-12 col-lg-6 banner__block-right">
              <div class="banner__content-btn">
                <a href="/game-schedule/" class="btn btn-big">Подать заявку</a>
              </div>
              <p><a href="#" class="link">Условия проведения турнира<br>
                  и требования к участникам</a></p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
  <section class="playing-with-us-new">
    <div class="container">
      <h2 class="playing-with-us-new__heading">Играя с нами, ты сможешь</h2>
      <div class="row">
        <div class="col-md-4">
          <div class="playing-with-us-new-item">
            <div class="playing-with-us-new-item__img">
              <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/icon-playing-1.svg" alt="">
            </div>
            <div class="playing-with-us-new-item__heading">Прокачаться</div>
            <div class="playing-with-us-new-item__description">тренироваться с tier 1 - tier 3 командами, анализировать свои результаты по готовым записям игр и совершенствовать навыки</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="playing-with-us-new-item">
            <div class="playing-with-us-new-item__img">
              <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/icon-playing-2.svg" alt="">
            </div>
            <div class="playing-with-us-new-item__heading">Стать первым</div>
            <div class="playing-with-us-new-item__description">
              в рейтингах игроков и команд, ежедневно соревнуясь с разными соперниками в практических играх
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="playing-with-us-new-item">
            <div class="playing-with-us-new-item__img">
              <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/icon-playing-3.svg" alt="">
            </div>
            <div class="playing-with-us-new-item__heading">Победить в турнире</div>
            <div class="playing-with-us-new-item__description">
              или нескольких, и забрать часть призового фонда размером в 1000€ или 10000€
            </div>
          </div>
        </div>
      </div>
      <div class="playing-with-us__btn">
        <a href="/personal/auth/reg.php" class="btn btn-big">Регистрация</a>
      </div>
      <div class="playing-with-us__action">
        14 дней бесплатный пробный период
      </div>
    </div>
  </section>
  <section class="game-schedule bg-blue-lighter">
    <div class="container">
      <h2 class="game-schedule__heading text-center">Расписание игр</h2>
      <div class="game-schedule-table">
        <div class="flex-table">
          <div class="flex-table--header bg-blue-lighter">
            <div class="flex-table--categories">
              <span>Тип игры</span>
              <span>Название</span>
              <span>Дата проведения</span>
              <span>Рейтинг</span>
              <span>Режим</span>
              <span>Комментатор</span>
            </div>
          </div>
          <div class="flex-table--body">
              <?php
              $curDate = date('Y-m-d H:i:s', time()-3600);
              GLOBAL $arrFilterDateTime;
              $arrFilterDateTime=Array(
                  "ACTIVE" => "Y",
                  ">=PROPERTY_DATE_START" => $curDate,
                  "PROPERTY_PREV_MATCH" => false,
                  //"PROPERTY_STAGE_TOURNAMENT" => 4,
                  //"!=PROPERTY_TOURNAMENT" => false, // турниры
                  //"=PROPERTY_TOURNAMENT" => false, // праки
              );
              $APPLICATION->IncludeComponent(
                  "bitrix:news.list",
                  "game-schedule",
                  array(
                      "ACTIVE_DATE_FORMAT" => "d.m.Y",
                      "ADD_SECTIONS_CHAIN" => "N",
                      "AJAX_MODE" => "N",
                      "AJAX_OPTION_ADDITIONAL" => "",
                      "AJAX_OPTION_HISTORY" => "N",
                      "AJAX_OPTION_JUMP" => "N",
                      "AJAX_OPTION_STYLE" => "Y",
                      "CACHE_FILTER" => "N",
                      "CACHE_GROUPS" => "Y",
                      "CACHE_TIME" => "36000000",
                      "CACHE_TYPE" => "A",
                      "CHECK_DATES" => "Y",
                      "DETAIL_URL" => "/game-schedule/#ELEMENT_CODE#/",
                      "DISPLAY_BOTTOM_PAGER" => "Y",
                      "DISPLAY_DATE" => "Y",
                      "DISPLAY_NAME" => "Y",
                      "DISPLAY_PICTURE" => "Y",
                      "DISPLAY_PREVIEW_TEXT" => "Y",
                      "DISPLAY_TOP_PAGER" => "N",
                      "FIELD_CODE" => array(
                          0 => "",
                          1 => "PROPERTY_TOURNAMENT.NAME",
                          2 => "PROPERTY_TOURNAMENT.DETAIL_PICTURE",
                          3 => "PROPERTY_STREAMER.NAME",
                          4 => "",
                      ),
                      "FILTER_NAME" => "arrFilterDateTime",
                      "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                      "IBLOCK_ID" => "3",
                      "IBLOCK_TYPE" => "matches",
                      "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                      "INCLUDE_SUBSECTIONS" => "Y",
                      "MESSAGE_404" => "",
                      "NEWS_COUNT" => "5",
                      "PAGER_BASE_LINK_ENABLE" => "N",
                      "PAGER_DESC_NUMBERING" => "N",
                      "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                      "PAGER_SHOW_ALL" => "N",
                      "PAGER_SHOW_ALWAYS" => "N",
                      "PAGER_TEMPLATE" => "ajax_pager",
                      "PAGER_TITLE" => "Расписание игр",
                      "PARENT_SECTION" => "",
                      "PARENT_SECTION_CODE" => "",
                      "PREVIEW_TRUNCATE_LEN" => "",
                      "PROPERTY_CODE" => array(
                          0 => "PUBG_LOBBY_ID",
                          1 => "DATE_START",
                          2 => "TYPE_MATCH",
                          3 => "TOURNAMENT",
                          4 => "",
                      ),
                      "SET_BROWSER_TITLE" => "N",
                      "SET_LAST_MODIFIED" => "N",
                      "SET_META_DESCRIPTION" => "N",
                      "SET_META_KEYWORDS" => "N",
                      "SET_STATUS_404" => "Y",
                      "SET_TITLE" => "N",
                      "SHOW_404" => "Y",
                      "SORT_BY1" => "PROPERTY_DATE_START",
                      "SORT_BY2" => "SORT",
                      "SORT_ORDER1" => "ASC",
                      "SORT_ORDER2" => "ASC",
                      "STRICT_SECTION_CHECK" => "N",
                      "COMPONENT_TEMPLATE" => "game-schedule",
                      "FILE_404" => ""
                  ),
                  false
              );
              ?>
          </div>
        </div>
        <div class="game-schedule-table__show-more">
          <div class="mt-3">
            <a href="/game-schedule/" class="btn">Поиск матча</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="functions-new">
    <div class="container">
      <h2 class="functions-new__heading">Функции</h2>
      <div class="row align-items-center ">
        <div class="col-md-12 col-lg-6">
          <div class="functions-new-item functions-new-item_1">
            <div class="functions-new-item__left">
              <div class="functions-new-item__heading">
                Поиск игроков и команд
              </div>
              <div class="functions-new-item__description">
                Возможность найти свою идеальную команду, напарника для дуо или бойца в сквад
              </div>
            </div>
            <div class="functions-new-item__right">
              <div class="functions-new-item__icon">
                <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/function-new-icon-search.svg" alt="function">
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 col-lg-6">
          <div class="functions-new-item functions-new-item_2">
            <div class="functions-new-item__left">
              <div class="functions-new-item__heading">
                Ранняя регистрация на игры
              </div>
              <div class="functions-new-item__description">
                Начать регистрироваться на праки можно за 1-2 дня до их начала, а на турниры — за 15 дней
              </div>
            </div>
            <div class="functions-new-item__right">
              <div class="functions-new-item__icon">
                <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/function-new-icon-reg.svg" alt="function">
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 col-lg-6">
          <div class="functions-new-item functions-new-item_3">
            <div class="functions-new-item__left">
              <div class="functions-new-item__heading">
                Удобное управление командой
              </div>
              <div class="functions-new-item__description">
                Создание команды, выбор игр, управление календарём, напоминания об играх и лёгкая замена игроков
              </div>
            </div>
            <div class="functions-new-item__right">
              <div class="functions-new-item__icon">
                <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/function-new-icon-manage.svg" alt="function">
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 col-lg-6">
          <div class="functions-new-item functions-new-item_4">
            <div class="functions-new-item__left">
              <div class="functions-new-item__heading">
                Трансляции и записи игр
              </div>
              <div class="functions-new-item__description">
                Все наши игры проходят со стримами, и остаются в открытом доступе для просмотра после завершения
              </div>
            </div>
            <div class="functions-new-item__right">
              <div class="functions-new-item__icon">
                <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/function-new-icon-stream.svg" alt="function">
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 col-lg-6">
          <div class="functions-new-item functions-new-item_5">
            <div class="functions-new-item__left">
              <div class="functions-new-item__heading">
                Игры для сквадов, дуо и соло
              </div>
              <div class="functions-new-item__description">
                От 15 праков ежедневно, кастомки и мимни-турики каждую неделю, крупные турниры каждый месяц
              </div>
            </div>
            <div class="functions-new-item__right">
              <div class="functions-new-item__icon">
                <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/function-new-icon-game.svg" alt="function">
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 col-lg-6">
          <div class="functions-new-item functions-new-item_6">
            <div class="functions-new-item__left">
              <div class="functions-new-item__heading">
                Рейтинги и статистика
              </div>
              <div class="functions-new-item__description">
                Квалификация команд и игроков, которая гарантирует игру с равными соперниками и личная статистика по сыграными играм
              </div>
            </div>
            <div class="functions-new-item__right">
              <div class="functions-new-item__icon">
                <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/function-new-icon-rating.svg" alt="function">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="functions__btn">
        <a href="/personal/auth/reg.php" class="btn btn-big">Регистрация</a>
      </div>
      <div class="functions__action">
        14 дней бесплатный пробный период
      </div>
    </div>
  </section><?php */ ?>
    <main class="main">
        <div class="adv">
            <div class="adv__text">
                <h3><?=GetMessage('CONTENT_ADV_TEXT_H3')?></h3>
                <p><?=GetMessage('CONTENT_ADV_TEXT_P')?></p>
            </div>
            <div class="adv__action">
                <a href="<?=SITE_DIR?>game-schedule/?arrFilterDateTime_pf%5BTYPE_MATCH%5D=5&set_filter=Y&set_filter=" class="button"><?=GetMessage('CONTENT_ADV_ACTION_BUTTON')?></a>
                <a href="<?=SITE_DIR?>regulations/" class="link link--text"><?=GetMessage('CONTENT_ADV_ACTION_P')?></a>
            </div>
        </div>

        <section class="section">
            <h2 class="main-title"><?=GetMessage('CONTENT_MAIN_TITLE')?></h2>
            <div class="features">
                <ul class="features__list">
                    <li class="features__item">
                        <img
                                width="80"
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/flash.png" alt="flash"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/flash.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/flash@2x.png 2x"
                        >
                        <h3><?=GetMessage('CONTENT_MAIN_PUMP_UP')?></h3>
                        <p><?=GetMessage('CONTENT_MAIN_PUMP_UP_TEXT')?></p>
                    </li>
                    <li class="features__item">
                        <img
                                width="80"
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/first.png" alt="first"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/first.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/first@2x.png 2x"
                        >
                        <h3><?=GetMessage('CONTENT_MAIN_FIRST')?></h3>
                        <p><?=GetMessage('CONTENT_MAIN_FIRST_TEXT')?></p>
                    </li>
                    <li class="features__item">
                        <img
                                width="80"
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament.png" alt="tournament"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/tournament.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/tournament@2x.png 2x"
                        >
                        <h3><?=GetMessage('CONTENT_MAIN_WIN')?></h3>
                        <p><?=GetMessage('CONTENT_MAIN_WIN_TEXT')?></p>
                    </li>
                </ul>
            </div>
            <div class="section__action">
                <? if (!$USER->IsAuthorized()) { ?>
                  <a href="<?=SITE_DIR?>personal/auth/reg.php" class="button"><?=GetMessage('CONTENT_MAIN_REGISTER')?></a>
                <? } else { ?>
                  <a href="<?=SITE_DIR?>personal/" class="button"><?=GetMessage('CONTENT_MAIN_LOGIN')?></a>
                <? } ?>
                <span><?=GetMessage('CONTENT_MAIN_14DAYS')?></span>
            </div>
            <div class="features-bg"></div>
        </section>

        <div class="separator"></div>

        <section class="section">
            <div class="games-bg"></div>
            <h2 class="main-title"><?=GetMessage('MAIN_GAMES')?></h2>
            <?php
            $curDate = date('Y-m-d H:i:s', time()-3600);
            GLOBAL $arrFilterDateTime;
            $arrFilterDateTime=Array(
                "ACTIVE" => "Y",
                ">=PROPERTY_DATE_START" => $curDate,
                array(
                    "LOGIC" => "OR",
                    array("PROPERTY_GROUP" => "A"),
                    array("PROPERTY_TYPE_MATCH" => 5)
                ),
                "PROPERTY_PREV_MATCH" => false,
                //"PROPERTY_STAGE_TOURNAMENT" => 4,
                //"!=PROPERTY_TOURNAMENT" => false, // турниры
                //"=PROPERTY_TOURNAMENT" => false, // праки
            );
            $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "game-schedule",
                array(
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => "36000000",
                    "CACHE_TYPE" => "A",
                    "CHECK_DATES" => "Y",
                    "DETAIL_URL" => SITE_DIR."/game-schedule/#ELEMENT_CODE#/",
                    "DISPLAY_BOTTOM_PAGER" => "Y",
                    "DISPLAY_DATE" => "Y",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "DISPLAY_TOP_PAGER" => "N",
                    "FIELD_CODE" => array(
                        0 => "",
                        1 => "PROPERTY_TOURNAMENT.NAME",
                        2 => "PROPERTY_TOURNAMENT.DETAIL_PICTURE",
                        3 => "PROPERTY_STREAMER.NAME",
                        4 => "",
                    ),
                    "FILTER_NAME" => "arrFilterDateTime",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                    "IBLOCK_ID" => "3",
                    "IBLOCK_TYPE" => "matches",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "MESSAGE_404" => "",
                    "NEWS_COUNT" => "5",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_TEMPLATE" => "ajax_pager",
                    "PAGER_TITLE" => "Расписание игр",
                    "PARENT_SECTION" => "",
                    "PARENT_SECTION_CODE" => "",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "PROPERTY_CODE" => array(
                        0 => "PUBG_LOBBY_ID",
                        1 => "DATE_START",
                        2 => "TYPE_MATCH",
                        3 => "TOURNAMENT",
                        4 => "",
                    ),
                    "SET_BROWSER_TITLE" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_STATUS_404" => "Y",
                    "SET_TITLE" => "N",
                    "SHOW_404" => "Y",
                    "SORT_BY1" => "PROPERTY_DATE_START",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER1" => "ASC",
                    "SORT_ORDER2" => "ASC",
                    "STRICT_SECTION_CHECK" => "N",
                    "COMPONENT_TEMPLATE" => "game-schedule",
                    "FILE_404" => ""
                ),
                false
            );
            ?>
            <div class="section__action">
                <a href="<?=SITE_DIR?>game-schedule/" class="button">Поиск игры</a>
                <!--<span>Показать ещё 5</span>-->
            </div>
        </section>


        <section class="section about">
            <h2 class="main-title">О платформе</h2>
            <div class="separator"></div>
            <div class="video-container">
                <div class="player">
                    <video class="player__video" src="<?php echo SITE_TEMPLATE_PATH;?>/images/video.mp4" poster="<?php echo SITE_TEMPLATE_PATH;?>/images/poster.jpg"></video>
                    <div class="player__controls">
                        <button type="button" class="player__button toggle js-play">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 163.861 163.861"><path d="M34.857 3.613C20.084-4.861 8.107 2.081 8.107 19.106v125.637c0 17.042 11.977 23.975 26.75 15.509L144.67 97.275c14.778-8.477 14.778-22.211 0-30.686L34.857 3.613z"/></svg>
                        </button>
                        <div class="player__progress">
                            <div class="player__progress-filled"></div>
                        </div>
                        <button class="player__button js-full">
                            <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26"><g clip-path="url(#clip0)"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.25 2H6a4 4 0 00-4 4v4.25h2.063V5.062a1 1 0 011-1h5.187V2zm5.5 2.063V2H20a4 4 0 014 4v4.25h-2.063V5.062a1 1 0 00-1-1H15.75zm0 17.875h5.188a1 1 0 001-1V15.75H24V20a4 4 0 01-4 4h-4.25v-2.063zM4.062 15.75v5.188a1 1 0 001 1h5.188V24H6a4 4 0 01-4-4v-4.25h2.063z" fill="#5F5A82"/></g><defs><clipPath id="clip0"><path fill="#fff" d="M0 0h26v26H0z"/></clipPath></defs></svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="section functions">
            <div class="functions-bg"></div>
            <h2 class="main-title">Функции</h2>
            <div class="functions__items">
                <div class="functions__items-row">
                    <div class="functions__item">
                        <div class="functions__item-text">
                            <h4>Поиск игроков и команд</h4>
                            <p>Возможность найти свою идеальную команду, напарника для дуо или бойца в сквад</p>
                        </div>
                        <img
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/search.png" alt="search"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/search.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/search@2x.png 2x"
                        >
                    </div>
                    <div class="functions__item">
                        <img
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/checked.png" alt="registration"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/checked.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/checked@2x.png 2x"
                        >
                        <div class="functions__item-text">
                            <h4>Ранняя регистрация на игры</h4>
                            <p>Начать регистрироваться на праки можно за 1-2 дня до их начала, а на турниры — за 15 дней</p>
                        </div>
                    </div>
                </div>
                <div class="functions__items-row">
                    <div class="functions__item">
                        <div class="functions__item-text">
                            <h4>Удобное управление командой</h4>
                            <p>Создание команды, выбор игр, управление календарём, напоминания об играх и лёгкая замена игроков</p>
                        </div>
                        <img
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/console.png" alt="team"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/console.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/console@2x.png 2x"
                        >
                    </div>
                    <div class="functions__item">
                        <img
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/stream.png" alt="stream"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/stream.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/stream@2x.png 2x"
                        >
                        <div class="functions__item-text">
                            <h4>Трансляции и записи игр</h4>
                            <p>Все наши игры проходят со стримами, и остаются в открытом доступе для просмотра после завершения</p>
                        </div>
                    </div>
                </div>
                <div class="functions__items-row">
                    <div class="functions__item">
                        <div class="functions__item-text">
                            <h4>Игры для сквадов, дуо и соло</h4>
                            <p>От 15 праков ежедневно, кастомки и мини-турики каждую неделю, крупные турниры каждый месяц</p>
                        </div>
                        <img
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/calendar.png" alt="calendar"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/calendar.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/calendar@2x.png 2x"
                        >
                    </div>
                    <div class="functions__item">
                        <img
                                src="<?php echo SITE_TEMPLATE_PATH;?>/images/rate.png" alt="rate"
                                srcset="<?php echo SITE_TEMPLATE_PATH;?>/images/rate.png 1x, <?php echo SITE_TEMPLATE_PATH;?>/images/rate@2x.png 2x"
                        >
                        <div class="functions__item-text">
                            <h4>Рейтинги и статистика</h4>
                            <p>Квалификация команд и игроков, которая гарантирует игру с равными соперниками и личная статистика по сыграными играм</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section__action">
                <?php if (!$USER->IsAuthorized()) { ?>
                  <a href="/personal/auth/reg.php" class="button">регистрация</a>
                <?php } else { ?>
                  <a href="/personal/" class="button">Войти</a>
                <?php } ?>
                <span>14 дней бесплатный пробный период</span>
            </div>
        </section>
    </main>

<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>