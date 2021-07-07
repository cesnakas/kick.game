<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetPageProperty("TITLE", "KICKGAME");
$APPLICATION->SetTitle("Главная");
?>

    <section class="section__header">
        <div class="container">

            <div class="row">
                <div class="col-lg-6 offset-lg-1">

                    <h1 class="display-1">KICKGAME</h1>
                    <p class="lead">Твой пропуск в киберспорт, пабгер</p>

                    <div class="row row-cols-xl-2">
                        <figure class="figure">
                            <img src="/local/templates/new_main/images/duo@2x.png" class="img-fluid" alt="...">
                            <figcaption class="figure-caption">Бесплатные праки <br class="d-none d-xl-block"> каждый день</figcaption>
                        </figure>
                        <figure class="figure">
                            <img src="/local/templates/new_main/images/squad@2x.png" class="img-fluid" alt="...">
                            <figcaption class="figure-caption">Турниры для сквадов <br> с призовым фондом <br class="d-none d-xl-block"> от 1000 €</figcaption>
                        </figure>
                        <figure class="figure">
                            <img src="/local/templates/new_main/images/customs@2x.png" class="img-fluid" alt="...">
                            <figcaption class="figure-caption">Рейтинговые игры, <br> которые гарантируют <br> игру с равными соперниками</figcaption>
                        </figure>
                        <figure class="figure">
                            <img src="/local/templates/new_main/images/free@2x.png" class="img-fluid" alt="...">
                            <figcaption class="figure-caption">Бесплатная <br> регистрация на праки <br> открывается в 12:00 МСК в день игры</figcaption>
                        </figure>
                    </div>

                    <!--<ul class="list-unstyled">
                        <li>Бесплатные праки каждый день</li>
                        <li>Турниры для сквадов с призовым фондом от 1000 €</li>
                        <li>Рейтинговые игры, которые гарантируют игру с равными соперниками</li>
                        <li>Бесплатная регистрация на праки открывается в 12:00 МСК в день игры</li>
                    </ul>-->
                    <div>
                        <button class="btn btn-warning">ПРОФИЛЬ</button>
                        <span class="ms-lg-3">Начни путь к победе сегодня!</span>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <section class="section__advantages">
        <div class="container">
            <h3>Играя с нами, ты сможешь</h3>
            <div>
                <div>Прокачаться</div>
                <p>тренироваться с tier 1 - tier 3 командами, анализировать свои результаты по готовым записям игр и совершенствовать навыки</p>
            </div>
            <div>
                <div>Стать первым</div>
                <p>в рейтингах игроков и команд, ежедневно соревнуясь с разными соперниками в практических играх</p>
            </div>
            <div>
                <div>Победить в турнире</div>
                <p>или нескольких, и забрать часть призового фонда размером в 1000€ или 10000€</p>
            </div>
        </div>
    </section>

    <section class="section__games">
        <div class="container">
            <h3>Игры</h3>
        </div>
    </section>

<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>