<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:54 PM
 */

class PropertyFacilities
{
    protected $pfID;
    protected $pID;
    protected $fID;
    protected $price;
    protected $name;
    protected $imagePath;

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

    public static function add($pID, $fID, $price)
    {

        $db    = Loader::db();
        $query = "INSERT INTO PropertyFacilities(pId, fId , price) VALUES ( ?, ? , ? ) ";
        $ret   = $db->Execute($query, [$pID, $fID, $price]);

        if ($ret) {
//            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM PropertyFacilities WHERE pfID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['pfID'])) {
            return new PropertyFacilities($row);
        }

        return null;
    }

    public static function getPropertyFacilities($propertyID)
    {
        $db     = Loader::db();
        $query  = "SELECT f.fID as fID,pf.pfID as pfID,pf.pID as pID, f.name as name , pf.price  FROM Facilities f LEFT JOIN 
                      PropertyFacilities pf on f.fID = pf.fID WHERE pf.pId = ?";
        $result = $db->Execute($query, [$propertyID]);

        $pFacilities = [];

        while ($row = $result->FetchRow()) {
            $pFacilities[] = new PropertyFacilities($row);
        }

        return $pFacilities;
    }

    public function updateFacilities($propertyID, $facilities)
    {
        $db = Loader::db();
        $q1 = "DELETE FROM PropertyFacilities WHERE  pID = ?";
        $db->Execute($q1, [$propertyID]);

        $data   = [];
        $values = '(';

        $last_key = end(array_keys($facilities));
        foreach ($facilities as $key => $facility) {
            $values .= '? , ?, ?';
            $data[] = $propertyID;
            $data[] = $key;
            $data[] = ($facility)?$facility:0;

            if ($key !== $last_key) {
                $values .= " ), (";
            }
        }
        $values .= ')';

        if($data) {
            $q2 = "INSERT INTO PropertyFacilities(pID, fID, price) VALUES {$values}";
            return $db->Execute($q2, $data);
        }
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->pfID;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getImagePath($width = 100, $height = 100)
    {
        if (!$this->imagePath) {
            $f               = Facility::getByID($this->getFacilityID());
            $this->imagePath = $f->getImagePath($width, $height);
        }
        return $this->imagePath;
    }

    /**
     * @return mixed
     */
    public function getPropertyID()
    {
        return $this->pID;
    }

    /**
     * @return mixed
     */
    public function getFacilityID()
    {
        return $this->fID;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}