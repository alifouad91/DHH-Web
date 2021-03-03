<?php
defined('C5_EXECUTE') or die('Access Denied.');
Loader::controller('/api');

class ApiBookingController extends ApiController
{

    const API_REVIEW = 'bookingReview';

    public function add()
    {
        /** @var TextHelper $th */
        /** @var DateHelper $dh */
        $th               = Loader::helper('text');
        $dh               = Loader::helper('date');
        $result['status'] = false;

        $u                = $this->validUser();
        $uID              = $u->getUserID();
        $bookingDate      = $dh->getSystemDateTime('now', 'Y-m-d H:i:s');
        $bookingStartDate = $th->sanitize($this->post('bookingStartDate'));
        $bookingEndDate   = $th->sanitize($this->post('bookingEndDate'));
        $pID              = $th->sanitize($this->post('pID'));
        $noOfGuest        = (int)$th->sanitize($this->post('noOfGuest'));
        $noOfChildren     = (int)$th->sanitize($this->post('noOfChildren')) ?: 0;
        $booking          = $this->checkAvailability($bookingStartDate, $bookingEndDate, $pID);
        $blockedDate      = $this->checkBlockDates($bookingStartDate, $bookingEndDate, $pID);
        $locale           = $th->sanitize($this->get('locale')) ?: null;
        $property         = Property::getByID($pID);

        if (!$bookingStartDate) {
            $this->addError('Booking start date is required');
        }
        if (!$bookingEndDate) {
            $this->addError('Number of guest is required');
        }
        if (!$noOfGuest) {
            $this->addError('Booking end date is required');
        }
        if (!$u) {
            $this->addError('Invalid userID');
        }
        if ($booking) {
            $this->addError('Property already booked for selected dates');
        }
        if ($blockedDate) {
            $this->addError('Property unavailable for selected dates');
        }

        $minNights = $property->getMinNights();
        $nights    = $dh->getNoOfNights($bookingStartDate, $bookingEndDate);

        $endDate    = $dh->subtractNight($bookingEndDate, date_interval_create_from_date_string("1 day"));
        $seasonData = SeasonHelper::getPricePerDay($pID, $bookingStartDate, $endDate);

        if ($nights < $minNights) {
            $this->addError('Minimum booking nights need to be ' . $minNights);
        }

        if ($nights < $seasonData['minSeasonNight']) {
            $this->addError('Minimum booking nights for the season need to be ' . $seasonData['minSeasonNight']);
        }

        if (!$this->hasErrors()) {

            // Clear Pending bookings
            $bookingStatus = [
                Booking::PAYMENT_UNPAID,
                Booking::PAYMENT_FAILED,
                Booking::PAYMENT_CANCELLED
            ];
            $bookingList   = new BookingList();
            $bookingList->filterByBookingStatus($bookingStatus);
            $bookingList->filterByUserID($uID);
            $bookings = $bookingList->get(0);
            /** @var Booking $booking */
            foreach ($bookings as $booking) {
                BookingHelper::clearPendingBooking($booking, true);
            }

            $ui           = $u->getUserInfoObject();
            $email        = $ui->getUserEmail();
            $creditAmount = $ui->getCreditAmount();

            $bookingStartDate = $dh->getFormattedDate($bookingStartDate, 'Y-m-d');
            $bookingEndDate   = $dh->getFormattedDate($bookingEndDate, 'Y-m-d');
            $noOfDays         = $dh->getNoOfNights($bookingStartDate, $bookingEndDate);

            $propertySubTotalArr = $property->getSubtotalAmount($bookingStartDate, $bookingEndDate, $creditAmount);
            $subtotal            = $propertySubTotalArr['subtotal'];
            $newSubtotal         = $subtotal;

            //process totals


            if ($creditAmount > $newSubtotal) {
                $creditRAmount = $creditAmount - $newSubtotal;
                $ui->updateReferralCredit($creditRAmount);
                $creditAmount = $newSubtotal;
                $newSubtotal  = 0;
            } else if ($creditAmount <= $newSubtotal && $creditAmount > 0) {
                $newSubtotal = $newSubtotal - $creditAmount;
                $ui->updateReferralCredit(0);
            }


            $vat_amount = ($newSubtotal * Config::get('VAT_PERCENT') / 100);
            $dhiram_fee = $property->getTourismFee() * $noOfDays;
            $total      = $newSubtotal + $vat_amount + $dhiram_fee;


            $priceBreakDown    = json_encode($propertySubTotalArr);
            $booking           = Booking::add($uID, $email, $bookingDate, $bookingStartDate, $bookingEndDate, $noOfDays, $pID, $noOfGuest, $noOfChildren, $subtotal, $total, $vat_amount, $dhiram_fee, $creditAmount, $priceBreakDown);
            $result['status']  = true;
            $property          = $booking->getProperty();
            $result['booking'] = [
                'bID'                       => $booking->getBID(),
                'pID'                       => $property->getID(),
                'bookingNo'                 => $booking->getBookingNo(),
                'name'                      => $property->getName(),
                'caption'                   => $property->getCaption(),
                'startDate'                 => $booking->getBookingStartDate(),
                'endDate'                   => $booking->getbookingEndDate(),
                'bookingAdditionFacilities' => $booking->getBookingPropertyFacilities(true),
                'additionalRequests'        => $booking->getAdditionalRequests(),
                'vatPercent'                => Config::get('VAT_PERCENT'),
                'vat'                       => $booking->getVat(),
                'dhiramFee'                 => $booking->getDhiramFee(),
                'subtotal'                  => $booking->getSubtotal(),
                'total'                     => $booking->getTotal(),
                'creditAmount'              => $booking->getCreditAmount() > 0 ? $booking->getCreditAmount() : null,
                'location'                  => $property->getLocation(),
                'avgRating'                 => $property->getAverageRating(),
                'reviews'                   => $property->getTotalRatings(),
                'thumbnail'                 => $property->getThumbnailPath(),
                'propertyRules'             => $property->getPropertyRules(true),
                'cancellationPolicy'        => $property->getCancellationPolicy(true),
                'guests'                    => $booking->getNoOfGuest(),
                'priceBreakDown'            => $booking->getPriceBreakDown(),
                'createdAt'                 => $booking->getCreatedAt()
            ];
        }
        return $result;
    }

    public static function vatDiscount(Booking $booking)
    {
        $booking          = Booking::getByID($booking->getBID());
        $discountReceived = $booking->getDiscountReceived();
        $creditAmount     = $booking->getCreditAmount() > 0 ? $booking->getCreditAmount() : 0;
        $bTotal           = $booking->getSubtotal() + $discountReceived + $booking->getBookingPropertyFacilitiesTotal() - $creditAmount;
        $vat              = ($bTotal * Config::get('VAT_PERCENT')) / 100;
        $vatDiscount      = $vat - $booking->getVat();
        return $vatDiscount = ($vatDiscount > 0) ? $vatDiscount : 0;
    }

    public function detail()
    {
        $th        = Loader::helper('text');
        $ph        = Loader::helper('price');
        $bookingNo = $th->sanitize($this->get('bookingNo'));
        $api       = $th->sanitize($this->get('api'));
        $dh        = Loader::helper('date');

        $u = $this->validUser();

        $booking = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
            return $this->addError('Invalid Booking ID');
        }
        if ($u->getUserID() != $booking->getUID()) {
            $this->addError('Authentication failed');
        }
        if ($api == self::API_REVIEW && $booking->getBookingStatus() == Booking::PAYMENT_COMPLETE) {
            $this->addError('booking_confirmed');
        }
        if ($this->hasErrors()) {
            return null;
        }

        $ui = $u->getUserInfoObject();

        $property     = $booking->getProperty();
        $breakDownArr = $property->getPriceBreakdown($booking->getBookingStartDate(), $booking->getbookingEndDate());
        $vatDiscount  = $this->vatDiscount($booking);
        $tempBooking  = [
            'bID'                       => $booking->getBID(),
            'pID'                       => $property->getID(),
            'bookingNo'                 => $booking->getBookingNo(),
            'name'                      => $property->getName(),
            'caption'                   => $property->getCaption(),
            'startDate'                 => $booking->getBookingStartDate(),
            'endDate'                   => $booking->getbookingEndDate(),
            'bookingAdditionFacilities' => $booking->getBookingPropertyFacilities(true),
            'additionalRequests'        => $booking->getAdditionalRequests(),
            'discount'                  => ($booking->getDiscountReceived() > 0) ? $ph->format($booking->getDiscountReceived()) : $booking->getDiscountReceived(),
            'subtotal'                  => $ph->format($booking->getSubtotal()),
            'total'                     => $ph->format($booking->getTotal()),
            'creditAmount'              => $booking->getCreditAmount() > 0 ? $ph->format($booking->getCreditAmount()) : null,
            'vatDiscount'               => $ph->format($vatDiscount),
            'location'                  => $property->getLocation(),
            'avgRating'                 => $property->getAverageRating(),
            'reviews'                   => $property->getTotalRatings(),
            'thumbnail'                 => $property->getThumbnailPath(),
            'propertyRules'             => $property->getPropertyRules(true),
            'cancellationPolicy'        => $property->getCancellationPolicy(true),
            'guests'                    => $booking->getNoOfGuest(),
            'createdAt'                 => $booking->getCreatedAt(),
            'perDayBreakdown'           => $breakDownArr,
            'noOfDays'                  => $booking->getNoOfDays(),
            'vatAmount'                 => $ph->format($booking->getVat()),
            'dirhamFee'                 => $ph->format($booking->getDhiramFee()),
            'path'                      => $property->getPath(),
            'AEDAmt'                    => $ph->format($booking->getTotal(), CurrencyRates::DEFAULT_LOCALE, true),
            // 'billing_first_name'        => $ui->getBillingFirstName(),
            // 'billing_last_name'         => $ui->getBillingLastName(),
            // 'billing_email'             => $ui->getBillingEmail(),
            // 'billing_phone'             => $ui->getBillingPhone(),
            // 'billing_address'           => $ui->getBillingAddress(),
            // 'billing_country'           => $ui->getBillingCountry(),
            // 'billing_city'              => $ui->getBillingCity(),
            'billingDetails'            => [
                'billing_first_name' => $ui->getBillingFirstName(),
                'billing_last_name'  => $ui->getBillingLastName(),
                'billing_email'      => $ui->getBillingEmail(),
                'billing_phone'      => $ui->getBillingPhone(),
                'billing_address'    => $ui->getBillingAddress(),
                'billing_country'    => $ui->getBillingCountry(),
                'billing_city'       => $ui->getBillingCity(),
            ],
        ];

        $result = [
            'additionalFacilities' => $property->getPropertyFacilities(true),
            'booking'              => $tempBooking
        ];
        return $result;

    }

    public function addAdditionalFacility()
    {
        $th               = Loader::helper('text');
        $bookingNo        = $th->sanitize($this->post('bookingNo'));
        $pfID             = $th->sanitize($this->post('pfID'));
        $result['status'] = 'removed';

        $u = $this->validUser();

        $propertyFacility = PropertyFacilities::getByID($pfID);
        $booking          = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
            return $this->addError('Invalid Booking ID');
        }
        if (!$propertyFacility) {
            $this->addError('Invalid Property Facility ID');
        }
        if ($u->getUserID() != $booking->getUID()) {
            $this->addError('Authentication failed');
        }
        if ($this->hasErrors()) {
            return null;
        }
        $bID = $booking->getBID();
        $bpf = BookingPropertyFacilities::getAdditionalFacilityBIDAndPFID($bID, $pfID);

        if (!$bpf) {
            BookingPropertyFacilities::add($pfID, $bID, $propertyFacility->getPrice());
            $result['status']                  = 'added';
            $result['additionalFacilityTotal'] = $booking->getBookingPropertyFacilitiesTotal();
        } else {
            $bpf->delete();
            $result['additionalFacilityTotal'] = $booking->getBookingPropertyFacilitiesTotal();
        }
        return $result;
    }

    public function updateAdditionRequest()
    {

        $th                 = Loader::helper('text');
        $bookingNo          = $th->sanitize($this->post('bookingNo'));
        $additionalRequests = $th->sanitize($this->post('additionalRequests'));
        $result['status']   = 'updated';

        $u = $this->validUser();

        $booking = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
            return $this->addError('Invalid Booking ID');
        }
        if ($u->getUserID() != $booking->getUID()) {
            return $this->addError('Authentication failed');
        }
        $booking->updateAdditionalRequest($additionalRequests);

        return $result;
    }

    public function update()
    {
        /** @var TextHelper $th */
        /** @var DateHelper $dh */
        $th               = Loader::helper('text');
        $dh               = Loader::helper('date');
        $result['status'] = false;

        $bookingNo        = $th->sanitize($this->post('bookingNo'));
        $email            = $th->sanitize($this->get('email'));
        $bookingDate      = $dh->getSystemDateTime('now', 'Y-m-d H:i:s');
        $bookingStartDate = $th->sanitize($this->get('bookingStartDate'));
        $bookingEndDate   = $th->sanitize($this->get('bookingEndDate'));
        //        $noOfDays           = $th->sanitize($this->get('noOfDays'));
        $noOfGuest          = $th->sanitize($this->get('noOfGuest'));
        $noOfChildren       = $th->sanitize($this->get('noOfChildren'));
        $bookingStatus      = $th->sanitize($this->get('bookingStatus'));
        $additionalRequests = $th->sanitize($this->get('additionalRequests'));
        $booking            = Booking::getByBookingNo($bookingNo);

        $bookingStartDate = $dh->getFormattedDate($bookingStartDate, 'Y-m-d');
        $bookingEndDate   = $dh->getFormattedDate($bookingEndDate, 'Y-m-d');
        $noOfDays         = $dh->getNoOfNights($bookingStartDate, $bookingEndDate);

        if ($booking) {
            $booking->update($email, $bookingDate, $bookingStartDate, $bookingEndDate, $noOfDays, $noOfGuest, $noOfChildren, $bookingStatus, $additionalRequests);
            $result['status'] = true;
        }
        return $result;
    }

    public function addReview()
    {
        $th            = Loader::helper('text');
        $bookingNo     = $th->sanitize($this->post('bookingNo'));
        $reviewRating  = $th->sanitize($this->post('reviewRating'));
        $reviewComment = $th->sanitize($this->post('reviewComment'));
        $result        = [];

        $u = $this->validUser();

        $booking = Booking::getByBookingNo($bookingNo);

        $review = Review::getByBookingID($booking->getBID());
        if (!$bookingNo) {
            return $this->addError('Invalid Booking ID');
        }
        if (!$reviewRating) {
            return $this->addError('Review rating is required');
        }
        if ($reviewComment == 'undefined') {
            $reviewComment = '';
        }
        if ($u->getUserID() != $booking->getUID()) {
            return $this->addError('Authentication failed');
        }
        if ($review) {
            return $this->addError('Review already exist for this booking');
        }

        if (!$this->hasErrors()) {
            $review                  = Review::add($u->getUserID(), $booking->getPID(), $booking->getBID(), $reviewRating, $reviewComment);
            $result['rID']           = $review->getId();
            $result['reviewRating']  = $review->getReviewRating();
            $result['reviewComment'] = $review->getReviewComment();
        }

        return $result;
    }

    public function updateReview()
    {
        $th            = Loader::helper('text');
        $rId           = $th->sanitize($this->post('rID'));
        $reviewRating  = $th->sanitize($this->post('reviewRating'));
        $reviewComment = $th->sanitize($this->post('reviewComment'));
        $result        = [];

        $u = $this->validUser();

        $review = Review::getById($rId);
        if (!$rId) {
            return $this->addError('Invalid Review ID');
        }
        if (!$reviewRating) {
            $reviewRating = $review->getReviewRating();
        }
        if (!$reviewComment) {
            $reviewComment = $review->getReviewComment();
        }

        $reviewCnt = $review->getUpdateCount();

        if ($u->getUserID() != $review->getUserId()) {
            return $this->addError('Authentication failed');
        }

        $reviewEditable = $review->isEditable();

        if (!$reviewEditable) {
            return $this->addError('You cannot edit the review');
        }

        if (!$this->hasErrors() && ($reviewCnt <= Review::REVIEW_EDITS)) {
            $reviewCnt++;
            $isUpdated = $review->update($reviewRating, $reviewComment, $reviewCnt);
            if ($isUpdated) {
                $review                  = Review::getById($rId);
                $result['rID']           = $review->getId();
                $result['reviewRating']  = $review->getReviewRating();
                $result['reviewComment'] = $review->getReviewComment();
                $result['editable']      = $reviewEditable;
            } else {
                $result['Error'] = 'Error in updating';
            }
        }

        return $result;
    }

    public function checkAvailability($startDate, $endDate, $pID)
    {
        $dh        = Loader::helper('date');
        $startDate = $dh->getFormattedDate($startDate, 'Y-m-d');
        $endDate   = $dh->getFormattedDate($endDate, 'Y-m-d');
        return Booking::findByDetails($startDate, $endDate, $pID);
    }

    public function checkBlockDates($startDate, $endDate, $pID)
    {
        $dh        = Loader::helper('date');
        $startDate = $dh->getFormattedDate($startDate, 'Y-m-d');
        $endDate   = $dh->getFormattedDate($endDate, 'Y-m-d');
        return Booking::findBlockedDates($startDate, $endDate, $pID);
    }

    public function applyCoupon()
    {
        $ph        = Loader::helper('price');
        $th        = Loader::helper('text');
        $bookingNo = $th->sanitize($this->post('bookingNo'));
        $pID       = $th->sanitize($this->post('pID'));
        $coupon    = $th->sanitize($this->post('couponCode'));

        $property         = Property::getByID($pID);
        $booking          = Booking::getByBookingNo($bookingNo);
        $result['status'] = 'none';

        $u  = $this->validUser();
        $ui = $u->getUserInfoObject();

        if (!$booking) {
            return $this->addError('Invalid Booking ID');
        }
        if (!$property) {
            $this->addError('Invalid Property ID');
        }
        if ($u->getUserID() != $booking->getUID()) {
            $this->addError('Authentication failed');
        }
        if ($this->hasErrors()) {
            return null;
        }

        if (!$coupon) {
            $booking->removeCouponDiscount();
            $result['booking'] = $this->getBookingDetails($booking, $property, $ui);
            return $result;
        }

        $discountCouponList = new DiscountCouponList();
        $discountCouponList->filterByCouponCode($coupon);
        $discountCouponList->filterByActive();
        $discountCouponList->populateUserApplied();
        $discountCouponList->filterByTimesUsableUser();
        $discountCoupon = reset($discountCouponList->get());

        if (!$discountCoupon) {
            $result['status'] = 'not-applicable';
            return $result;
        }
        /** @var DiscountCoupon $discount */
        $discount = $discountCoupon;

        $filterApplicable = true;
        if ($discount->getStartDate() != '0000-00-00 00:00:00' && $discount->getEndDate() != '0000-00-00 00:00:00') {
            $dh        = Loader::helper('date');
            $date      = strtotime($dh->getSystemDateTime('now', 'Y-m-d H:i'));
            $startDate = strtotime($discount->getStartDate());
            $endDate   = strtotime($discount->getEndDate());

            if (!($date >= $startDate && $date <= $endDate)) {
                $filterApplicable = false;
            }
        }

        $discountUserGroups = $discount->getDiscountCouponUserGroups();

        $userGroupApp = true;
        if ($discountUserGroups) {
            $userGroups   = $u->getUserGroups();
            $userGroupApp = false;
            /** @var DiscountCouponUserGroups $discountGroup */
            foreach ($discountUserGroups as $discountGroup) {
                if (array_key_exists($discountGroup->getUserGroupID(), $userGroups)) {
                    $userGroupApp = true;
                    break;
                }
            }
        }

        $discountProperties = $discount->getDiscountCouponProperties();

        $propertiesApp = true;
        if ($discountProperties) {
            $propertiesApp = false;
            /** @var DiscountCouponProperties $discountProperty */
            foreach ($discountProperties as $discountProperty) {
                if ($pID == $discountProperty->getPID()) {
                    $propertiesApp = true;
                    break;
                }
            }
        }

        $couponApplied = false;
        $appliedCoupon = $booking->getAppliedCoupons();
        if (is_array($appliedCoupon) && in_array($discount->getID(), $appliedCoupon)) {
            $couponApplied = true;
        }

        if ($userGroupApp && $propertiesApp && $filterApplicable && !$couponApplied) {

            $result['status'] = 'valid';
            $result['dcID']   = $discount->getID();
            if ($discount->getType() == 1) {
                $result['discountTotal'] = $discount->getValue();
            } else {
                $result['discountTotal'] = (($booking->getSubtotal() + $booking->getBookingPropertyFacilitiesTotal() - $booking->getCreditAmount()) * $discount->getValue()) / 100;
            }
            $booking->updateCouponDiscount($result['discountTotal'], $result['dcID']);

        } else {

            $result['status'] = 'not-applicable';
        }

        $result['booking'] = $this->getBookingDetails($booking, $property, $ui);

        return $result;
    }

    protected function getBookingDetails(Booking $booking, Property $property, UserInfo $ui)
    {
        $ph = Loader::helper('price');

        $booking = Booking::getByID($booking->getBID());

        //	    $discountReceived = ($booking->getDiscountReceived() > 0) ? $booking->getDiscountReceived() : $booking->getDiscountReceived();
        //	    $bTotal = $booking->getSubtotal() + $discountReceived;
        //	    $vat = ($bTotal * Config::get('VAT_PERCENT'))/100;
        //	    $vatDiscount = $booking->getVat() - $vat;

        $vatDiscount = $this->vatDiscount($booking);

        $breakDownArr = $property->getPriceBreakdown($booking->getBookingStartDate(), $booking->getbookingEndDate());
        return [
            'bID'                       => $booking->getBID(),
            'pID'                       => $property->getID(),
            'bookingNo'                 => $booking->getBookingNo(),
            'name'                      => $property->getName(),
            'caption'                   => $property->getCaption(),
            'startDate'                 => $booking->getBookingStartDate(),
            'endDate'                   => $booking->getbookingEndDate(),
            'bookingAdditionFacilities' => $booking->getBookingPropertyFacilities(true),
            'additionalRequests'        => $booking->getAdditionalRequests(),
            'discount'                  => ($booking->getDiscountReceived() > 0) ? $ph->format($booking->getDiscountReceived()) : $booking->getDiscountReceived(),
            'vatDiscount'               => $ph->format($vatDiscount),
            'subtotal'                  => $ph->format($booking->getSubtotal()),
            'vatAmount'                 => $ph->format($booking->getVat()),
            'total'                     => $ph->format($booking->getTotal()),
            'AEDAmt'                    => $ph->format($booking->getTotal()),
            'creditAmount'              => $booking->getCreditAmount() > 0 ? $ph->format($booking->getCreditAmount()) : null,
            'location'                  => $property->getLocation(),
            'avgRating'                 => $property->getAverageRating(),
            'reviews'                   => $property->getTotalRatings(),
            'thumbnail'                 => $property->getThumbnailPath(),
            'propertyRules'             => $property->getPropertyRules(true),
            'cancellationPolicy'        => $property->getCancellationPolicy(true),
            'guests'                    => $booking->getNoOfGuest(),
            'createdAt'                 => $booking->getCreatedAt(),
            'perDayBreakdown'           => $breakDownArr,
            // 'billing_first_name'        => $ui->getBillingFirstName(),
            // 'billing_last_name'         => $ui->getBillingLastName(),
            // 'billing_email'             => $ui->getBillingEmail(),
            // 'billing_phone'             => $ui->getBillingPhone(),
            // 'billing_address'           => $ui->getBillingAddress(),
            // 'billing_country'           => $ui->getBillingCountry(),
            // 'billing_city'              => $ui->getBillingCity(),
            'billingDetails'            => [
                'billing_first_name' => $ui->getBillingFirstName(),
                'billing_last_name'  => $ui->getBillingLastName(),
                'billing_email'      => $ui->getBillingEmail(),
                'billing_phone'      => $ui->getBillingPhone(),
                'billing_address'    => $ui->getBillingAddress(),
                'billing_country'    => $ui->getBillingCountry(),
                'billing_city'       => $ui->getBillingCity(),
            ],
        ];

    }
}
