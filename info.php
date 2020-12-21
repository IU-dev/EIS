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
    if ($user->admin > 9) {
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
<html>
<head>
    <title>Информационная карта | <?php echo $pname; ?></title>
</head>
<body><br>
<div class="modal fade" id="MakeWrite" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog cascading-modal modal-avatar modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <img src="placeholder.png" alt="avatar" class="rounded-circle img-responsive">
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
<div class="container">
    <div class="row align-items-center">
        <div class="col-3 justify-content-center">
            <img src="placeholder.png"
                 alt="avatar mx-auto white" class="rounded-circle img-fluid">
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
                    if ($display_create == 1 || ($counts['2'] > $counts['0'] && $counts['2'] > $counts['1'])) echo '<a href="info.php?uid=' . $usr->id . '&make_sogl=1" class="badge badge-pill badge-primary">Создать</a><br>';
                    echo '</td>';

                    ?>
                </tr>
                </tbody>
            </table>
        </div>
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
                    foreach($forms as $form){
                        echo '<li><a target="_blank" href="print.php?customdoc='.$form['id'].'&id='.$usr->id.'">'.$form['name'].'</a></li>';
                    }
                    echo '</ul>';
                    ?>
                </details>
                <details>
                    <summary>Создание реестрового документа</summary>
                    <?php
                    echo '<ul>';
                    $forms = $db->select_fs('forms', "type = 'user_reestr' AND state = '1'");
                    foreach($forms as $form){
                        echo '<li><a target="_blank" href="print.php?reestrdoc='.$form['id'].'&id='.$usr->id.'">'.$form['name'].'</a></li>';
                    }
                    echo '</ul>';
                    ?>
                </details>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.inc.php'; ?>
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