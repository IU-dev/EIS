<?php
//login.php
require_once 'includes/global_api.inc.php';
$page = "api.php";
$error = "";
$pid = "";

if (isset($_GET['data'])) {

    $data['counter_id'] = "'" . $_GET['counter'] . "'";
    $data['data'] = "'" . $_GET['data'] . "'";
    $db->insert($data, 'pool');
    echo 'Success';
} else die('<h3>Попытка взлома.</h3>Данная страница была открыта не через запрос.');
?>