<?php
defined('C5_EXECUTE') or die('Access Denied.');

class ApartmentTypeList extends DatabaseItemList
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
        $this->setQuery("SELECT * FROM ApartmentTypes");
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
        $this->filter('aptID', $id);
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
        $apartmentTypes = [];

        foreach ($rows as $row) {
            $apartmentType                       = new ApartmentType($row);
            $apartmentTypes[$apartmentType->getID()] = $apartmentType;
        }

        return $apartmentTypes;
    }


}