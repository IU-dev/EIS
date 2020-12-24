<?php

//UserTools.class.php
require_once 'User.class.php';
require_once 'DB.class.php';
require_once 'UserTools.class.php';

class Tools
{
    public function logout()
    {
        unset($_SESSION['user']);
        unset($_SESSION['login_time']);
        unset($_SESSION['logged_in']);
        session_destroy();
        header("Location: index.php");
    }

    public function log()
    {

    }

    public function getGlobal($name){
        $db = new DB();
        $data = $db->select('globals', "name = '".$name."'");
        return $data['value'];
    }
}

?>