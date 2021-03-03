<?php
/**
 * Created BY PhpStorm.
 * User: backend1
 * Date: 18/2/19
 * Time: 11:26 AM
 */

clASs StatisticList extends DatabaseItemList
{

    protected $queryCreated;
    protected $startDate;
    protected $endDate;
    protected $propertyID;
    protected $userID;
    protected $year;

    public function __construct()
    {
        $this->propertyID = false;
        $this->userID     = false;
    }

    protected function setBASeQuery()
    {
        /** @var DateHelper $dh */
        $db = Loader::db();
        $dh = Loader::helper('date');

        $userCol   = '';
        $propTable = '';
        $where     = '';
        if ($this->startDate && $this->endDate) {
            $this->startDate = $dh->getFormattedDate($this->startDate, 'Y-m-d');
            $this->endDate   = $dh->getFormattedDate($this->endDate, 'Y-m-d');
            $this->startDate = $db->Quote($this->startDate);
            $this->endDate   = $db->Quote($this->endDate);
            $where           = "WHERE b1.bookingStartDate BETWEEN {$this->startDate} AND {$this->endDate}";
        }
        if ($this->propertyID) {
            if (!$where) {
                $where .= " WHERE b1.pID = {$this->propertyID}";
            } else {
                $where .= " AND b1.pID = {$this->propertyID}";
            }
        }
        if ($this->userID) {
            if (!$where) {
                $where .= " WHERE p.owner = {$this->userID}";
            } else {
                $where .= " AND p.owner = {$this->userID}";
            }
            $userCol   = ',p.owner';
            $propTable = "LEFT JOIN (SELECT p.pID,p.owner from Properties p) AS p ON p.pID = b1.pID";
        }
        if ($this->year) {
            if (!$where) {
                $where .= " WHERE  (YEAR(b1.bookingEndDate) = {$this->year} || YEAR(b1.bookingStartDate) ={$this->year}) ";
            } else {
                $where .= " and  (YEAR(b1.bookingEndDate) = {$this->year} || YEAR(b1.bookingStartDate) ={$this->year}) ";
            }
        }

        $where .= " and b1.bookingStatus='paid' GROUP BY bID";

        $table = "FROM Booking AS b1
                   {$propTable}
                   {$where} ";
        $cols  = "SELECT b1.bID,b1.pID,b1.bookingStartDate,b1.bookingEndDate, YEAR(b1.bookingStartDate) AS year,
                   UPPER(DATE_FORMAT(b1.bookingStartDate,'%b')) AS month,b1.total,b1.bookingStatus, DATEDIFF(b1.bookingEndDate, b1.bookingStartDate) AS avgNights{$userCol}";
        $this->setQuery("SELECT t.* from ({$cols} {$table}) t");
    }

    protected function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBASeQuery();
            $this->queryCreated = true;
        }
    }

    public function filterByUserID($uID)
    {
        $this->userID = $uID;
    }

    public function filterByPropertyID($id)
    {
        $this->propertyID = $id;
    }

    public function filterByYear($year)
    {
        $this->year = $year;
    }

    public function setDateRange($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows       = parent::get($itemsToGet, $offset);
        $statistics = [];

        foreach ($rows AS $row) {
            $statistic    = new Statistic($row);
            $statistics[] = $statistic;

        }
        return $statistics;
    }
}