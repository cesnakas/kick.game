<?
$userID = CUser::GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$teamID = $arUser['UF_ID_TEAM'];
$IsCapitan='';
$arResult['IS_CAPITAN1']=getTeamById($teamID);
if ($teamID) {
    if ($arResult['IS_CAPITAN1']['AUTHOR']["VALUE"] == $userID) {
        $IsCapitan='true';
    } else {
        $IsCapitan='false';
    }
}
$arResult['IS_CAPITAN']=$IsCapitan;
?>
