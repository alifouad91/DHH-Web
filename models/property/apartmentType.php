<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:11 PM
 */

class ApartmentType
{
    protected $aptID;
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
        $query = "INSERT INTO ApartmentTypes(name) VALUES ( ? ) ";
        $ret   = $db->Execute($query, [$name]);

        if ($ret) {
            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public function update($name)
    {

        $db    = Loader::db();
        $query = "UPDATE ApartmentTypes SET name = ? WHERE aptID = ?";
        $ret   = $db->Execute($query, [$name, $this->getID()]);

        if ($ret) {
            return self::getByID($this->getID());
        }

        return null;
    }

    public static function getAll($format = false)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM ApartmentTypes";
        $result = $db->Execute($query);

        $apartmentTypes = [];

        while ($row = $result->FetchRow()) {
            $apt = new static($row);
            $apartmentTypes[$row['aptID']] = $format ? $apt->getName() : $apt;
        }

        return $apartmentTypes;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM ApartmentTypes WHERE aptID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['aptID'])) {
            return new ApartmentType($row);
        }

        return null;
    }

    public function getID()
    {
        return $this->aptID;
    }

    public function getName()
    {
        return $this->name;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM ApartmentTypes WHERE aptID = ?";
        $ret   = $db->Execute($query, [$this->getID()]);

        return $ret;
    }
}