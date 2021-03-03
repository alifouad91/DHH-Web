<?php

//use Request;

class DashboardBookingBookingsController extends DashboardBaseController
{

    const BOOKING_STATUS_PAYMENT_FAILED = 'payment_failed';
    const BOOKING_STATUS_PAID           = 'paid';
    const BOOKING_STATUS_CANCELLED      = 'cancelled';

    const EVENT_STATUS_UPCOMING   = 'upcoming';
    const EVENT_STATUS_INPROGRESS = 'in-progress';
    const EVENT_STATUS_COMPLETED  = 'completed';

    const MODE_ADD    = 'add';
    const MODE_UPDATE = 'update';

    const DATE_TIME_PICKER_POSTFIX = '_dt';

    protected $configUrl = 'dashboard/booking/bookings';
    protected $pluginsPath = DIR_REL . JS_PLUGINS_DIR;

    public function view()
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');

        /** @var DateHelper $dh */
        $dh = Loader::helper('date');

        $itemsOptions = [
            10  => 10,
            25  => 25,
            50  => 50,
            100 => 100,
        ];

        $from             = $th->sanitize($this->get('from'));
        $to               = $th->sanitize($this->get('to'));
        $bookingType      = $th->sanitize($this->get('bookingType'));
        $generate_preview = $th->sanitize($this->get('generate_preview'));
        $pId = $th->sanitize($this->get('pID'));
        $items    = intval($this->request('items'));
        $items    = in_array($items, $itemsOptions) ? $items : 10;

        $bl = new BookingList();
        if ($generate_preview) {
            $bl->populatePropertyDetails();
        }
        if ($from) {
            $from = $dh->date('Y-m-d', strtotime($from));
            $bl->filterByFromDate($from);
        }
        if ($to) {
            $to = $dh->date('Y-m-d', strtotime($to));
            $bl->filterByToDate($to);
        }
        $bl->populateEventStatus();

        if ($bookingType) {
            $currDate = $dh->date('Y-m-d');
            $bl->filterByBookingType($bookingType, $currDate);
        }
        if($pId) {
            $bl->filterByPropertyId($pId);
            $property = Property::getByID($pId);
        }
        $bl->sortBy('b.bID','desc');
        $bl->setItemsPerPage($items);
        $bookings = $bl->getPage();
        if ($generate_preview) {
            $this->excel($bookings);
        }
        $this->set('itemsOptions', $itemsOptions);
        $this->set('bookingsList', $bl);
        $this->set('bookings', $bookings);
        $this->set('configUrl', $this->configUrl);
        $this->set('task', 'overview');
        $this->set('property', $property);

        //on update
        if ($this->get('successtype') == 'update') {
            $this->set('success_message', 'Booking Successfully Updated');
        }
        if ($this->get('successtype') == 'delete') {
            $this->set('success_message', 'Booking Deleted Successfully');
        }
        $htmlHelper = Loader::helper('html');
        $this->loadFlatPickrPlugin();
        $this->loadSelect2Plugin();
        $this->addFooterItem($htmlHelper->javascript('bill.js'));

    }

    protected function loadFlatPickrPlugin()
    {
        /** @var HtmlHelper $html */
        $html = Loader::helper('html');

        $this->addHeaderItem($html->css($this->pluginsPath . "/flatpickr/flatpickr.min.css"));
        $this->addFooterItem($html->javascript($this->pluginsPath . "/flatpickr/flatpickr.min.js"));
    }

    protected function loadSelect2Plugin()
    {
        /** @var HtmlHelper $html */
        $html = Loader::helper('html');

        $this->addHeaderItem($html->css($this->pluginsPath . "/select2/select2.min.css"));
        $this->addFooterItem($html->javascript($this->pluginsPath . "/select2/select2.min.js"));
    }

    public function details($bid)
    {
        if (is_numeric($bid)) {
            $booking = Booking::getByID($bid);
            $property = Property::getByID($booking->getPID());
            $user = User::getByUserID($booking->getUID());
            $dh   = Loader::helper('date');
            $date = $dh->getSystemDateTime('now', 'Y-m-d');

            $eventStatus = 'in-progress';
            if($booking->getBookingStartDate() > $date) {
                $eventStatus = 'upcoming';
            } else if($booking->getBookingStartDate() < $date) {
                $eventStatus = 'completed';
            }

            $this->set('booking', $booking);
            $this->set('property', $property);
            $this->set('uname', $user->getUserInfoObject()->getBillingFirstName().' '.$user->getUserInfoObject()->getBillingLastName());
            $this->set('uphone', $user->getUserInfoObject()->getBillingPhone());
            $this->set('address', $user->getUserInfoObject()->getBillingAddress());
            $this->set('city', $user->getUserInfoObject()->getBillingCity());
            $this->set('country', $user->getUserInfoObject()->getBillingCountry());
            $this->set('task', 'details');
            $this->set('eventStatus', $eventStatus);
            $this->set('bookingStatuses', $this->getBookingStatusSelect());
            $this->set('eventStatuses', $this->getEventStatusSelect());
        }

    }

    public function add_edit()
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');
        /** @var $eh ValidationErrorHelper */
        $eh = Loader::helper('validation/error');
        /** @var DateHelper $dh */
        $dh = Loader::helper('date');
        /* @var  SecurityHelper $sh */
        $sh = Loader::helper('security');


        $mode               = $th->sanitize($this->post('mode'));
        $bID                = $th->sanitize($this->post('bID'));
        $email              = $th->sanitize($this->post('email'));
        $bookingDate        = $th->sanitize($this->post('bookingDate'));
        $bookingStartDate   = $th->sanitize($this->post('bookingStartDate'));
        $bookingEndDate     = $th->sanitize($this->post('bookingEndDate'));
        $noOfGuest          = $th->sanitize($this->post('noOfGuest'));
        $noOfChildren       = $th->sanitize($this->post('noOfChildren'));
        $bookingStatus      = $th->sanitize($this->post('bookingStatus'));
        $additionalRequests = $th->sanitize($this->post('additionalRequests'));


        if (!$mode) {
            $eh->add('Mode Invalid.');
        }
        if (!$bID) {
            $eh->add('Booking ID Invalid.');
        }
        if (!$email) {
            $eh->add('Please enter an email.');
        }
        if (!$bookingDate) {
            $eh->add('Please select a booking date.');
        }
        if (!($bookingStartDate . self::DATE_TIME_PICKER_POSTFIX)) {
            $eh->add('Please select a valid start date.');
        }
        if (!($bookingEndDate . self::DATE_TIME_PICKER_POSTFIX)) {
            $eh->add('Please select a valid end date');
        }
        if (!$noOfGuest) {
            $eh->add('Please select number of guest.');
        }
        if (!$bookingStatus) {
            $eh->add('Please select a booking status.');
        }


        if (!$eh->has()) {

            if ($mode == self::MODE_ADD) {
                //Add Mode Validations

            } else {

                //Edit Mode Validations
                $booking = Booking::getByID($bID);
                if (!$booking) {
                    $eh->add('Booking Id Invalid');
                }

                if (!$sh->sanitizeEmail($email)) {
                    $eh->add('Please Enter a valid email address');
                }
                $bookingDate      = $dh->getFormattedDate($bookingDate, 'Y-m-d H:i:s');
                $bookingStartDate = $dh->getFormattedDate($bookingStartDate, 'Y-m-d');
                $bookingEndDate   = $dh->getFormattedDate($bookingEndDate, 'Y-m-d');
                $noOfDays         = $dh->getNoOfNights($bookingStartDate, $bookingEndDate);


                if (strtotime($bookingStartDate) > strtotime($bookingEndDate)) {
                    $eh->add('Booking start date cannot be greater than end date');
                }

                if (!in_array($bookingStatus, $this->getBookingStatusSelect())) {
                    $eh->add('Invalid Booking Status');
                }
                //End Edit Mode Validations

                $booking = Booking::getByID($bID);

                if ($booking) {

                    $fieldListArr = [
                        'email'             => $email,
                        'bookingDate'       => $bookingDate,
                        'bookingStartDate'  => $bookingStartDate,
                        'bookingEndDate'    => $bookingEndDate,
                        'noOfDays'          => $noOfDays,
                        'noOfGuest'         => $noOfGuest,
                        'noOfChildren'      => $noOfChildren,
                        'bookingStatus'     => $bookingStatus,
                        'additionalRequest' => $additionalRequests
                    ];

                    $fieldChanged = UserLogs::compareBookingFieldsValue($booking, $fieldListArr);

                    if ($fieldChanged != '') {
                        UserLogs::add($fieldChanged, 'edited_booking');
                    }
                    if ($booking->getBookingStatus() == 'paid' && $bookingStatus == 'cancelled') {
                        $u = User::getByUserID($booking->getUID());
                        $ui = $u->getUserInfoObject();
                        Events::fire('on_booking_delete',$bID,$ui);
                    }
                    $booking->update($email, $bookingDate, $bookingStartDate, $bookingEndDate, $noOfDays,
                        $noOfGuest, $noOfChildren, $bookingStatus, $additionalRequests);
                }

                $this->redirect($this->configUrl . '?successtype=update');
            }
        } else {
            $this->set('errors', $eh->getList());
        }

    }


    private function getBookingStatusSelect()
    {
        $arr = [
            'cancelled'      => self::BOOKING_STATUS_CANCELLED,
            'paid'           => self::BOOKING_STATUS_PAID,
            'payment_cancelled' => Booking::PAYMENT_CANCELLED,
            'payment_failed' => self::BOOKING_STATUS_PAYMENT_FAILED,
            'payment_processing' => Booking::PAYMENT_PROCESSING,
            'unpaid'      => Booking::PAYMENT_UNPAID,
        ];
        return $arr;
    }

    private function getEventStatusSelect()
    {
        $arr = [
            'upcoming'    => self::EVENT_STATUS_UPCOMING,
            'in-progress' => self::EVENT_STATUS_INPROGRESS,
            'completed'   => self::EVENT_STATUS_COMPLETED,
        ];
        return $arr;
    }


    public function delete($bID)
    {
        /** @var $eh ValidationErrorHelper */
        $eh = Loader::helper('validation/error');

        if (!is_numeric($bID)) {
            $eh->add('Invalid Booking Id');

        }
        $booking = Booking::getByID($bID);
        if (!$booking) {
            $eh->add('Invalid Booking Id');
        }

        $u = User::getByUserID($booking->getUID());
        $ui = $u->getUserInfoObject();

        if (!$ui) {
            $eh->add('Invalid User');
        }

        if ($eh->has()) {
            $this->details($bID);
            $this->set('errors', $eh->getList());

        } else {
            //Events::fire('on_booking_delete',$bID,$ui);
            $booking->delete();
            $this->redirect($this->configUrl . '?successtype=delete');
        }


    }

    public function excel($bookings)
    {
        $fileName = "Booking Results";

//        header("Content-Type: application/vnd.ms-excel");
//        header("Cache-control: private");
//        header("Pragma: public");
//        header("Content-Disposition: attachment; filename=" . $fileName . "_form_data_{$date}.xls");

        $date = date('Ymd');
        header("Content-Type: text/csv");
        header("Cache-control: private");
        header('Content-Transfer-Encoding: binary');
        header("Pragma: public");
        print "\xEF\xBB\xBF"; // UTF-8 BOM
        header("Content-Disposition: attachment; filename=" . $fileName . "_form_data_{$date}.csv");
        header("Content-Title: Booking Export - Run on {$date}");

        $fp = fopen('php://output', 'w');

        // write the columns
        $row = array(
            t('Booking No'),
            t('User'),
            t('Email'),
            t('Phone'),
            t('Address'),
            t('City'),
            t('Country'),
            t('Property Name'),
            t('Booking Date'),
            t('Start Date'),
            t('End Date'),
            t('Number of Days'),
            t('No Of Guest'),
            t('No Of Children'),
            t('Booking Status'),
            t('Event Status'),
            t('Additional Requests'),

            t('Property Price'),
            t('Sub Total'),
            t('Additional Items Total'),
            t('Vat Amount'),
            t('Tourism Fee'),
            t('Referral Credit'),
            t('Coupon Applied'),
            t('Coupon Discount'),
            t('Total'),
            t('Payment Status'),
            t('Attempts Done'),
        );
        fputcsv($fp, $row);

        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            $uID = $booking->getUID();
            if (empty($uID)) {
                $user = t("Guest");
            } else {
                $u = User::getByUserID($uID);
                if (is_object($u)) {
                    $user = $u->getUserInfoObject()->getBillingFirstName().' '.$u->getUserInfoObject()->getBillingLastName();
                    $user_phone = $u->getUserInfoObject()->getBillingPhone();
                    $address = $u->getUserInfoObject()->getBillingAddress();
                    $city = $u->getUserInfoObject()->getBillingCity();
                    $country = $u->getUserInfoObject()->getBillingCountry();
                } else {
                    $user = tc('Deleted user', 'Deleted (id: %s)', $uID);
                }
            }

            $coupons_code = [];
            if($booking->getDiscountReceived()) {
                $coupons =  $booking->getAppliedCoupons();
                foreach ($coupons as $k => $v) {
                    $coupon = DiscountCoupon::getByID($v);
                    if($coupon) {
                        $coupons_code[] = $coupon->getCouponCode();
                    }
                }
            }

            $paymentList = new PaymentList();
            $paymentList->filterByBookingId($booking->getBID());
            $paymentList->sortByCreatedAt();
            $paymentDetails = $paymentList->get();

            foreach($paymentDetails as $payment){
                $bookingStatus = $payment->getOrderStatus();
                break;
            }

            $priceBreakdown = $booking->getPriceBreakdown();
            $breakdownArray = [];
            if(is_array($priceBreakdown)){
                    foreach($priceBreakdown as $key => $breakdown){
                        $breakdownArray[] = $breakdown->price." | ".date("d-m-Y", strtotime($breakdown->day));
                    }
            }

            $row = array(
                $booking->getBookingNo(),
                $user,
                $booking->getEmail(),
                $user_phone,
                $address,
                $city,
                $country,
                $booking->getProperty()->getName(),
                $booking->getBookingDate(),
                $booking->getBookingStartDate(),
                $booking->getbookingEndDate(),
                $booking->getNoOfDays(),
                $booking->getNoOfGuest(),
                $booking->getNoOfChildren(),
                $booking->getBookingStatus(),
                $booking->getEventStatus(),
                $booking->getAdditionalRequests(),
                implode(',',$breakdownArray),
                $booking->getSubtotal(),
                $booking->getBookingPropertyFacilitiesTotal(),
                $booking->getVat(),
                $booking->getDhiramFee(),
                $booking->getCreditAmount(),
                implode(',',$coupons_code),
                $booking->getDiscountReceived(),
                $booking->getTotal(),
                $bookingStatus,
                count($paymentDetails),
            );

            fputcsv($fp, $row);
        }
        fclose($fp);
        die;
    }
}