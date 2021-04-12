<?php
/**
 *  
 * Вывод списка каскадов матчей(игр), в которых 
 * будет участвовать данный пользователь платформы 
 * 
 * */

//require_once($_SERVER["DOCUMENT_ROOT"] . "/personal/mygames.php");
if( !function_exists('p') ){function p($o){echo '<pre>'.print_r($o,1).'</pre>'; }}
?>
<?php

$userID = $GLOBALS['USER']->GetID() + 0;
$rsUser = CUser::GetByID($userID)->Fetch();
$myTeamID = $rsUser['UF_ID_TEAM']+0;
//p( $userID );
//p( $myTeamID );

function getFutureMatchesByUser( $userID, $matchType = 0 ){
    GLOBAL $DB;
    // b_iblock_element_prop_s6 - iblock squad fields
    $subSql = 'SELECT p.PROPERTY_27 AS matchID
                FROM b_iblock_element_prop_s6 AS p 
                WHERE 
                    (
                            p.PROPERTY_24 = '.$userID.'
                        OR  p.PROPERTY_25 = '.$userID.'
                        OR  p.PROPERTY_26 = '.$userID.'
                        OR  p.PROPERTY_45 = '.$userID.'
                        OR  p.PROPERTY_46 = '.$userID.'
                        OR  p.PROPERTY_47 = '.$userID.'
                    ) 
                        AND p.PROPERTY_27 IS NOT NULL';
    // b_iblock_element_prop_s3 - iblock matches
    $strSql = 'SELECT   m.IBLOCK_ELEMENT_ID AS matchID 
                        ,m.PROPERTY_8 AS parentMatchID
                        ,m.PROPERTY_6 AS lobbyID
                        ,m.PROPERTY_4 AS dateStart
                        ,m.PROPERTY_22 AS stageMatch
                        ,m.PROPERTY_23 AS typeMatch
                        ,pp.matchID AS squadMatchID
                FROM b_iblock_element_prop_s3 AS m 
                LEFT JOIN ('.$subSql.') AS pp ON pp.matchID = m.IBLOCK_ELEMENT_ID
                WHERE m.PROPERTY_4 > NOW()
                ORDER BY m.IBLOCK_ELEMENT_ID';

    $res = $DB->Query($strSql);
    $matches = [];
    while ($row = $res->Fetch()) {
        $matches[ $row['matchID'] ] = $row;
    }

    if( count($matches) ){
        for( $i = 0; $i<10; $i++ ){
            foreach( $matches AS $k=>$v ){
                if( $v['parentMatchID']+0 ){
                    $matches[ $v['parentMatchID']+0 ]['nextMatch'] = $v;
                }
            }
        }

        foreach( $matches AS $k=>$v ){
            if( $v['parentMatchID']+0 || !$v['squadMatchID']+0 ){
                unset( $matches[$k] );
            }
        }
        //dump($matches);
    }
    return $matches;
}
if(!function_exists('getParticipationByMatchIdMyMatches')) {
function getParticipationByMatchIdMyMatches($idMatch)
{
    $arSelect = Array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_*",
    );
    $arFilter = Array(
        "IBLOCK_ID" =>4,
        "PROPERTY_WHICH_MATCH" => $idMatch,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $arrTeams = [];
    $key = [
        3 => "TEAM_PLACE_03",
        4 => "TEAM_PLACE_04",
        5 => "TEAM_PLACE_05",
        6 => "TEAM_PLACE_06",
        7 => "TEAM_PLACE_07",
        8 => "TEAM_PLACE_08",
        9 => "TEAM_PLACE_09",
        10 => "TEAM_PLACE_10",
        11 => "TEAM_PLACE_11",
        12 => "TEAM_PLACE_12",
        13 => "TEAM_PLACE_13",
        14 => "TEAM_PLACE_14",
        15 => "TEAM_PLACE_15",
        16 => "TEAM_PLACE_16",
        17 => "TEAM_PLACE_17",
        18 => "TEAM_PLACE_18",
        19 => "TEAM_PLACE_19",
        20 => "TEAM_PLACE_20",
    ];
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        foreach ($key as $place=>$name) {
            if ($arProps[$name]['VALUE']+0 > 0) {
                $arrTeams[$place] = $arProps[$name]['VALUE'];
            }
        }
        return $arrTeams;
    }
    return  false;
}
}
$matches = getFutureMatchesByUser( $userID );

    function getMatchByIdWhitCode($matchId) {
        $arSelect = Array(
                "ID",
                "CODE",
            "NAME",
            "DATE_ACTIVE_FROM",
            "PROPERTY_*"
        );//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
        $arFilter = Array("IBLOCK_ID" =>3, "ID" => $matchId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        if ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            return $arFields;
        }
        return null;
    }

if( count($matches) ){ ?>
  <style>
    .game-schedule__my-games .flex-table--body {
        margin-bottom: 30px;
    }
  </style>
  <section class="game-schedule bg-blue-lighter game-schedule__my-games">
    <div class="container">
      <h2 class="game-schedule__heading text-center">Мои ближайшие игры</h2>
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


<?php    foreach ($matches as $k=>$v) {
        $tmp = getParticipationByMatchIdMyMatches($v['matchID']);
        $tmp  = array_flip($tmp);
        $matches[$k]['place'] = $tmp[$myTeamID];
        $matches[$k]['url'] = '#';
       $url = getMatchByIdWhitCode($v['matchID']);
        $matches[$k]['url'] = $url['CODE'];
    }

function viewMatchNext($match) {
  ?>
  <div class="flex-table--row">
              <span>
                <div class="game-schedule__type-game">
                  <?php  if( $match['typeMatch'] == 6 ){ ?>
                    <div class="game-schedule__icon-type-game game-schedule__icon-type-game_prac">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.6 22.8"><path d="M963.34,529.28h-7.7l-4.4,12.48h6.6v8.32l11-13.52h-7.7Z" transform="translate(-950.24 -528.28)"/></svg>
                  </div>
                    <div class="color-practical">Праки</div>
                  <?php  } else { ?>
                    <div class="game-schedule__icon-type-game game-schedule__icon-type-game_tournament">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.67 21.2">
                      <path d="M676.21,374.4H689v7.68a6.41,6.41,0,0,1-6.4,6.4h0a6.4,6.4,0,0,1-6.4-6.4Z" transform="translate(-671.27 -373.4)"/>
                      <path d="M689,377h1.3a2.42,2.42,0,0,1,2.57,2.83c-.42,1.8-1.43,3.86-3.87,4.21" transform="translate(-671.27 -373.4)"/>
                      <path d="M676.21,377H674.9a2.42,2.42,0,0,0-2.57,2.83c.42,1.8,1.44,3.86,3.88,4.21" transform="translate(-671.27 -373.4)"/>
                      <path d="M682.61,388.48v5.12" transform="translate(-671.27 -373.4)"/>
                      <path d="M678.77,393.6h7.68" transform="translate(-671.27 -373.4)"/>
                    </svg>
                  </div>
                    <div class="color-tournament">Турнир</div>
                  <?php  } ?>
                </div>
              </span>
    <span>
      <?php if( $match['lobbyID']+0 ){
          echo '<div class="yb">Лобби ID: '.$match['lobbyID'].'</div> <div>Pass: kick</div>';
      } else {
          echo '<div class="gr">LobbyID: формируется</div>';
      } ?>

              </span>
    <span class="game-schedule__param-wrap">
                <div class="game-schedule__param">Дата проведения</div>
                  <?php
                  $dateTime = explode(' ', $match['dateStart']);
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
                    <div>x2</div>
                  </div>
              </span>
    <span class="game-schedule__param-wrap">
                <div class="game-schedule__param">Комментатор</div>
                Юрий Дудь
              </span>
  </div>
  <?
    if( isset($match['nextMatch']) ){
        viewMatchNext( $match['nextMatch'] );
    }
}
function viewMatch( $match ){
        if($match['parentMatchID']+0 == 0) {
            //echo '<div>SLOT №: '.$match['place'].'</div>';
           // echo '<div>ULR: '.$match['url'].'</div>';
        } ?>
  <div class="flex-table--body">
  <div class="flex-table--row">
              <span>
                <div class="game-schedule__type-game">
                  <?php  if( $match['typeMatch'] == 6 ){ ?>
                    <div class="game-schedule__icon-type-game game-schedule__icon-type-game_prac">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.6 22.8"><path d="M963.34,529.28h-7.7l-4.4,12.48h6.6v8.32l11-13.52h-7.7Z" transform="translate(-950.24 -528.28)"/></svg>
                  </div>
                    <div class="color-practical">Праки</div>
                  <?php  } else { ?>
                    <div class="game-schedule__icon-type-game game-schedule__icon-type-game_tournament">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.67 21.2">
                      <path d="M676.21,374.4H689v7.68a6.41,6.41,0,0,1-6.4,6.4h0a6.4,6.4,0,0,1-6.4-6.4Z" transform="translate(-671.27 -373.4)"/>
                      <path d="M689,377h1.3a2.42,2.42,0,0,1,2.57,2.83c-.42,1.8-1.43,3.86-3.87,4.21" transform="translate(-671.27 -373.4)"/>
                      <path d="M676.21,377H674.9a2.42,2.42,0,0,0-2.57,2.83c.42,1.8,1.44,3.86,3.88,4.21" transform="translate(-671.27 -373.4)"/>
                      <path d="M682.61,388.48v5.12" transform="translate(-671.27 -373.4)"/>
                      <path d="M678.77,393.6h7.68" transform="translate(-671.27 -373.4)"/>
                    </svg>
                  </div>
                    <div class="color-tournament">Турнир</div>
                  <?php  } ?>
                  <div class="game-schedule__participation-label">Слот №<?php echo $match['place'];?></div>
                </div>
              </span>
              <span>
                <?php if( $match['lobbyID']+0 ){
                    echo '<div class="yb">Лобби ID: '.$match['lobbyID'].'</div> <div>Pass: kick</div>';
                } else {
                    echo '<div class="gr">LobbyID: формируется</div>';
                } ?>
                <a href="game.html" class="game-schedule__link">

                </a>
              </span>
    <span class="game-schedule__param-wrap">
                <div class="game-schedule__param">Дата проведения</div>
                  <?php
                  $dateTime = explode(' ', $match['dateStart']);
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
                    <div>x2</div>
                  </div>
              </span>
    <span class="game-schedule__param-wrap">
                <div class="game-schedule__param">Комментатор</div>
                Юрий Дудь
              </span>
  </div>
    <?php //echo '<div>ID: '.$match['matchID'].'</div>';
    //echo '<div>Начало: '.$match['dateStart'].'</div>';

    if( isset($match['nextMatch']) ){
        viewMatchNext( $match['nextMatch'] );
    }?>
  </div>
    <?php
}

?>

<?
//p( $matches );
$cnt = 0;
foreach( $matches AS $game ){
    viewMatch( $game );
  ?>

    <?/*echo '<div class="col-lg-3 col-md-3">';
    if( $game['typeMatch'] == 6 ){
        echo '<h6>PRAC</h6>';
    } else {
        echo '<h6>TURN</h6>';
    }
    viewMatch( $game );
    echo '</div>';
    if( (++$cnt)%4 == 0 ){
        echo '</div><hr><br><br><div class="row justify-content-center">';
    }*/?>

<?php } ?>
        </div>
      </div>
    </div>
  </section>
<?php 
} /* end count $matches */
?>
