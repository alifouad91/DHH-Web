<?php

class DiscountCouponUserGroups
{

    protected $dcugID;
    protected $dcID;
    protected $userGroupID;


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
		$this->dcugID      = $row['dcugID'];
		$this->dcID        = $row['dcID'];
		$this->userGroupID = $row['userGroupID'];
	}

    public static function add($dcID, $userGroupID)
    {
        $db    = Loader::db();
        $query = "INSERT INTO DiscountCouponUserGroups (dcID,userGroupID ) 
              VALUES ( ? , ? , ? ) ";
        $ret   = $db->Execute($query, [$dcID, $userGroupID]);
        if ($ret) {
            $obj = self::getByID($db->Insert_ID());
            return $obj;
        }
        return null;
    }

    public static function getByID($dcugID)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM DiscountCouponUserGroups WHERE dcugID = ?";
        $row   = $db->GetRow($query, [$dcugID]);
        if (isset($row['dcugID'])) {
            return new DiscountCouponUserGroups($row);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getDcugID()
    {
        return $this->dcugID;
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
    public function getUserGroupID()
    {
        return $this->userGroupID;
    }

    public function update($dcugID, $dcID, $userGroupID)
    {

        $db    = Loader::db();
        $query = "UPDATE DiscountCouponUserGroups SET dcID = ? ,userGroupID = ?   WHERE dcugID = ?";
        $ret   = $db->Execute($query, [$dcID, $userGroupID, $dcugID]);
        if ($ret) {
            return self::getByID($dcugID);
        }
        return null;
    }

    public function updateGroups($dcID, $userGroup)
    {
        $db = Loader::db();
        $q1 = "DELETE FROM DiscountCouponUserGroups WHERE  dcID = ?";
        $db->Execute($q1, [$dcID]);
        if ($userGroup) {
            $data   = [];
            $values = '(';

            $last_key = end(array_keys($userGroup));
            foreach ($userGroup as $key => $group) {
                $values .= '? , ?';
                $data[] = $dcID;
                $data[] = $group;

                if ($key !== $last_key) {
                    $values .= " ), (";
                }
            }
            $values .= ')';

            $q2 = "INSERT INTO DiscountCouponUserGroups(dcID, userGroupID) VALUES {$values}";
            return $db->Execute($q2, $data);
        }
    }

    public function delete($dcID)
    {
        $db    = Loader::db();
        $query = "DELETE FROM DiscountCouponUserGroups WHERE dcID = ?";
        $ret   = $db->Execute($query, [$dcID]);
        return $ret;
    }

    /* return array of objects */
    public static function getByDiscountCouponID($dcID)
    {

        $db    = Loader::db();
        $query = "select * from DiscountCouponUserGroups where dcID = ? ";
        /** @var ADORecordSet $rows */
        $rows = $db->Execute($query, [$dcID]);
        $arr  = [];
        foreach ($rows as $row) {
            $arr [] = new DiscountCouponUserGroups($row);
        }
        return $arr;

    }

}
