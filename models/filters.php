<?php

class Filters
{
    protected $minPrice;
    protected $maxPrice;
    protected $maxGuests;
    protected $maxBedrooms;
    protected $propertyTypes;
    protected $locations;
    protected $moreFilters;

    /**
     * @return mixed
     */
    public function getMinPrice()
    {
        if (!$this->minPrice) {
            $this->setData();
        }
        return $this->minPrice;
    }

    /**
     * @param mixed $minPrice
     * @return Filters
     */
    public function setMinPrice($minPrice)
    {
        $this->minPrice = $minPrice;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getMaxBedrooms()
    {
        if (!$this->maxBedrooms) {
            $this->setData();
        }
        return $this->maxBedrooms;
    }

    /**
     * @param mixed $maxBedrooms
     */
    public function setMaxBedrooms($maxBedrooms)
    {
        $this->maxBedrooms = $maxBedrooms;
    }

    /**
     * @return mixed
     */
    public function getMaxPrice()
    {
        if (!$this->minPrice) {
            $this->setData();
        }
        return $this->maxPrice;
    }

    /**
     * @param mixed $maxPrice
     * @return Filters
     */
    public function setMaxPrice($maxPrice)
    {
        $this->maxPrice = $maxPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxGuests()
    {
        return $this->maxGuests;
    }

    /**
     * @param mixed $maxGuests
     * @return Filters
     */
    public function setMaxGuests($maxGuests)
    {
        $this->maxGuests = $maxGuests;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPropertyTypes()
    {
        if (!$this->propertyTypes) {
            $db            = Loader::db();
            $propertyTypes = [];
            $q             = "SELECT DISTINCT name FROM ApartmentTypes";
            $res           = $db->Execute($q);
            while ($row = $res->FetchRow()) {
                $propertyTypes[] = $row['name'];
            }
            $this->propertyTypes = $propertyTypes;
        }
        return $this->propertyTypes;
    }

    /**
     * @param mixed $propertyTypes
     * @return Filters
     */
    public function setPropertyTypes($propertyTypes)
    {
        $this->propertyTypes = $propertyTypes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocations()
    {
        if (!$this->locations) {
            $db        = Loader::db();
            $locations = [];
            $q         = "SELECT DISTINCT name FROM Locations";
            $res       = $db->Execute($q);
            while ($row = $res->FetchRow()) {
                $locations[] = $row['name'];
            }
            $this->locations = $locations;
        }
        return $this->locations;
    }

    /**
     * @param mixed $locations
     * @return Filters
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMoreFilters()
    {
        if (!$this->moreFilters) {
            $result = [];
            $db     = Loader::db();
            $q      = "SELECT fID, name FROM Facilities";
            $res    = $db->Execute($q);
            while ($row = $res->FetchRow()) {
                // $result[$row['fID']] = $row['name'];
                array_push($result, $row['name']);
            }

            $this->moreFilters = $result;
        }
        return $this->moreFilters;
    }

    /**
     * @param mixed $moreFilters
     * @return Filters
     */
    public function setMoreFilters($moreFilters)
    {
        $this->moreFilters = $moreFilters;
        return $this;
    }

    public function setData()
    {
        $db  = Loader::db();
        $q   = "SELECT MIN(perDayPrice) AS minPrice, MAX(perDayPrice) AS maxPrice,MAX(maxGuests) AS maxGuests ,
              MAX(bedrooms) AS maxBedrooms FROM Properties";
        $res = $db->GetRow($q);

        if ($res) {
            $this->setMinPrice($res['minPrice']);
            $this->setMaxPrice($res['maxPrice']);
            $this->setMaxGuests($res['maxGuests']);
            $this->setMaxBedrooms($res['maxBedrooms']);
            return null;
        }
        $this->setMinPrice(50);
        $this->setMaxPrice(5000);
        $this->setMaxGuests(5);
    }
}