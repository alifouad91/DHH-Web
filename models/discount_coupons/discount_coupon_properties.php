<?php

class DiscountCouponProperties
{

    protected $dcpID;
    protected $dcID;
    protected $pID;

	public function __construct()
	{
		$a = func_get_args();
		$i = func_num_args();
		if (method_exists($this,$f='__construct'.$i)) {
			call_user_func_array(array($this,$f),$a);
		}
	}

	public function __construct1($row)
	{
		$this->dcpID = $row['dcpID'];
		$this->dcID  = $row['dcID'];
		$this->pID   = $row['pID'];
	}

    public static function add($dcID, $pID)
    {
        $db    = Loader::db();
        $query = "INSERT INTO DiscountCouponProperties (dcID,pID ) 
              VALUES ( ? , ?  ) ";
        $ret   = $db->Execute($query, [$dcID, $pID]);
        if ($ret) {
            $obj = self::getByID($db->Insert_ID());
            return $obj;
        }
        return null;
    }

    public static function getByID($dcpID)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM DiscountCouponProperties WHERE dcpID = ?";
        $row   = $db->GetRow($query, [$dcpID]);
        if (isset($row['dcpID'])) {
            return new DiscountCouponProperties($row);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getDcpID()
    {
        return $this->dcpID;
    }

    /**
     * @return mixed
     */
    public function getDcID()
    {
        return $this->dcID;
    }

    /**
     * @return mixed
     */
    public function getPID()
    {
        return $this->pID;
    }

    public function update($dcpID, $dcID, $pID)
    {

        $db    = Loader::db();
        $query = "UPDATE DiscountCouponProperties SET dcID = ? ,pID = ?   WHERE dcpID = ?";
        $ret   = $db->Execute($query, [$dcID, $pID, $dcpID]);
        if ($ret) {
            return self::getByID($dcpID);
        }
        return null;
    }

    public function updateProperties($dcID, $properties)
    {
        $db = Loader::db();
        $q1 = "DELETE FROM DiscountCouponProperties WHERE  dcID = ?";
        $db->Execute($q1, [$dcID]);
        if ($properties) {
            $data   = [];
            $values = '(';

            $last_key = end(array_keys($properties));
            foreach ($properties as $key => $property) {
                $values .= '? , ?';
                $data[] = $dcID;
                $data[] = $property;

                if ($key !== $last_key) {
                    $values .= " ), (";
                }
            }
            $values .= ')';

            $q2 = "INSERT INTO DiscountCouponProperties(dcID, pID) VALUES {$values}";
            return $db->Execute($q2, $data);
        }
    }

    public function delete($dcID)
    {
        $db    = Loader::db();
        $query = "DELETE FROM DiscountCouponProperties WHERE dcID = ?";
        $ret   = $db->Execute($query, [$dcID]);
        return $ret;
    }

    /* reutur array of objects */
    public static function getByDiscountCouponID($dcID)
    {

        $db    = Loader::db();
        $query = "select * from DiscountCouponProperties where dcID = ? ";
        /** @var ADORecordSet $rows */
        $rows = $db->Execute($query, [$dcID]);
        $arr  = [];
        foreach ($rows as $row) {
            $arr [] = new DiscountCouponProperties($row);
        }
        return $arr;

    }


}
