<?php
defined('C5_EXECUTE') or die('Access Denied.');

class ReferralList extends DatabaseItemList
{
    protected $userID;
    protected $queryCreated;
    protected $cDate;

    public function __construct()
    {
        $this->queryCreated                  = false;
    }

    public function setBaseQuery()
    {

        $this->setQuery("SELECT p.* FROM Referral p");
    }

    public function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = 1;
        }
    }

    public function getTotal()
    {
        $this->createQuery();

        return parent::getTotal();
    }

    public function filterByReferredEmail($value)
    {
        $this->filter('p.referredEmail', $value);
    }

    public function filterByReferrerEmail($value)
    {
        $this->filter('p.referrerEmail', $value);
    }

    public function filterByCreditSent($value)
    {
        $this->filter('p.creditSent', $value);
    }

    public function filterByUserID($value)
    {
        $this->filter('p.uID', $value);
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows      = parent::get($itemsToGet, $offset);
        $referrals = [];

        foreach ($rows as $row) {
            $referral                      = new Referral($row);
            $referrals[$referral->getID()] = $referral;
        }

        return $referrals;
    }
}