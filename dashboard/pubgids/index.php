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


$strSql = 'SELECT  t.VALUE_ID, t.UF_PUBG_ID, t.UF_PUBG_ID_VERIFIED, b.LOGIN, b.NAME, b.DATE_REGISTER
            FROM b_uts_user AS t
            INNER JOIN b_user AS b ON b.ID = t.VALUE_ID
            WHERE t.UF_PUBG_ID IS NOT NULL 
                AND ( t.UF_PUBG_ID_VERIFIED = "0" OR t.UF_PUBG_ID_VERIFIED IS NULL)
            ORDER BY t.VALUE_ID DESC';
$res = $DB->Query($strSql);
$members = [];
while ($row = $res->Fetch()) {
    $members[$row['VALUE_ID']] = $row;
}
?>
<div class="container py-5">
    <h1 class="mb-3 text-center">PUBG ID VERIFIED</h1>
    <div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Player</th>
            <th scope="col">PUBG ID</th>
            <th scope="col">DATE REG</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach( $members as $userID=>$data ){ ?>
        <tr>
            <th scope="row"><?php echo $userID;?></th>
            <td><a href="/bitrix/admin/user_edit.php?lang=ru&ID=<?php echo $userID; ?>" target="_blank"><?php echo $data['LOGIN']; ?>(<?php echo !empty($data['NAME']) ? $data['NAME'] : '';?>)</a></td>
            <td><?php echo $data['UF_PUBG_ID']; ?></td>
            <td><?php echo $data['DATE_REGISTER']; ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    </div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>