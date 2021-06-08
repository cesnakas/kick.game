<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
global $DB;
$teamID = intval($_GET['teamID']);



$sql="SELECT u.ID as userID, u.LOGIN as login FROM b_user as u
INNER JOIN b_uts_user as f ON f.VALUE_ID = u.ID
WHERE f.UF_ID_TEAM = " . $teamID;

$rsData = $DB->Query($sql);



    echo "
            <div class='table-responsive'>
               <table class='table table-striped table-sm table-hover '>
                  <thead class ='table-primary'>
                     <tr>
                         <th scope='col'>Выбрать</th>
                         <th scope='col'>Никнейм</th>
                     </tr>
                  </thead>
                  <tbody>";

                    while($player = $rsData->fetch()) {

                       echo " <tr>
                                    <th scope='row' style='width:10%;'>
                                        <input type='checkbox' name='formedSquad[]' value='{$player["userID"]}'>
                                    </th>
                                     <td>{$player["login"]}</td>
                              </tr> ";
                    }
echo "            </tbody>
               </table>
            </div>
            <input type='submit' class='btn btn-success' name='btn_invite' value='Записать на матч'>
          ";



