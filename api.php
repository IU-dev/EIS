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
    }
} else die('HACKING_ATTEMPT');
?>