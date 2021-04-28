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

    public function log($cat, $sub, $who, $log)
    {
        $db = new DB();
        $data['datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
        $data['category'] = "'" . $cat . "'";
        $data['sub'] = "'" . $sub . "'";
        $data['who'] = "'" . $who . "'";
        $data['text'] = "'" . $log . "'";
        return $db->insert($data, 'log');
    }

    public function getGlobal($name)
    {
        $db = new DB();
        $data = $db->select('globals', "field = '" . $name . "'");
        return $data['value'];
    }

    public function first_class_order_position($id)
    {
        $db = new DB();
        $applics = $db->select_fs('applic_1class', "state = '1' AND period_id = '".$this->getGlobal('1class_period')."' ORDER BY datetime ASC, spec_cat DESC");
        foreach($applics as $key=>$value){
            if($value['id'] == $id) return $key+1;
        }
    }
}

?>