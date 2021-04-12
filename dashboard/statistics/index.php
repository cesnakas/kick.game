<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Statistics");
function getCoreTeam($teamID)
{
    $filter = Array("GROUPS_ID" => Array(7), ["UF_ID_TEAM" => $teamID]);
    $arParams["SELECT"] = array("UF_*");
    $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
    $output = [];
    while ($rsUser = $elementsResult->Fetch())
    {
        $output[] = $rsUser;
    }
    return $output;
}

function getRecruitTeam($teamID)
{
    $filter = Array("GROUPS_ID" => Array(7), ["UF_REQUEST_ID_TEAM" => $teamID]);
    $arParams["SELECT"] = array("UF_*");
    $elementsResult = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
    $output = [];
    while ($rsUser = $elementsResult->Fetch())
    {
        $output[] = $rsUser;
    }
    return $output;
}
$strSql = 'SELECT  t.UF_REQUEST_ID_TEAM AS TEAM, count(t.UF_REQUEST_ID_TEAM) AS MEMBERS
            FROM b_uts_user AS t
            WHERE t.UF_REQUEST_ID_TEAM IS NOT NULL 
            GROUP BY t.UF_REQUEST_ID_TEAM 
            ORDER BY  t.UF_REQUEST_ID_TEAM';
$res = $DB->Query($strSql);
//dump($res, 1);
$reqruits = [];
while ($row = $res->Fetch()) {
    $reqruits[$row['TEAM']] = [
            'MEMBERS' => $row['MEMBERS']
    ];
}
$strSql = 'SELECT  t.UF_ID_TEAM AS TEAM, count(t.UF_ID_TEAM) AS MEMBERS
            FROM b_uts_user AS t
            WHERE t.UF_ID_TEAM IS NOT NULL 
            GROUP BY t.UF_ID_TEAM 
            ORDER BY  t.UF_ID_TEAM';
$res = $DB->Query($strSql);
$members = [];
while ($row = $res->Fetch()) {
    $members[$row['TEAM']] = [
        'MEMBERS' => $row['MEMBERS'],
        'REQRUITS' => isset($reqruits[$row['TEAM']])? $reqruits[$row['TEAM']]['MEMBERS'] : 0
    ];
}
//dump($members);
?>
    <div class="container py-5">
        <h1 class="mb-3 text-center">TEAM</h1>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#team ID</th>
                    <th scope="col">MEMBERS</th>
                    <th scope="col">REQUESTS</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach( $members as $teamID=>$counts ){ ?>
                    <tr>
                        <th scope="row"><?php echo $teamID;?></th>
                        <td><?php echo $counts['MEMBERS']; ?></td>
                        <td><?php echo $counts['REQRUITS']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>