<?php
//index.php 
require_once 'includes/global.inc.php';
$page = "show.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title>Мои учетные записи | <?php echo $pname; ?></title>
</head>
<body>
<center>
    <br>
    <h3>Мои учетные записи</h3><br>
    <br>
    <?php
    $accs = $db->select_fs('accounts', "user_eis = '".$user->username."'");
    $i = 0;
    foreach($accs as $acc){
        echo '<div class="alert alert-primary fixed-35em">';
        $service = $db->select('services', "id = '".$acc['service_id']."'");
        echo '<h4>'.$service['name'].'</h4>';
        echo '<details><summary>Посмотреть данные</summary>';
        echo '<hr><h5><strong>Логин: </strong>'.$acc['login'].'<br>';
        echo '<strong>Пароль: </strong>'.$acc['password'].'</h5>';
        echo '<hr>';
        echo '<strong>Запись в ЕИС: </strong>'.$acc['id'].'<br>';
        echo '<strong>Последнее обновление: </strong>'.date("d.m.Y H:i:s", strtotime($acc['last_update'] . " GMT")).'<br>';
        $usr = $db->select('users', "username = '".$acc['last_update_user_eis']."'");
        echo '<strong>Обновил: </strong>'.$usr['f'].' '.$usr['i'].' '.$usr['o'].' (ЕИС-'.$usr['username'].')<br>';
        echo '<a class="btn btn-sm btn-primary" role="button">Подать заявку на сброс</a>';
        echo '</details></div><br>';
        $i = $i + 1;
    }
    if($i == 0){
        echo '<div class="alert alert-warning fixed-35em"><h5>Учетных записей нет.</h5></div>';
    }
    ?>
</center>
</body>
</html>