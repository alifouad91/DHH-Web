<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:54 PM
 */

class PropertyImages
{
    protected $pimID;
    protected $pID;
    protected $imgID;

    public function __construct($data)
    {
        $this->setPropertiesFromArray($data);
    }

    public function setPropertiesFromArray($arr)
    {
        foreach ($arr as $key => $prop) {
            $this->{$key} = $prop;
        }
    }

    public static function add($pID, $imgID)
    {

        $db    = Loader::db();
        $query = "INSERT INTO PropertyImages(pID, imgID) VALUES ( ?, ? ) ";
        $ret   = $db->Execute($query, [$pID, $imgID]);

        if ($ret) {
//            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM PropertyImages WHERE imgID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['imgID'])) {
            return new PropertyImages($row);
        }

        return null;
    }

    public static function getPropertyImages($propertyID)
    {
        $db     = Loader::db();
        $query  = "SELECT im.imgID as imgID, im.path as path, im.position as position,im.caption as caption FROM Images im LEFT JOIN PropertyImages pim on im .imgID = pim .imgID WHERE pim .pID = ?";
        $result = $db->Execute($query, [$propertyID]);

        $amenities = [];

        while ($row = $result->FetchRow()) {
            $amenities[] = new Images($row);
        }

        return $amenities;
    }

}