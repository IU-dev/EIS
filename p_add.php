<?php

require_once 'includes/global.inc.php';
$page = "p_add.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$display = 0;

$user = unserialize($_SESSION['user']);

if ($user->admin < 2) {
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
            $data['join_date'] = "'" . date("Y-m-d H:i:s", time()) . "'";
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
    <title>Добавить участников | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
    <h1><?php echo $_SESSION['grand']['name']; ?></h1>
    <?php if (isset($msg)) echo "<h3>" . $msg . "</h3>"; ?>
    <form class="md-form border border-light p-5" action="p_add.php" method="post">
        <p class="h4 mb-4 text-center">Регистрация участников </p>

        <select class="browser-default custom-select mb-4" id="select" name="group_id">
            <?php
            $sections = $db->select_fs('groups', "id != '0'");
            foreach ($sections as $section) {
                $cur = $db->select('users', "username = '" . $section['curator_id'] . "'");
                echo '<option value="' . $section['id'] . '">' . "(" . $section['id'] . ") " . $section['name'] . ' (куратор ' . $cur['f'] . ' ' . $cur['i'] . ' ' . $cur['o'] . ' (ЕИС-' . $cur['username'] . '))</option>';
            }
            ?>
        </select>

        <table>
            <thead>
            <tr>
                <td>ФИО</td>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 1; $i <= 30; $i = $i + 1) {
                echo '<tr>';
                echo '<td><input type="text" id="textInput" name="fio[]" class="form-control mb-4" placeholder="ФИО участника"></td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
        <button class="btn btn-info btn-block" type="submit" name="submit">Зарегистрировать</button>
    </form>
</body>
</html>