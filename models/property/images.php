<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:11 PM
 */

class Images
{
    protected $imgID;
    protected $path;
    protected $caption;
    protected $position;

    const PROPERTY_DIR = '/property';

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

    public static function add($path, $caption, $position)
    {

        $db    = Loader::db();
        $query = "INSERT INTO Images(path ,caption, position) VALUES ( ?, ?, ? ) ";
        $ret   = $db->Execute($query, [$path, $caption, $position]);

        if ($ret) {
            return self::getByID($db->Insert_ID());
        }

        return null;
    }


    public function delete()
    {
        $db = Loader::db();
        $db->query("DELETE FROM Images WHERE imgID = ?", array($this->getID()));

        return true;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM Images WHERE imgID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['imgID'])) {
            return new Images($row);
        }

        return null;
    }

    public function getID()
    {
        return $this->imgID;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getCaption()
    {
        return $this->caption;
    }
}