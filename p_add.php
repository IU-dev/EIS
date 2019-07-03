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
    $data['f'] = "'".$_POST['f']."'";
    $data['i'] = "'".$_POST['i']."'";
    $data['o'] = "'".$_POST['o']."'";
    $data['email'] = "'".$_POST['email']."'";
    $data['username'] = "'".$_POST['username']."'";
    $data['group_id'] = "'".$_POST['group_id']."'";
    $password = mt_rand(1000, 9999);
    $data['password'] = "'".md5($password)."'";
    $data['join_date'] = "'" . date("Y-m-d H:i:s", time()) . "'";
    $itog = $db->insert($data, 'users');
    $msg = "Регистрация произведена успешно. <strong>ПАРОЛЬ УЧАСТНИКА - ".$password.'</strong>';
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Добавить участника | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
    <h1><?php echo $_SESSION['grand']['name']; ?></h1>
    <?php if(isset($msg)) echo "<h3>".$msg."</h3>"; ?>
    <form class="md-form border border-light p-5" action="p_add.php" method="post">
        <p class="h4 mb-4 text-center">Регистрация участника</p>
        <input type="text" id="textInput" name="f" class="form-control mb-4" placeholder="Фамилия">
        <input type="text" id="textInput" name="i" class="form-control mb-4" placeholder="Имя">
        <input type="text" id="textInput" name="o" class="form-control mb-4" placeholder="Отчество">
        <input type="text" id="textInput" name="email" class="form-control mb-4" placeholder="E-mail">
        <input type="text" id="textInput" name="username" class="form-control mb-4 fixed-15em" placeholder="ID системы ЕИС">
        <select class="browser-default custom-select mb-4" id="select" name="group_id">
            <?php
            $sections = $db->select_fs('groups', "id != '0'");
            foreach ($sections as $section) {
                $cur = $db->select('users', "username = '".$section['curator_id']."'");
                echo '<option value="' . $section['id'] . '">' . "(" . $section['id'] . ") " . $section['name'] . ' (куратор ' . $cur['f'] . ' ' . $cur['i'] . ' ' . $cur['o'] . ' (ЕИС-'. $cur['username'] . '))</option>';
            }
            ?>
        </select>
        <button class="btn btn-info btn-block" type="submit" name="submit">Зарегистрировать</button>
    </form>
</body>
</html>