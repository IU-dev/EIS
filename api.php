<?php
//login.php
require_once 'includes/global_api.inc.php';
$page = "api.php";
$error = "";
$pid = "";

if (isset($_GET['act'])) {
    if($_GET['act'] == "auth"){
        echo 'INDEV';
    }
    else if($_GET['act'] == "request"){
        echo 'INDEV';
    }
    else if($_GET['act'] == "notify"){
        echo 'INDEV';
    }
    else if($_GET['act'] == "getlog"){
        echo 'INDEV';
    }
} else die('HACKING_ATTEMPT');
?>