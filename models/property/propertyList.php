<?php
defined('C5_EXECUTE') or die('Access Denied.');

class PropertyList extends DatabaseItemList
{

    protected $userID;
    protected $queryCreated;
    protected $populateByStatus;
    protected $populateFavourites;
    protected $populateAverageAndTotalRating;
    protected $populateLocation;
    protected $populateApartmentArea;
    protected $populateApartmentType;
    protected $populateAreaType;
    protected $populateAvailableStatus;
    protected $cDate;

    public function __construct()
    {
        $this->queryCreated                  = false;
        $this->populateByStatus              = true;
        $this->populateFavourites            = false;
        $this->populateAverageAndTotalRating = false;
        $this->populateApartmentArea         = false;
        $this->populateAreaType              = false;
        $this->populateAvailableStatus       = false;
    }

    public function setBaseQuery()
    {
        $cols = '';

        if ($this->populateAverageAndTotalRating) {
            $cols .= ", pr.totalRatings, pr.averageRating";
        }
        if ($this->populateApartmentType) {
            $cols .= ", apt.name as apartmentType";
        }
        if ($this->populateApartmentArea) {
            $cols .= ", aa.name as apartmentArea";
        }
        if ($this->populateAreaType) {
            $cols .= ", at.name as areaType";
        }
        if ($this->populateAvailableStatus) {
            $cols .= ", " . $this->populateAvailabilityStatus();
        }
        $cols .= ", loc.name as location";

        $this->setQuery("SELECT p.*{$cols} FROM Properties p");
        $this->addToQuery("LEFT JOIN Locations loc on loc.locID = p.locID");
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

    public function populateFavourites($userID)
    {
        $this->populateFavourites = true;
        $this->userID             = (int) $userID;
    }

    public function populateByStatus($value = true)
    {
        $this->populateByStatus = $value;
    }

    public function populateAvailability()
    {
        $dh                            = Loader::helper('date');
        $date                          = $dh->getSystemDateTime('now', 'Y-m-d');
        $this->populateAvailableStatus = true;
        $this->cDate                   = $date;
    }


    /**
     * @param $id |array
     * @param string $comparison
     */
    public function filterById($id, $comparison = '=')
    {
        $this->filter('p.pID', $id, $comparison);
    }

    public function filterByStatus($value)
    {
        $this->filter('p.status', $value);
    }

    public function filterByGuests($guests)
    {
        $this->filter('p.maxGuests', $guests, '>=');
    }

    public function filterByBedrooms($bedrooms)
    {
       // $nonNumeric = !is_numeric($bedrooms);
       // if ($nonNumeric)
       // {
       //     $bedrooms = Property::BEDROOM_MAP[$bedrooms];
       // }
       // $this->filter('p.bedrooms', $bedrooms, $nonNumeric ? '=' : '>=');
       $bedrooms = (int)$bedrooms;
       $this->filter('p.bedrooms', $bedrooms, $bedrooms <= 3 ? '=' : '>=');
    }

    public function filterByPrice($price, $comparison = '>=')
    {
        $this->filter('p.perDayPrice', $price, $comparison);
    }

    public function filterByOwner($userID)
    {
        $this->filter('p.owner', $userID);
    }

    public function filterByAreaType($areaType)
    {
        $db                     = Loader::db();
        $this->populateAreaType = true;
        $areaType               = $db->Quote($areaType);
        $this->addToQuery("LEFT JOIN AreaTypes at on at.atID = p.atID");
        $this->filter(false, "at.name = {$areaType}");
    }

    public function filterByApartmentArea($apartmentArea)
    {
        /** @var DatabaseHelper $dh */
        $dh                          = Loader::helper('database');
        $this->populateApartmentArea = true;
        $apartmentArea               = $dh->quoteCommaSeparatedValues($apartmentArea);
        $this->addToQuery("LEFT JOIN ApartmentAreas aa on aa.aaID = p.aaID");
        $this->filter(false, "aa.name IN ( {$apartmentArea} )");
    }

    public function filterByApartmentType($apartmentType)
    {
        $db                          = Loader::db();
        $this->populateApartmentType = true;
        $apartmentType               = $db->Quote($apartmentType);
        $this->addToQuery("LEFT JOIN ApartmentTypes apt on apt.aptID = p.aptID");
        $this->filter(false, "apt.name = {$apartmentType}");
    }

    public function filterByLocation($location)
    {
        $db       = Loader::db();
        $loArr = explode(',',$location);
        if($location && is_array($loArr)) {
            if(is_array($loArr)) {
                foreach ($loArr as $k => $v) {
                    $loArr[$k] = $db->Quote($v);
                }
            }
            $location = implode(',',$loArr);
        } else {
            $location = $db->Quote($location);
        }
        $this->filter(false, "loc.name IN ( {$location} )");
    }

    public function filterByMonthlyAvailability()
    {
        $this->filter(false, "p.monthlyPrice > 0");
    }

    public function filterByWeeklyAvailability()
    {
        $this->filter(false, "p.weeklyPrice > 0");
    }

    public function sortByPrice($type = 'asc')
    {
        $this->sortBy('p.perDayPrice', $type);
    }

    public function sortByRating($type = 'asc')
    {
        $this->sortBy('pr.averageRating', $type);
    }

    public function filterByKeywords($keywords)
    {
        $db        = Loader::db();
        $qkeywords = $db->quote('%' . $keywords . '%');
        $otherFilters = '';
        $this->filter(
            false,
            "( p.name LIKE  {$qkeywords}  OR p.caption LIKE {$qkeywords}
             OR p.description LIKE {$qkeywords} OR loc.name LIKE {$qkeywords} {$otherFilters})
          ");
    }

    public function filterByAmenities($amenities)
    {
        /** @var DatabaseHelper $dh */
        $dh        = Loader::helper('database');
        $amenities = $dh->quoteCommaSeparatedValues($amenities);
        $this->addToQuery("LEFT JOIN (SELECT pa.pID,am.name FROM PropertyAmenities pa LEFT JOIN Amenities am ON pa.amID = am.amID) AS pam ON p.pID = pam.pID");
        $this->filter(false, "pam.name IN ( {$amenities} )");
    }

    public function filterByFacilities($facilities)
    {
        /** @var DatabaseHelper $dh */
        $dh        = Loader::helper('database');
        $facilities = $dh->quoteCommaSeparatedValues($facilities);
        $this->addToQuery("LEFT JOIN (SELECT pf.pID,f.name FROM PropertyFacilities pf LEFT JOIN Facilities f ON pf.fID = f.fID) AS pfa ON p.pID = pfa.pID");
        $this->filter(false, "pfa.name IN ( {$facilities} )");
    }

    public function filterByHomePageFilters($filters)
    {
        /** @var DatabaseHelper $dh */
        $dh        = Loader::helper('database');
        $filters = $dh->quoteCommaSeparatedValues($filters);
        $this->addToQuery("LEFT JOIN (SELECT phpf.pID,hpf.hpfID,hpf.name FROM PropertyHomePageFilters phpf LEFT JOIN HomePageFilters hpf ON phpf.hpfID = hpf.hpfID) AS homeF ON p.pID = homeF.pID");
        $this->filter(false, "homeF.name IN ( {$filters} ) OR homeF.hpfID IN ( {$filters} )");
    }

    public function filterByAvailability($startDate, $endDate)
    {
        $db        = Loader::db();
        $dh        = Loader::helper('date');
        $endDate   = $dh->getSystemDateTime($endDate . '  -1 hour', 'Y-m-d H');
        $startDate = $dh->getSystemDateTime($startDate . ' +1 hour', 'Y-m-d H');
        $startDate = $db->Quote($startDate);
        $endDate   = $db->Quote($endDate);
        $this->addToQuery("LEFT JOIN (SELECT DISTINCT(p1.pID) FROM Properties p1 Where pID not In (SELECT DISTINCT(ac.pID) FROM AvailabilityCalendar ac WHERE {$startDate} BETWEEN ac.startDate AND ac.endDate OR {$endDate} BETWEEN ac.startDate AND ac.endDate)) AS p2 on p.pID = p2.pID");
        $this->filter(false, "p2.pID IN (p.pID)");
    }

    protected function populateAvailabilityStatus()
    {
        $db    = Loader::db();
        $cDate = $db->Quote($this->cDate);
        $col   = "(case
                    when p.PID  IN (SELECT DISTINCT(ac.pID) FROM AvailabilityCalendar ac WHERE {$cDate} = ac.startDate) THEN 'Booked'
                    when p.PID  IN (SELECT DISTINCT(pbd.pID) FROM PropertyBlockDates pbd WHERE {$cDate} between pbd.startDate and pbd.endDate) THEN 'Booked'
                    else 'Unoccupied'
                    end) as bookingStatus";
        return $col;
    }

    public function filterUserFavourites($userID)
    {
        $this->addToQuery("LEFT JOIN UserFavourites uf on uf.pID = p.pID");
        $this->filter('uf.uID', $userID);
    }

    public function populateAverageAndTotalRatings()
    {
        $this->populateAverageAndTotalRating = true;
        $this->addToQuery("LEFT JOIN (SELECT r.pId,count(r.id) AS totalRatings, ROUND((SUM(reviewRating)/count(r.id)), 2) AS averageRating FROM Reviews r LEFT JOIN Properties p1 ON p1.pID = r.pId GROUP BY r.pId) AS pr ON p.pID = pr.pID");
    }

    /**
     * @param int $itemsToGet
     * @param int $offset
     * @return Property[]|bool
     */
    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        if ($this->populateByStatus) {
            $this->filterByStatus(1);
        }

        $rows       = parent::get($itemsToGet, $offset);
        $properties = [];

        foreach ($rows as $row) {
            if (!$row['pID']) return [];
            $property = new Property($row);

            $property->setLocation($row['location'] ? : Property::EMPTY_VALUE);
            if ($this->populateAverageAndTotalRating) {
                $property->averageRating = $row['averageRating'] ? : 0;
                $property->totalRatings  = $row['totalRatings'] ? : 0;
            }
            if ($this->populateApartmentType) {
                $property->setApartmentType($row['ApartmentType'] ? : Property::EMPTY_VALUE);
            }
            if ($this->populateApartmentArea) {
                $property->setApartmentArea($row['ApartmentArea'] ? : Property::EMPTY_VALUE);
            }
            if ($this->populateAreaType) {
                $property->setAreaType($row['AreaType'] ? : Property::EMPTY_VALUE);
            }
            if ($this->populateAvailableStatus) {
                $property->setBookingStatus($row['bookingStatus']);
            }

            $properties[$property->getID()] = $property;
        }

        /** Populate favourite */
        if ($this->populateFavourites && $this->userID) {

            $propertyIDs = array_keys($properties);

            $userFavouriteList = new UserFavouriteList();
            $userFavouriteList->filterByUserID($this->userID);
            $userFavouriteList->filterByPropertyID($propertyIDs);
            $userFavourites = $userFavouriteList->get();

            /** @var UserFavourite $favourite */
            foreach ($userFavourites as $favourite) {
                if (array_key_exists($favourite->getPropertyID(), $properties)) {
                    /** @var Property $property */
                    $property = $properties[$favourite->getPropertyID()];
                    $property->setFavorite();
                }
            }

        }

        return $properties;
    }


}