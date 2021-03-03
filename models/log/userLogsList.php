<?php
defined('C5_EXECUTE') or die('Access Denied.');

class UserLogsList extends DatabaseItemList
{
    protected $userID;
    protected $queryCreated;

    public function __construct()
    {
        $this->queryCreated                  = false;
    }

    public function setBaseQuery()
    {
        $this->setQuery("SELECT * FROM UserLogs p");
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
        $this->filter('p.ulID', $id, $comparison);
    }

    public function filterByUserID($value)
    {
        $this->filter('p.uID', $value);
    }

    public function filterByKeyword($keyword)
    {
        $db        = Loader::db();
        $qkeywords = $db->quote('%' . $keyword . '%');

        $this->filter(
            false,
            "( p.message LIKE  {$qkeywords} )
          ");
    }

    public function sortByCreatedAt()
    {
        $this->sortBy('createdAt', 'desc');
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows      = parent::get($itemsToGet, $offset);
        $user_logs = [];

        foreach ($rows as $row) {
            $user_log                      = new UserLogs($row);
            $user_logs[$user_log->getUlID()] = $user_log;
        }

        return $user_logs;
    }
}