<?php
defined('C5_EXECUTE') or die('Access Denied.');

class PropertySeasonList extends DatabaseItemList
{

    protected $userID;
    protected $queryCreated;
    protected $populateByStatus;
    protected $cDate;

    public function __construct()
    {
        $this->queryCreated                  = false;
        $this->populateByStatus              = true;
    }

    public function setBaseQuery()
    {
        $this->setQuery("SELECT * FROM PropertySeasons p");
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

    public function populateByStatus($value = true)
    {
        $this->populateByStatus = $value;
    }

    /**
     * @param $id |array
     * @param string $comparison
     */
    public function filterById($id, $comparison = '=')
    {
        $this->filter('p.psID', $id, $comparison);
    }

    public function filterByStatus($value)
    {
        $this->filter('p.status', $value);
    }

    public function filterByPropertyID($value)
    {
        $this->filter('p.pID', $value);
    }

    public function filterByPrice($price, $comparison = '>=')
    {
        $this->filter('p.seasonPrice', $price, $comparison);
    }

    public function filterBySeasonName($seasonName)
    {
        $db        = Loader::db();
        $qkeywords = $db->quote('%' . $seasonName . '%');

        $this->filter(
            false,
            "( p.seasonName LIKE  {$qkeywords} )
          ");
    }

    public function filterByStartDate($date)
    {
        $this->filter(false, "date(p.seasonStartDate) >= '{$date}'");
    }

    public function filterByEndDate($date)
    {
        $this->filter(false, "date(p.seasonEndDate) <= '{$date}'");
    }

    public function filterByStartEndDate($startDate, $endDate)
    {
        $this->filter(false, "((date(p.seasonStartDate) between '{$startDate}' and '{$endDate}') || (date(p.seasonEndDate) between '{$startDate}' and '{$endDate}'))");
    }

    public function filterByStartEndDateBooking($startDate, $endDate)
    {
        $this->filter(false, "((date(p.seasonStartDate) between '{$startDate}' and '{$endDate}') || (date(p.seasonEndDate) between '{$startDate}' and '{$endDate}') || ('{$startDate}' between date(p.seasonStartDate) and date(p.seasonEndDate)) || ('{$endDate}' between date(p.seasonStartDate) and date(p.seasonEndDate)) )");
    }

    public function filterByNotInID($value)
    {
        $this->filter(false, "p.psID not in('{$value}')");
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows      = parent::get($itemsToGet, $offset);
        $prop_seasons = [];

        foreach ($rows as $row) {
            $prop_season                      = new PropertySeason($row);
            $prop_seasons[$prop_season->getID()] = $prop_season;
        }

        return $prop_seasons;
    }

}