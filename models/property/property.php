<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class Property
{
    protected $pID;
    protected $name;
    protected $caption;
    protected $description;
    protected $pPath;
    protected $pPathOld;
    protected $thumbID;/*FK - Images  */
    protected $latitude;
    protected $longitude;
    protected $locID; /*FK - Location  */
    protected $noOfRooms;
    protected $bedrooms;
    protected $bathrooms;
    protected $maxGuests;
    protected $beds;
    protected $aaID; /*FK -Apartment Area  */
    protected $atID; /*FK -Area Type   */
    protected $aptID;  /*FK - Apartment Type */
    protected $monthlyOffering;
    protected $dailyOffering;
    protected $monthlyPrice;
    protected $weeklyPrice;
    protected $perDayPrice;
    protected $owner;  /*FK - User Id */
    protected $checkInTime;
    protected $checkOutTime;
    protected $status;
    protected $propertyRules;
    protected $cancellationPolicy;
    protected $locationDescription;
    protected $createdAt;
    protected $updatedAt;

    protected $amenities;
    protected $apartmentArea;
    protected $areaType;
    protected $apartmentType;
    protected $images;
    protected $facilities;
    protected $homePageFilters;
    protected $thumbnail;
    protected $isFavorite;
    protected $location;
    protected $reviews = [];

    public    $averageRating;
    public    $totalRatings;
    protected $bookingStatus;
    protected $ownerInfo;
    protected $tourismFee;
    protected $minNights;


    const EMPTY_VALUE     = '-1';
    const THUMB_PATH      = BASE_URL . DIR_REL . '/files/properties/';
    const PARENT_PATH     = '/properties';
    const BEDROOM_OPTIONS = [
        1  => 1,
        2  => 2,
        3  => 3,
        4  => 4,
        5  => 5,
        -1 => 'Studio'
    ];
    const BEDROOM_MAP     = [
        'Studio' => -1,
        'studio' => -1
    ];

    const PATH     = 'pPath';
    const PATH_OLD = 'pPathOld';

    function __construct($row)
    {
        $this->pID                 = $row['pID'];
        $this->name                = $row['name'];
        $this->caption             = $row['caption'];
        $this->description         = $row['description'];
        $this->pPath               = $row['pPath'];
        $this->pPathOld            = $row['pPathOld'];
        $this->thumbID             = $row['thumbID'];
        $this->latitude            = $row['latitude'];
        $this->longitude           = $row['longitude'];
        $this->locID               = $row['locID'];
        $this->noOfRooms           = $row['noOfRooms'];
        $this->bedrooms            = $row['bedrooms'];
        $this->bathrooms           = $row['bathrooms'];
        $this->maxGuests           = $row['maxGuests'];
        $this->beds                = $row['beds'];
        $this->aaID                = $row['aaID'];
        $this->atID                = $row['atID'];
        $this->aptID               = $row['aptID'];
        $this->monthlyPrice        = $row['monthlyPrice'];
        $this->weeklyPrice         = $row['weeklyPrice'];
        $this->perDayPrice         = $row['perDayPrice'];
        $this->owner               = $row['owner'];
        $this->checkInTime         = $row['checkInTime'];
        $this->checkOutTime        = $row['checkOutTime'];
        $this->status              = $row['status'];
        $this->propertyRules       = $row['propertyRules'];
        $this->cancellationPolicy  = $row['cancellationPolicy'];
        $this->locationDescription = $row['locationDescription'];
        $this->tourismFee          = $row['tourismFee'];
        $this->minNights           = $row['minNights'];
        $this->createdAt           = $row['createdAt'];
        $this->updatedAt           = $row['updatedAt'];
        $this->averageRating       = false;
        $this->totalRatings        = false;
        $this->isFavorite          = false;

    }


    /**
     * @param $name
     * @param $caption
     * @param $description
     * @param $latitude
     * @param $longitude
     * @param $locationID
     * @param $noOfRooms
     * @param $bedrooms
     * @param $bathrooms
     * @param $maxGuests
     * @param $beds
     * @param $apartmentAreaID
     * @param $areaTypeID
     * @param $apartmentTypeID
     * @param $monthlyPrice
     * @param $perDayPrice
     * @param $owner
     * @param $checkInTime
     * @param $checkOutTime
     * @param $weeklyPrice
     * @param $tourismFee
     * @return Property
     */
    public static function add($name, $caption, $description, $latitude, $longitude, $locationID, $noOfRooms, $bedrooms, $bathrooms, $maxGuests, $beds, $apartmentAreaID, $areaTypeID, $apartmentTypeID, $monthlyPrice, $perDayPrice, $owner, $checkInTime, $checkOutTime, $weeklyPrice, $tourismFee, $minNights)
    {

        $db    = Loader::db();
        $query = "INSERT INTO Properties(name,caption,description,latitude, longitude ,locID ,noOfRooms, 
                      bedrooms, bathrooms, maxGuests, beds, aaID, atID, aptID, monthlyPrice, 
                      perDayPrice ,owner ,checkInTime ,checkOutTime,createdAt, weeklyPrice, tourismFee, minNights ) 
              VALUES ( ? , ? , ? , ? , ? ,  ? , ? , ? , ? , ?, ? ,  ? , ? , ? , ? , ? ,  ? , ? , ? , ?, ?, ?, ?  ) ";
        $ret   = $db->Execute($query, [
            $name,
            $caption,
            $description,
            $latitude,
            $longitude,
            $locationID,
            $noOfRooms,
            $bedrooms,
            $bathrooms,
            $maxGuests,
            $beds,
            $apartmentAreaID,
            $areaTypeID,
            $apartmentTypeID,
            $monthlyPrice,
            $perDayPrice,
            $owner,
            $checkInTime,
            $checkOutTime,
            null,
            $weeklyPrice,
            $tourismFee,
            $minNights
        ]);
        if ($ret) {
            $pr = self::getByID($db->Insert_ID());
            $pr->updatePath();
            return $pr;
        }

        return null;
    }

    public function update($name, $caption, $description, $latitude, $longitude, $locationID, $noOfRooms, $bedrooms, $bathrooms, $maxGuests, $beds, $apartmentAreaID, $areaTypeID, $apartmentTypeID, $monthlyPrice, $perDayPrice, $owner, $checkInTime, $checkOutTime, $status, $weeklyPrice, $tourismFee, $minNights)
    {

        $db    = Loader::db();
        $query = "UPDATE Properties SET name = ?,caption = ?,description = ?,latitude = ?, longitude = ?,locID = ?,noOfRooms = ?, 
                      bedrooms = ?, bathrooms = ?, maxGuests = ?, beds = ?, aaID = ?, atID = ?, aptID = ?, monthlyPrice = ?, 
                      perDayPrice = ? ,owner = ? ,checkInTime = ? ,checkOutTime = ?, status = ?, weeklyPrice = ?, tourismFee = ?, minNights = ? WHERE pID = ?";
        $ret   = $db->Execute($query, [
            $name,
            $caption,
            $description,
            $latitude,
            $longitude,
            $locationID,
            $noOfRooms,
            $bedrooms,
            $bathrooms,
            $maxGuests,
            $beds,
            $apartmentAreaID,
            $areaTypeID,
            $apartmentTypeID,
            $monthlyPrice,
            $perDayPrice,
            $owner,
            $checkInTime,
            $checkOutTime,
            $status,
            $weeklyPrice,
            $tourismFee,
            $minNights,
            $this->getID()
        ]);
        if ($ret) {
            return self::getByID($this->getID());
        }

        return null;
    }

    public function updatePath($key = 'pPath', $newPath = '')
    {
        $db = Loader::db();
        /** @var TextHelper $th */
        $th    = Loader::helper('text');
        if (!$newPath) {
            $path  = $th->handle($this->getName());
            $path  = $this->slugSafeString($path);
            $path  = $this->getUniquePath($path, $key);
        } else {
            $path = $newPath;
        }
        $query = "UPDATE Properties SET {$key} = ? WHERE pID = ?";
        $db->Execute($query, [
            $path,
            $this->getID()
        ]);
    }

    public function updatePropertyRules($propertyRules, $cancellationPolicy, $locationDescription)
    {
        $db = Loader::db();
        /** @var TextHelper $th */
        $th    = Loader::helper('text');
        $query = "UPDATE Properties SET propertyRules = ?, cancellationPolicy = ?, locationDescription = ? WHERE pID = ?";
        $db->Execute($query, [
            $propertyRules,
            $cancellationPolicy,
            $locationDescription,
            $this->getID()
        ]);
    }

    public function updateAmenities($amenities)
    {
        return PropertyAmenities::updateAmenities($this->getID(), $amenities);
    }

    public function updateFacilities($facilities)
    {
        $facilities = array_filter($facilities);
        return PropertyFacilities::updateFacilities($this->getID(), $facilities);
    }

    public function updateHomePageFilters($homePageFilters)
    {
        return PropertyHomePageFilters::updateHomePageFilters($this->getID(), $homePageFilters);
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM Properties WHERE pID = ?";
        $ret   = $db->Execute($query, [$this->getID()]);

        return $ret;
    }

    public static function where($condition = null)
    {
        $condition_vars   = [];
        $condition_suffix = '1';

        if (!is_array($condition)) {
            if ($condition) {
                $condition_suffix = $condition;
            }
        } else {

            $condition_suffix = '';
            $last_key         = end(array_keys($condition));

            foreach ($condition as $key => $value) {

                if ($value === null) {
                    $condition_suffix .= $key . ' IS NULL';
                } else {
                    $condition_suffix .= $key . ' = ?';
                    $condition_vars[] = $value;
                }

                if ($key !== $last_key) {
                    $condition_suffix .= " AND ";
                }
            }
        }

        $db     = Loader::db();
        $query  = "SELECT * FROM Properties WHERE {$condition_suffix}";
        $result = $db->Execute($query, $condition_vars);

        $links = [];

        while ($row = $result->FetchRow()) {
            $links[] = new static($row);
        }

        return $links;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM Properties WHERE pID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['pID'])) {
            return new Property($row);
        }

        return null;
    }

    public static function getByPath($path, $key = 'pPath')
    {
        $db    = Loader::db();
        $query = "SELECT * FROM Properties WHERE {$key} = ?";
        $row   = $db->GetRow($query, [$path]);

        if (isset($row['pID'])) {
            return new Property($row);
        }

        return null;
    }

    /**
     * @return float
     */
    public function getAverageRating()
    {
        if (!$this->averageRating && !is_numeric($this->averageRating)) {
            $this->populateAverageAndTotalRatings();
        }

        return $this->averageRating;
    }

    /**
     * @return int
     */
    public function getTotalRatings()
    {
        if (!$this->totalRatings && !is_numeric($this->averageRating)) {
            $this->populateAverageAndTotalRatings();
        }

        return $this->totalRatings;
    }

    private function populateAverageAndTotalRatings()
    {

        $propertyList = new PropertyList();
        $propertyList->filterById($this->getID());
        $propertyList->populateAverageAndTotalRatings();
        /** @var Property $property */
        $property = reset($propertyList->get());

        $this->totalRatings  = $property->getTotalRatings();
        $this->averageRating = $property->getAverageRating();
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->pID;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->pPath;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        if (!$this->createdAt) {
            $this->createdAt = null;
        }
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        if (!$this->updatedAt) {
            $this->updatedAt = null;
        }
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        if (!$this->location) {
            /** @var Location $loc */
            $loc            = Location::getByID($this->locID);
            $this->location = $loc ? $loc->getName() : null;
        }
        return $this->location != self::EMPTY_VALUE ? $this->location : null;
    }

    /**
     * @return string
     */
    public function setLocation($location)
    {
        return $this->location = $location;
    }

    /**
     * @return string
     */
    public function getNoOfRooms()
    {
        return $this->noOfRooms;
    }

    /**
     * @return string
     */
    public function getBedrooms()
    {
        return $this->bedrooms;
    }

    /**
     * @return string
     */
    public function getBathrooms()
    {
        return $this->bathrooms;
    }

    /**
     * @return string
     */
    public function getMaxGuests()
    {
        return $this->maxGuests;
    }

    /**
     * @return string
     */
    public function getApartmentAreaId()
    {
        return $this->aaID;
    }

    /**
     * @return string
     */
    public function getAreaTypeID()
    {
        return $this->atID;
    }

    /**
     * @return string
     */
    public function getLocationID()
    {
        return $this->locID;
    }

    /**
     * @return string
     */
    public function getApartmentTypeID()
    {
        return $this->aptID;
    }

    /**
     * @return string
     */
    public function getMonthlyPrice()
    {
        return $this->monthlyPrice;
    }

    /**
     * @return string
     */
    public function getWeeklyPrice()
    {
        return $this->weeklyPrice;
    }

    /**
     * @return float
     */
    public function getPerDayPrice()
    {
        return $this->perDayPrice;
    }

	/**
	 * @return int|string
	 */
	public function getMinPropertyPrice()
	{
		/** @var DateHelper $dh */
		$dh = Loader::helper('date');

		$seasonsPrice = 0;

		$db    = Loader::db();
		$query = "SELECT min(seasonPrice) as price FROM PropertySeasons WHERE pID = ?  and seasonStartDate = ?";
		$row   = $db->GetRow($query, [$this->getID(), $dh->date('Y-m-d')]);


		$seasonsPrice = isset($row['price']) ? $row['price'] : 0;

		if($seasonsPrice == 0 || ($seasonsPrice > $this->getPerDayPrice())) {
			return $this->getPerDayPrice();
		}

		return $seasonsPrice;
	}

    /**
     * @return string
     */
    public function getOwnerID()
    {
        return $this->owner;
    }

    /**
     * @return UserInfo
     */
    public function getOwnerInfo()
    {
        if (!$this->ownerInfo) {
            $this->ownerInfo = UserInfo::getByID($this->getOwnerID());
        }
        return $this->ownerInfo;
    }

    /**
     * @param mixed $ownerInfo
     */
    public function setOwnerInfo($ownerInfo)
    {
        $this->ownerInfo = $ownerInfo;
    }

    /**
     * @return string
     */
    public function getCheckInTime()
    {
        return $this->checkInTime;
    }

    /**
     * @return string
     */
    public function getCheckOutTime()
    {
        return $this->checkOutTime;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getBeds()
    {
        return $this->beds;
    }

    /**
     * @return mixed
     */
    public function getBookingStatus()
    {
        return $this->bookingStatus;
    }

    /**
     * @param mixed $bookingStatus
     */
    public function setBookingStatus($bookingStatus)
    {
        $this->bookingStatus = $bookingStatus;
    }

    public function getTourismFee()
    {
        return $this->tourismFee;
    }

    /**
     * @return mixed
     */
    public function getMinNights()
    {
        return $this->minNights;
    }



    /**
     * @return mixed
     */
    public function getPropertyRules($format = false)
    {
        /** @var TextHelper $th */
        $th = Loader::helper('text');
        if ($format) {
            return $th->getArrayFromHtml($this->propertyRules);
        }
        return $this->propertyRules;
    }

    /**
     * @return mixed
     */
    public function getCancellationPolicy($format = false)
    {
        /** @var TextHelper $th */
        $th = Loader::helper('text');
        if ($format) {
            return $th->getArrayFromHtml($this->cancellationPolicy);
        }
        return $this->cancellationPolicy;
    }

    /**
     * @return mixed
     */
    public function getLocationDescription($format = false)
    {
        /** @var TextHelper $th */
        $th = Loader::helper('text');
        if ($format) {
            return $th->getArrayFromHtml($this->locationDescription);
        }
        return $this->locationDescription;
    }

    /**
     * @param bool $format
     * @return array|Facility[]
     */
    public function getAmenities($format = false)
    {

        if (!$this->amenities) {
            $amenities = PropertyAmenities::getPropertyAmenities($this->getID());
            if ($format) {
                $temp = [];
                /** @var Amenity $amenity */
                foreach ($amenities as $amenity) {
                    array_push($temp, [
                        'id'    => $amenity->getID(),
                        'value' => $amenity->getName(),
                        'icon'  => $amenity->getImagePath(),
                    ]);
                }
                $amenities = $temp;
            }
            $this->amenities = $amenities;
        }
        return $this->amenities;
    }

    public function getApartmentArea()
    {
        if (!$this->apartmentArea) {
            if ($this->getApartmentAreaId()) {
                $apartmentArea       = ApartmentArea::getByID($this->getApartmentAreaId());
                $this->apartmentArea = ($apartmentArea) ? $apartmentArea->getName() : null;
            }
        }

        return $this->apartmentArea != self::EMPTY_VALUE ? $this->apartmentArea : null;
    }

    public function setApartmentArea($apartmentArea)
    {
        return $this->apartmentArea = $apartmentArea;
    }

    public function getAreaType()
    {
        if (!$this->areaType) {
            if ($this->getAreaTypeID()) {
                $areaType       = AreaType::getByID($this->getAreaTypeID());
                $this->areaType = ($areaType) ? $areaType->getName() : null;
            }
        }

        return $this->areaType != self::EMPTY_VALUE ? $this->areaType : null;
    }

    public function setAreaType($areaType)
    {
        return $this->areaType = $areaType;
    }

    public function getApartmentType()
    {
        if (!$this->apartmentType) {
            if ($this->getApartmentTypeId()) {
                $apartmentType       = ApartmentType::getByID($this->getApartmentTypeId());
                $this->apartmentType = ($apartmentType) ? $apartmentType->getName() : null;
            }
        }

        return $this->apartmentType != self::EMPTY_VALUE ? $this->apartmentType : null;
    }

    public function setApartmentType($apartmentType)
    {
        return $this->apartmentType = $apartmentType;
    }

    public function getThumbnailID()
    {
        return $this->thumbID;
    }

    public function setThumbnail($imgID)
    {
        $db    = Loader::db();
        $query = "UPDATE Properties SET thumbID = ? WHERE pID = ?";
        $ret   = $db->Execute($query, [
            $imgID,
            $this->getID()
        ]);

        return $ret;
    }

    /**
     * @return Images|null|string
     */
    public function getThumbnail($helper = false)
    {
        /** @var ImageHelper $ih */
        $ih = Loader::helper('image');

        if (!$this->thumbnail) {
            if ($this->getThumbnailID()) {
                $this->thumbnail = Images::getByID($this->getThumbnailID());
            }
        }
        if(!$this->thumbnail) {
            $this->thumbnail = reset($this->getPropertyImages());
        }
        if($helper && $this->thumbnail) {
            if ($this->thumbnail instanceof Images) {
                $img = $ih->getThumbnail($this->getThumbnailPath(), 500, 500);
                return $img->src ? $img->src : $this->getThumbnailPath();
            }
        }
        return $this->thumbnail;
    }


    public function setFavorite()
    {
        $this->isFavorite = true;
    }

    public function getFavorite()
    {
        return $this->isFavorite;
    }

    /**
     * @return string|null
     */
    public function getThumbnailPath()
    {
        return $this->getThumbnail() ? self::THUMB_PATH . $this->getThumbnail()->getPath() : null;
    }

    public function getThumbnailViaHelper()
    {
        return $this->getThumbnail(true);
    }

    /**
     * @return array|Images[]
     */
    public function getPropertyImages($format = false)
    {
        if (!$this->images) {
            $images = PropertyImages::getPropertyImages($this->getID());;
            if ($format) {
                $temp = [];
                /** @var Images $image */
                foreach ($images as $image) {
                    $temp[] = Property::getImagePath($image);
                }
                $images = $temp;
            }
            $this->images = $images;
        }

        return $this->images;
    }

    /**
     * @param bool $format
     * @param bool $currency
     * @return array
     * @throws Zend_Currency_Exception
     */
    public function getPropertyFacilities($format = false, $currency = true)
    {
        /** @var PriceHelper $ph */
        $ph = Loader::helper('price');
        if (!$this->facilities) {
            $facilities = PropertyFacilities::getPropertyFacilities($this->getID());
            if ($format) {
                $temp = [];
                /** @var PropertyFacilities $facility */
                foreach ($facilities as $facility) {
                    $temp[$facility->getFacilityID()] = [
                        'id'    => $facility->getID(),
                        'value' => $facility->getName(),
                        'price' => $currency ? $ph->format($facility->getPrice()) : $facility->getPrice(),
                        'icon'  => $facility->getImagePath(),
                    ];
                }
                $facilities = $temp;
            }
            $this->facilities = $facilities;
        }

        return $this->facilities;
    }

    /**
     * @param bool $format
     * @return array|HomePageFilters[]
     */
    public function getHomePageFilters($format = false)
    {
        if (!$this->homePageFilters) {
            $homePageFilters = PropertyHomePageFilters::getPropertyHomePageFilters($this->getID());
            if ($format) {
                $temp = [];
                /** @var HomePageFilters $filter */
                foreach ($homePageFilters as $filter) {
                    array_push($temp, [
                        'id'    => $filter->getID(),
                        'value' => $filter->getName(),
                    ]);
                }
                $homePageFilters = $temp;
            }
            $this->homePageFilters = $homePageFilters;
        }

        return $this->homePageFilters;
    }

    /**
     * @param Images $image
     * @param int $maxWidth
     * @param int $maxHeight
     * @param bool $crop
     * @param string $class
     */

    public static function outputImage($image, $maxWidth = 300, $maxHeight = 300, $crop = false, $class = '')
    {
        echo '<img src="' . self::getImagePath($image, $maxWidth, $maxHeight, $crop) . '" alt="' . e($image->getCaption()) . '" class="' . $class . '">';
    }

    /**
     * Generates the image object for the article
     * using the image helper and returns the
     * thumbnail path.
     *
     * @param Images $image
     * @param int $maxWidth
     * @param int $maxHeight
     * @param bool $crop
     * @param int $compression
     *
     * @return string
     */
    public static function getImagePath($image, $maxWidth = 1312, $maxHeight = 1080, $crop = false,$compression = 80)
    {
        /** @var ImageHelper $ih */
        $ih = Loader::helper('image');
        $ih->setJpegCompression($compression);

        $imageFilename    = $image ? $image->getPath() : null;
        $imagePath        = self::THUMB_PATH . $imageFilename;
        $defaultImagePath = self::THUMB_PATH . 'default.jpg';
        $img              = null;

        if ($imageFilename) {
            $img = $ih->getThumbnail($imagePath, $maxWidth, $maxHeight, $crop);
            $img = $img->src ? $img->src : $imagePath;
        }

        if (!$img) {
            $img = $ih->getThumbnail($defaultImagePath, $maxWidth, $maxHeight, $crop);
            $img = $img->src ? $img->src : $defaultImagePath;
        }
        return BASE_URL . str_replace(BASE_URL, '', $img);
    }

    public function getReviews()
    {
        if (!$this->reviews) {
            $this->reviews = $this->populateReviews();
        }
        return $this->reviews;
    }

    protected function populateReviews()
    {
        /** @var DateHelper $dh */
        $dh         = Loader::helper('date');
        $reviewList = new ReviewList();
        $reviewList->filterByProperty($this->getID());
        $reviewList->populateUsers();
        $reviewList->populateBookings();
        $reviewList->setItemsPerPage(5);

        $reviews = $reviewList->getPage(1);
        $temp    = [];

        $rating0 = 0;
        $rating1 = 0;
        $rating2 = 0;
        $rating3 = 0;
        $rating4 = 0;
        $rating5 = 0;

        /** @var Review $review */
        foreach ($reviews as $review) {
            $booking  = $review->getBooking();
            $ui       = $review->getUserInfo();
            $property = $review->getProperty();
            switch ($review->getReviewRating()) {
                case 0:
                    $rating0++;
                    break;
                case 1:
                    $rating1++;
                    break;
                case 2:
                    $rating2++;
                    break;
                case 3:
                    $rating3++;
                    break;
                case 4:
                    $rating4++;
                    break;
                case 5:
                    $rating5++;
                    break;
            }

            $totalNights = $dh->getNoOfNights($booking->getBookingStartDate(), $booking->getbookingEndDate());
            $temp[]      = [
                'rID'           => $review->getId(),
                'userName'      => $ui->getFullName(),
                'avatar'        => $ui->getAvatar(),
                'totalNights'   => $totalNights,
                'reviewRating'  => $review->getReviewRating(),
                'reviewComment' => $review->getReviewComment(),
                'createdAt'     => $review->getCreatedAt(),
                'location'      => $property->getLocation(),
            ];
        }
        $reviews = $temp;
        // $reviews['reviews'] = $temp;
        // $reviews['rating0'] = $rating0;
        // $reviews['rating1'] = $rating1;
        // $reviews['rating2'] = $rating2;
        // $reviews['rating3'] = $rating3;
        // $reviews['rating4'] = $rating4;
        // $reviews['rating0'] = $rating5;
        return $reviews;
    }

    /**
     * @param        $inputName
     * @param string $caption
     * @param string $bgPosition
     * @return Images|null
     */
    public function saveImage($inputName, $caption = '', $bgPosition = '')
    {
        /** @var FileHelper $fh */
        $fh = Loader::helper('file');

        $path = DIR_FILES_UPLOADED_STANDARD . '/properties';
        $file = $fh->uploadFile($inputName, $path, $this->getID());

        if ($file) {
            $image = Images::add(basename($file), $caption, $bgPosition);
            PropertyImages::add($this->getID(), $image->getID());
        }
        return $file ? $image : null;
    }

    /**
     * @param $inputName
     * @param string $caption
     * @param string $bgPosition
     * @return array
     */
    public function saveImages($inputName, $caption = '', $bgPosition = '')
    {
        /** @var FileHelper $fh */
        $fh     = Loader::helper('file');
        $images = [];
        $path   = DIR_FILES_UPLOADED_STANDARD . '/properties';

        $files = $fh->uploadFiles($inputName, $path, $this->getID());
        if ($files) {
            foreach ($files as $file) {
                $image = Images::add(basename($file), $caption, $bgPosition);
                PropertyImages::add($this->getID(), $image->getID());
                $images[] = $image;
            }
        }
        return $files ? $images : [];
    }

    public function getUniquePath($origPath, $key = 'pPath')
    {
        $db = Loader::db();

        $proceed = false;
        $suffix  = 0;
        while ($proceed != true) {
            $newPath = ($suffix == 0) ? $origPath : $origPath . $suffix;
            $v       = array(
                $newPath,
                $this->getID()
            );
            $q       = "SELECT pID FROM Properties WHERE {$key} = ? AND pID <> ?";
            $r       = $db->query($q, $v);
            if ($r->numRows() == 0) {
                $proceed = true;
            } else {
                $suffix++;
            }
        }

        return $newPath;
    }

    protected function slugSafeString($handle, $maxlength = 128)
    {
        $handle = preg_replace('/[^\\p{L}\\p{Nd}\-_]+/u', ' ', $handle); // remove unneeded chars
        $handle = preg_replace('/[-\s]+/', '-', $handle); // convert spaces to hyphens
        return trim(substr($handle, 0, $maxlength), '-'); // trim to first $max_length chars
    }

    public function getBlockedDates($fromDate = null)
    {
        /** @var DateHelper $dh */
        $dh = Loader::helper('date');
        if (!$fromDate) {
            $fromDate = $dh->getSystemDateTime();
        }
        $db          = Loader::db();
        $fromDate    = $db->Quote($dh->getFormattedDate($fromDate, 'Y-m-d'));
        $pID =  $this->getID();
        $q           = "SELECT startDate,endDate FROM AvailabilityCalendar WHERE pID = {$pID} AND startDate >= {$fromDate}";
        $res         = $db->Execute($q);
        $bookedDates = [];
        while ($row = $res->FetchRow()) {
            $startDate   = $dh->getFormattedDate($row['startDate'] . ' +1 day', 'Y-m-d');
            $endDate     = $dh->getFormattedDate($row['endDate'] . ' -1 day', 'Y-m-d');
            $bookedDates = array_merge($bookedDates, $dh->getDatesFromRange($startDate, $endDate));
        }

        $q            = "SELECT startDate,endDate FROM PropertyBlockDates WHERE pID = ? AND startDate >= ?";
        $res          = $db->Execute($q, [
            $this->getID(),
            $fromDate
        ]);
        $blockedDates = [];
        while ($row = $res->FetchRow()) {
            $startDate    = $dh->getFormattedDate($row['startDate'], 'Y-m-d');
            $endDate      = $dh->getFormattedDate($row['endDate'], 'Y-m-d');
            $blockedDates = array_merge($blockedDates, $dh->getDatesFromRange($startDate, $endDate));
        }

        $blockedDatesArr = array_merge($bookedDates, $blockedDates);

        return $blockedDatesArr;
    }

    public function getVatAmount($noOfDays = 1)
    {
        return $noOfDays * ($this->getPerDayPrice() * Config::get('VAT_PERCENT')) / 100;
    }

    public function getSeasonByPropertyID($pID)
    {
        return PropertySeason::getSeasonByPropertyID($pID);
    }

    public function getSubtotalAmount($startDate, $endDate, $creditAmount = null)
    {
        /** @var DateHelper $dh */

        $dh      = Loader::helper('date');
        $endDate = $dh->subtractNight($endDate, date_interval_create_from_date_string("1 day"));

        $propertyID = $this->getID();
        if ($propertyID) {

            $retArr = SeasonHelper::getPricePerDay($propertyID, $startDate, $endDate, '', $creditAmount);

            return $retArr;
        }
    }

    public function getPriceBreakdown($startDate, $endDate)
    {
        $retArr = $this->getSubtotalAmount($startDate, $endDate);

        return $retArr['pricePerDay'];
    }

    public function getLink()
    {
        return self::PARENT_PATH . '/' . $this->getPath();
    }
}
