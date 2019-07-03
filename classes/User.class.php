<?php
//User.class.php

require_once 'DB.class.php';

class User
{
    public $id;
    public $username;
    public $hashedPassword;
    public $email;
    public $joinDate;
    public $f;
    public $i;
    public $o;
    public $group_id;
    public $admin;

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
    }

    public function save($isNewUser = false)
    {
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
                "admin" => "'$this->admin'"
            );

            $db->update($data, 'users', 'id = ' . $this->id);
        } else {
            $data = array(
                "username" => "'$this->username'",
                "password" => "'$this->hashedPassword'",
                "email" => "'$this->email'",
                "join_date" => "'" . date("Y-m-d H:i:s", time()) . "'",
                "f" => "'$this->f'",
                "i" => "'$this->i'",
                "o" => "'$this->o'",
                "group_id" => "'$this->group_id'",
                "admin" => "0"
            );
            $this->id = $db->insert($data, 'users');
            $this->joinDate = time();
        }
        return true;
    }
}

?>