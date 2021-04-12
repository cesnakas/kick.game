<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Referee");
?>
<div class="container">
    <div class="row my-5">
        <div class="col-6 text-center">
            <a class="btn btn-lg btn-warning" href="/referee/prakticheskiy/">Практический</a>
        </div>
        <div class="col-6 text-center">
            <a class="btn btn-lg btn-warning" href="/referee/tournament/">Турнирный</a>
        </div>
    </div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>