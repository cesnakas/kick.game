<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подписка");
?>
  <div class="container">
    <h1 class="text-center">Подписка</h1>
    <div class="row justify-content-center">
      <div class="col-lg-10 col-md-12">
        <div class="subscription-plans__description text-center">
          Базовая подписка позволяет играть в кастомки, смотреть трансляции игр и играть в открытых квартальных турнирах. Чтобы играть в праки, участвовать в ежемесячных турнирах на 1000€ и иметь доступ к личной статистике, нужно приобрести любой вариант платной подписки. Это так, потому что у нас на платформе нет рекламы, и подписка - единственный источник ресурсов, чтобы развивать платформу дальше
        </div>
      </div>
    </div>
    <div id="accordion" role="tablist" class="subscription-plans">
      <div class="row">
        <div class="col-lg-3">
          <div class="card card-custom active">
            <div class="card-custom-heading" id="accordionHeading1" role="tab">
              <div class="card-custom-title">
                <a role="button" data-toggle="collapse"  href="#accordionCollapse1" aria-controls="accordionCollapse1">
                  <div class="subscription-plans-item">
                    <div class="subscription-plans-item__icon">
                      <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/plan-basic.svg" alt="basic">
                    </div>
                    <div class="subscription-plans-item__name">
                      Базовая подписка
                    </div>
                    <div class="subscription-plans-item__heading">
                      БЕСПЛАТНО
                    </div>
                    <div class="subscription-plans-item__sub-heading">

                    </div>
                  </div>
                </a>
              </div>
            </div>
            <div class="card-custom-collapse collapse show" id="accordionCollapse1" role="tabpanel" aria-labelledby="accordionHeading1" data-parent="#accordion">
              <div class="card-custom-body">
                <ul>
                  <li>Участие в кастомках</li>
                  <li>Поиск игроков и команд</li>
                  <li>Создание и управление командой</li>
                  <li>Полная статистика игроков</li>
                  <li>Полная статистика команд</li>
                  <li>Просмотр трансляций</li>
                  <li>Участие в квартальном турнире от 10000 €</li>
                  <li class="not">Участие в практических играх</li>
                  <li class="not">Участие в ежемесячном турнире от 1000 €</li>
                  <li class="not">Участие в еженедельном турнире от  100 €</li>
                  <li class="not">Участие в играх со стримами и коммента- торами </li>
                  <li class="not">Мой личный рейтинг </li>
                  <li class="not">Рейтинг моей команды </li>
                </ul>
                <div class="subscription-plans-item__btn text-center" style="opacity: 0;">
                  <a href="#" class="btn">Купить подписку</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="card card-custom">
            <div class="card-custom-heading" id="accordionHeading2" role="tab">
              <div class="card-custom-title">
                <a class="collapsed" role="button" data-toggle="collapse" href="#accordionCollapse2" aria-controls="accordionCollapse2">
                  <div class="subscription-plans-item">
                    <div class="subscription-plans-item__icon">
                      <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/plan-standart.svg" alt="basic">
                    </div>
                    <div class="subscription-plans-item__name">
                      Стандарт
                    </div>
                    <div class="subscription-plans-item__heading">
                      € 3,99<span>/мес</span>
                    </div>
                    <div class="subscription-plans-item__sub-heading">

                    </div>
                  </div>
                </a>
              </div>
            </div>
            <div class="card-custom-collapse collapse" id="accordionCollapse2" data-parent="#accordion"  role="tabpanel" aria-labelledby="accordionHeading2">
              <div class="card-custom-body">
                <ul>
                  <li>Участие в кастомках</li>
                  <li>Поиск игроков и команд</li>
                  <li>Создание и управление командой</li>
                  <li>Полная статистика игроков</li>
                  <li>Полная статистика команд</li>
                  <li>Просмотр трансляций</li>
                  <li>Участие в квартальном турнире от 10000 €</li>
                  <li>Участие в практических играх</li>
                  <li>Участие в ежемесячном турнире от 1000 €</li>
                  <li>Участие в еженедельном турнире от  100 €</li>
                  <li>Участие в играх со стримами и коммента- торами </li>
                  <li>Мой личный рейтинг </li>
                  <li>Рейтинг моей команды </li>
                </ul>
                <div class="subscription-plans-item__btn text-center">
                  <a href="#" class="btn">Купить подписку</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="card card-custom">
            <div class="card-custom-heading" id="accordionHeading3" role="tab">
              <div class="card-custom-title">
                <a class="collapsed" role="button" data-toggle="collapse"  href="#accordionCollapse3" aria-controls="accordionCollapse3">
                  <div class="subscription-plans-item">
                    <div class="subscription-plans-item__icon">
                      <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/plan-premium.svg" alt="basic">
                    </div>
                    <div class="subscription-plans-item__name">
                      Премиум
                    </div>
                    <div class="subscription-plans-item__heading">
                      € 3,59<span>/мес</span>
                    </div>
                    <div class="subscription-plans-item__sub-heading">
                      (Действительно при покупке на 6 мес)
                    </div>
                  </div>
                </a>
              </div>
            </div>
            <div class="card-custom-collapse collapse" id="accordionCollapse3" data-parent="#accordion" role="tabpanel" aria-labelledby="accordionHeading3">
              <div class="card-custom-body">
                <ul>
                  <li>Участие в кастомках</li>
                  <li>Поиск игроков и команд</li>
                  <li>Создание и управление командой</li>
                  <li>Полная статистика игроков</li>
                  <li>Полная статистика команд</li>
                  <li>Просмотр трансляций</li>
                  <li>Участие в квартальном турнире от 10000 €</li>
                  <li>Участие в практических играх</li>
                  <li>Участие в ежемесячном турнире от 1000 €</li>
                  <li>Участие в еженедельном турнире от  100 €</li>
                  <li>Участие в играх со стримами и коммента- торами </li>
                  <li>Мой личный рейтинг </li>
                  <li>Рейтинг моей команды </li>
                </ul>
                <div class="subscription-plans-item__btn text-center">
                  <a href="#" class="btn">Купить подписку</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="card card-custom">
            <div class="card-custom-heading" id="accordionHeading4" role="tab">
              <div class="card-custom-title">
                <a class="collapsed" role="button" data-toggle="collapse"  href="#accordionCollapse4" aria-controls="accordionCollapse4">
                  <div class="subscription-plans-item">
                    <div class="subscription-plans-item__icon">
                      <img src="<?php echo SITE_TEMPLATE_PATH;?>/dist/images/plan-elit.svg" alt="basic">
                    </div>
                    <div class="subscription-plans-item__name">
                      Элайт
                    </div>
                    <div class="subscription-plans-item__heading">
                      € 3,39<span>/мес</span>
                    </div>
                    <div class="subscription-plans-item__sub-heading">
                      (Действительно при покупке на 12 мес)
                    </div>
                  </div>
                </a>
              </div>
            </div>
            <div class="card-custom-collapse collapse" id="accordionCollapse4" data-parent="#accordion" role="tabpanel" aria-labelledby="accordionHeading4">
              <div class="card-custom-body">
                <ul>
                  <li>Участие в кастомках</li>
                  <li>Поиск игроков и команд</li>
                  <li>Создание и управление командой</li>
                  <li>Полная статистика игроков</li>
                  <li>Полная статистика команд</li>
                  <li>Просмотр трансляций</li>
                  <li>Участие в квартальном турнире от 10000 €</li>
                  <li>Участие в практических играх</li>
                  <li>Участие в ежемесячном турнире от 1000 €</li>
                  <li>Участие в еженедельном турнире от  100 €</li>
                  <li>Участие в играх со стримами и коммента- торами </li>
                  <li>Мой личный рейтинг </li>
                  <li>Рейтинг моей команды </li>
                </ul>
                <div class="subscription-plans-item__btn text-center">
                  <a href="#" class="btn">Купить подписку</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>