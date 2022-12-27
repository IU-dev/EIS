<?php
//User.class.php

require_once 'DB.class.php';
require_once 'Tools.class.php';

class Prikaz
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

    static function get($id){
        $db = new DB();
        return new Prikaz($db->select("prikazy", "id = '". $id ."'"));
    }

    public function save($isNew = false)
    {
        $tool = new Tools();
        $db = new DB();
        $user = unserialize($_SESSION['user']);
        if (!$isNew) {
            date_default_timezone_set("GMT");
            $data = array(
                "reg_n" => "'$this->reg_n'",
                "date" => "'$this->date'",
                "edited_by" => "'$user->id'",
                "edited_when" => "'" .  date("Y-m-d H:i:s", time()) . "'",
                "signed_by" => "'$this->signed_by'",
                "signed_when" => "'$this->signed_when'",
                "status" => "'$this->status'",
                "type" => "'$this->type'",
                "link_to_file" => "'$this->link_to_file'"
            );
            date_default_timezone_set($tool->getGlobal('tz'));
            $db->update($data, 'prikazy', 'id = ' . $this->id);
        } else {
            date_default_timezone_set("GMT");
            $data = array(
                "reg_n" => "'$this->reg_n'",
                "date" => "'$this->date'",
                "edited_by" => "'$user->id'",
                "edited_when" => "'" .  date("Y-m-d H:i:s", time()) . "'",
                "status" => "'0'",
                "type" => "'$this->type'",
                "link_to_file" => "'$this->link_to_file'",
                "created_by" => "'$user->id'",
                "created_when" => "'" .  date("Y-m-d H:i:s", time()) . "'"
            );
            $this->id = $db->insert($data, 'prikazy');
            date_default_timezone_set($tool->getGlobal('tz'));
        }
        return true;
    }

    public function sign()
    {
        $tool = new Tools();
        $db = new DB();
        $user = unserialize($_SESSION['user']);
        if ($user->admin >= 5 && $this->status == "1") {
            date_default_timezone_set("GMT");
            $this->signed_by = $user->id;
            $this->signed_when = date("Y-m-d H:i:s", time());
            $this->status = "2";
            $this->save();
            date_default_timezone_set($tool->getGlobal('tz'));
            return true;
        } else return false;
    }
}

?>