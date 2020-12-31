<?php

require_once 'includes/global.inc.php';
$page = "p_add.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$display = 0;

$user = unserialize($_SESSION['user']);

if ($user->admin < 9) {
    header("Location: access_denied.php");
}

if (isset($_POST['submit'])) {
    foreach ($_POST['fio'] as $key => $item) {
        if ($_POST['fio'][$key] != "") {
            $fio = $_POST['fio'][$key];
            $fio = explode(" ", $fio);
            $data['f'] = "'" . $fio[0] . "'";
            $data['i'] = "'" . $fio[1] . "'";
            $data['o'] = "'" . $fio[2] . "'";
            $data['username'] = "'newgen'";
            $data['group_id'] = "'" . $_POST['group_id'] . "'";
            $password = mt_rand(1000, 9999);
            $data['password'] = "'" . md5($password) . "'";
            date_default_timezone_set("GMT");
            $data['join_date'] = "'" . date("Y-m-d H:i:s", time()) . "'";
            date_default_timezone_set($tool->getGlobal('tz'));
            $itog = $db->insert($data, 'users');
            echo $itog . '; ' . $_POST['fio'][$key] . '; ' . $password . '<br>';
        }
    }
    echo '<strong>Операция завершена успешно.</strong>';
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Регистрация пользователей | <?php echo $pname; ?></title>
</head>
<body>
<center>
    <?php if (isset($msg)) echo "<h3>" . $msg . "</h3>"; ?>
    <form id="form" class="md-form border border-light p-5" action="p_add.php" method="post">
        <p class="h4 mb-4 text-center">Регистрация пользователей</p>
        Выберите группу, в которую будут зарегистрированы пользователи:
        <select class="browser-default custom-select mb-4" id="select" name="group_id">
            <?php
            if ($user->admin >= 3) $sections = $db->select_fs('groups', "id != '0' ORDER BY parallel ASC, name ASC");
            else $sections = $db->select_fs('groups', "curator_id = '" . $user->id . "' ORDER BY parallel ASC, name ASC");
            foreach ($sections as $section) {
                $cur = $db->select('users', "id = '" . $section['curator_id'] . "'");
                echo '<option value="' . $section['id'] . '">' . $section['name'] . ' (куратор ' . $cur['f'] . ' ' . $cur['i'] . ' ' . $cur['o'] . ' (ЕИС-' . $cur['id'] . '))</option>';
            }
            ?>
        </select>
        <br>
        <table id="participants" class="table table-sm">
            <thead>
            <tr>
                <td>ФИО</td>
                <td>Дата рождения</td>
                <td>Личное дело</td>
                <td>Права</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><input type="text" id="textInput" name="fio[]" class="form-control mb-4"
                           placeholder="ФИО участника"></td>
                <td><input type="date" id="dateInput" name="dr[]" class="form-control mb-4" placeholder=""></td>
                <td><input type="text" id="textInput" name="ld[]" class="form-control mb-4" placeholder="ФИО участника">
                </td>
                <td><select class="browser-default custom-select mb-4" id="select2" name="prava[]">
                        <option value="0">Обучающийся</option>
                        <option value="1">Дежурный</option>
                        <option value="2">Сотрудник</option>
                        <option value="3">Секретарь</option>
                        <option value="4">Зам. директора</option>
                        <option value="5">Директор</option>
                        <option value="9">Администратор</option>
                    </select></td>
            </tr>
            </tbody>
        </table>
        <button class="btn btn-info btn-block" type="submit" name="submit">Зарегистрировать</button>
    </form>
    <button class="btn btn-info" onclick="add_field()" href="#">Добавить поле</button>
    <?php require_once 'includes/footer.inc.php'; ?>
    <script>

        $(document).ready(function () {
            $('.mdb-select').materialSelect();
        });

        $('.datepicker').pickadate();

        $(document).ready(function () {
            $('#participants').DataTable();
            $('.dataTables_length').addClass('bs-select');
        });

        function add_field(){

            var x = document.getElementById("form");
            var new_field1
            var new_field2 = document.createElement("input");
            new_field2.setAttribute("type", "text");
            new_field2.setAttribute("name", "fio[]");
            var pos = x.childElementCount;
            x.insertBefore(new_field, x.childNodes[pos]);
        }

    </script>
</body>
</html>