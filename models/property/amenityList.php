<?php
defined('C5_EXECUTE') or die('Access Denied.');

class AmenityList extends DatabaseItemList
{

    protected $queryCreated;
    protected $groupByKey = null;
    protected $select     = '*';

    public function __construct()
    {
        $this->queryCreated = false;
    }

    public function setBaseQuery()
    {
        $this->setQuery("SELECT * FROM Amenities");
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
        $this->filter('amID', $id);
    }

    public function setGroupQuery()
    {
        $this->groupBy($this->groupByKey);
    }

    public function setGroupKey($key)
    {
        $this->groupByKey = $key;
    }

    public function setSelectFields($key)
    {
        $this->select = $key;
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows      = parent::get($itemsToGet, $offset);
        $amenities = [];

        foreach ($rows as $row) {
            $amenity                      = new Amenity($row);
            $amenities[$amenity->getID()] = $amenity;
        }

        return $amenities;
    }


}