<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:54 PM
 */

class PropertyHomePageFilters
{
    protected $phpfID;
    protected $pID;
    protected $hpfID;
    protected $name;

    public function setPropertiesFromArray($arr)
    {
        foreach ($arr as $key => $prop) {
            $this->{$key} = $prop;
        }
    }

    public static function add($pID, $hpfID)
    {

        $db    = Loader::db();
        $query = "INSERT INTO PropertyHomePageFilters(pID, hpfID) VALUES ( ?, ? ) ";
        $ret   = $db->Execute($query, [$pID, $hpfID]);

        if ($ret) {
//            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM PropertyHomePageFilters WHERE phpfID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['phpfID'])) {
            $phf = new PropertyHomePageFilters();
            $phf->setPropertiesFromArray($row);
            return $phf;
        }

        return null;
    }

    public static function getPropertyHomePageFilters($propertyID)
    {
        $db     = Loader::db();
        $query  = "SELECT hpf.hpfID as hpfID,phpf.phpfID as pfID,phpf.pID as pID, hpf.name as name FROM HomePageFilters hpf LEFT JOIN 
                      PropertyHomePageFilters phpf on hpf.hpfID = phpf.hpfID WHERE phpf.pID = ?";
        $result = $db->Execute($query, [$propertyID]);

        $amenities = [];

        while ($row = $result->FetchRow()) {

            $phf = new PropertyHomePageFilters();
            $phf->setPropertiesFromArray($row);
            $amenities[] = $phf;
        }

        return $amenities;
    }

    public function updateHomePageFilters($propertyID, $filters)
    {
        $db = Loader::db();
        $q1 = "DELETE FROM PropertyHomePageFilters WHERE  pID = ?";
        $db->Execute($q1, [$propertyID]);

        $data   = [];
        $values = '(';

        if(!count($filters)) return;
        $last_key = end(array_keys($filters));
        foreach ($filters as $key => $filter) {
            $values .= '? , ?';
            $data[] = $propertyID;
            $data[] = $filter;

            if ($key !== $last_key) {
                $values .= " ), (";
            }
        }
        $values .= ')';

        $q2 = "INSERT INTO PropertyHomePageFilters(pID, hpfID) VALUES {$values}";
        return $db->Execute($q2, $data);

    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->phpfID;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}