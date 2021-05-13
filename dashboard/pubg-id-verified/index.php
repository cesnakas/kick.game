<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Pubg Id Verified");

function getUsersWhoSendPubgId()
{
    $filter = array(
        //"DATE_REGISTER_1" => date('d.m.Y H:i:s', $s1),
        //"DATE_REGISTER_2" => date('d.m.Y H:i:s', $s2),
        /* date register
        "DATE_REGISTER_1" => date('21.03.2021 23:59:00'),
        "DATE_REGISTER_2" => date('29.03.2021 23:59:00'),
        */
        'UF_PUBG_ID_CHECK' => 20,
        "ACTIVE" => 'Y',
    );
    $arParams["SELECT"] = array("UF_*");
    $elementsResult = CUser::GetList(($by="DATE_REGISTER"), ($order="DESC"), $filter, $arParams);
    $users = [];
    while ($rsUser = $elementsResult->Fetch())
    {
        $users[] = $rsUser;
        //updateUserPrem($rsUser["ID"], 10);
        //echo $rsUser["ID"] . $rsUser["LOGIN"] . " - " . $rsUser["UF_DATE_PREM_EXP"] . "<br>";
    }
    return $users;
}
function getUsersWhoSendPubgIdYet()
{
  $filter = array(
    //"DATE_REGISTER_1" => date('d.m.Y H:i:s', $s1),
    //"DATE_REGISTER_2" => date('d.m.Y H:i:s', $s2),
    /* date register
    "DATE_REGISTER_1" => date('21.03.2021 23:59:00'),
    "DATE_REGISTER_2" => date('29.03.2021 23:59:00'),
    */
    'UF_PUBG_ID_CHECK' => 22,
    "ACTIVE" => 'Y',
  );
  $arParams["SELECT"] = array("UF_*");
  $elementsResult = CUser::GetList(($by="DATE_REGISTER"), ($order="DESC"), $filter, $arParams);
  $users = [];
  while ($rsUser = $elementsResult->Fetch())
  {
    $users[] = $rsUser;
    //updateUserPrem($rsUser["ID"], 10);
    //echo $rsUser["ID"] . $rsUser["LOGIN"] . " - " . $rsUser["UF_DATE_PREM_EXP"] . "<br>";
  }
  return $users;
}
if (isset($_REQUEST['acceptPubgId']) && check_bitrix_sessid()) {
  updateStatusChekingPubgId($_POST['acceptPubgId']+0, 24);
  $alertPubgId = 'Пользователь успешно прошел проверку';
  createSession('pubgid_success', $alertPubgId);
  LocalRedirect('');
} else if(isset($_REQUEST['rejected']) && check_bitrix_sessid()) {
  $reason = trim(strip_tags($_POST['rejectedReason']));
  addReasonRejected($_POST['rejected']+0, $reason);
  updateStatusChekingPubgId($_POST['rejected']+0, 23);
  $alertPubgId = 'Пользователь не прошел проверку';
  createSession('pubgid_success', $alertPubgId);
  LocalRedirect('');
}
$usersNew = getUsersWhoSendPubgId();
$usersYet = getUsersWhoSendPubgIdYet();
?>
  <style>
    .table td, .table th {
      vertical-align: inherit;
    }
  </style>
    <div class="container py-5">
      <?php

      if(isset($_SESSION['pubgid_success'])) { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php echo $_SESSION['pubgid_success'];?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <?php
      }
      unset($_SESSION['pubgid_success']);
      ?>

      <h1 class="mb-3 text-center">Pubg Id Verified</h1>
      <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Новые <span class="badge badge-light"><?php echo count($usersNew);?></span></a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Повторная проверка <span class="badge badge-light"><?php echo count($usersYet);?></span></a>
        </li>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
          <div class="table-responsive">
          <table class="table table-striped table-dark">
            <thead>
            <tr>
              <th scope="col">UserID</th>
              <th scope="col">Nickname</th>
              <th scope="col">Pubg Id</th>
              <th scope="col">Screenshot</th>
              <th scope="col">Date Register</th>
              <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
          <?php foreach ($usersNew as $user) {
            //dump($user);
            ?>
            <tr>
              <th scope="row"><?php echo $user['ID'];?></th>
              <td><?php echo $user['LOGIN'];?></td>
              <td><?php echo $user['UF_PUBG_ID'];?></td>
              <td><a href="<?php echo CFile::GetPath($user["WORK_LOGO"]); ?>" data-lity><img src="<?php echo CFile::GetPath($user["WORK_LOGO"]); ?>" width="100" alt=""></a></td>
              <td><?php echo $user['DATE_REGISTER'];?></td>
              <td style="max-width: 200px">
                <br>
                <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                  <?=bitrix_sessid_post()?>
                  <button type="submit" name="acceptPubgId" value="<?php echo $user['ID'];?>" class="btn btn-success btn-sm">Принять</button>
                </form>
                <br>
                <p>Укажите причину отклонения:</p>
                <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                  <?=bitrix_sessid_post()?>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="rejectedReason" id="exampleRadios1" value="на твоем скриншоте не видны данные pubg id и/или nickname" checked>
                    <label class="form-check-label" for="exampleRadios1">
                      на твоем скриншоте не видны данные pubg id и/или nickname
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="rejectedReason" id="exampleRadios2" value="pubg id и/или nickname не соответствует указанным у тебя в профиле">
                    <label class="form-check-label" for="exampleRadios2">
                      pubg id и/или nickname не соответствует указанным у тебя в профиле
                    </label>
                  </div>
                  <br>
                  <button type="submit" name="rejected" class="btn btn-danger btn-sm" value="<?php echo $user['ID'];?>">Отклонить</button>
                </form>
                <br>
              </td>
            </tr>
          <?php } ?>
            </tbody>
          </table>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
          <div class="table-responsive">
            <table class="table table-striped table-dark">
              <thead>
              <tr>
                <th scope="col">UserID</th>
                <th scope="col">Nickname</th>
                <th scope="col">Pubg Id</th>
                <th scope="col">Screenshot</th>
                <th scope="col">Date Register</th>
                <th scope="col">Comments</th>
                <th scope="col">Action</th>
              </tr>
              </thead>
              <tbody>
              <?php foreach ($usersYet as $user) {
                //dump($user);
                ?>
                <tr>
                  <th scope="row"><?php echo $user['ID'];?></th>
                  <td><?php echo $user['LOGIN'];?></td>
                  <td><?php echo $user['UF_PUBG_ID'];?></td>
                  <td><a href="<?php echo CFile::GetPath($user["WORK_LOGO"]); ?>" data-lity><img src="<?php echo CFile::GetPath($user["WORK_LOGO"]); ?>" width="100" alt=""></a></td>
                  <td><?php echo $user['DATE_REGISTER'];?></td>
                  <td style="max-width: 200px"><?php echo $user['WORK_NOTES'];?></td>
                  <td style="max-width: 200px">
                    <br>
                    <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                      <?=bitrix_sessid_post()?>
                      <button type="submit" name="acceptPubgId" value="<?php echo $user['ID'];?>" class="btn btn-success btn-sm">Принять</button>
                    </form>
                    <br>
                    <p>Укажите причину отклонения:</p>
                    <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                      <?=bitrix_sessid_post()?>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="rejectedReason" id="exampleRadios1" value="на твоем скриншоте не видны данные pubg id и/или nickname" checked>
                        <label class="form-check-label" for="exampleRadios1">
                          на твоем скриншоте не видны данные pubg id и/или nickname
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="rejectedReason" id="exampleRadios2" value="pubg id и/или nickname не соответствует указанным у тебя в профиле">
                        <label class="form-check-label" for="exampleRadios2">
                          pubg id и/или nickname не соответствует указанным у тебя в профиле
                        </label>
                      </div>
                      <br>
                      <button type="submit" name="rejected" class="btn btn-danger btn-sm" value="<?php echo $user['ID'];?>">Отклонить</button>
                    </form>
                    <br>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

<?php /*
function getUsersWhoSendPubgId()
{
    $filter = array(
        'UF_PUBG_ID_CHECK' => 20,
        "ACTIVE" => 'Y',
    );
    $arParams["SELECT"] = array("UF_*");
   return CUser::GetList(($by="DATE_REGISTER"), ($order="DESC"), $filter, $arParams);
}



function getUsersWhoSendPubgIdYet()
{
  $filter = array(
    'UF_PUBG_ID_CHECK' => 22,
    "ACTIVE" => 'Y',
  );
  $arParams["SELECT"] = array("UF_*");

    return CUser::GetList(($by="DATE_REGISTER"), ($order="DESC"), $filter, $arParams);
}
//dump($_GET);
$userWhoSendResults = getUsersWhoSendPubgId();
$userWhoSendResults->NavStart(1, false);
$navStrWhoSendResults = $userWhoSendResults->GetPageNavStringEx($navComponentObject, "Страницы:", "round");

$userWhoSendResultsYet = getUsersWhoSendPubgIdYet();
$userWhoSendResultsYet->NavStart(1, false);
$navStrWhoSendResultsYet = $userWhoSendResultsYet->GetPageNavStringEx($navComponentObject, "Страницы:", "round");

if (isset($_REQUEST['acceptPubgId']) && check_bitrix_sessid()) {
  updateStatusChekingPubgId($_POST['acceptPubgId']+0, 24);
  $alertPubgId = 'Пользователь успешно прошел проверку';
  createSession('pubgid_success', $alertPubgId);
  LocalRedirect('');
} else if(isset($_REQUEST['rejected']) && check_bitrix_sessid()) {
  $reason = trim(strip_tags($_POST['rejectedReason']));
  addReasonRejected($_POST['rejected']+0, $reason);
  updateStatusChekingPubgId($_POST['rejected']+0, 23);
  $alertPubgId = 'Пользователь не прошел проверку';
  createSession('pubgid_success', $alertPubgId);
  LocalRedirect('');
}
//$usersNew = getUsersWhoSendPubgId();
//$usersYet = getUsersWhoSendPubgIdYet();
?>
  <style>
    .table td, .table th {
      vertical-align: inherit;
    }
  </style>
    <div class="container py-5">
      <?php

      if(isset($_SESSION['pubgid_success'])) { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php echo $_SESSION['pubgid_success'];?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <?php
      }
      unset($_SESSION['pubgid_success']);
      ?>

      <h1 class="mb-3 text-center">Pubg Id Verified</h1>
      <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Новые <span class="badge badge-light"><?php echo $userWhoSendResults->result->num_rows;?></span></a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Повторная проверка <span class="badge badge-light"><?php echo $userWhoSendResultsYet->result->num_rows;?></span></a>
        </li>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
          <div class="table-responsive">
          <table class="table table-striped table-dark">
            <thead>
            <tr>
              <th scope="col">UserID</th>
              <th scope="col">Nickname</th>
              <th scope="col">Pubg Id</th>
              <th scope="col">Screenshot</th>
              <th scope="col">Date Register</th>
              <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
          <?php while ($user = $userWhoSendResults->Fetch())
        {
            //dump($user);
            ?>
            <tr>
              <th scope="row"><?php echo $user['ID'];?></th>
              <td><?php echo $user['LOGIN'];?></td>
              <td><?php echo $user['UF_PUBG_ID'];?></td>
              <td><a href="<?php echo CFile::GetPath($user["WORK_LOGO"]); ?>" data-lity><img src="<?php echo CFile::GetPath($user["WORK_LOGO"]); ?>" width="100" alt=""></a></td>
              <td><?php echo $user['DATE_REGISTER'];?></td>
              <td style="max-width: 200px">
                <br>
                <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                  <?=bitrix_sessid_post()?>
                  <button type="submit" name="acceptPubgId" value="<?php echo $user['ID'];?>" class="btn btn-success btn-sm">Принять</button>
                </form>
                <br>
                <p>Укажите причину отклонения:</p>
                <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                  <?=bitrix_sessid_post()?>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="rejectedReason" id="exampleRadios1" value="на твоем скриншоте не видны данные pubg id и/или nickname" checked>
                    <label class="form-check-label" for="exampleRadios1">
                      на твоем скриншоте не видны данные pubg id и/или nickname
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="rejectedReason" id="exampleRadios2" value="pubg id и/или nickname не соответствует указанным у тебя в профиле">
                    <label class="form-check-label" for="exampleRadios2">
                      pubg id и/или nickname не соответствует указанным у тебя в профиле
                    </label>
                  </div>
                  <br>
                  <button type="submit" name="rejected" class="btn btn-danger btn-sm" value="<?php echo $user['ID'];?>">Отклонить</button>
                </form>
                <br>
              </td>
            </tr>
          <?php } ?>
            </tbody>
          </table>
          </div>
          <?php echo $navStrWhoSendResults;?>
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
          <div class="table-responsive">
            <table class="table table-striped table-dark">
              <thead>
              <tr>
                <th scope="col">UserID</th>
                <th scope="col">Nickname</th>
                <th scope="col">Pubg Id</th>
                <th scope="col">Screenshot</th>
                <th scope="col">Date Register</th>
                <th scope="col">Comments</th>
                <th scope="col">Action</th>
              </tr>
              </thead>
              <tbody>
              <?php while ($user = $userWhoSendResultsYet->Fetch()) {
                //dump($user);
                ?>
                <tr>
                  <th scope="row"><?php echo $user['ID'];?></th>
                  <td><?php echo $user['LOGIN'];?></td>
                  <td><?php echo $user['UF_PUBG_ID'];?></td>
                  <td><a href="<?php echo CFile::GetPath($user["WORK_LOGO"]); ?>" data-lity><img src="<?php echo CFile::GetPath($user["WORK_LOGO"]); ?>" width="100" alt=""></a></td>
                  <td><?php echo $user['DATE_REGISTER'];?></td>
                  <td style="max-width: 200px"><?php echo $user['WORK_NOTES'];?></td>
                  <td style="max-width: 200px">
                    <br>
                    <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                      <?=bitrix_sessid_post()?>
                      <button type="submit" name="acceptPubgId" value="<?php echo $user['ID'];?>" class="btn btn-success btn-sm">Принять</button>
                    </form>
                    <br>
                    <p>Укажите причину отклонения:</p>
                    <form action="<?= POST_FORM_ACTION_URI; ?>" method="post">
                      <?=bitrix_sessid_post()?>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="rejectedReason" id="exampleRadios1" value="на твоем скриншоте не видны данные pubg id и/или nickname" checked>
                        <label class="form-check-label" for="exampleRadios1">
                          на твоем скриншоте не видны данные pubg id и/или nickname
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="rejectedReason" id="exampleRadios2" value="pubg id и/или nickname не соответствует указанным у тебя в профиле">
                        <label class="form-check-label" for="exampleRadios2">
                          pubg id и/или nickname не соответствует указанным у тебя в профиле
                        </label>
                      </div>
                      <br>
                      <button type="submit" name="rejected" class="btn btn-danger btn-sm" value="<?php echo $user['ID'];?>">Отклонить</button>
                    </form>
                    <br>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
            <?php echo $navStrWhoSendResultsYet;?>
        </div>
      </div>
    </div>*/?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>