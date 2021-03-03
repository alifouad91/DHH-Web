<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:11 PM
 */

class Location
{
    protected $locID;
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
        $query = "INSERT INTO Locations(name) VALUES ( ? ) ";
        $ret   = $db->Execute($query, [$name]);

        if ($ret) {
            return self::getByID($db->Insert_ID());
        }

        return null;
    }


    public function update($name)
    {

        $db    = Loader::db();
        $query = "UPDATE Locations SET name = ? WHERE locID = ?";
        $ret   = $db->Execute($query, [$name,$this->getID()]);

        if ($ret) {
            return self::getByID($this->getID());
        }

        return null;
    }

    public static function getAll($format = false)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM Locations";
        $result = $db->Execute($query);

        $locations = [];

        while ($row = $result->FetchRow()) {
            $loc = new static($row);
            $locations[$row['locID']] = $format ? $loc->getName() : $loc;
        }

        return $locations;
    }

    /**
     * @param $id
     * @return Location|null
     */
    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM Locations WHERE locID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['locID'])) {
            return new Location($row);
        }

        return null;
    }

    public function getID()
    {
        return $this->locID;
    }

    public function getName()
    {
        return $this->name;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM Locations WHERE locID = ?";
        $ret   = $db->Execute($query, [$this->getID()]);

        return $ret;
    }
}