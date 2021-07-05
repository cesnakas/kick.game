<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetPageProperty("TITLE", "KICKGAME");
$APPLICATION->SetTitle("Главная");
?>

    <section>
        <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/header_bg.jpg" alt="" style="position:absolute;top:0;z-index:-1;width:100%;height:100%;object-fit:cover;object-position:center;">
        <div class="container">
            <h1 class="text-center text-white">Твой пропуск <br> в киберспорт, пабгер</h1>
        </div>
    </section>

<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>