<?php
require_once 'classes/User.class.php';
require_once 'classes/UserTools.class.php';
require_once 'classes/DB.class.php';
require_once 'classes/Tools.class.php';

$pname = "Рейтинг 2.0";
//connect to the database
$db = new DB();
$tool = new Tools();
$db->connect();
date_default_timezone_set($tool->getGlobal('tz'));

//initialize UserTools object
$userTools = new UserTools();
//start the session

session_start();

//refresh session variables if logged in
if (isset($_SESSION['logged_in'])) {
    $user = unserialize($_SESSION['user']);
    $_SESSION['user'] = serialize($userTools->get($user->id));
}
?>