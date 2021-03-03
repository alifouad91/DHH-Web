<?php
defined('C5_EXECUTE') or die('Access Denied.');

class NotificationList extends DatabaseItemList
{

    protected $queryCreated;

    public function __construct()
    {
        $this->queryCreated = false;
    }

    public function setBaseQuery()
    {
        $this->setQuery("SELECT * FROM Notifications");
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

    public function filterByID($id)
    {
        $this->filter('nID', $id);
    }

    public function filterByType($type)
    {
        $this->filter('type', $type);
    }

    public function filterByCategory($category)
    {
        $this->filter('category', $category);
    }

    public function filterByUserID($uID)
    {
        $this->filter('uID', $uID);
    }

    public function filterByReadStatus($status)
    {
        $this->filter('`read`', 1,$status ? '=' : '<>');
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows          = parent::get($itemsToGet, $offset);
        $notifications = [];

        foreach ($rows as $row) {
            $notification = new Notification();
            $notification->setPropertiesFromArray($row);
            $notifications[$notification->getId()] = $notification;
        }

        return $notifications;
    }


}