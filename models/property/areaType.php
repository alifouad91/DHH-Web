<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:11 PM
 */

class AreaType
{
    protected $atID;
    protected $name;
    protected $caption;

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

    public static function add($name, $caption)
    {

        $db    = Loader::db();
        $query = "INSERT INTO AreaTypes(name, caption) VALUES ( ?, ? ) ";
        $ret   = $db->Execute($query, [$name, $caption]);

        if ($ret) {
            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public function update($name, $caption)
    {

        $db    = Loader::db();
        $query = "UPDATE AreaTypes SET name = ?, caption = ? WHERE atID = ?";
        $ret   = $db->Execute($query, [$name, $caption, $this->getID()]);

        if ($ret) {
            return self::getByID($this->getID());
        }

        return null;
    }

    public static function getAll($format = false)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM AreaTypes";
        $result = $db->Execute($query);

        $areaTypes = [];

        while ($row = $result->FetchRow()) {
            $at = new static($row);
            $areaTypes[$row['atID']] = $format ? $at->getName() : $at;
        }

        return $areaTypes;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM AreaTypes WHERE atID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['atID'])) {
            return new AreaType($row);
        }

        return null;
    }

    public function getID()
    {
        return $this->atID;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM AreaTypes WHERE atID = ?";
        $ret   = $db->Execute($query, [$this->getID()]);

        return $ret;
    }
}