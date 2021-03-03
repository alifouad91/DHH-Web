<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:54 PM
 */

class PropertyAmenities
{
    protected $pamID;
    protected $pID;
    protected $amID;

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

    public static function add($pID, $amID)
    {

        $db    = Loader::db();
        $query = "INSERT INTO PropertyAmenities(pID, amID) VALUES ( ?, ? ) ";
        $ret   = $db->Execute($query, [$pID, $amID]);

        if ($ret) {
//            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM PropertyAmenities WHERE pamID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['pamID'])) {
            return new PropertyAmenities($row);
        }

        return null;
    }

    public static function getPropertyAmenities($propertyID)
    {
        $db    = Loader::db();
        $query = "SELECT am.amID as amID, am.name as name, am.icon as icon FROM Amenities am LEFT JOIN PropertyAmenities pm on am.amID = pm.amID WHERE pm.pID = ?";
        $result   = $db->Execute($query, [$propertyID]);

        $amenities = [];

        while ($row = $result->FetchRow()) {
            $amenities[] = new Amenity($row);
        }

        return $amenities;
    }

    public function updateAmenities($propertyID, $amenities)
    {
        $db = Loader::db();
        $q1 = "DELETE FROM PropertyAmenities WHERE  pID = ?";
        $db->Execute($q1, [$propertyID]);

        $data = [];
        $values = '(';

        $last_key         = end(array_keys($amenities));
        foreach ($amenities as $key => $amenity)
        {
            $values .= '? , ?';
            $data[] = $propertyID;
            $data[] = $amenity;

            if ($key !== $last_key) {
                $values .= " ), (";
            }
        }
        $values .= ')';

        $q2 = "INSERT INTO PropertyAmenities(pID, amID) VALUES {$values}";
        return $db->Execute($q2, $data);

    }
}