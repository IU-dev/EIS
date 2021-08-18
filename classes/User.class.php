<?php
//User.class.php

require_once 'DB.class.php';
require_once 'Tools.class.php';

class User
{
    public $id;
    public $username;
    public $hashedPassword;
    public $joinDate;
    public $f;
    public $i;
    public $o;
    public $group_id;
    public $admin;
    public $birthday;
    public $phone;
    public $email;
    public $delo;
    public $token;

    function __construct($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : "";
        $this->username = (isset($data['username'])) ? $data['username'] : "";
        $this->hashedPassword = (isset($data['password'])) ? $data['password'] : "";
        $this->email = (isset($data['email'])) ? $data['email'] : "";
        $this->joinDate = (isset($data['join_date'])) ? $data['join_date'] : "";
        $this->f = (isset($data['f'])) ? $data['f'] : "";
        $this->i = (isset($data['i'])) ? $data['i'] : "";
        $this->o = (isset($data['o'])) ? $data['o'] : "";
        $this->group_id = (isset($data['group_id'])) ? $data['group_id'] : "";
        $this->admin = (isset($data['admin'])) ? $data['admin'] : "";
        $this->phone = (isset($data['phone'])) ? $data['phone'] : "";
        $this->birthday = (isset($data['birthday'])) ? $data['birthday'] : "";
        $this->delo = (isset($data['delo'])) ? $data['delo'] : "";
        $this->token = (isset($data['token'])) ? $data['token'] : "";
    }

    public function save($isNewUser = false)
    {
        $tool = new Tools();
        $db = new DB();
        if (!$isNewUser) {
            $data = array(
                "username" => "'$this->username'",
                "password" => "'$this->hashedPassword'",
                "email" => "'$this->email'",
                "f" => "'$this->f'",
                "i" => "'$this->i'",
                "o" => "'$this->o'",
                "group_id" => "'$this->group_id'",
                "admin" => "'$this->admin'",
                "token" => "'$this->token'"
            );

            $db->update($data, 'users', 'id = ' . $this->id);
        } else {
            date_default_timezone_set("GMT");
            $data = array(
                "username" => "'$this->username'",
                "password" => "'$this->hashedPassword'",
                "email" => "'$this->email'",
                "join_date" => "'" . date("Y-m-d H:i:s", time()) . "'",
                "f" => "'$this->f'",
                "i" => "'$this->i'",
                "o" => "'$this->o'",
                "group_id" => "'$this->group_id'",
                "admin" => "'0'",
                "token" => "'".mt_rand(10000000,99999999)."'"
            );
            $this->id = $db->insert($data, 'users');
            $this->joinDate = time();
            date_default_timezone_set($tool->getGlobal('tz'));
        }
        return true;
    }
}

?>