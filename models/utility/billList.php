<?php
defined('C5_EXECUTE') or die('Access Denied.');

class BillList extends DatabaseItemList
{

    protected $queryCreated;
    protected $populatePropertyDetails;

    public function __construct()
    {
        $this->queryCreated           = false;
        $this->populatePropertyDetails = false;
    }

    public function setBaseQuery()
    {
        $cols = "";
        if ($this->populatePropertyDetails)
        {
            $this->addToQuery("LEFT JOIN Properties p on p.pID = bill.pID");
            $cols .= "p.*";
        }
        if (!$cols)
        {
            $cols = "bill.*";
        }
        else{
            $cols .= ",bill.billID,bill.amount,bill.type,bill.description,bill.fixedBy,bill.billImage,bill.date";
        }
        $this->setQuery("SELECT {$cols} FROM Bills bill");
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

    public function filterById($id)
    {
        $this->filter('billID', $id);
    }

    public function filterByType($type)
    {
        $this->filter('type', $type);
    }

    public function filterByPropertyID($pID)
    {
        $this->filter('p.pID', $pID);
    }

    public function populatePropertyDetails()
    {
        $this->populatePropertyDetails = true;
    }

    public function filterByOwner($userID)
    {
        $this->populatePropertyDetails();
        $this->filter('owner', $userID);
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows  = parent::get($itemsToGet, $offset);
        $bills = [];

        foreach ($rows as $row) {
            $bill                  = new Bill($row);
            $bills[$bill->getID()] = $bill;
            if ($this->populatePropertyDetails) {
                $property = new Property($row);
                $bill->setProperty($property);
            }
        }

        return $bills;
    }


}