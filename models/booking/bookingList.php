<?php
defined('C5_EXECUTE') or die('Access Denied.');

class BookingList extends DatabaseItemList
{

    protected $queryCreated;
    protected $populatePropertyDetails;
    protected $populateEventStatus;
    protected $filterByCheckout;

    public function __construct()
    {
        $this->populatePropertyDetails = false;
        $this->populateEventStatus     = false;
        $this->filterByCheckout     = false;
    }

    protected function setBaseQuery()
    {
        $cols = '';
        if ($this->populatePropertyDetails) {
            $cols .= ", p.*";
            $cols .= ", loc.name as location";
            $cols .= ", pr.totalRatings, pr.averageRating";
            if ($this->populateEventStatus) {
                $cols .= ", " . $this->getEventStatusColumns();
            }
            $this->addToQuery("INNER JOIN Properties AS p ON p.pID = b.pID");
            $this->addToQuery("LEFT JOIN Locations AS loc on loc.locID = p.locID");
            $this->addToQuery("LEFT JOIN (SELECT r.pId,count(r.id) AS totalRatings, ROUND((SUM(reviewRating)/count(r.id)), 2) AS averageRating 
            FROM Reviews r LEFT JOIN Properties p1 ON p1.pID = r.pId) AS pr ON p.pID = pr.pID");

        }
        if(!$this->populatePropertyDetails && $this->filterByCheckout){
            $cols .= ", p.checkOutTime";
            $this->addToQuery("INNER JOIN Properties AS p ON p.pID = b.pID");
            $this->addToQuery("INNER JOIN Users AS u ON b.uID = u.uID");
            $this->addToQuery("INNER JOIN UserDetails AS ud ON b.uID = u.uID");
            $this->addToQuery("INNER JOIN Referral AS r ON (r.referrerEmail = ud.referredBy and r.referredEmail = u.uEmail)");
        }
        $this->setQuery("select b.*{$cols} from Booking b");
    }

    protected function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = true;
        }
    }

    public function filterUpcoming()
    {
    }

    public function filterByDate()
    {
    }

    public function filterByDateBooked()
    {
    }

    public function filterByUserID($uID)
    {
        $this->filter('b.uID', $uID);

    }

    public function filterByID($id)
    {
        $this->filter('b.bID', $id);
    }

    public function filterByBookingStatus($bookingStatus)
    {
        $this->filter('b.bookingStatus', $bookingStatus);
    }

    public function filterByPropertyId($id)
    {
        $this->filter('b.pID', $id);
    }

    public function filterByToDate($date)
    {
        $this->filter(false, "date(b.bookingEndDate) <= '{$date}'");
    }

    public function filterByBookingType($type, $currDate)
    {
        if ($type == 'previous') {
            $this->filter(false, "date(b.bookingStartDate) < '{$currDate}'");
        } else if ($type == 'upcoming') {
            $this->filter(false, "date(b.bookingStartDate) > '{$currDate}'");
        }
    }

    public function filterByFromDate($date)
    {
        $this->filter(false, "date(b.bookingStartDate) >= '{$date}'");
    }

    public function filterCompleted($time = '')
    {
        /** @var DateHelper $dh */
        $dh   = Loader::helper('date');
        $date = $dh->getSystemDateTime("now {$time}", 'Y-m-d H:i:s');
        $this->filter(false, "date(b.bookingEndDate) < '{$date}' and b.bookingStatus = 'paid'");
    }

    public function filterReferredBooking()
    {
        $this->filter(false, "r.creditSent = 'NO'");
        $this->groupBy('b.bID');
    }

    public function filterByRateNotified($value = 1)
    {
        $this->filter('b.notifiedToRate', $value);
    }

    public function populatePropertyDetails()
    {
        $this->populatePropertyDetails = true;
    }

    public function filterByCheckoutTime()
    {
        $this->filterByCheckout = true;
        /** @var DateHelper $dh */
        $dh   = Loader::helper('date');
        $date = $dh->getSystemDateTime("now", 'Y-m-d H:i:s');
        $this->filter(false, "CONCAT(b.bookingEndDate,' ',p.checkOutTime) <= '{$date}'");
    }

    public function filterByKeywords($keywords)
    {
        $db        = Loader::db();
        //$qkeywords = $db->quote('%' . $keywords . '%');
        $qkeywords = $db->quote($keywords . '%');
        $this->filter(
            false,
            "( bookingNo LIKE  {$qkeywords}  )
          ");
    }

    public function populateEventStatus()
    {
        $this->populateEventStatus = true;
    }

    public function getEventStatusColumns()
    {
        $db = Loader::db();
        /** @var DateHelper $dh */
        $dh   = Loader::helper('date');
        $date = $dh->getSystemDateTime('now', 'Y-m-d');
        $date = $db->Quote($date);
        $col  = "(case
                    WHEN date(b.bookingStartDate) > {$date} THEN 'upcoming'
                    WHEN date(b.bookingStartDate) < {$date} THEN 'completed'
                    else 'in-progress'
                    end) as eventStatus";
        return $col;
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows     = parent::get($itemsToGet, $offset);
        $bookings = [];

        foreach ($rows as $row) {
            $booking                      = new Booking($row);
            $bookings[$booking->getBID()] = $booking;
            if ($this->populatePropertyDetails) {
                $property                = new Property($row);
                $property->averageRating = $row['averageRating'];
                $property->totalRatings  = $row['totalRatings'];
                $booking->setProperty($property);
            }

        }
        return $bookings;
    }
}
