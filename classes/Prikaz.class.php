<?php
//User.class.php

require_once 'DB.class.php';
require_once 'Tools.class.php';

class User
{
    public $id;
    public $reg_n;
    public $date;
    public $created_by;
    public $created_when;
    public $edited_by;
    public $edited_when;
    public $signed_by;
    public $signed_when;
    public $status;
    public $type;
    public $link_to_file;

    function __construct($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : "";
        $this->reg_n = (isset($data['reg_n'])) ? $data['reg_n'] : "";
        $this->date = (isset($data['date'])) ? $data['date'] : "";
        $this->created_by = (isset($data['created_by'])) ? $data['created_by'] : "";
        $this->created_when = (isset($data['created_when'])) ? $data['created_when'] : "";
        $this->edited_by = (isset($data['$edited_by'])) ? $data['$edited_by'] : "";
        $this->edited_when = (isset($data['edited_when'])) ? $data['edited_when'] : "";
        $this->signed_by = (isset($data['signed_by'])) ? $data['signed_by'] : "";
        $this->signed_when = (isset($data['signed_when'])) ? $data['signed_when'] : "";
        $this->status = (isset($data['status'])) ? $data['status'] : "";
        $this->type = (isset($data['type'])) ? $data['type'] : "";
        $this->link_to_file = (isset($data['link_to_file'])) ? $data['link_to_file'] : "";
    }

    public function save($isNew = false)
    {
        $tool = new Tools();
        $db = new DB();
        if (!$isNew) {
            $data = array(
                "username" => "'$this->username'",
                "password" => "'$this->hashedPassword'",
                "email" => "'$this->email'",
                "f" => "'$this->f'",
                "i" => "'$this->i'",
                "o" => "'$this->o'",
                "group_id" => "'$this->group_id'",
                "admin" => "'$this->admin'",
                "token" => "'$this->token'",
                "token2" => "'$this->token2'",
                "state" => "'$this->state'"
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
                "token" => "'".bin2hex(random_bytes(16))."'",
                "token2" => "'".bin2hex(random_bytes(16))."'",
                "state" => "'1'"
            );
            $this->id = $db->insert($data, 'users');
            $this->joinDate = time();
            date_default_timezone_set($tool->getGlobal('tz'));
        }
        return true;
    }
}

?>