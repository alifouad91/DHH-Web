<?php
defined('C5_EXECUTE') or die('Access Denied.');

class DiscountCouponList extends DatabaseItemList
{

    protected $queryCreated;
    protected $populateDcProperties;
    protected $populateDcUserGroups;
    protected $populateUserApplied;


    public function __construct()
    {
        $this->queryCreated         = false;
        $this->populateDcProperties = false;
        $this->populateDcUserGroups = false;
        $this->populateUserApplied = false;
    }

    public function getTotal()
    {
        $this->createQuery();

        return parent::getTotal();
    }

    public function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = 1;
        }
    }

    public function setBaseQuery()
    {
        $u = new User();
        $userId = $u->getUserID();
        $cols = '';
        if ($this->populateUserApplied) {
            $cols .= ", dca.uID, dca.bID, count(dca.uID) as appliedCnt";
            $this->addToQuery("LEFT JOIN DiscountCouponApplied AS dca ON (dc.dcID = dca.dcID and dca.uId=".$userId.")");
            $this->groupBy('dca.uID');
        }
        $this->setQuery("SELECT dc.*{$cols} FROM DiscountCoupon dc");
    }

    public function populateUserApplied()
    {
        $this->populateUserApplied = true;
    }

    public function filterByTimesUsableUser()
    {
        $this->having('dc.timesUsableUser','appliedCnt','>');
    }

    public function filterByID($id)
    {
        $this->filter('dc.dcID', $id);
    }

    public function filterByUID($uID)
    {
        $this->filter('dca.uID', $uID);
    }

    public function filterByType($type)
    {
        $this->filter('dc.type', $type);
    }

    public function filterByKeyword($keyword)
    {
        $this->filter(false, "(dc.name LIKE '%{$keyword}%' || dc.couponCode LIKE '%{$keyword}%') ");
    }

    public function filterByStartDate($date)
    {
        $this->filter(false, "date(dc.startDate) >= '{$date}'");
    }

    public function filterByEndDate($date)
    {
        $this->filter(false, "date(dc.endDate) <= '{$date}'");
    }

    public function filterByStartEndDate($startDate, $endDate)
    {
        $this->filter(false, "((date(dc.startDate) between '{$startDate}' and '{$endDate}') || (date(dc.endDate) between '{$startDate}' and '{$endDate}'))");
    }

    public function filterByCouponCode($code)
    {
        $this->filter('dc.couponCode', $code);
    }

    public function filterByNotInID($id)
    {
        $this->filter('dc.dcID',$id,'!=');
    }

    public function filterByActive($active = 1)
    {
        $this->filter('dc.active', $active);
    }

    public function filterByApplicable()
    {
        $db = Loader::db();
        /** @var DateHelper $dh */
        $dh   = Loader::helper('date');
        $date = $dh->getSystemDateTime('now', 'Y-m-d H:i');
        $date = $db->Quote($date);
        $this->filter(false, "({$date} between dc.startDate and dc.endDate and dc.startDate != '0000-00-00 00:00:00' && dc.endDate != '0000-00-00 00:00:00')");
    }

    public function populateDcProperties()
    {
        $this->populateDcProperties = true;
    }

    public function populateDcUserGroups()
    {
        $this->populateDcUserGroups = true;
    }


    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows = parent::get($itemsToGet, $offset);
        $arr  = [];
        foreach ($rows as $row) {
            $dc = new DiscountCoupon($row);

            if ($this->populateDcProperties)
                $dc->getDiscountCouponProperties();
            if ($this->populateDcUserGroups)
                $dc->getDiscountCouponUserGroups();
            $arr[$dc->getId()] = $dc;
        }
        return $arr;
    }


}