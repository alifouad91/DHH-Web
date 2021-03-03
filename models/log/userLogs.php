<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class UserLogs
{

    protected $ulID;
    protected $uID;
    protected $createdAt;
    protected $message;

    function __construct($row)
    {
        $this->ulID      = $row['ulID'];
        $this->uID       = $row['uID'];
        $this->createdAt = $row['createdAt'];
        $this->message   = $row['message'];
    }

    /**
     * @param $uID
     * @param $message
     * @param $type
     * @return $ul
     */
    public static function add($message, $type)
    {
        $db   = Loader::db();
        $user = new User();
        $uID  = $user->getUserID();

        switch ($type) {
            case 'added_property':
                $message = 'Added property ' . '<strong>' . $message . '</strong>';
                break;
            case 'deleted_property':
                $message = 'Deleted property ' . '<strong>' . $message . '</strong>';
                break;
            case 'edited_property':
                $message = 'Edited property ' . $message;
                break;
            case 'edited_user_profile':
                $message = 'Edited profile ' . $message;
                break;
            case 'edited_booking':
                $message = 'Edited booking ' . $message;
                break;
            case 'deleted_booking':
                $message = 'Deleted booking ' . '<strong>' . $message . '</strong>';
                break;
            case 'deleted_season':
                $message = 'Deleted season ' . $message;
                break;
            case 'deleted_blockedDate':
                $message = 'Deleted blocked dates ' . $message;
                break;
            case 'added_season':
                $message = 'Added season ' . $message;
                break;
            case 'added_blockedDate':
                $message = 'Added blocked date range ' . $message;
                break;
            case 'edited_season':
                $message = 'Edited season ' . $message;
                break;
            case 'edited_blockedDate':
                $message = 'Edited blocked date range ' . $message;
                break;

        }

        $query = "INSERT INTO UserLogs(uID,createdAt,message ) 
              VALUES ( ? , ? , ? ) ";
        $ret   = $db->Execute($query, [$uID, null, $message]);
        if ($ret) {
            $ul = self::getByID($db->Insert_ID());
            return $ul;
        }

        return null;
    }

    public static function clear()
    {
        $db    = Loader::db();
        $query = "DELETE FROM UserLogs";
        $ret   = $db->Execute($query);

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
        $query  = "SELECT * FROM UserLogs WHERE {$condition_suffix}";
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
        $query = "SELECT * FROM UserLogs WHERE ulID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['pID'])) {
            return new UserLogs($row);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getUlID()
    {
        return $this->ulID;
    }

    /**
     * @return mixed
     */
    public function getUID()
    {
        return $this->uID;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function comparePropertyFieldsValue($property, $fieldListArr)
    {
        /** @var Property $property */
        $dh      = Loader::helper('date');
        $message = '';
        foreach ($fieldListArr as $k => $v) {
            switch ($k) {
                case 'name':
                    if ($v != $property->getName()) {
                        $message .= '<strong>Name</strong> from ' . '<strong>' . $property->getName() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'caption':
                    if ($v != $property->getCaption()) {
                        $message .= '<strong>Caption</strong> from ' . '<strong>' . $property->getCaption() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'description':
                    if ($v != $property->getDescription()) {
                        $message .= '<strong>Description</strong> from ' . '<strong>' . $property->getDescription() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'latitude':
                    if ($v != $property->getLatitude()) {
                        $message .= '<strong>Latitude</strong> from ' . '<strong>' . $property->getLatitude() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'longitude':
                    if ($v != $property->getLongitude()) {
                        $message .= '<strong>Longitude</strong> from ' . '<strong>' . $property->getLongitude() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'locationID':
                    if ($v != $property->getLocationID()) {
                        $locationOptions = Location::getAll(true);
                        $message         .= '<strong>Location</strong> from ' . '<strong>' . $locationOptions[$property->getLocationID()] . '</strong> to <strong>' . $locationOptions[$v] . '</strong>' . "\n";
                    }
                    break;
                case 'noOfRooms':
                    if ($v != $property->getNoOfRooms()) {
                        $message .= '<strong>Number of Rooms</strong> from ' . '<strong>' . $property->getNoOfRooms() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'minNights':
                    if ($v != $property->getMinNights()) {
                        $message .= '<strong>Minimum Nights</strong> from ' . '<strong>' . $property->getMinNights() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'bedrooms':
                    if ($v != $property->getBedrooms()) {
                        $message .= '<strong>Bedrooms</strong> from ' . '<strong>' . $property->getBedrooms() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'bathrooms':
                    if ($v != $property->getBathrooms()) {
                        $message .= '<strong>Bathrooms</strong> from ' . '<strong>' . $property->getBathrooms() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'maxGuests':
                    if ($v != $property->getMaxGuests()) {
                        $message .= '<strong>Guests per room</strong> from ' . '<strong>' . $property->getMaxGuests() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'beds':
                    if ($v != $property->getBeds()) {
                        $message .= '<strong>Beds</strong> from ' . '<strong>' . $property->getBeds() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'apartmentAreaID':
                    if ($v != $property->getApartmentAreaId()) {
                        $apartmentAreaOptions = ApartmentArea::getAll(true);
                        $message              .= '<strong>Apartment Area</strong> from ' . '<strong>' . $apartmentAreaOptions[$property->getApartmentAreaId()] . '</strong> to <strong>' . $apartmentAreaOptions[$v] . '</strong>' . "\n";
                    }
                    break;
                case 'areaTypeID':
                    if ($v != $property->getAreaTypeID()) {
                        $areaTypeOptions = AreaType::getAll(true);
                        $message         .= '<strong>Area Type</strong> from ' . '<strong>' . $areaTypeOptions[$property->getAreaTypeID()] . '</strong> to <strong>' . $areaTypeOptions[$v] . '</strong>' . "\n";
                    }
                    break;
                case 'apartmentTypeID':
                    if ($v != $property->getApartmentTypeID()) {
                        $apartmentTypeOptions = ApartmentType::getAll(true);
                        $message              .= '<strong>Apartment Type</strong> from ' . '<strong>' . $apartmentTypeOptions[$property->getApartmentTypeID()] . '</strong> to <strong>' . $apartmentTypeOptions[$v] . '</strong>' . "\n";
                    }
                    break;
                case 'monthlyPrice':
                    if ($v != $property->getMonthlyPrice()) {
                        $message .= '<strong>Monthly Price</strong> from ' . '<strong>' . $property->getMonthlyPrice() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'weeklyPrice':
                    if ($v != $property->getWeeklyPrice()) {
                        $message .= '<strong>Weekly Price</strong> from ' . '<strong>' . $property->getWeeklyPrice() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'perDayPrice':
                    if ($v != $property->getPerDayPrice()) {
                        $message .= '<strong>Per Day Price</strong> from ' . '<strong>' . $property->getPerDayPrice() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'owner':
                    if ($v != $property->getOwnerID()) {
                        $ownerOld = UserInfo::getByID($property->getOwnerID());
                        $ownerNew = UserInfo::getByID($v);
                        $message  .= '<strong>Owner Name</strong> from ' . '<strong>' . $ownerOld->getUserEmail() . ' , ' .
                            $ownerOld->getFullName() . '</strong> to <strong>' . $ownerNew->getUserEmail() . ' , ' .
                            $ownerNew->getFullName() . '</strong>' . "\n";
                    }
                    break;
                case 'checkInTime':
                    $oldCheckInTime = $dh->date('h:i', strtotime($property->getCheckInTime()));
                    if ($v != $oldCheckInTime) {
                        $message .= '<strong>CheckIn Time</strong> from ' . '<strong>' . $oldCheckInTime . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'checkOutTime':
                    $oldCheckOutTime = $dh->date('h:i', strtotime($property->getCheckOutTime()));
                    if ($v != $oldCheckOutTime) {
                        $message .= '<strong>CheckOut Time</strong> from ' . '<strong>' . $oldCheckOutTime . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'status':
                    if ($v != $property->getStatus()) {
                        $oldStatus = ($property->getStatus() == 1) ? 'Active' : 'In Active';
                        $newStatus = ($v == 1) ? 'Active' : 'In Active';
                        $message   .= '<strong>Property Status</strong> from ' . '<strong>' . $oldStatus . '</strong> to <strong>' . $newStatus . '</strong>' . "\n";
                    }
                    break;
                case 'amenities':
                    $changed = 0;
                    if ($v) {
                        $propertyAmenities = $property->getAmenities(true);
                        $propertyAmenities = array_column($propertyAmenities, 'id');

                        if (count($v) != count($propertyAmenities) && count($v) > 0) {
                            $changed = 1;
                        }

                        foreach ($v as $k1 => $v1) {
                            if (!in_array($v1, $propertyAmenities)) {
                                $changed = 1;
                                break;
                            }
                        }

                        if ($changed) {
                            $amenities = Amenity::getAll();
                            $message1  = '<strong>Amenities</strong> updated to';
                            $message2  = '';
                            foreach ($amenities as $amenity) {
                                if (in_array($amenity->getID(), $v)) {
                                    $message2 .= ' ' . $amenity->getName() . ',';
                                }
                            }
                            $message2 = rtrim($message2, ',');
                            $message  .= $message1 . $message2 . "\n";
                        } else if (count($v) == 0) {
                            if (count($propertyAmenities) > 0) {
                                $message .= '<strong>Amenities</strong> emptied';
                            }
                        }
                    }
                    break;
                case 'homePageFilters':
                    $changed = 0;
                    if ($v) {
                        $homePageFiltersArr = $property->getHomePageFilters(true);
                        $homePageFiltersArr = array_column($homePageFiltersArr, 'value');
                        $homePageFilters    = [];
                        $filters            = HomePageFilters::getAll();
                        foreach ($filters as $filter) {
                            if (in_array($filter->getName(), $homePageFiltersArr)) {
                                $homePageFilters[] = $filter->getID();
                            }
                        }

                        if (count($v) != count($homePageFilters) && count($v) > 0) {
                            $changed = 1;
                        }

                        foreach ($v as $k1 => $v1) {
                            if (!in_array($v1, $homePageFilters)) {
                                $changed = 1;
                                break;
                            }
                        }

                        if ($changed == 1) {
                            $message1 = '<strong>Homepage Filters</strong> updated to';
                            $message2 = '';
                            foreach ($filters as $filter) {
                                if (in_array($filter->getID(), $v)) {
                                    $message2 .= ' ' . $filter->getName() . ',';
                                }
                            }
                            $message2 = rtrim($message2, ',');
                            $message  .= $message1 . $message2 . "\n";
                        } else if (count($v) == 0) {
                            if (count($homePageFilters) > 0) {
                                $message .= '<strong>Homepage Filters</strong> emptied';
                            }
                        }
                    }
                    break;
            }
        }
        if ($message) {
            $message = '<strong>' . $fieldListArr['name'] . '</strong>' . "\n" . $message;
        }

        return $message;
    }

    /** @var $season PropertySeason */
    public function comparePropertySeasonFieldsValue($season, $fieldListArr, $property)
    {
        $message = '';
        $dh      = Loader::helper('date');
        foreach ($fieldListArr as $k => $v) {
            switch ($k) {
                case 'seasonName':
                    if ($v != $season->getSeasonName()) {
                        $message .= '<strong>Season Name</strong> from ' . '<strong>' . $season->getSeasonName() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'seasonPrice':
                    if ($v != $season->getSeasonPrice()) {
                        $message .= '<strong>Price</strong> from ' . '<strong>' . $season->getSeasonPrice() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'seasonStartDate':
                    $seasonStartDateOld = $dh->getFormattedDate($season->getSeasonStartDate(), 'd-m-Y');
                    $seasonStartDateNew = $dh->getFormattedDate($v, 'd-m-Y');
                    if ($seasonStartDateOld != $seasonStartDateNew) {
                        $message .= '<strong>Season Start Date</strong> from ' . '<strong>' . $seasonStartDateOld . '</strong> to <strong>' . $seasonStartDateNew . '</strong>' . "\n";
                    }
                    break;
                case 'seasonEndDate':
                    $seasonEndDateOld = $dh->getFormattedDate($season->getSeasonEndDate(), 'd-m-Y');
                    $seasonEndDateNew = $dh->getFormattedDate($v, 'd-m-Y');
                    if ($seasonEndDateOld != $seasonEndDateNew) {
                        $message .= '<strong>Season End Date</strong> from ' . '<strong>' . $seasonEndDateOld . '</strong> to <strong>' . $seasonEndDateNew . '</strong>' . "\n";
                    }
                    break;
                case 'seasonStatus':
                    if ($v != $season->getSeasonStatus()) {
                        $oldStatus = ($season->getSeasonStatus() == 1) ? 'Active' : 'In Active';
                        $newStatus = ($v == 1) ? 'Active' : 'In Active';
                        $message   .= '<strong>Status</strong> from ' . '<strong>' . $oldStatus . '</strong> to <strong>' . $newStatus . '</strong>' . "\n";
                    }
                    break;
            }
        }
        if ($message) {
            $message = '<strong>' . $fieldListArr['seasonName'] . '</strong>' . ' belonging to property ' . '<strong>' . $property->getName() . '</strong>' . "\n" . $message;
        }
        return $message;
    }

    /** @var $property Property */

    public function comparePropertyFacilities($propertyFacilitiesNew, $property)
    {
        $propertyFacilitiesOld = $property->getPropertyFacilities(true, false);
        $facilities            = Facility::getAll();
        $message               = '';
        foreach ($facilities as $facility) {
            $oldValue = ($propertyFacilitiesOld[$facility->getID()]['price']) ? $propertyFacilitiesOld[$facility->getID()]['price'] : 0;
            $newValue = ($propertyFacilitiesNew[$facility->getID()]) ? $propertyFacilitiesNew[$facility->getID()] : 0;
            if ($oldValue != $newValue) {
                $message .= '<strong>' . $facility->getName() . '</strong> from ' . '<strong>' . $oldValue . '</strong> to <strong>' . $newValue . '</strong>' . "\n";
            }
        }

        if ($message) {
            $message = 'Edited <strong>Facilities</strong> belonging to property ' . '<strong>' . $property->getName() . '</strong>' . "\n" . $message;
        }

        return $message;
    }

    /** @var $property Property */
    /** @var $blockDate PropertyBlockDates */

    public function comparePropertyBlockedDates($blockDate, $fieldListArr, $property)
    {
        $message = '';
        $dh      = Loader::helper('date');
        foreach ($fieldListArr as $k => $v) {
            switch ($k) {
                case 'startDate':
                    $startDateOld = $dh->getFormattedDate($blockDate->getStartDate(), 'd-m-Y');
                    $startDateNew = $dh->getFormattedDate($v, 'd-m-Y');
                    if ($startDateOld != $startDateNew) {
                        $message .= '<strong>Start Date</strong> from ' . '<strong>' . $startDateOld . '</strong> to <strong>' . $startDateNew . '</strong>' . "\n";
                    }
                    break;
                case 'endDate':
                    $endDateOld = $dh->getFormattedDate($blockDate->getEndDate(), 'd-m-Y');
                    $endDateNew = $dh->getFormattedDate($v, 'd-m-Y');
                    if ($endDateOld != $endDateNew) {
                        $message .= '<strong>End Date</strong> from ' . '<strong>' . $endDateOld . '</strong> to <strong>' . $endDateNew . '</strong>' . "\n";
                    }
                    break;
            }
        }
        if ($message) {
            $message = '<strong>' . $startDateNew . '</strong>' . ' to ' . '<strong>' . $endDateNew . '</strong>' . ' belonging to property ' . '<strong>' . $property->getName() . '</strong>';
        }
        return $message;
    }

    /** @var $booking Booking */
    public function compareBookingFieldsValue($booking, $fieldListArr)
    {
        $message = '';
        $dh      = Loader::helper('date');
        foreach ($fieldListArr as $k => $v) {
            switch ($k) {
                case 'email':
                    if ($v != $booking->getEmail()) {
                        $message .= '<strong>Email</strong> from ' . '<strong>' . $booking->getEmail() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'bookingDate':
                    $bookingDateOld = $dh->getFormattedDate($booking->getBookingDate(), 'd-m-Y');
                    $bookingDateNew = $dh->getFormattedDate($v, 'd-m-Y');

                    if ($bookingDateNew != $bookingDateOld) {
                        $message .= '<strong>Booking Date</strong> from ' . '<strong>' . $bookingDateOld . '</strong> to <strong>' . $bookingDateNew . '</strong>' . "\n";
                    }
                    break;
                case 'bookingStartDate':
                    $bookingStartDateOld = $dh->getFormattedDate($booking->getBookingStartDate(), 'd-m-Y');
                    $bookingStartDateNew = $dh->getFormattedDate($v, 'd-m-Y');

                    if ($bookingStartDateOld != $bookingStartDateNew) {
                        $message .= '<strong>Booking Start Date</strong> from ' . '<strong>' . $bookingStartDateOld . '</strong> to <strong>' . $bookingStartDateNew . '</strong>' . "\n";
                    }
                    break;
                case 'bookingEndDate':
                    $bookingEndDateOld = $dh->getFormattedDate($booking->getbookingEndDate(), 'd-m-Y');
                    $bookingEndDateNew = $dh->getFormattedDate($v, 'd-m-Y');

                    if ($bookingEndDateOld != $bookingEndDateNew) {
                        $message .= '<strong>Booking End Date</strong> from ' . '<strong>' . $bookingEndDateOld . '</strong> to <strong>' . $bookingEndDateNew . '</strong>' . "\n";
                    }
                    break;
                case 'noOfDays':
                    if ($v != $booking->getNoOfDays()) {
                        $message .= '<strong>Number of Days</strong> from ' . '<strong>' . $booking->getNoOfDays() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'noOfGuest':
                    if ($v != $booking->getNoOfGuest()) {
                        $message .= '<strong>No Of Guest</strong> from ' . '<strong>' . $booking->getNoOfGuest() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'noOfChildren':
                    if ($v != $booking->getNoOfChildren()) {
                        $message .= '<strong>No Of Children</strong> from ' . '<strong>' . $booking->getNoOfChildren() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'bookingStatus':
                    if ($v != $booking->getBookingStatus()) {
                        $message .= '<strong>Booking Status</strong> from ' . '<strong>' . $booking->getBookingStatus() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
                case 'additionalRequest':
                    if ($v != $booking->getAdditionalRequests()) {
                        $message .= '<strong>Additional Requests</strong> from ' . '<strong>' . $booking->getAdditionalRequests() . '</strong> to <strong>' . $v . '</strong>' . "\n";
                    }
                    break;
            }
        }
        if ($message) {
            $message = '<strong>' . $booking->getBookingNo() . '</strong>' . "\n" . $message;
        }

        return $message;
    }
}