<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:11 PM
 */

class ApartmentArea
{
    protected $aaID;
    protected $name;
    protected $caption;
    protected $description;

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

    public static function add($name, $caption, $description)
    {

        $db    = Loader::db();
        $query = "INSERT INTO ApartmentAreas(name, caption, description) VALUES ( ?, ?, ? ) ";
        $ret   = $db->Execute($query, [$name, $caption, $description]);

        if ($ret) {
            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public function update($name, $caption, $description)
    {

        $db    = Loader::db();
        $query = "UPDATE ApartmentAreas SET name = ?, caption = ?, description = ? WHERE aaID = ?";
        $ret   = $db->Execute($query, [$name, $caption, $description, $this->getID()]);

        if ($ret) {
            return self::getByID($this->getID());
        }

        return null;
    }

    public static function getAll($format = false)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM ApartmentAreas";
        $result = $db->Execute($query);

        $apartmentAreas = [];

        while ($row = $result->FetchRow()) {
            $aa = new static($row);
            $apartmentAreas[$row['aaID']] = $format ? $aa->getName() : $aa;
        }

        return $apartmentAreas;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM ApartmentAreas WHERE aaID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['aaID'])) {
            return new ApartmentArea($row);
        }

        return null;
    }

    public function getID()
    {
        return $this->aaID;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM ApartmentAreas WHERE aaID = ?";
        $ret   = $db->Execute($query, [$this->getID()]);

        return $ret;
    }
}