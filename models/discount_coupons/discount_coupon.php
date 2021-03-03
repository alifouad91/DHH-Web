<?php

class DiscountCoupon
{

    protected $dcID;
    protected $name;
    protected $couponCode;
    protected $type;
    protected $value;
    protected $startDate;
    protected $endDate;
    protected $timesUsableUser;
    protected $timesUsableProperty;
    protected $active;

    //fetched
    protected $discount_coupon_properties;
    protected $discount_coupon_user_groups;

    const TYPE_FIXED   = 1;
    const TYPE_PERCENT = 2;

    function __construct($row)
    {

        $this->dcID                = $row['dcID'];
        $this->name                = $row['name'];
        $this->couponCode          = $row['couponCode'];
        $this->type                = $row['type'];
        $this->value               = $row['value'];
        $this->startDate           = $row['startDate'];
        $this->endDate             = $row['endDate'];
        $this->timesUsableUser     = $row['timesUsableUser'];
        $this->timesUsableProperty = $row['timesUsableProperty'];
        $this->active              = $row['active'];

    }

    public static function add($name, $couponCode, $type, $value, $startDate, $endDate, $timesUsableUser, $timesUsableProperty, $active)
    {
    	if(empty($startDate) && empty($endDate) ) {
		    $startDate = '0000-00-00 00:00:00';
		    $endDate   = '0000-00-00 00:00:00';
	    }

        $db    = Loader::db();
        $query = "INSERT INTO DiscountCoupon (name,couponCode,type,value,startDate,endDate,timesUsableUser,timesUsableProperty,active ) 
              VALUES ( ? , ? , ? , ? , ? ,  ? , ? , ?, ? ) ";
        $ret   = $db->Execute($query, [$name, $couponCode, $type, $value, $startDate, $endDate, $timesUsableUser, $timesUsableProperty, $active]);
        if ($ret) {
            $obj = self::getByID($db->Insert_ID());
            return $obj;
        }
        return null;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM DiscountCoupon WHERE dcID = ?";
        $row   = $db->GetRow($query, [$id]);
        if (isset($row['dcID'])) {
            return new DiscountCoupon($row);
        }

        return null;
    }

    public static function getByCouponCode($code)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM DiscountCoupon WHERE couponCode = ? and active=1";
        $row   = $db->GetRow($query, [$code]);
        if (isset($row['dcID'])) {
            return new DiscountCoupon($row);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->dcID;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return mixed
     */
    public function getTimesUsableUser()
    {
        return $this->timesUsableUser;
    }

    /**
     * @return mixed
     */
    public function getTimesUsableProperty()
    {
        return $this->timesUsableProperty;
    }

    public function update($dcID, $name, $couponCode, $type, $value, $startDate, $endDate, $timesUsableUser, $timesUsableProperty, $active)
    {
        $db    = Loader::db();
        $query = "UPDATE DiscountCoupon SET name = ? ,couponCode = ? ,type = ? ,value = ? ,startDate = ? ,endDate = ? ,timesUsableUser = ? ,timesUsableProperty = ?, active = ?  WHERE dcID = ?";
        $ret   = $db->Execute($query, [$name, $couponCode, $type, $value, $startDate, $endDate, $timesUsableUser, $timesUsableProperty, $active, $dcID]);
        if ($ret) {
            return self::getByID($dcID);
        }
        return null;
    }

    public function delete()
    {
        $db                = Loader::db();
        $query             = "DELETE FROM DiscountCoupon WHERE dcID = ?";
        $ret               = $db->Execute($query, [$this->getID()]);
        $discountUserGroup = new DiscountCouponUserGroups();
        $discountUserGroup->delete($this->getID());
        $discountProperties = new DiscountCouponProperties();
        $discountProperties->delete($this->getID());
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

    /**
     * @return mixed
     */
    public function getDiscountCouponProperties()
    {
        if (!$this->discount_coupon_properties) {
            $this->discount_coupon_properties = DiscountCouponProperties::getByDiscountCouponID($this->dcID);
        }
        return $this->discount_coupon_properties;
    }

    /**
     * @return mixed
     */
    public function getDiscountCouponUserGroups()
    {
        if (!$this->discount_coupon_user_groups) {
            $this->discount_coupon_user_groups = DiscountCouponUserGroups::getByDiscountCouponID($this->dcID);
        }
        return $this->discount_coupon_user_groups;
    }

    public function updateUserGroups($groups)
    {
        $groups = array_filter($groups);
        return DiscountCouponUserGroups::updateGroups($this->getID(), $groups);
    }

    public function updateProperties($properties)
    {
        $properties = array_filter($properties);
        return DiscountCouponProperties::updateProperties($this->getID(), $properties);
    }

    public function getUsedByUser()
    {
        $db    = Loader::db();
        $query = "select count(bID) as cnt from DiscountCouponApplied where dcID = ? ";
        $row   = $db->GetRow($query, [$this->getID()]);
        if (isset($row['cnt'])) {
            return $row['cnt'];
        }

        return 0;
    }

}

?>
