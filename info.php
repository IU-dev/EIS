<?php

$msg = '';
require_once 'includes/global.inc.php';
$page = "myclass.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

if (!isset($_GET['uid'])) {
    die('Отсутствует UID.');
} else {
    $uid = $_GET['uid'];
    $usr = $userTools->get($uid);
}

if(isset($_POST['submit-makewrite'])){
    $data['from_id'] = "'".$user->id."'";
    $data['to_id'] = "'".$usr->id."'";
    $data['datetime'] =  "'" . date("Y-m-d H:i:s", time()) . "'";
    $data['text'] = "'".$_POST['record']."'";
    $not = $db->insert($data, 'notes');
    $msg = '<script type="text/javascript">toastr.success("Внесена запись NOT-'.$not.'", "Успешно!");</script>';
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
                    <td><?php echo $usr->birthday; ?></td>
                </tr>
                <tr>
                    <td>Телефон</td>
                    <td><?php echo $usr->phone; ?></td>
                </tr>
                <tr>
                    <td>Электронная почта</td>
                    <td><?php echo $usr->email; ?></td>
                </tr>
                <tr>
                    <td>Личное дело</td>
                    <td><?php echo $usr->delo; ?></td>
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
                <a class="nav-link" id="profile-tab-md" data-toggle="tab" href="#accs" role="tab"
                   aria-controls="profile-md"
                   aria-selected="false">Аккаунты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#contact-md" role="tab"
                   aria-controls="contact-md"
                   aria-selected="false">Мониторинги</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#contact-md" role="tab"
                   aria-controls="contact-md"
                   aria-selected="false">История действий</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#contact-md" role="tab"
                   aria-controls="contact-md"
                   aria-selected="false">Личный рейтинг</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#contact-md" role="tab"
                   aria-controls="contact-md"
                   aria-selected="false">Персональные данные</a>
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
                    $accs = $db->select_fs('accounts', "user_eis = '" . $usr->username . "'");
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
            <div class="tab-pane fade" id="contact-md" role="tabpanel" aria-labelledby="contact-tab-md">
                <p>Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney's organic lomo
                    retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork tattooed craft
                    beer,
                    iphone skateboard locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony.
                    Leggings gentrify squid 8-bit cred pitchfork. Williamsburg banh mi whatever gluten-free, carles
                    pitchfork biodiesel fixie etsy retro mlkshk vice blog. Scenester cred you probably haven't heard of
                    them, vinyl craft beer blog stumptown. Pitchfork sustainable tofu synth chambray yr.</p>
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

</script>
<?php if($msg != ''){
    echo $msg;
}
?>
</body>
</html>