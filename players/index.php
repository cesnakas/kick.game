<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Игроки");
function getUsers()
{
    $filter = array("GROUPS_ID" => array(7));
    $arParams["SELECT"] = array("UF_*");
    $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
    // $elementsResult->NavStart(50);
    $output = [];
    while ($rsUser = $elementsResult->Fetch()) {
        $output[] = $rsUser;
    }
    return $output;
}

function getUser($key)
{
    if (preg_match("/^([0-9]{1,})\_(.*)$/", $key, $match)) {
        $id = $match[1] + 0;
        //todo xxs
        $nic = strip_tags($match[2]);

        $filter = array("GROUPS_ID" => array(7), 'ID' => $id, 'LOGIN' => $nic);
        $arParams["SELECT"] = array("UF_*");
        $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
        $output = false;
        if ($rsUser = $elementsResult->Fetch()) {
            $output = $rsUser;
        }
        return $output;
    } else {
        return false;
    }
}


function countPointsByUserID($userID)
{
    global $DB;
    $userID += 0;
    if ($userID) {
        $sql = 'SELECT  sum(t.TOTAL) AS total, sum(t.KILLS) AS kills
				FROM b_squad_member_result AS t 
				WHERE t.USER_ID = ' . $userID . ' AND t.TYPE_MATCH = 6
				GROUP BY t.USER_ID';
        $res = $DB->Query($sql);
        if ($row = $res->Fetch()) {
            $points = ['kills' => $row['kills'], 'total' => $row['total']];
            return $points;
        }
    }
    return false;
}

function countPointsAllUsers()
{
    global $DB;
    $sql = 'SELECT t.USER_ID, count(t.USER_ID) as count_matches, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills
			FROM b_squad_member_result AS t 
			WHERE t.TYPE_MATCH = 6
			GROUP BY t.USER_ID';
    $res = $DB->Query($sql);
    $points = [];
    while ($row = $res->Fetch()) {
        $points[$row['USER_ID']] = ['kills' => $row['kills'], 'total' => $row['total'], 'count_matches' => $row['count_matches']];
    }
    return $points;
}

function countPlayers()
{
    global $DB;
    $sql = 'SELECT count(g.USER_ID) as c
			FROM  b_user_group AS g
            WHERE g.GROUP_ID = 7';

    $res = $DB->Query($sql);
    $count = [];
    $count = $res->Fetch();

    return $count['c'];
}

function getPlayersList()
{
    global $DB;
    $sql = 'SELECT g.GROUP_ID, u.LOGIN, u.PERSONAL_PHOTO, u.ID, count_matches, IF(total IS NOT Null,total, 0) + IF(r.UF_RATING IS NOT Null, r.UF_RATING, 300) as total, kills 
			FROM  b_user as u 
            LEFT JOIN (SELECT t.USER_ID, count(t.USER_ID) as count_matches, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills FROM b_squad_member_result AS t WHERE t.TYPE_MATCH = 6 GROUP BY t.USER_ID) AS r1 ON r1.USER_ID = u.ID 
            LEFT JOIN b_uts_user AS r ON r.VALUE_ID = u.ID 
            INNER JOIN b_user_group AS g ON g.USER_ID = u.ID 
            AND g.GROUP_ID = 7 
            ORDER BY total DESC, kills DESC';

    $res = $DB->Query($sql);
    $players = [];
    while ($row = $res->Fetch()) {
        $players[$row['ID']] = ['kills' => $row['kills'],
            'total' => $row['total'],
            'count_matches' => $row['count_matches'],
            'login' => $row['LOGIN'],
            'photo' => $row['PERSONAL_PHOTO']];
    }
    return $players;
}

function showPlayers()
{
    global $DB;
    global $APPLICATION;
    //пагинация

    $count_tema = 20; // выводим по 5 Записей на страницу
    //создаем объект пагинации
    $nav = new \Bitrix\Main\UI\PageNavigation("nav-more-players");
    $nav->allowAllRecords(false)
        ->setPageSize($count_tema)
        ->initFromUri();

    $count_zap = countPlayers(); // сделать запрос для определения количества всех строк
    // в sql вставляем limit и Offset
    $strSql = 'SELECT g.GROUP_ID, u.LOGIN, u.PERSONAL_PHOTO, u.ID, count_matches, IF(total IS NOT Null,total, 0) + IF(r.UF_RATING IS NOT Null, r.UF_RATING, 300) as total, kills 
			FROM  b_user as u 
            LEFT JOIN (SELECT t.USER_ID, count(t.USER_ID) as count_matches, sum(t.TOTAL) AS total, sum(t.KILLS) AS kills FROM b_squad_member_result AS t WHERE t.TYPE_MATCH = 6 GROUP BY t.USER_ID) AS r1 ON r1.USER_ID = u.ID 
            LEFT JOIN b_uts_user AS r ON r.VALUE_ID = u.ID 
            INNER JOIN b_user_group AS g ON g.USER_ID = u.ID 
            AND g.GROUP_ID = 7 
            ORDER BY total DESC, kills DESC LIMIT ' . $nav->getLimit() . '  OFFSET ' . $nav->getOffset(); //
    $rsData = $DB->Query($strSql);
    $i = $nav->getOffset();
    while ($el = $rsData->fetch()) {
        $i += 1;
        showRow($el, $i);
    }

    $nav->setRecordCount($count_zap);
    $APPLICATION->IncludeComponent(
        "bitrix:main.pagenavigation",
        ".default",
        array(
            "NAV_OBJECT" => $nav,
            "SEF_MODE" => "N",
        ),
        false
    );
}

function showRow($player, $rank)
{
    $cntMatches = isset($player["count_matches"]) ? $player["count_matches"] : '..';
    $kills = isset($player["kills"]) ? $player["kills"] : '..'; ?>

    <div class="flex-table--row">
        <span>
            <div class="core-team__user">
                <div class="core-team__user-avatar"
                     <?php if (!empty($player["PERSONAL_PHOTO"])) { ?>
                         style="background-image: url(<?php echo CFile::GetPath($player["PERSONAL_PHOTO"]); ?>)"
                     <?php } else { ?>
                         style="background-image: url(<?php echo SITE_TEMPLATE_PATH; ?>/dist/images/default-avatar.svg)"
                     <?php } ?>>
                </div>
                <a href="<?=SITE_DIR?>players/<?= $player["ID"] . '_' . $player["LOGIN"] . '/'; ?>"
                   class="core-team__user-link" target="_blank">
                    <?= $player["LOGIN"]; ?>
                </a>
            </div>
        </span>
        <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('PLAYERS_RANK')?></div>
                  <?php echo $rank; ?>
                </span>
        <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('PLAYERS_RATING')?></div>
                  <?php echo $player["total"]; ?>
                </span>
        <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('PLAYERS_KILLS')?></div>
                  <?php echo $kills; ?>
                </span>
        <span class="core-team__param-wrap">
                  <div class="core-team__param"><?=GetMessage('PLAYERS_MATCHES')?></div>
                  <?php echo $cntMatches; ?>
                </span>
    </div>

<?php }


//dump(getPlayersList());
?>
<?php /*
$path = explode('/', trim($APPLICATION->GetCurPage()));

if(isset($path[2]) && trim($path[2]) != '') {
	$arUser = getUser(trim($path[2]));
	//dump($arUser);
	$points = countPointsByUserID( $arUser['ID'] );
	?>


	<section class="profile">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-11 col-md-12">
					<div class="profile__avatar-bg">
						<div class="profile__avatar"
							<?php if (!empty($arUser["PERSONAL_PHOTO"])) { ?>
								style="background-image: url(<?php echo CFile::GetPath($arUser["PERSONAL_PHOTO"]); ?>)"
							<?php } else { ?>
								style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
							<?php } ?>>
							<div class="profile__avatar-rating-bg">
								<div class="profile__avatar-rating">
                    <?php if(!$arUser['UF_RATING']) { ?>
                      300
                    <?php } else { ?>
                        <?php echo $arUser['UF_RATING'];?>
                    <?php } ?>
                </div>
							</div>
						</div>
					</div>
					<div class="profile-info">
						<div class="row justify-content-center">

							<div class="profile-info__item">
								<div class="profile-info__nic">
									<span><?php echo $arUser["LOGIN"]; ?></span>
									<div class="profile-info__element">
										<i>
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><defs><style>.cls-1{fill:#100b2e;stroke:#ff0052;stroke-width:2px;}.cls-2{fill:#ff0052;}</style></defs><circle class="cls-1" cx="11" cy="11" r="10"/><path class="cls-2" d="M959.62,535.52a.64.64,0,0,1,1.21,0l.82,2.49a.65.65,0,0,0,.61.44h2.67a.62.62,0,0,1,.37,1.13l-2.16,1.54a.64.64,0,0,0-.23.7l.83,2.5a.63.63,0,0,1-1,.7l-2.16-1.54a.65.65,0,0,0-.75,0L957.69,545a.63.63,0,0,1-1-.7l.82-2.5a.62.62,0,0,0-.23-.7l-2.16-1.54a.62.62,0,0,1,.38-1.13h2.67a.63.63,0,0,0,.6-.44Z" transform="translate(-949.22 -529.43)"/></svg>
										</i>
									</div>
								</div>
							</div>

						</div>
						<div class="row">

							<div class="col-6 col-md-3">
								<div class="profile-info__next-item">
									<div>Мое настроение</div>
									<div><?php echo $arUser["TITLE"]; ?></div>
								</div>
							</div>
							<div class="col-6 col-md-3">
								<div class="profile-info__next-item">
									<div>Команда</div>
									<div><?php
										if( $arUser['UF_ID_TEAM']+0 ){
											$team = getTeamById($arUser['UF_ID_TEAM']+0);		
											echo '<a href="/teams/'.$team['ID'].'/">'.$team['NAME'].'</a>';
										}
									?></div>
								</div>
							</div>

							<div class="col-6 col-md-3">
								<div class="profile-info__next-item">
									<div>Язык общения</div>
									<div>-</div>
								</div>
							</div>
							<div class="col-6 col-md-3">
								<div class="profile-info__next-item">
									<div>Активность в игре</div>
									<div>-</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php } else {
	$players = getUsers();

    $arRating = [];
    foreach($players as $items){
        //dump($items["PROPERTIES"]["RATING"]['VALUE']);
        $arRating[$items["ID"]] = $items["UF_RATING"];
    }
    //dump($arRating);
	$points = countPointsAllUsers();
	//dump($points);
	?>
	<section class="py-10">

	<div class="container">
			<h1 class="core-team__heading">Игроки</h1>
	</div>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-10 col-md-12">
      <div class="layout__content-anons text-center">
		<a class="btn btn_auth" href="/teams/">Список команд</a>
		<a class="btn btn_auth  btn_border">Список игроков</a>
      </div>
    </div>
  </div>
</div>


		<div class="container">
			<div class="core-team">
				<div class="flex-table">
					<div class="flex-table--header bg-default">
						<div class="flex-table--categories">
							<span>Игрок</span>
							<span>Ранк</span>
							<span>Рейтинг</span>
							<span>Киллы</span>
							<span>Количество игр</span>
						</div>
					</div>
					<div class="flex-table--body">
                        <?php

                        $tmp = [];
                        $gamers = [];
                        foreach( $players as $v ) {
                            $tmp[$v['ID']] = $v['LOGIN'];
                            $gamers[$v['ID']] = [ 'login' => $v['LOGIN'],
                                                  'photo' => $v["PERSONAL_PHOTO"]];
                        }
                        //dump($gamers);
                        asort( $tmp );

                        $places = [];
                        foreach( $points as $k=>$v ){
                            $places[$k] = empty($arRating[$k]) ? 300 + $v['total'] : $arRating[$k] + $v['total'];
                            //dump($places[$v['teamID']]);
                            unset( $tmp[$k] );
                        }
                        //dump($points);
                        foreach( $tmp as $k=>$v ){
                            // dump($points[ $k ]);
                            $points[ $k ] = [ 'total' => 300 ];
                            $places[ $k ] = 300;
                        }
                        //////////////////////////
                        arsort($places);
                        //dump($places);
                        ?>
						<?php
                        $rank = 0;
                        foreach ($places as $k=>$item) {
							$rank+=1;
							$cntMatches = '..';
							$kills = '..';
							$total = '..';
							if( isset($points[$k]) ){
								$cntMatches = ceil($points[$k]['count_matches']);
								$kills = ceil($points[$k]['kills']);
								$total = $item;
							}
							
							?>
							<div class="flex-table--row">
                <span>
                  <div class="core-team__user">
                    <div class="core-team__user-avatar"
                         <?php if (!empty($gamers[$k]["photo"])) { ?>
							 style="background-image: url(<?php echo CFile::GetPath($gamers[$k]["photo"]); ?>)"
						 <?php } else { ?>
							 style="background-image: url(<?php echo SITE_TEMPLATE_PATH;?>/dist/images/default-avatar.svg)"
						 <?php } ?>>
                    </div>
                    <a href="/players/<?php echo $k.'_'.$gamers[$k]["login"].'/';?>" class="core-team__user-link"><?php echo $gamers[$k]["login"];?></a>
                  </div>
                </span>
								<span class="core-team__param-wrap">
                  <div class="core-team__param">Ранк</div>
                  <?php echo $rank; ?>
                </span>
								<span class="core-team__param-wrap">
                  <div class="core-team__param">Рейтинг</div>
                  <?php echo $total; ?>
                </span>
								<span class="core-team__param-wrap">
                  <div class="core-team__param">Киллы</div>
                  <?php echo $kills; ?>
                </span>
								<span class="core-team__param-wrap">
                  <div class="core-team__param">Количество игр</div>
                  <?php echo $cntMatches; ?>
                </span>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php } ?>
*/ ?>

<?php
$path = explode('/', trim($APPLICATION->GetCurPage()) );

if (isset($path[2]) && (LANGUAGE_ID == 'ru') ? trim($path[2]) : trim($path[3]) != '') {
    $arUser = (LANGUAGE_ID == 'ru') ? getUser(trim($path[2])) : getUser(trim($path[3]));
    //dump($arUser);
    $points = countPointsByUserID($arUser['ID']);
    ?>

    <section class="profile">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11 col-md-12">
                    <div class="profile__avatar-bg">
                        <div class="profile__avatar"
                            <?php if (!empty($arUser["PERSONAL_PHOTO"])) { ?>
                                style="background-image: url(<?php echo CFile::GetPath($arUser["PERSONAL_PHOTO"]); ?>)"
                            <?php } else { ?>
                                style="background-image: url(<?php echo SITE_TEMPLATE_PATH; ?>/dist/images/default-avatar.svg)"
                            <?php } ?>>
                            <div class="profile__avatar-rating-bg">
                                <div class="profile__avatar-rating">
                                    <?php if (!$arUser['UF_RATING']) { ?>
                                        300
                                    <?php } else { ?>
                                        <?php echo $arUser['UF_RATING']; ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profile-info">
                        <div class="row justify-content-center">

                            <div class="profile-info__item">
                                <div class="profile-info__nic">
                                    <span><?php echo $arUser["LOGIN"]; ?></span>
                                    <div class="profile-info__element">
                                        <i>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                                <defs>
                                                    <style>.cls-1 {
                                                            fill: #100b2e;
                                                            stroke: #ff0052;
                                                            stroke-width: 2px;
                                                        }

                                                        .cls-2 {
                                                            fill: #ff0052;
                                                        }</style>
                                                </defs>
                                                <circle class="cls-1" cx="11" cy="11" r="10"/>
                                                <path class="cls-2"
                                                      d="M959.62,535.52a.64.64,0,0,1,1.21,0l.82,2.49a.65.65,0,0,0,.61.44h2.67a.62.62,0,0,1,.37,1.13l-2.16,1.54a.64.64,0,0,0-.23.7l.83,2.5a.63.63,0,0,1-1,.7l-2.16-1.54a.65.65,0,0,0-.75,0L957.69,545a.63.63,0,0,1-1-.7l.82-2.5a.62.62,0,0,0-.23-.7l-2.16-1.54a.62.62,0,0,1,.38-1.13h2.67a.63.63,0,0,0,.6-.44Z"
                                                      transform="translate(-949.22 -529.43)"/>
                                            </svg>
                                        </i>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-6 col-md-3">
                                <div class="profile-info__next-item">
                                    <div><?=GetMessage('PLAYERS_MOOD')?></div>
                                    <div><?php echo $arUser["TITLE"]; ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="profile-info__next-item">
                                    <div><?=GetMessage('PLAYERS_TEAM')?></div>
                                    <div><?php
                                        if ($arUser['UF_ID_TEAM'] + 0) {
                                            $team = getTeamById($arUser['UF_ID_TEAM'] + 0);
                                            echo '<a href="'.SITE_DIR.'teams/'.$team['ID'].'/">'.$team['NAME'].'</a>';
                                        }
                                        ?></div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="profile-info__next-item">
                                    <div><?=GetMessage('PLAYERS_LANG')?></div>
                                    <div>-</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="profile-info__next-item">
                                    <div><?=GetMessage('PLAYERS_ACTIVITY')?></div>
                                    <div>-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<? } else { ?>

    <section class="py-10">
        <div class="container">
            <h1 class="core-team__heading"><?=GetMessage('PLAYERS_TITLE')?></h1>
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="layout__content-anons text-center">
                        <a class="btn btn_auth" href="<?=SITE_DIR?>teams/"><?=GetMessage('PLAYERS_LIST_TEAMS')?></a>
                        <a class="btn btn_auth btn_border"><?=GetMessage('PLAYERS_LIST_PLAYERS')?></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="core-team">
                <div class="flex-table">
                    <div class="flex-table--header bg-default">
                        <div class="flex-table--categories">
                            <span><?=GetMessage('PLAYERS_TABLE_PLAYER')?></span>
                            <span><?=GetMessage('PLAYERS_TABLE_RANK')?></span>
                            <span><?=GetMessage('PLAYERS_TABLE_RATING')?></span>
                            <span><?=GetMessage('PLAYERS_TABLE_KILLS')?></span>
                            <span><?=GetMessage('PLAYERS_TABLE_TOTAL')?></span>
                        </div>
                    </div>
                    <div class="flex-table--body">
                        <?php showPlayers(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php } ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>