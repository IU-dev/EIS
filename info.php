<?php

$msg = '';
require_once 'includes/global.inc.php';
$page = "myclass.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

if (!isset($_GET['uid'])) {
    http_response_code(400);
    die('<div class="alert alert-danger"><strong><h3>Error 400: Bad Request</h3></strong><hr>Отсутствует параметр "UID" в запросе</div>');
} else {
    $uid = $_GET['uid'];
    $usr = $userTools->get($uid);
}

if (isset($_POST['submit-makewrite'])) {
    $data['from_id'] = "'" . $user->id . "'";
    $data['to_id'] = "'" . $usr->id . "'";
    $data['datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
    $data['text'] = "'" . $_POST['record'] . "'";
    $not = $db->insert($data, 'notes');
    $msg = '<script type="text/javascript">toastr.success("Внесена запись NOT-' . $not . '", "Успешно!");</script>';
    $usr = $userTools->get($uid);
}

if (isset($_POST['submit-editinfo'])) {
    $data['f'] = "'" . $_POST['f'] . "'";
    $data['i'] = "'" . $_POST['i'] . "'";
    $data['o'] = "'" . $_POST['o'] . "'";
    $data['delo'] = "'" . $_POST['delo'] . "'";
    $data['birthday'] = "'" . date('Y-m-d', strtotime($_POST['dr'] . " GMT")) . "'";
    $not = $db->update($data, 'users', "id = '" . $_GET['uid'] . "'");
    $msg = '<script type="text/javascript">toastr.success("Основные данные пользователя изменены", "Успешно!");</script>';
    sleep(2);
    $usr = $userTools->get($uid);
}

if (isset($_POST['submit-editavatar'])) {
    $dirname = "avatars";
    mkdir($dirname, 0777, true);
    if (move_uploaded_file($_FILES["file"]["tmp_name"], "avatars/" . $uid . ".jpg")) $msg = '<script type="text/javascript">toastr.success("Аватар изменён", "Успешно!");</script>';
    else $msg = '<script type="text/javascript">toastr.error("Файл не загружен. Возможно, он слишком большой, либо имеет расширение, отличное от jpg", "Ошибка");</script>';
}

if (isset($_POST['submit-editpassword'])) {
    $pass = random_int(1000, 9999);
    $data['password'] = "'" . md5($pass) . "'";
    $n = $db->update($data, 'users', "id = '" . $uid . "'");
    $msg = '<script type="text/javascript">toastr.success("Произведен сброс пароля. Новый пароль: '.$pass.'", "Успешно!"); $("NewPassword").modal("show");</script>';
}

if ($_GET['make_sogl'] == "1") {
    if ($db->counter('pdata_docs', "user_id = '" . $uid . "' AND (state = '0' OR state = '1')") > 0) {
        $msg = '<script type="text/javascript">toastr.error("Для данного пользователя уже сформировано действующее соглашение", "Ошибка!");</script>';
    } else {
        $data['user_id'] = "'" . $uid . "'";
        $data['state'] = "'0'";
        $pdc = $db->insert($data, 'pdata_docs');
        $msg = '<script type="text/javascript">toastr.success("Внесено соглашение об обработке ПД PDC-' . $pdc . '", "Успешно!");</script>';
    }
}

if (isset($_GET['sign_sogl'])) {
    if ($user->admin >= 9) {
        $sogl = $db->select('pdata_docs', "id = '" . $_GET['sign_sogl'] . "'");
        if ($sogl['state'] == "0") {
            $data['state'] = "'1'";
            $data['who_signed'] = "'" . $user->id . "'";
            $data['date_accept'] = "'" . date("Y-m-d H:i:s", time()) . "'";
            $p = $db->update($data, 'pdata_docs', "id = '" . $_GET['sign_sogl'] . "'");
            $msg = '<script type="text/javascript">toastr.success("Вы успешно подписали соглашение", "Успешно!");</script>';
        } else $msg = '<script type="text/javascript">toastr.error("Данное соглашение уже подписано, либо отозвано", "Ошибка!");</script>';
    } else $msg = '<script type="text/javascript">toastr.error("У вас нет прав для подписания соглашения", "Ошибка!");</script>';
}

if ($user->admin < 1) {
    header("Location: access_denied.php");
}

require_once 'includes/header.inc.php';
?>
<?php require_once 'includes/footer.inc.php'; ?>
<html>
<head>
    <title>Информационная карта | <?php echo $pname; ?></title>
</head>
<body><br>
<?php if (isset($pass)) : ?>
<div class="modal fade show" id="NewPassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog cascading-modal modal-avatar modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <?php
                if (file_exists("avatars/" . $uid . ".jpg")) echo '<img src="avatars/' . $uid . '.jpg" alt="avatar mx-auto white"class="rounded-circle quadro-img img-responsive">';
                else echo '<img src="avatars/placeholder.png" alt="avatar mx-auto white" class="rounded-circle img-responsive">';
                ?>
            </div>
            <div class="modal-body text-center mb-1">
                <form action="info.php?uid=<?php echo $_GET['uid']; ?>" method="post">
                    <h5 class="mt-1 mb-2">Новый пароль
                        пользователя:<br><?php echo $usr->f . ' ' . $usr->i . ' ' . $usr->o; ?></h5>
                    <div class="md-form ml-0 mr-0">
                        <h3><?php echo $pass ?></h3>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif ?>
<div class="modal fade" id="MakeWrite" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog cascading-modal modal-avatar modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <?php
                if (file_exists("avatars/" . $uid . ".jpg")) echo '<img src="avatars/' . $uid . '.jpg" alt="avatar mx-auto white"class="rounded-circle quadro-img img-responsive">';
                else echo '<img src="avatars/placeholder.png" alt="avatar mx-auto white" class="rounded-circle img-responsive">';
                ?>
            </div>
            <div class="modal-body text-center mb-1">
                <form action="info.php?uid=<?php echo $_GET['uid']; ?>" method="post">
                    <h5 class="mt-1 mb-2">Внести запись
                        пользователю:<br><?php echo $usr->f . ' ' . $usr->i . ' ' . $usr->o; ?></h5>
                    <div class="md-form ml-0 mr-0">
                        <input type="text" id="form29" class="form-control form-control-sm ml-0" name="record">
                        <label data-error="wrong" data-success="right" for="form29" class="ml-0">Текст записи</label>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-cyan mt-1" name="submit-makewrite">Оставить запись</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="EditInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog cascading-modal modal-avatar modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <?php
                if (file_exists("avatars/" . $uid . ".jpg")) echo '<img src="avatars/' . $uid . '.jpg" alt="avatar mx-auto white"class="rounded-circle quadro-img img-responsive">';
                else echo '<img src="avatars/placeholder.png" alt="avatar mx-auto white" class="rounded-circle img-responsive">';
                ?>
            </div>
            <div class="modal-body text-center">
                <form action="info.php?uid=<?php echo $_GET['uid']; ?>" method="post">
                    <h5 class="mt-1 mb-2">Внесение изменения в данные
                        пользователя:<br><?php echo $usr->f . ' ' . $usr->i . ' ' . $usr->o; ?></h5>
                    <br>
                    <small>Прежде, чем изменить данные, убедитесь, что вы правомочны это сделать. История изменений
                        записывается.
                    </small>
                    <div class="md-form mb-5">
                        <input type="text" id="form1" class="form-control form-control-sm ml-0" name="f"
                               value="<?php echo $usr->f ?>">
                        <label data-error="wrong" data-success="right" for="form1" class="ml-0">Фамилия</label>
                    </div>
                    <div class="md-form mb-5">
                        <input type="text" id="form2" class="form-control form-control-sm ml-0" name="i"
                               value="<?php echo $usr->i ?>">
                        <label data-error="wrong" data-success="right" for="form2" class="ml-0">Имя</label>
                    </div>
                    <div class="md-form mb-5">
                        <input type="text" id="form3" class="form-control form-control-sm ml-0" name="o"
                               value="<?php echo $usr->o ?>">
                        <label data-error="wrong" data-success="right" for="form3" class="ml-0">Отчество</label>
                    </div>
                    <div class="md-form mb-5">
                        <input type="text" id="form4" class="form-control form-control-sm ml-0" name="delo"
                               value="<?php echo $usr->delo ?>">
                        <label data-error="wrong" data-success="right" for="form4" class="ml-0">Личное дело</label>
                    </div>
                    <div class="md-form mb-5">
                        <input type="date" id="form5" name="dr" class="form-control validate"
                               value="<?php echo date('Y-m-d', strtotime($usr->birthday . " GMT")) ?>">
                        <label for="form5">Дата рождения</label>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-cyan mt-1" name="submit-editinfo">Изменить данные</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="EditAvatar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog cascading-modal modal-avatar modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <?php
                if (file_exists("avatars/" . $uid . ".jpg")) echo '<img src="avatars/' . $uid . '.jpg" alt="avatar mx-auto white"class="rounded-circle quadro-img img-responsive">';
                else echo '<img src="avatars/placeholder.png" alt="avatar mx-auto white" class="rounded-circle img-responsive">';
                ?>
            </div>
            <div class="modal-body text-center">
                <form action="info.php?uid=<?php echo $_GET['uid']; ?>" method="post" enctype="multipart/form-data">
                    <h5 class="mt-1 mb-2">Изменение аватара
                        пользователя:<br><?php echo $usr->f . ' ' . $usr->i . ' ' . $usr->o; ?></h5>
                    <br>
                    <small>Внимание! Система принимает картинки только в формате .jpg и размером менее 2 мегабайт
                    </small>
                    <div class="md-form mb-5">
                        <div class="file-upload-wrapper">
                            <input type="file" id="input-file-max-fs" class="file-upload" data-max-file-size="2M"
                                   name="file" accept=".jpg"/>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-cyan mt-1" name="submit-editavatar">Изменить аватар</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="EditPassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog cascading-modal modal-avatar modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <?php
                if (file_exists("avatars/" . $uid . ".jpg")) echo '<img src="avatars/' . $uid . '.jpg" alt="avatar mx-auto white"class="rounded-circle quadro-img img-responsive">';
                else echo '<img src="avatars/placeholder.png" alt="avatar mx-auto white" class="rounded-circle img-responsive">';
                ?>
            </div>
            <div class="modal-body text-center">
                <form action="info.php?uid=<?php echo $_GET['uid']; ?>" method="post">
                    <h5 class="mt-1 mb-2">Изменение авторизационных
                        данных:<br><?php echo $usr->f . ' ' . $usr->i . ' ' . $usr->o; ?></h5>
                    <div class="md-form mb-5">
                        <h4>Печать заявления</h4>
                        Распечатайте заявление и сдайте его техническому специалисту<br>
                        <a class="btn btn-primary mb-4" href="print.php?sysdoc=2&id=<?php echo $usr->id ?>"
                           target="_blank">Скачать заявление</a>
                    </div>
                    <?php if ($user->admin >= 9) : ?>
                    <h4>Сброс пароля</h4>
                    <small>Доступно только техническому администратору</small>
                    <div class="text-center mt-4">
                        <button class="btn btn-cyan mt-1" name="submit-editpassword">Сбросить пароль</button>
                    </div>
                    <?php endif ?>
                    <br>
                    <?php if (isset($pass)) : ?>
                    <h5 class="mt-1 mb-2">Новый пароль</h5>
                    <div class="md-form ml-0 mr-0">
                        <h3><?php echo $pass ?></h3>
                    </div>
                    <?php endif ?>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row align-items-center">
        <div class="col-3 justify-content-center">
            <?php
            if (file_exists("avatars/" . $uid . ".jpg")) echo '<img src="avatars/' . $uid . '.jpg" alt="avatar mx-auto white" height="250" width="250" class="rounded-circle img-responsive">';
            else echo '<img src="avatars/placeholder.png" alt="avatar mx-auto white" height="250" width="250" class="rounded-circle img-responsive">';
            ?>
        </div>
        <div class="col-9">
            <h1><?php echo $usr->f . ' ' . $usr->i . ' ' . $usr->o; ?></h1>
            <h3>Состоит в группе
                <?php $gr = $db->select('groups', "id = '" . $usr->group_id . "'");
                echo $gr['name']; ?>
            </h3>
            <table class="table table-bordered table-hover table-striped table-sm">
                <thead>
                <tr>
                    <th scope="col">Параметр</th>
                    <th scope="col">Значение</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Дата рождения</td>
                    <td><?php echo date("d.m.Y", strtotime($usr->birthday . " GMT")); ?></td>
                </tr>
                <tr>
                    <td>Личное дело</td>
                    <td><?php echo $usr->delo; ?></td>
                </tr>
                <tr>
                    <td>Соглашение об обработке ПД</td>
                    <?php
                    $display_create = 0;
                    $counts = [];
                    $counts['0'] = 0;
                    $counts['1'] = 0;
                    $counts['2'] = 0;
                    $docs = $db->select_fs('pdata_docs', "user_id = '" . $usr->id . "'");
                    if (count($db->select('pdata_docs', "user_id = '" . $usr->id . "'")) == 0) {
                        echo '<td>Не существует ';
                        $display_create = 1;
                    } else {
                        echo '<td>';
                        foreach ($docs as $doc) {
                            if ($doc['state'] == "0") {
                                echo '№ ' . $doc['id'] . ', <strong>не подписано</strong> <a href="print.php?sysdoc=1&id=' . $doc['id'] . '" class="badge badge-pill badge-primary" target="_blank">Распечатать</a> ';
                                if ($user->admin == 9) echo '<a href="info.php?uid=' . $usr->id . '&sign_sogl=' . $doc['id'] . '" class="badge badge-pill badge-primary">Подписать</a> ';
                                else echo '<small>Согласие может подписать технический администратор системы</small> ';
                                $counts['0'] = $counts['0'] + 1;
                            } else if ($doc['state'] == "1") {
                                echo '№ ' . $doc['id'] . ', подписано ' . date("d.m.Y H:i:s", strtotime($doc['date_accept'] . " GMT")) . ' (' . $userTools->get_name($doc['who_signed']) . ') ';
                                $counts['1'] = $counts['1'] + 1;
                            } else if ($doc['state'] == "2") {
                                echo '№ ' . $doc['id'] . ', отозвано ' . date("d.m.Y H:i:s", strtotime($doc['date_null'] . " GMT")) . ' (' . $userTools->get_name($doc['who_null']) . ') ';
                                $counts['2'] = $counts['2'] + 1;
                            }
                            echo "<br>";
                        }
                    }
                    if ($display_create == 1 || ($counts['0'] == 0 && $counts['1'] == 0)) echo '<a href="info.php?uid=' . $usr->id . '&make_sogl=1" class="badge badge-pill badge-primary">Создать</a><br>';
                    echo '</td>';

                    ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <a href="" class="btn btn-rounded btn-primary btn-sm" data-toggle="modal" data-target="#EditInfo"><i
                    class="fas fa-pen"></i> Изменить основные данные</a>
        <a href="" class="btn btn-rounded btn-primary btn-sm" data-toggle="modal" data-target="#EditAvatar"><i
                    class="fas fa-image"></i> Изменить аватар</a>
        <?php if ($user->admin >= 9) : ?>
            <a href="" class="btn btn-rounded btn-danger btn-sm" data-toggle="modal" data-target="#EditPassword"><i
                        class="fas fa-broom"></i> Сбросить данные доступа</a>
        <?php endif ?>
    </div>
    <br>
    <div class="row">
        <ul class="nav nav-tabs md-tabs primary-color" id="myTabMD" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab-md" data-toggle="tab" href="#writes" role="tab"
                   aria-controls="home-md"
                   aria-selected="true">Записи</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#rating" role="tab"
                   aria-controls="contact-md"
                   aria-selected="false">Личный рейтинг</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#pdata" role="tab"
                   aria-controls="contact-md"
                   aria-selected="false">Персональные данные</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#visits" role="tab"
                   aria-controls="contact-md"
                   aria-selected="false">Посещаемость</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#monitors" role="tab"
                   aria-controls="contact-md"
                   aria-selected="false">Мониторинги</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab-md" data-toggle="tab" href="#accs" role="tab"
                   aria-controls="profile-md"
                   aria-selected="false">Аккаунты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#actions" role="tab"
                   aria-controls="contact-md"
                   aria-selected="false">Действия</a>
            </li>
        </ul>
        <div class="tab-content card pt-5 mw100" id="myTabContentMD">
            <div class="tab-pane fade show active" id="writes" role="tabpanel" aria-labelledby="profile-tab-md">
                <a href="" class="btn btn-rounded btn-primary btn-sm" data-toggle="modal" data-target="#MakeWrite">Добавить
                    запись</a>
                <br>
                <table id="writes2" class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">NOT-</th>
                        <th scope="col">Автор</th>
                        <th scope="col">Дата</th>
                        <th scope="col">Содержание</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $nots = $db->select_desc_fs('notes', "to_id = '" . $usr->id . "'");
                    foreach ($nots as $note) {
                        echo '<tr>';
                        echo '<td>' . $note['id'] . '</td>';
                        $who = $db->select('users', "id = '" . $note['from_id'] . "'");
                        echo '<td>' . $who['f'] . ' ' . $who['i'] . ' ' . $who['o'] . ' (ЕИС-' . $who['id'] . ')</td>';
                        echo '<td>' . date("d.m.Y H:i:s", strtotime($note['datetime'] . " GMT")) . '</td>';
                        echo '<td>' . $note['text'] . '</td>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="accs" role="tabpanel" aria-labelledby="profile-tab-md">
                <table id="accs2" class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">ACC-</th>
                        <th scope="col">Система</th>
                        <th scope="col">Логин</th>
                        <th scope="col">Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $accs = $db->select_fs('accounts', "user_eis = '" . $usr->id . "'");
                    foreach ($accs as $acc) {
                        echo '<tr>';
                        echo '<td>' . $acc['id'] . '</td>';
                        $srv = $db->select('services', "id = '" . $acc['service_id'] . "'");
                        echo '<td>' . $srv['name'] . '</td>';
                        echo '<td>' . $acc['login'] . '</td>';
                        echo '<td>Нет доступных действий.</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="visits" role="tabpanel" aria-labelledby="profile-tab-md">
                <table id="visits2" class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">VAD-</th>
                        <th scope="col">День</th>
                        <th scope="col">Причина пропуска</th>
                        <th scope="col">Отметку поставил</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $parts = $db->select_fs('visits', "eis_id = '" . $usr->id . "'");
                    foreach ($parts as $part) {
                        echo '<tr>';
                        echo '<td>' . $part['id'] . '</td>';
                        echo '<td>' . date("d.m.Y", strtotime($part['date'] . " GMT")) . '</td>';
                        if ($part['reason'] == '0') echo '<td>Не установлена</td>';
                        else if ($part['reason'] == '1') echo '<td>Пропуск по болезни</td>';
                        else if ($part['reason'] == '2') echo '<td>Уважительная причина (Заявление родителей)</td>';
                        else if ($part['reason'] == '3') echo '<td>Уважительная причина (Мероприятие)</td>';
                        $by = $db->select('users', "id = '" . $part['set_by'] . "'");
                        echo '<td>' . $by['f'] . ' ' . $by['i'] . ' ' . $by['o'] . ' <a class="badge badge-primary">ЕИС-' . $by['id'] . '</a></td>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="monitors" role="tabpanel" aria-labelledby="profile-tab-md">
                <table id="monitors2" class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">MBD-</th>
                        <th scope="col">Наименование работы</th>
                        <th scope="col">Тип</th>
                        <th scope="col">Результат</th>
                        <th scope="col">Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $avg = 0.0;
                    $nm = 0;
                    $accs = $db->select_desc_fs('monitors_bids', "usr_id = '" . $usr->id . "'");
                    foreach ($accs as $acc) {
                        echo '<tr>';
                        echo '<td>' . $acc['id'] . '</td>';
                        $srv = $db->select('monitors', "id = '" . $acc['monitor_id'] . "'");
                        echo '<td>' . $srv['name'] . '</td>';
                        if ($srv['type'] == "rated") {
                            echo '<td>Рейтинговый</td>';
                            $avg = $avg + (float)$acc['value'];
                            $nm = $nm + 1;
                        } else if ($srv['type'] == "notrated") echo '<td>Нерейтинговый</td>';
                        echo '<td>' . $acc['value'] . '</td>';
                        echo '<td><a class="badge badge-primary" href="mjob.php?id=' . $acc['id'] . '" target="_blank">Посмотреть подробно</a></td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
                <br>
                <strong>Средний балл по рейтинговым работам: </strong><?php echo $avg / $nm; ?>
            </div>
            <div class="tab-pane fade" id="rating" role="tabpanel" aria-labelledby="contact-tab-md">
                <p>Модуль в разработке.</p>
            </div>
            <div class="tab-pane fade" id="pdata" role="tabpanel" aria-labelledby="profile-tab-md">
                <table id="pdata2" class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">PDA-</th>
                        <th scope="col">Поле</th>
                        <th scope="col">Значение</th>
                        <th scope="col">Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $accs = $db->select_fs('pdata', "eis_id = '" . $usr->id . "'");
                    foreach ($accs as $acc) {
                        echo '<tr>';
                        echo '<td>' . $acc['id'] . '</td>';
                        $fld = $db->select('pdata_fields', "id = '" . $acc['field_id'] . "'");
                        echo '<td>' . $fld['name'] . '</td>';
                        echo '<td>' . $acc['data'] . '</td>';
                        echo '<td><a class="badge badge-primary" href="info_cpd.php?id=' . $usr->id . '&pd=' . $acc['id'] . '">Изменить</a></td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade show" id="actions" role="tabpanel" aria-labelledby="profile-tab-md">
                <strong>Действия с пользователем:</strong><br>
                <details>
                    <summary>Печать документов пользователя</summary>
                    <?php
                    echo '<ul>';
                    $forms = $db->select_fs('forms', "type = 'user' AND state = '1'");
                    foreach ($forms as $form) {
                        echo '<li><a target="_blank" href="print.php?customdoc=' . $form['id'] . '&id=' . $usr->id . '">' . $form['name'] . '</a></li>';
                    }
                    echo '</ul>';
                    ?>
                </details>
                <details>
                    <summary>Создание реестрового документа</summary>
                    <?php
                    echo '<ul>';
                    $forms = $db->select_fs('forms', "type = 'user_reestr' AND state = '1'");
                    foreach ($forms as $form) {
                        echo '<li><a target="_blank" href="print.php?reestrdoc=' . $form['id'] . '&id=' . $usr->id . '">' . $form['name'] . '</a></li>';
                    }
                    echo '</ul>';
                    ?>
                </details>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {
        $('#writes2').DataTable({
            "order": [[0, "desc"]]
        });
        $('.dataTables_length').addClass('bs-select');
    });

    $(document).ready(function () {
        $('#accs2').DataTable({
            "order": [[0, "asc"]]
        });
        $('.dataTables_length').addClass('bs-select');
    });

    $(document).ready(function () {
        $('#visits2').DataTable({
            "order": [[1, "desc"]]
        });
        $('.dataTables_length').addClass('bs-select');
    });

    $(document).ready(function () {
        $('#monitors2').DataTable({
            "order": [[0, "desc"]]
        });
        $('.dataTables_length').addClass('bs-select');
    });

    $(document).ready(function () {
        $('#pdata2').DataTable({
            "order": [[0, "asc"]],
            "iDisplayLength": 25
        });
        $('.dataTables_length').addClass('bs-select');
    });


</script>
<?php if ($msg != '') {
    echo $msg;
}
?>
</body>
</html>