<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetPageProperty("TITLE", "KICKGAME");
$APPLICATION->SetTitle("Главная");
?>

    <section class="section__header">
        <div class="container">

            <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/header-bg.png" alt="...">
                        <div class="carousel-caption">
                            <h5 class="display-3">ТРЕНИРУЙСЯ</h5>
                            <p>Бесплатные праки каждый день и <br> рейтинговые игры — играй с равными!</p>
                            <button class="btn btn-warning">Зарегистрироваться</button>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/header-bg.png" alt="...">
                        <div class="carousel-caption">
                            <h5 class="display-3">ТРЕНИРУЙСЯ</h5>
                            <p>Бесплатные праки каждый день и <br> рейтинговые игры — играй с равными!</p>
                            <button class="btn btn-warning">Зарегистрироваться</button>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/header-bg.png" alt="...">
                        <div class="carousel-caption">
                            <h5 class="display-3">ТРЕНИРУЙСЯ</h5>
                            <p>Бесплатные праки каждый день и <br> рейтинговые игры — играй с равными!</p>
                            <button class="btn btn-warning">Зарегистрироваться</button>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
            </div>

        </div>
    </section>

    <?/*
    <section class="section__header">
        <div class="container">

            <div class="row">
                <div class="col-lg-6 offset-lg-1">

                    <h1 class="display-1">KICKGAME</h1>
                    <p class="lead">Твой пропуск в киберспорт, пабгер</p>

                    <div class="row row-cols-xl-2">
                        <figure class="figure">
                            <img src="/local/templates/new_main/images/duo@2x.png" class="img-fluid" alt="Бесплатные праки каждый день">
                            <figcaption class="figure-caption">Бесплатные праки <br class="d-none d-xl-block"> каждый день</figcaption>
                        </figure>
                        <figure class="figure">
                            <img src="/local/templates/new_main/images/squad@2x.png" class="img-fluid" alt="Турниры для сквадов с призовым фондом от 1000 €">
                            <figcaption class="figure-caption">Турниры для сквадов <br> с призовым фондом <br class="d-none d-xl-block"> от 1000 €</figcaption>
                        </figure>
                        <figure class="figure">
                            <img src="/local/templates/new_main/images/customs@2x.png" class="img-fluid" alt="Рейтинговые игры, которые гарантируют игру с равными соперниками">
                            <figcaption class="figure-caption">Рейтинговые игры, <br> которые гарантируют <br> игру с равными соперниками</figcaption>
                        </figure>
                        <figure class="figure">
                            <img src="/local/templates/new_main/images/free@2x.png" class="img-fluid" alt="Бесплатная регистрация на праки открывается в 12:00 МСК в день игры">
                            <figcaption class="figure-caption">Бесплатная <br> регистрация на праки <br> открывается в 12:00 МСК в день игры</figcaption>
                        </figure>
                    </div>

                    <div>
                        <button class="btn btn-warning">ПРОФИЛЬ</button>
                        <span class="ms-lg-3">Начни путь к победе сегодня!</span>
                    </div>

                </div>
            </div>

        </div>
    </section>
    */?>

    <section class="section__advantages">
        <div class="container">

            <h3 class="display-4 text-center">Играя с нами, ты сможешь</h3>
            <div class="row">
                <div class="col-lg">
                    <div>Прокачаться</div>
                    <p>тренироваться с tier 1 - tier 3 командами, анализировать свои результаты по готовым записям игр и совершенствовать навыки</p>
                </div>
                <div class="col-lg">
                    <div>Стать первым</div>
                    <p>в рейтингах игроков и команд, ежедневно соревнуясь с разными соперниками в практических играх</p>
                </div>
                <div class="col-lg">
                    <div>Победить в турнире</div>
                    <p>или нескольких, и забрать часть призового фонда размером в 1000€ или 10000€</p>
                </div>
            </div>
            <div>
                <button class="btn btn-warning">Войти</button>
            </div>

        </div>
    </section>

    <section class="section__games">
        <div class="container">
            <h3 class="display-4">Игры</h3>
            <button class="btn btn-warning">Поиск</button>
        </div>
    </section>

    <section class="section__about">
        <div class="container">
            <h3 class="display-4">О платформе</h3>
        </div>
    </section>

    <section class="section__functions">
        <div class="container">
            <h3 class="display-4">Функции</h3>
        </div>
    </section>

<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>