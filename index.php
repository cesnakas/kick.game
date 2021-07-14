<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetPageProperty("TITLE", "KICKGAME");
$APPLICATION->SetTitle("Главная");
?>

    <section class="section__header">
        <div class="container-xl">

            <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/header-bg.png" alt="...">
                        <div class="carousel-caption">
                            <h5 class="display-3">ТРЕНИРУЙСЯ</h5>
                            <p>Бесплатные праки каждый день и <br> рейтинговые игры — играй с равными!</p>
                            <br>
                            <button class="btn btn-warning">Зарегистрироваться</button>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/header-bg.png" alt="...">
                        <div class="carousel-caption">
                            <h5 class="display-3">ТРЕНИРУЙСЯ</h5>
                            <p>Бесплатные праки каждый день и <br> рейтинговые игры — играй с равными!</p>
                            <br>
                            <button class="btn btn-warning">Зарегистрироваться</button>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/header-bg.png" alt="...">
                        <div class="carousel-caption">
                            <h5 class="display-3">ТРЕНИРУЙСЯ</h5>
                            <p>Бесплатные праки каждый день и <br> рейтинговые игры — играй с равными!</p>
                            <br>
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

    <section class="section__advantages section__games">
        <div class="container-xl">

            <h3 class="display-6">РЕКОМЕНДУЕМЫЕ <br> БЛИЖАЙШИЕ ИГРЫ</h3>

            <div class="row row-cols-1 row-cols-lg-3 g-4">

                <div class="col col-xl-6">
                    <!-- card scrims -->
                    <div class="card">

                        <div class="position-relative d-xl-none">
                            <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/games-a.png" class="card-img-top" alt="...">
                            <div class="card-img-overlay">
                                <div class="card__top">
                                    <div class="badge-scrims">Кастомки</div>
                                    <div class="badge-rating">1200 - 3600</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body d-xl-none">
                            <a class="card-link" href="#">Kickgame Scrims GROUP C</a>
                            <div class="card__bottom">
                                <div class="card__bottom-bar row row-cols-2 row-cols-xl-auto g-3">
                                    <div class="col card__bottom-item">
                                        <div class="card__bottom-title">Режим</div>
                                        <div>Squad</div>
                                    </div>
                                    <div class="col card__bottom-item">
                                        <div class="card__bottom-title">Свободно мест</div>
                                        <div class="seats">
                                            <div><span class="text-success">16 </span>/ 18</div>
                                        </div>
                                    </div>
                                    <div class="col card__bottom-item">
                                        <div class="card__bottom-title">Призовой фонд</div>
                                        <div class="d-flex align-items-center">
                                            <div class="coins-win">1500</div>
                                            <div class="info"></div>
                                        </div>
                                    </div>
                                    <div class="col card__bottom-item">
                                        <div class="card__bottom-title">Участие</div>
                                        <div class="d-flex align-items-center">
                                            <div class="coins-chick">500</div>
                                            <div class="info"></div>
                                        </div>
                                    </div>
                                    <div class="col card__bottom-item">
                                        <div class="card__bottom-title">Рейтинг</div>
                                        <div>350</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/games-a.png" class="card-img d-none d-xl-block" alt="...">

                        <div class="card-img-overlay card-img-blur d-none d-xl-flex">

                            <div class="card__top">
                                <div class="badge-scrims">Кастомки</div>
                                <div class="badge-rating">1200 - 3600</div>
                            </div>

                            <div class="card__bottom">
                                <a class="card-link" href="#">Kickgame Scrims GROUP C</a>
                                <div class="card__bottom-bar row row-cols-2 row-cols-xl-auto g-3">
                                    <div class="col card__bottom-item">
                                        <div class="card__bottom-title">Режим</div>
                                        <div>Squad</div>
                                    </div>
                                    <div class="col card__bottom-item">
                                        <div class="card__bottom-title">Свободно мест</div>
                                        <div class="seats">
                                            <div><span class="text-success">16 </span>/ 18</div>
                                        </div>
                                    </div>
                                    <div class="col card__bottom-item">
                                        <div class="card__bottom-title">Призовой фонд</div>
                                        <div class="d-flex align-items-center">
                                            <div class="coins-win">1500</div>
                                            <div class="info"></div>
                                        </div>
                                    </div>
                                    <div class="col card__bottom-item">
                                        <div class="card__bottom-title">Участие</div>
                                        <div class="d-flex align-items-center">
                                            <div class="coins-chick">500</div>
                                            <div class="info"></div>
                                        </div>
                                    </div>
                                    <div class="col card__bottom-item">
                                        <div class="card__bottom-title">Рейтинг</div>
                                        <div>350</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- /card scrims -->
                </div>

                <div class="col col-xl-3">
                    <div class="card">
                        <div class="position-relative">
                            <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/games-b.png" class="card-img-top" alt="...">
                            <div class="card-img-overlay">
                                <div class="card__top">
                                    <div class="badge-tour">Турнир</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            <a class="card-link" href="#">Kickgame Scrims GROUP A</a>

                            <div class="card__bottom-bar row row-cols-2 g-3">
                                <div class="col card__bottom-item">
                                    <div class="card__bottom-title">Режим</div>
                                    <div>Squad</div>
                                </div>
                                <div class="col card__bottom-item">
                                    <div class="card__bottom-title">Статус</div>
                                    <div>Идёт регистрация</div>
                                </div>
                                <div class="col card__bottom-item">
                                    <div class="card__bottom-title">Призовой фонд</div>
                                    <div class="d-flex align-items-center">
                                        <div class="coins-win">1000</div>
                                        <div class="info"></div>
                                    </div>
                                </div>
                                <div class="col card__bottom-item">
                                    <div class="card__bottom-title">Участие</div>
                                    <div class="d-flex align-items-center">
                                        <div class="coins-chick">500</div>
                                        <div class="info"></div>
                                    </div>
                                </div>
                                <div class="col card__bottom-item">
                                    <div class="card__bottom-title">Даты провения</div>
                                    <div>23.08.21-28.08.21</div>
                                </div>
                                <div class="col card__bottom-item">
                                    <div class="card__bottom-title">Рейтинг</div>
                                    <div>>350</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col col-xl-3">
                    <div class="card">
                        <div class="position-relative">
                            <img src="<?=SITE_TEMPLATE_PATH?>/dist/images/games-c.png" class="card-img-top" alt="...">
                            <div class="card-img-overlay">
                                <div class="card__top">
                                    <div class="badge-prac">Праки</div>
                                    <div class="badge-rating">500 - 800</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <a class="card-link" href="#">Kickgame Scrims GROUP B</a>
                            <div class="card__bottom-bar row row-cols-2 g-3">
                                <div class="col card__bottom-item">
                                    <div class="card__bottom-title">Режим</div>
                                    <div>Squad</div>
                                </div>
                                <div class="col card__bottom-item">
                                    <div class="card__bottom-title">Свободно мест</div>
                                    <div class="seats">
                                        <div><span class="text-success">16 </span>/ 18</div>
                                    </div>
                                </div>
                                <div class="col card__bottom-item">
                                    <div class="card__bottom-title">Призовой фонд</div>
                                    <div>Отсутствует</div>
                                </div>
                                <div class="col card__bottom-item">
                                    <div class="card__bottom-title">Участие</div>
                                    <div>Бесплатно</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="d-grid col-lg-3 mx-auto py-5">
                <button class="btn btn-outline-warning">Смотреть расписание игр</button>
            </div>

        </div>
    </section>

    <section>
        <div class="container-xl">

        </div>
    </section>

    <section>
        <div class="container-xl">

        </div>
    </section>

<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>