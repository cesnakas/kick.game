<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */
/** @var PageNavigationComponent $component */
$component = $this->getComponent();
$this->setFrameMode(true);?>

<?$plus = $arResult['CURRENT_PAGE']+1;
	$url = "/management-compositional/account-history/?page=page-".$plus;
	$c=$arResult['RECORD_COUNT']-$arResult['PAGE_SIZE'];
	if($arResult['RECORD_COUNT'] >$arResult['PAGE_SIZE'] ):?>
	<div class="btn-italic load-more-items btn-italic " data-url="<?=$url?>" data-count="<?=$c?>"  data-page="<?=$arResult['PAGE_SIZE']?>"><?=GetMessage('SHOW_MORE')?></div>
	
	   
<?endif  ?>

<?//print_r($arResult);?>





