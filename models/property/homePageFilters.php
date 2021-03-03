<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:11 PM
 */

class HomePageFilters
{
    protected $hpfID;
    protected $name;

    public function __construct($row)
    {
        $this->setPropertiesFromArray($row);
    }

    public function setPropertiesFromArray($arr)
    {
        foreach ($arr as $key => $prop) {
            $this->{$key} = $prop;
        }
    }

    public static function add($name)
    {

        $db    = Loader::db();
        $query = "INSERT INTO HomePageFilters(name) VALUES ( ? ) ";
        $ret   = $db->Execute($query, [$name]);

        if ($ret) {
            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public function update($name)
    {

        $db    = Loader::db();
        $query = "UPDATE HomePageFilters SET name = ? WHERE hpfID = ?";
        $ret   = $db->Execute($query, [$name,$this->getID()]);

        if ($ret) {
            return self::getByID($this->getID());
        }

        return null;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM HomePageFilters WHERE hpfID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['hpfID'])) {
            return new HomePageFilters($row);
        }

        return null;
    }

    public static function getAll()
    {
        $db    = Loader::db();
        $query = "SELECT * FROM HomePageFilters";
        $result = $db->Execute($query);

        $facilities = [];

        while ($row = $result->FetchRow()) {
            $facilities[$row['hpfID']] = new static($row);
        }

        return $facilities;
    }

    public function getID()
    {
        return $this->hpfID;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM HomePageFilters WHERE hpfID = ?";
        $ret   = $db->Execute($query, [$this->getID()]);

        return $ret;
    }
}