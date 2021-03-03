<?php
defined('C5_EXECUTE') or die('Access Denied.');

class FacilityList extends DatabaseItemList
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
        $this->setQuery("SELECT * FROM Facilities");
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
        $this->filter('fID', $id);
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
        $rows       = parent::get($itemsToGet, $offset);
        $facilities = [];

        foreach ($rows as $row) {
            $facility                       = new Facility($row);
            $facilities[$facility->getID()] = $facility;
        }

        return $facilities;
    }


}