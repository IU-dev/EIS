<?php
//login.php
require_once 'includes/global_api.inc.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
$page = "api.php";
$error = "";
$pid = "";

if (isset($_GET['act'])) {
    if ($_GET['act'] == "auth") {
        $login = $_GET['login'];
        $passwd = md5($_POST['password']);
        $usr = $db->select('users', "id = '" . $login . "' AND password = '" . $passwd . "'");
        if ($usr['password'] == $passwd) {
            $result['answer'] = "OK";
            $group = $db->select('groups', "id = '" . $usr['group_id'] . "'");
            $result['group'] = $group['name'];
            $result['userid'] = $usr['id'];
            $result['token'] = rand('10000000', '99999999');
            $data['token'] = $result['token'];
            $u = $db->update($data, 'users', "id = '" . $_GET['login'] . "'");
        } else $result['answer'] = "AUTH_ERROR";
        echo json_encode($result);
    }
    else if ($_GET['act'] == "request") {
        echo 'INDEV';
    } else if ($_GET['act'] == "notify") {
        echo 'INDEV';
    } else if ($_GET['act'] == "getlog") {
        echo 'INDEV';
    } else if ($_GET['act'] == "regenerate" && $_GET['secret'] == "bondartop14072003") {
        $grps = $db->select_fs('groups', "id != '0'");
        foreach ($grps as $grp) {
            echo '<strong>Группа ' . $grp['name'] . '</strong><br>';
            $usrs = $db->select_fs('users', "group_id = '" . $grp['id'] . "'");
            foreach ($usrs as $usr) {
                $np = random_int(1000, 9999);
                $data['password'] = "'" . md5($np) . "'";
                $b = $db->update($data, 'users', "id = '" . $usr['id'] . "'");
                echo $usr['f'] . ';' . $usr['i'] . ';' . $usr['o'] . ';' . $usr['id'] . ';' . $np . '<br>';
            }
            echo '<br>';
        }
    } else if ($_GET['act'] == "getUsersDataCheck") {
        $grp = $db->select('groups', "id = '" . $_GET['gid'] . "'");
        echo '<strong>Группа: ' . $grp['name'] . '</strong><br><br>';
        $usrs = $db->select_fs('users', "group_id = '" . $_GET['gid'] . "' ORDER BY f ASC, i ASC");
        foreach ($usrs as $usr) {
            echo '(' . $usr['id'] . ') ' . $usr['f'] . ' ' . $usr['i'] . ' ' . $usr['o'] . '<br>';
        }
        echo '<hr>Выписка из Единой информационной системы<br>МБОУ "ИТ-лицей №24"';
        echo '<br>' . date("d.m.Y H:i:s");
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
    } else if ($_GET['act'] == "getLinksForParents") {
        $group = $db->select('groups', "id = '" . $_GET['gid'] . "'");
        $users = $db->select_fs('users', "group_id = '" . $_GET['gid'] . "' ORDER BY f ASC");
        foreach ($users as $u) {
            echo '<strong>Единая информационная система МБОУ "ИТ-лицей №24"<br>Доступ к внесению первичных персональных данных</strong><br><br>';
            echo 'ФИО ученика: ' . $u['f'] . ' ' . $u['i'] . ' ' . $u['o'] . ' (' . $group['name'] . ')<br><br>';
            echo 'Для внесения данных перейдите по ссылке, либо распознайте QR-код:<br><br>';
            echo 'http://eis.it-lyceum24.ru/info_an.php?id=' . $u['id'] . '&gid=0&firstpass=' . $u['firstpass'] . '<br>';
            echo '<img src="https://chart.googleapis.com/chart?chs=180x180&cht=qr&chl=http%3A%2F%2Feis.it-lyceum24.ru%2Finfo_an.php%3Fid%3D' . $u['id'] . '%26gid%3D1%26firstpass%3D' . $u['firstpass'] . '&choe=UTF-8" title="Link to Google.com" />';
            echo '<hr>';
        }
    }
    # с этого места идут $_GET['act'], требующие авторизации с использованием токена
    else {
        $p = $db->select('users', "id = '" . $_GET['uid'] . "'");
        if ($_GET['token'] == $p['token']) {
            echo 'INDEV';
        }
    }
} else die('HACKING_ATTEMPT');
?>