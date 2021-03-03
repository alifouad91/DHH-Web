<?php

class Booking
{

    protected $bID;
    protected $bookingNo;
    protected $uID;
    protected $email;
    protected $bookingDate;
    protected $bookingStartDate;
    protected $bookingEndDate;
    protected $noOfDays;
    protected $pID;
    protected $noOfGuest;
    protected $noOfChildren;
    protected $subtotal;
    protected $total;
    protected $vat;
    protected $dhiramFee;
    protected $dcID;
    protected $discountReceived;
    protected $bookingStatus;
    protected $createdAt;
    protected $updatedAt;
    protected $additionalRequests;
    protected $eventStatus;
    protected $remainingDays;
    protected $creditAmount;
    protected $priceBreakDown;

    //fetched
    protected $propertyFacilities;
    protected $property;

    const PAST_HANDLE        = 'completed';
    const UPCOMING_HANDLE    = 'upcoming';
    const PAYMENT_COMPLETE   = 'paid';
    const PAYMENT_UNPAID     = 'unpaid';
    const PAYMENT_PROCESSING = 'payment_processing';
    const PAYMENT_FAILED     = 'payment_failed';
    const PAYMENT_CANCELLED  = 'payment_cancelled';
    const BOOKING_CANCELLED  = 'cancelled';
    const BOOKING_PAID  = 'paid';

    public function __construct($row)
    {
        $this->setPropertiesFromArray($row);
    }

    public function setPropertiesFromArray($arr)
    {
        foreach ($arr as $key => $prop) {
            $this->{$key} = $prop;
        }
    }

    /**
     * @return mixed
     */
    public function getBID()
    {
        return $this->bID;
    }

    /**
     * @return mixed
     */
    public function getBookingNo()
    {
        return $this->bookingNo;
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
    public function getDhiramFee()
    {
        return $this->dhiramFee;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getBookingDate()
    {
        return $this->bookingDate;
    }

    /**
     * @return mixed
     */
    public function getBookingStartDate()
    {
        return $this->bookingStartDate;
    }

    /**
     * @return mixed
     */
    public function getbookingEndDate()
    {
        return $this->bookingEndDate;
    }

    /**
     * @return mixed
     */
    public function getNoOfDays()
    {
        return $this->noOfDays;
    }

    /**
     * @return mixed
     */
    public function getPID()
    {
        return $this->pID;
    }

    /**
     * @return Property
     */
    public function getProperty()
    {
        if (!$this->property) {
            $this->property = Property::getByID($this->getPID());
        }
        return $this->property;
    }

    /**
     * @return Property
     */
    public function setProperty($property)
    {
        return $this->property = $property;
    }

    /**
     * @return mixed
     */
    public function getNoOfGuest()
    {
        return $this->noOfGuest;
    }

    /**
     * @return mixed
     */
    public function getNoOfChildren()
    {
        return $this->noOfChildren;
    }

    /**
     * @return mixed
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @return mixed
     */
    public function setSubtotal($total)
    {
        $this->subtotal = $total;
        return true;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return mixed
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @return mixed
     */
    public function getDcID()
    {
        return $this->dcID;
    }

    /**
     * @return mixed
     */
    public function getDiscountReceived()
    {
        return $this->discountReceived;
    }


    /**
     * @return mixed
     */
    public function getBookingStatus()
    {
        return $this->bookingStatus;
    }

    /**
     * @return mixed
     */
    public function getAdditionalRequests()
    {
        return $this->additionalRequests;
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public static function getByID($bID)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM  Booking where bID = ? ";
        $row   = $db->GetRow($query, [$bID]);

        if (isset($row['bID'])) {
            return new Booking($row);
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getEventStatus()
    {
        return $this->eventStatus;
    }

    /**
     * @return mixed
     */
    public function getPriceBreakDown()
    {
        $property            = $this->getProperty();
        $propertySubTotalArr = $property->getSubtotalAmount($this->getBookingStartDate(), $this->getbookingEndDate());
        $priceObj            = json_decode(json_encode($propertySubTotalArr));
        return $this->priceBreakDown ? json_decode($this->priceBreakDown)->pricePerDay : $priceObj->pricePerDay;
    }

    /**
     * @return mixed
     */
    public function getCreditAmount()
    {
        if (!$this->creditAmount) {
            return 0;
        }
        return $this->creditAmount;
    }

    /**
     * @param mixed $eventStatus
     */
    public function setEventStatus($eventStatus)
    {
        $this->eventStatus = $eventStatus;
    }

    public static function getByBookingNo($bookingNo)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM  Booking where bookingNo = ? ";
        $row   = $db->GetRow($query, [$bookingNo]);

        if (isset($row['bID'])) {
            return new Booking($row);
        }
        return null;
    }

    public function getBookingPropertyFacilities($format = false)
    {
        /** @var PriceHelper $ph */
        $ph = Loader::helper('price');
        if (!$this->propertyFacilities) {
            $db    = Loader::db();
            $query = "SELECT bpf.bpfID ,bpf.pfID ,bpf.bID ,f.fID ,f.name ,pf.price  FROM BookingPropertyFacilities bpf
                          inner join PropertyFacilities pf on bpf.pfID = pf.pfID
                          inner join Facilities f on  f.fID = pf.fID where bpf.bID = ?";

            $result                    = $db->Execute($query, [$this->bID]);
            $bookingPropertyFacilities = [];

            while ($row = $result->FetchRow()) {
                $bookingPropertyFacilities[] = new BookingPropertyFacilities($row);
            }

            if ($format) {
                $temp = [];
                /** @var BookingPropertyFacilities $bpf */
                foreach ($bookingPropertyFacilities as $bpf) {
                    array_push($temp, [
                        'id'    => $bpf->getPfID(),
                        'value' => $bpf->getName(),
                        'price' => $ph->format($bpf->getPrice())
                    ]);
                }
                $bookingPropertyFacilities = $temp;
            }
            $this->propertyFacilities = $bookingPropertyFacilities;

        }

        return $this->propertyFacilities;

    }

    public function getBookingPropertyFacilitiesTotal()
    {

        if (!$this->propertyFacilities) {
            $this->propertyFacilities = $this->getBookingPropertyFacilities();
        }
        $price = 0;
        foreach ($this->propertyFacilities as $propertyFacility) {
            /** @var BookingPropertyFacilities $propertyFacility */
            if (is_array($propertyFacility)) {
                $price = $price + $propertyFacility['price'];
            } else {
                $price = $price + $propertyFacility->getPrice();
            }
        }
        return $price;

    }

    public static function add($uID, $email, $bookingDate, $bookingStartDate, $bookingEndDate, $noOfDays, $pID, $noOfGuest, $noOfChildren, $subtotal, $total, $vat, $dhiram_fee, $creditAmount, $priceBreakDown)
    {
        /** @var DatabaseHelper $dh */
        $dh        = Loader::helper('database');
        $db        = Loader::db();
        $bookingNo = $dh->generate('Booking', 'bookingNo', 12);
        $query     = "INSERT INTO Booking(  bookingNo,uID,email,bookingDate,bookingStartDate,bookingEndDate,noOfDays,pID,noOfGuest,noOfChildren,subtotal,total,createdAt , vat, dhiramFee , creditAmount, priceBreakDown ) VALUES ( ? ,?, ? , ? , ? , ? ,?, ? , ? , ? , ? ,?, ?, ? , ?, ?, ? )";
        $ret       = $db->Execute($query, [
            $bookingNo,
            $uID,
            $email,
            $bookingDate,
            $bookingStartDate,
            $bookingEndDate,
            $noOfDays,
            $pID,
            $noOfGuest,
            $noOfChildren,
            $subtotal,
            $total,
            null,
            $vat,
            $dhiram_fee,
            $creditAmount,
            $priceBreakDown
        ]);
        if ($ret) {
            $booking = self::getByID($db->Insert_ID());
            $query   = "INSERT INTO AvailabilityCalendar(bID,pID,startDate,endDate) VALUES ( ?, ?, ?, ? )";
            $db->Execute($query, [
                $booking->getBID(),
                $booking->getPID(),
                $booking->getBookingStartDate(),
                $booking->getbookingEndDate()
            ]);
            $booking->updateBookingTotal();
            return $booking;
        }
        return null;
    }

    public function update($email, $bookingDate, $bookingStartDate, $bookingEndDate, $noOfDays, $noOfGuest, $noOfChildren, $bookingStatus, $additionalRequests, $priceBreakDown = '')
    {
        $db    = Loader::db();
        $query = "UPDATE Booking SET  email = ?,bookingDate = ?,bookingStartDate = ?,bookingEndDate = ?,noOfDays = ?,noOfGuest = ?,noOfChildren = ?,bookingStatus = ?, additionalRequests = ? 
                  WHERE bID = ?";
        $ret   = $db->Execute($query, [
            $email,
            $bookingDate,
            $bookingStartDate,
            $bookingEndDate,
            $noOfDays,
            $noOfGuest,
            $noOfChildren,
            $bookingStatus,
            $additionalRequests,
            $this->getBID()
        ]);

        if ($ret) {

            if($bookingStatus == self::BOOKING_CANCELLED) {
                $query = "DELETE FROM AvailabilityCalendar WHERE bID = ?";
                $db->Execute($query, [
                    $this->getBID()
                ]);
            } else if($bookingStatus == self::BOOKING_PAID) {
                $query = "SELECT * FROM AvailabilityCalendar WHERE bID = ? ";
                $res   = $db->GetRow($query, [
                    $this->getBID()
                ]);
                if ($res['bID']) {
                    $query = "UPDATE AvailabilityCalendar SET startDate = ?,endDate = ? WHERE bID = ?";
                    $db->Execute($query, [
                        $bookingStartDate,
                        $bookingEndDate,
                        $this->getBID()
                    ]);
                } else {
                    $query   = "INSERT INTO AvailabilityCalendar(bID,pID,startDate,endDate) VALUES ( ?, ?, ?, ? )";
                    $db->Execute($query, [
                        $this->getBID(),
                        $this->getPID(),
                        $bookingStartDate,
                        $bookingEndDate,
                    ]);
                }
            }


            $this->updateBookingSubTotal();
            $this->updateBookingTotal();
            $this->updatePriceBreakDown();
            return $this;

        }
        return null;
    }

    public function updateBookingSubTotal()
    {
        $db      = Loader::db();
        $bsTotal = (int)$this->getNoOfDays() * (double)$this->getProperty()->getPerDayPrice();
        $query   = "UPDATE Booking SET  subtotal = ? WHERE bID = ?";
        $db->Execute($query, [
            $bsTotal,
            $this->getBID()
        ]);
        return null;
    }


    public function updateBookingTotal()
    {
        $db         = Loader::db();
        $bTotal     = $this->getSubtotal() + $this->getBookingPropertyFacilitiesTotal();

        if ($this->getCreditAmount() > 0 && $this->getCreditAmount() >= $bTotal) {
            $bTotal = 0;
        } else if ($this->getCreditAmount() > 0 && $this->getCreditAmount() < $bTotal) {
            $bTotal = $bTotal - $this->getCreditAmount();
        }

        $vat_amount = ($bTotal * Config::get('VAT_PERCENT') / 100);
        $dhiram_fee = ($this->getDhiramFee());
        $bTotal     = $bTotal + $vat_amount + $dhiram_fee;

        $query = "UPDATE Booking SET vat = ? , total = ?  WHERE bID = ?";
        $db->Execute($query, [
            $vat_amount,
            $bTotal,
            $this->getBID()
        ]);
        return null;
    }

    public function updatePriceBreakDown()
    {
        $db                  = Loader::db();
        $property            = $this->getProperty();
        $propertySubTotalArr = $property->getSubtotalAmount($this->getBookingStartDate(), $this->getbookingEndDate());
        $query               = "UPDATE Booking SET priceBreakDown = ? WHERE bID = ?";
        $db->Execute($query, [
            json_encode($propertySubTotalArr),
            $this->getBID()
        ]);
        return null;
    }

    public function markNotifiedToRate($value = 1)
    {
        $db    = Loader::db();
        $query = "UPDATE Booking SET  notifiedToRate = ? WHERE bID = ?";
        $db->Execute($query, [
            $value,
            $this->getBID()
        ]);
        return null;
    }

    public function updateAdditionalRequest($additionalRequests)
    {
        $db    = Loader::db();
        $query = "UPDATE Booking SET  additionalRequests = ? WHERE bID = ?";
        $db->Execute($query, [
            $additionalRequests,
            $this->getBID()
        ]);
        return null;
    }

    public function updateCouponDiscount($discountTotal, $dcID)
    {
        $db    = Loader::db();
        $total = $this->getSubtotal();
        if ($total > $discountTotal) {
            $total = $total - $discountTotal;
        } else {
            $total = 0;
        }
        $discountTotal = $discountTotal + $this->getDiscountReceived();
        $query         = "UPDATE Booking SET  subtotal = ?, discountReceived = ? WHERE bID = ?";
        $db->Execute($query, [
            $total,
            $discountTotal,
            $this->getBID()
        ]);
        $this->setSubtotal($total);
        $this->updateBookingTotal();
        $this->updateAppliedCoupon($dcID);
        return null;
    }

    public function updateAppliedCoupon($dcID)
    {
        $db = Loader::db();

        $query = "INSERT INTO DiscountCouponApplied(uID,bID,dcID) VALUES ( ?, ?, ? )";
        $db->Execute($query, [
            $this->getUID(),
            $this->getBID(),
            $dcID
        ]);
        return null;
    }

    public function removeCouponDiscount()
    {
        $db    = Loader::db();
        $total = $this->getSubtotal() + $this->getDiscountReceived();
        $query = "UPDATE Booking SET  subtotal = ?, discountReceived = 0 WHERE bID = ?";
        $db->Execute($query, [
            $total,
            $this->getBID()
        ]);
        $this->removeCouponApplied();
        $this->setSubtotal($total);
        $this->updateBookingTotal();
        return null;
    }

    public function removeCouponApplied()
    {
        $db    = Loader::db();
        $query = "DELETE FROM DiscountCouponApplied WHERE bID  = ?";
        $db->Execute($query, [$this->getBID()]);
        return null;
    }

    public function updatePaymentStatus($status)
    {
        $db    = Loader::db();
        $query = "UPDATE Booking SET bookingStatus  = ? WHERE bID = ?";
        $db->Execute($query, [
            $status,
            $this->getBID()
        ]);
        return null;
    }

    public static function findBlockedDates($startDate, $endDate, $pID)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM PropertyBlockDates WHERE pID = ? AND (? between startDate and endDate || ? between startDate and endDate)";
        $res   = $db->GetRow($query, [
            $pID,
            $startDate,
            $endDate
        ]);
        if ($res['pbdID']) {
            return $res;
        }
        return false;
    }

    public static function findByDetails($startDate, $endDate, $pID)
    {
        $db        = Loader::db();
        $dh        = Loader::helper('date');
        $endDate   = $dh->getSystemDateTime($endDate . '  -1 hour', 'Y-m-d H');
        $startDate = $dh->getSystemDateTime($startDate . ' +1 hour', 'Y-m-d H');

        $query = "SELECT * FROM  Booking WHERE pID = ? AND (? between bookingStartDate and bookingEndDate || ? between bookingStartDate and bookingEndDate)";

        $res = $db->GetRow($query, [
            $pID,
            $startDate,
            $endDate
        ]);
        if ($res['bID']) {
            return new Booking($res);
        }
        return false;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM Booking WHERE bId = ?";
        return $db->Execute($query, [$this->getBID()]);
    }

    public function getRemainingDays()
    {
        /** @var DateHelper $dh */
        if (!$this->remainingDays) {
            $dh                  = Loader::helper('date');
            $this->remainingDays = $dh->getNoOfNights($dh->getSystemDateTime(), $this->getBookingStartDate());
        }
        return $this->remainingDays;
    }

    public function getAppliedCoupons()
    {
        $db    = Loader::db();
        $query = "SELECT dcID FROM  DiscountCouponApplied where bID = ? ";
        $row   = $db->GetAll($query, [$this->getBID()]);
        $ret   = [];
        foreach ($row as $k => $v) {
            $ret[] = $v['dcID'];
        }

        if (is_array($ret) && count($ret)) {
            return $ret;
        }
        return false;
    }
    /*
    //Update All Fields Template
    public static function update($bID ,$bookingNo,$uID,$email,$bookingDate,$bookingStartDate,$bookingEndDate,$noOfDays,$pID,$noOfGuest,$noOfChildren,$subtotal,$total,$vat,$dcID,$discountReceived,$bookingStatus,$eventStatus,$additionalRequests)
     {
         $db    = Loader::db();
         $query = "UPDATE Booking SET bookingNo = ? ,uID = ? ,email = ? ,bookingDate = ? ,bookingStartDate = ? ,bookingEndDate = ? ,noOfDays = ? ,pID = ? ,
                   noOfGuest = ? ,noOfChildren = ? ,subtotal = ? ,total = ? ,vat = ? ,dcID = ? ,discountReceived = ?
                   ,bookingStatus = ? ,eventStatus = ? ,additionalRequests = ?
                   WHERE bID = ?";
         $ret   = $db->Execute($query, [$bookingNo,$uID,$email,$bookingDate,$bookingStartDate,$bookingEndDate,$noOfDays,$pID,$noOfGuest,$noOfChildren,$subtotal,$total,$vat,$dcID,$discountReceived,$bookingStatus,$eventStatus,$additionalRequests , $bID]);
         if ($ret) {
             return self::getByID($db->Insert_ID());
         }
         return null;
     }*/


    public function getSavedAmount()
    {
        $savedAmount = 0;
        if (!empty($this->getDiscountReceived())) {
            $discountReceived = ($this->getDiscountReceived() > 0) ? $this->getDiscountReceived() : $this->getDiscountReceived();
            $bTotal = $this->getSubtotal() + $discountReceived;
            $vat = ($bTotal * Config::get('VAT_PERCENT')) / 100;
            $vatDiscount = $vat - $this->getVat();
            $savedAmount = $this->getDiscountReceived() + $vatDiscount;
        }
        return $savedAmount;
    }
}

