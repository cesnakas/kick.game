<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetPageProperty("TITLE", "KICKGAME");
$APPLICATION->SetTitle("Главная");
?>

    <section>
        <div class="container">
            <h1 class="text-center text-white">Твой пропуск <br> в киберспорт, пабгер</h1>
            <img class="w-100" src="<?=SITE_TEMPLATE_PATH?>/dist/images/frame.png" alt="">
        </div>
    </section>

<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>