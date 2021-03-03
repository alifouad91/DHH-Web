<?php
defined('C5_EXECUTE') or die('Access Denied.');

class PaymentList extends DatabaseItemList
{
    protected $userID;
    protected $queryCreated;

    public function __construct()
    {
        $this->queryCreated                  = false;
    }

    public function setBaseQuery()
    {

        $this->setQuery("SELECT p.* FROM BookingPayment p");
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

    /**
     * @param $id |array
     * @param string $comparison
     */
    public function filterById($id, $comparison = '=')
    {
        $this->filter('p.bpID', $id, $comparison);
    }

    public function filterByBookingId($id, $comparison = '=')
    {
        $this->filter('p.bID', $id, $comparison);
    }

    public function filterByUserId($id, $comparison = '=')
    {
        $this->filter('p.uID', $id, $comparison);
    }

    public function filterByStatus($id, $comparison = '=')
    {
        $this->filter('p.orderStatus', $id, $comparison);
    }

    public function sortByCreatedAt()
    {
        $this->sortBy('createdAt', 'desc');
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows      = parent::get($itemsToGet, $offset);
        $payments = [];

        foreach ($rows as $row) {
            $payment = new Payment($row);
            $payments[$payment->getID()] = $payment;
        }

        return $payments;
    }
}