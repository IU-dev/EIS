<?php
//login.php
require_once 'includes/global_api.inc.php';
$page = "api.php";
$error = "";
$pid = "";

if (isset($_GET['act'])) {
    if ($_GET['act'] == "auth") {
        $login = $_GET['login'];
        $passwd = $_GET['password'];
        $usr = $db->select('users', "username = '" . $login . "' AND password = '" . $passwd . "'");
        if ($usr['password'] == $passwd) {
            $result['answer'] = "OK";
            $group = $db->select('groups', "id = '" . $usr['group_id'] . "'");
            $result['group'] = $group['name'];
        } else $result['answer'] = "ERR";
        echo json_encode($result);
    } else if ($_GET['act'] == "request") {
        echo 'INDEV';
    } else if ($_GET['act'] == "notify") {
        echo 'INDEV';
    } else if ($_GET['act'] == "getlog") {
        echo 'INDEV';
    } else if ($_GET['act'] == "getIrbisData") {
        $usrs = $db->select_fs('users', "id != '0'");
        echo 'getIrbisData: Генерация данных для системы ИРБИС64.<br><strong>Внимание! Не забудьте перекодировать txt файл в кодировку Windows!!!</strong><hr>';
        $i = 1;
        foreach ($usrs as $usr) {
            $group = $db->select('groups', "id = '" . $usr['group_id'] . "'");
            echo '#920: RDR<br>';
            echo '#10: ' . $usr['f'] . '<br>';
            echo '#11: ' . $usr['i'] . '<br>';
            echo '#12: ' . $usr['o'] . '<br>';
            echo '#30: ' . $usr['id'] . '<br>';
            echo '#50: ' . $group['name'] . '<br>';
            echo '#907: ^A20190813^B1<br>';
            echo '*****<br>';
            $i = $i + 1;
        }
        $i = $i - 1;
        echo '<hr>Операция завершена. Всего пользователей: ' . $i . '.';
    }
} else die('HACKING_ATTEMPT');
?>