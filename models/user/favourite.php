<?php
defined('C5_EXECUTE') or die('Access Denied.');

class UserFavourite
{

    protected $id;
    protected $uID;
    protected $pID;

    protected $property;

    public $event;

    public function __construct($data)
    {
        $this->id  = $data['id'];
        $this->uID = $data['uID'];
        $this->pID = $data['pID'];
    }

    public static function add($uID, $pID)
    {
        $db    = Loader::db();
        $query = "INSERT INTO UserFavourites(uID, pID) VALUES (?, ?)";
        $ret   = $db->Execute($query, [$uID, $pID]);

        if ($ret) {
            return static::getByID($db->Insert_ID());
        }

        return null;
    }

    public function update($uID, $pID)
    {
        $db    = Loader::db();
        $query = "UPDATE UserFavourites SET uID = ?, pID = ? WHERE id = ?";
        $ret   = $db->Execute($query, [$uID, $pID, $this->getId()]);

        if ($ret) {
            return self::getByID($this->getId());
        }

        return null;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM UserFavourites WHERE id = ?";
        $ret   = $db->Execute($query, [$this->getId()]);

        return $ret;
    }


    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM UserFavourites WHERE id = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['id'])) {
            return new UserFavourite($row);
        }

        return null;
    }

    public static function getByUserId($uID)
    {
        $db     = Loader::db();
        $query  = "SELECT * FROM UserFavourites WHERE uID = ?";
        $result = $db->Execute($query, [$uID]);

        $links = [];

        while ($row = $result->FetchRow()) {
            $links[] = new static($row);
        }

        return $links;
    }

    public static function getByUserIdAndPropertyID($uID, $pID)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM UserFavourites WHERE uID = ? AND pID = ?";
        $row   = $db->GetRow($query, [$uID, $pID]);

        if (isset($row['id'])) {
            return new UserFavourite($row);
        }

        return null;
    }

    public static function isFavourited($uID, $pID)
    {
        return static::getByUserIdAndPropertyID($uID, $pID) ? true : false;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getUserID()
    {
        return $this->uID;
    }

    public function getPropertyID()
    {
        return $this->pID;
    }

    public function getProperty()
    {
        if (!$this->property) {
            $this->property = Property::getByID($this->getPropertyID());
        }
        return $this->property;
    }

    public function setProperty($property)
    {
        $this->property = $property;
    }


}
