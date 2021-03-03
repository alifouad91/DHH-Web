<?php
defined('C5_EXECUTE') or die('Access Denied.');

class PropertyBlockDatesList extends DatabaseItemList
{

    protected $userID;
    protected $queryCreated;
    protected $cDate;

    protected $populateProperty;
    protected $userId;

    public function __construct()
    {
        $this->queryCreated                  = false;
        $this->populateProperty                  = false;
        $this->userId                  = false;
    }

    public function populateProperty()
    {
        $this->populateProperty = true;
    }

    public function setOwner($id)
    {
        $this->userId = $id;
    }

    public function setBaseQuery()
    {
        if($this->populateProperty && $this->userId) {
            $this->setQuery("SELECT p.*, p.price as total,p.startDate, p.endDate,YEAR(p.startDate) AS year,
                   UPPER(DATE_FORMAT(p.startDate,'%b')) AS month, DATEDIFF(p.endDate,
                   p.startDate) AS avgNights FROM  PropertyBlockDates p inner join Properties p1  on(p1.pID=p.pID and p1.owner = {$this->userId})");
        } else {
            $this->setQuery("SELECT p.* FROM PropertyBlockDates p");
        }

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
        $this->filter('p.pbdID', $id, $comparison);
    }

    /**
     * @param $id |array
     * @param string $comparison
     */
    public function filterPrice()
    {
        $this->filter('p.price', 0, '>');
    }

    public function groupByCustom()
    {
        $this->groupByString = 'p.pbdID';
    }

    public function filterByPropertyID($value)
    {
        $this->filter('p.pID', $value);
    }

    public function filterByNotInID($value)
    {
        $this->filter(false, "p.pbdID not in('{$value}')");
    }

    public function filterByStartDate($date)
    {
        $this->filter(false, "date(p.startDate) >= '{$date}'");
    }

    public function filterByEndDate($date)
    {
        $this->filter(false, "date(p.endDate) <= '{$date}'");
    }

    public function filterByStartEndDate($startDate, $endDate)
    {
        $this->filter(false, "((date(p.startDate) between '{$startDate}' and '{$endDate}') || (date(p.endDate) between '{$startDate}' and '{$endDate}'))");
    }

    public function filterByYear($year)
    {
        $this->filter(false, "(YEAR(p.endDate) = {$year} || YEAR(p.startDate) ={$year})");
    }

    public function filterByStartDateBetween($startDate, $endDate)
    {
        $this->filter(false, "(date(p.startDate) between '{$startDate}' and '{$endDate}')");
    }

    public function filterByAvailability($startDate, $endDate)
    {
        $db        = Loader::db();
        $dh        = Loader::helper('date');
        $endDate   = $dh->getSystemDateTime($endDate . '  -1 hour', 'Y-m-d H');
        $startDate = $dh->getSystemDateTime($startDate . ' +1 hour', 'Y-m-d H');

        $startDate = $db->Quote($startDate);
        $endDate   = $db->Quote($endDate);
        $this->addToQuery("LEFT JOIN (SELECT DISTINCT(ac.pID) FROM AvailabilityCalendar ac WHERE {$startDate} BETWEEN ac.startDate AND ac.endDate OR {$endDate} BETWEEN ac.startDate AND ac.endDate) AS p1 on p.pID = p1.pID");
        $this->filter(false, "(p1.pID > 0)");

    }

    public function filterByBooking($startDate, $endDate)
    {
        $db        = Loader::db();
        $dh        = Loader::helper('date');
        $endDate   = $dh->getSystemDateTime($endDate . '  -1 hour', 'Y-m-d H');
        $startDate = $dh->getSystemDateTime($startDate . ' +1 hour', 'Y-m-d H');

        $startDate = $db->Quote($startDate);
        $endDate   = $db->Quote($endDate);
        $this->addToQuery("LEFT JOIN (SELECT DISTINCT(b.pID) FROM Booking b WHERE {$startDate} BETWEEN b.bookingStartDate AND b.bookingEndDate OR {$endDate} BETWEEN b.bookingStartDate AND b.bookingEndDate) AS p2 on p.pID = p2.pID");
        $this->filter(false, "p2.pID > 0");
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows      = parent::get($itemsToGet, $offset);
        $block_dates = [];

        foreach ($rows as $row) {
            $block_date                      = new PropertyBlockDates($row);
            if($this->populateProperty) {
                $block_date->setTotal($row['total']);
                $block_date->setYear($row['year']);
                $block_date->setMonth($row['month']);
                $block_date->setAvgNights($row['avgNights']);
            }
            $block_dates[$block_date->getID()] = $block_date;


        }

        return $block_dates;
    }
}