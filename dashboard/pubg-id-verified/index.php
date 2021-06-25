<?
require($_SERVER["DOCUMENT_ROOT"].'/bitrix/header.php');

function getUsersWhoSendPubgId() {
    $filter = [
        'UF_PUBG_ID_CHECK' => 20,
        'ACTIVE' => 'Y',
    ];
    $arParams['SELECT'] = ['UF_*'];
    $elementsResult = CUser::GetList(($by='DATE_REGISTER'), ($order='DESC'), $filter, $arParams);
    $users = [];
    while ($rsUser = $elementsResult->Fetch()) {
        $users[] = $rsUser;
    }
    return $users;
}
function getUsersWhoSendPubgIdYet() {
    $filter = [
        'UF_PUBG_ID_CHECK' => 22,
        'ACTIVE' => 'Y',
    ];
    $arParams['SELECT'] = ['UF_*'];
    $elementsResult = CUser::GetList(($by='DATE_REGISTER'), ($order='DESC'), $filter, $arParams);
    $users = [];
    while ($rsUser = $elementsResult->Fetch()) {
        $users[] = $rsUser;
    }
    return $users;
}

if (isset($_REQUEST['acceptPubgId']) && check_bitrix_sessid()) {
    updateStatusChekingPubgId($_POST['acceptPubgId'] + 0, 24);
    $alertPubgId = 'Пользователь успешно прошел проверку';
    createSession('pubgid_success', $alertPubgId);
    LocalRedirect('');
} else if(isset($_REQUEST['rejected']) && check_bitrix_sessid()) {
    $reason = trim(strip_tags($_POST['rejectedReason']));
    addReasonRejected($_POST['rejected']+0, $reason);
    updateStatusChekingPubgId($_POST['rejected'] + 0, 23);
    $alertPubgId = 'Пользователь не прошел проверку';
    createSession('pubgid_success', $alertPubgId);
    LocalRedirect('');
}
$usersNew = getUsersWhoSendPubgId();
$usersYet = getUsersWhoSendPubgIdYet();

$rsUsersNew = new CDBResult;
$rsUsersNew->InitFromArray($usersNew);
$rsUsersNew->NavStart(5);

$rsUsersYet = new CDBResult;
$rsUsersYet->InitFromArray($usersYet);
$rsUsersYet->NavStart(5);
?>

<style>
    body {
        background-color: #100b2e;
    }
    footer {
        height: 0 !important;
    }
    /* tabs */
    .nav-tabs {
        border-bottom-color: transparent;
    }
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        color: var(--light);
    }
    .nav-tabs .nav-link:focus,
    .nav-tabs .nav-link:hover {
        border-color: transparent;
    }
    .nav-tabs .nav-link.active {
        border-color: transparent;
        color: var(--white);
        background-color: var(--dark);
    }
    /* table head */
    .table thead th {
        vertical-align: middle;
        text-align: center;
    }
    /* table */
    .table td, .table th {
        vertical-align: middle;
    }
</style>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    $(function() {
        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('lastTab', $(this).attr('href'));
        });
        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            $('[href="' + lastTab + '"]').tab('show');
        }
    });
</script>

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
    </div>

    <div class="container-fluid">

        <h1 class="mb-5 text-center text-white">Pubg Id Verified</h1>

        <ul class="nav nav-tabs nav-justified" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
                    Новые
                    <span class="badge badge-pill badge-warning ml-1"><?=count($usersNew);?></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">
                    Повторная проверка
                    <span class="badge badge-pill badge-warning ml-1"><?=count($usersYet);?></span>
                </a>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="table-responsive">
                    <? if($rsUsersNew->IsNavPrint()): ?>
                    <table class="table table-striped table-dark table-bordered">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col" width="80">UserID</th>
                            <th scope="col">Nickname</th>
                            <th scope="col" width="100">Pubg Id</th>
                            <th scope="col">Screenshot</th>
                            <th scope="col" width="100">Date Register</th>
                            <th scope="col" width="260">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? while ($user = $rsUsersNew->Fetch()): ?>
                        <tr>
                            <th scope="row"><?=$user['ID'];?></th>
                            <td><?=$user['LOGIN'];?></td>
                            <td><?=$user['UF_PUBG_ID'];?></td>
                            <td class="text-center">
                                <a href="<?php echo CFile::GetPath($user['WORK_LOGO']); ?>" data-lity>
                                    <img src="<?php echo CFile::GetPath($user['WORK_LOGO']); ?>" style="max-width:220px;max-height:110px;-webkit-object-fit:contain;object-fit:contain;">
                                </a>
                            </td>
                            <td><?=$user['DATE_REGISTER'];?></td>
                            <td class="text-right" style="max-width:200px;">

                                <form action="<?=POST_FORM_ACTION_URI;?>" method="post" class="mb-3">
                                    <?=bitrix_sessid_post()?>
                                    <button type="submit" name="acceptPubgId" value="<?=$user['ID'];?>" class="btn btn-sm btn-success">Принять</button>
                                </form>

                                <p class="mb-1">Укажите причину отклонения:</p>

                                <form action="<?=POST_FORM_ACTION_URI;?>" method="post">
                                    <?=bitrix_sessid_post()?>
                                    <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-secondary active">
                                            <input type="radio" name="rejectedReason" id="exampleRadios1" value="на твоем скриншоте не видны данные pubg id и/или nickname" checked>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor">
                                                <use href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#bi-camera" xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#bi-camera"></use>
                                            </svg>
                                            <span class="badge badge-pill badge-info ml-2" data-toggle="tooltip" title="На твоем скриншоте не видны данные pubg id и/или nickname">?</span>
                                        </label>
                                        <label class="btn btn-outline-secondary">
                                            <input type="radio" name="rejectedReason" id="exampleRadios2" value="pubg id и/или nickname не соответствует указанным у тебя в профиле">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor">
                                                <use href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#bi-person-bounding-box" xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#bi-person-bounding-box"></use>
                                            </svg>
                                            <span class="badge badge-pill badge-info ml-2" data-toggle="tooltip" title="Pubg id и/или nickname не соответствует указанным у тебя в профиле">?</span>
                                        </label>
                                        <button type="submit" name="rejected" class="btn btn-sm btn-danger" value="<?=$user['ID'];?>">
                                            Отклонить
                                        </button>
                                    </div>
                                </form>

                            </td>
                        </tr>
                        <? endwhile; ?>
                        </tbody>
                    </table>
                    <?=$rsUsersNew->GetPageNavStringEx($navComponentObject, 'Страницы', 'round');?>
                    <? endif; ?>
                </div>
            </div>

            <?/* // */?>

            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                <div class="table-responsive">
                    <?/* if($rsUsersYet->IsNavPrint()): */?>
                    <table class="table table-striped table-bordered table-dark">
                        <thead>
                        <tr>
                            <th scope="col" width="80">UserID</th>
                            <th scope="col" width="200">Nickname</th>
                            <th scope="col" width="100">Pubg Id</th>
                            <th scope="col">Screenshot</th>
                            <th scope="col" width="100">Date Register</th>
                            <th scope="col">Comments</th>
                            <th scope="col" width="260">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? while ($user = $rsUsersYet->Fetch()): ?>
                        <tr>
                            <th scope="row"><?=$user['ID'];?></th>
                            <td><?=$user['LOGIN'];?></td>
                            <td><?=$user['UF_PUBG_ID'];?></td>
                            <td class="text-center">
                                <a href="<?php echo CFile::GetPath($user["WORK_LOGO"]); ?>" data-lity>
                                    <img src="<?php echo CFile::GetPath($user["WORK_LOGO"]); ?>" style="max-width:50px;max-height:30px;">
                                </a>
                            </td>
                            <td>
                                <div><?=$user['DATE_REGISTER'];?></div>
                            </td>
                            <td>
                                <div style="display:flex;overflow:auto;max-height:135px;">
                                    <?=$user['WORK_NOTES'];?>
                                </div>
                            </td>
                            <td class="text-right">
                                <form action="<?=POST_FORM_ACTION_URI;?>" method="post" class="mb-3">
                                    <?=bitrix_sessid_post()?>
                                    <button type="submit" name="acceptPubgId" value="<?=$user['ID'];?>" class="btn btn-sm btn-success -btn-block">Принять</button>
                                </form>

                                <p class="mb-1">Укажите причину отклонения:</p>

                                <form action="<?=POST_FORM_ACTION_URI;?>" method="post">
                                    <?=bitrix_sessid_post()?>
                                    <div class="btn-group btn-group-sm btn-group-toggle -btn-block" data-toggle="buttons">
                                        <label class="btn btn-outline-secondary active">
                                            <input type="radio" name="rejectedReason" id="exampleRadios1" value="на твоем скриншоте не видны данные pubg id и/или nickname" checked>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor">
                                                <use href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#bi-camera" xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#bi-camera"></use>
                                            </svg>
                                            <span class="badge badge-pill badge-info ml-2" data-toggle="tooltip" title="На твоем скриншоте не видны данные pubg id и/или nickname">?</span>
                                        </label>
                                        <label class="btn btn-outline-secondary">
                                            <input type="radio" name="rejectedReason" id="exampleRadios2" value="pubg id и/или nickname не соответствует указанным у тебя в профиле">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor">
                                                <use href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#bi-person-bounding-box" xlink:href="<?=SITE_TEMPLATE_PATH?>/dist/img/icons.svg#bi-person-bounding-box"></use>
                                            </svg>
                                            <span class="badge badge-pill badge-info ml-2" data-toggle="tooltip" title="Pubg id и/или nickname не соответствует указанным у тебя в профиле">?</span>
                                        </label>
                                        <button type="submit" name="rejected" class="btn btn-sm btn-danger" value="<?=$user['ID'];?>">
                                            Отклонить
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <? endwhile; ?>
                        </tbody>
                    </table>
                    <?=$rsUsersYet->GetPageNavStringEx($navComponentObject, 'Страницы:', 'round');?>
                    <?/* endif; */?>
                </div>
            </div>
        </div>

    </div>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>