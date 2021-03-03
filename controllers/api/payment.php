<?php
defined('C5_EXECUTE') or die('Access Denied.');
Loader::controller('/api');

class ApiPaymentController extends ApiController
{
    public function paymentSuccess()
    {
        $th        = Loader::helper('text');
        $bookingNo = $th->sanitize($this->post('orderNo'));

        $booking = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
			$this->redirect('booking/failed/' . $bookingNo);
        }
        if ($this->hasErrors()) {
			$this->redirect('booking/failed/' . $bookingNo);
        }

        $working_key = Config::get('ENCRYPTION_KEY');
        $encResponse = $th->sanitize($this->post('encResp'));            //This is the response sent by the CCAvenue Server

        $rcvdString    = CCAvenuePaymentSetup::decrypt($encResponse, $working_key);        //Crypto Decryption used as per the specified working key.
        $order_status  = "";
        $message       = "";
        $decryptValues = explode('&', $rcvdString);
        $dataSize      = sizeof($decryptValues);

        $responseArr = [];
        for ($i = 0; $i < $dataSize; $i++) {
            $information                  = explode('=', $decryptValues[$i]);
            $responseArr[$information[0]] = $information[1];
            if ($i == 3) {
                $order_status = $information[1];
            }
            if ($i == 4) {
                $message = $information[1];
            }
        }


        $status = 'failure';
        if ($order_status == 'Success') {
			Payment::add($booking->getUID(), $booking, $responseArr);
            $status = 'success';
            $booking->updatePaymentStatus('paid');
            Events::fire('payment_success', $booking->getBID());
            Events::fire('on_property_booked', $booking->getBID());
            $this->redirect('booking/confirm/' . $bookingNo);
        } else {
            $booking->updatePaymentStatus('payment_failed');
            Events::fire('payment_failed', $booking->getBID());
            $this->redirect('booking/failed/' . $bookingNo);
        }


        $property = $booking->getProperty();

        $tempBooking = [
            'bID'                       => $booking->getBID(),
            'pID'                       => $property->getID(),
            'bookingNo'                 => $booking->getBookingNo(),
            'name'                      => $property->getName(),
            'caption'                   => $property->getCaption(),
            'startDate'                 => $booking->getBookingStartDate(),
            'endDate'                   => $booking->getbookingEndDate(),
            'bookingAdditionFacilities' => $booking->getBookingPropertyFacilities(true),
            'additionalRequests'        => $booking->getAdditionalRequests(),
            'subtotal'                  => $booking->getSubtotal(),
            'total'                     => $booking->getTotal(),
            'creditAmount'              => $booking->getCreditAmount(),
            'location'                  => $property->getLocation(),
            'avgRating'                 => $property->getAverageRating(),
            'reviews'                   => $property->getTotalRatings(),
            'thumbnail'                 => $property->getThumbnailPath(),
            'propertyRules'             => $property->getPropertyRules(true),
            'cancellationPolicy'        => $property->getCancellationPolicy(true),
            'guests'                    => $booking->getNoOfGuest(),
            'createdAt'                 => $booking->getCreatedAt()
        ];

        $result = [
            'additionalFacilities' => $property->getPropertyFacilities(true),
            'booking'              => $tempBooking,
            'status'               => $status,
            'message'              => $message,
        ];
        return $result;

    }

    public function paymentProcessing()
    {
        $th        = Loader::helper('text');
        $bookingNo = $th->sanitize($this->post('bookingNo'));

        $billing_firstName = urldecode($th->sanitize($this->post('billing_first_name')));
        $billing_lastName  = urldecode($th->sanitize($this->post('billing_last_name')));
        $billing_no        = $th->sanitize($this->post('billing_phone'));
        $billing_email     = urldecode($th->sanitize($this->post('billing_email')));
        $billing_address   = urldecode($th->sanitize($this->post('billing_address')));
        $billing_city      = urldecode($th->sanitize($this->post('billing_city')));
        $billing_country   = urldecode($th->sanitize($this->post('billing_country')));
        $additionalRequests   = urldecode($th->sanitize($this->post('additionalRequests')));
        $billing_name      = $billing_firstName . ' ' . $billing_lastName;
        $u                 = $this->validUser();

        $booking = Booking::getByBookingNo($bookingNo);
        $booking->updateAdditionalRequest($additionalRequests);
        if (!$booking) {
            return $this->addError('Invalid Booking ID');
        }
        if ($u->getUserID() != $booking->getUID()) {
            return $this->addError('Authentication failed');
        }
        if ($this->hasErrors()) {
            return null;
        }

        UserInfo::updateBillingDetails($u->getUserID(), $billing_firstName, $billing_lastName, $billing_no, $billing_email, $billing_address, $billing_city, $billing_country);

        $working_key   = Config::get('ENCRYPTION_KEY');
        $access_code   = Config::get('ACCESS_CODE');
        $merchantID    = Config::get('MERCHANT_ID');
        $merchant_data = '';

        $property = $booking->getProperty();

        Events::fire('payment_processing', $booking->getBID());
        $tempBooking = [
            'bID'                       => $booking->getBID(),
            'pID'                       => $property->getID(),
            'bookingNo'                 => $booking->getBookingNo(),
            'name'                      => $property->getName(),
            'caption'                   => $property->getCaption(),
            'startDate'                 => $booking->getBookingStartDate(),
            'endDate'                   => $booking->getbookingEndDate(),
            'bookingAdditionFacilities' => $booking->getBookingPropertyFacilities(true),
            'additionalRequests'        => $booking->getAdditionalRequests(),
            'subtotal'                  => $booking->getSubtotal(),
            'total'                     => $booking->getTotal(),
            'creditAmount'              => $booking->getCreditAmount(),
            'location'                  => $property->getLocation(),
            'avgRating'                 => $property->getAverageRating(),
            'reviews'                   => $property->getTotalRatings(),
            'thumbnail'                 => $property->getThumbnailPath(),
            'propertyRules'             => $property->getPropertyRules(true),
            'cancellationPolicy'        => $property->getCancellationPolicy(true),
            'guests'                    => $booking->getNoOfGuest(),
            'createdAt'                 => $booking->getCreatedAt()
        ];

        $postFields = [
            'merchant_id'         => $merchantID,
            'order_id'            => $booking->getBookingNo(),
            'amount'              => $booking->getTotal(),
            'currency'            => CurrencyRates::DEFAULT_CURRENCY,
            'redirect_url'        => BASE_URL . View::url('api/payment/paymentSuccess'),
            'cancel_url'          => BASE_URL . View::url('api/payment/paymentCancelled'),
            'language'            => 'EN',
            'billing_name'        => $billing_name,
            'billing_address'     => $billing_address,
            'billing_city'        => $billing_city,
            'billing_state'       => '',
            'billing_zip'         => '',
            'billing_country'     => $billing_country,
            'billing_tel'         => $billing_no,
            'billing_email'       => $billing_email,
            'delivery_name'       => '',
            'delivery_address'    => '',
            'delivery_city'       => '',
            'delivery_state'      => '',
            'delivery_zip'        => '',
            'delivery_country'    => '',
            'delivery_tel'        => '',
            'merchant_param1'     => substr($property->getName(), 0, 90),
            'merchant_param2'     => $booking->getBookingStartDate(),
            'merchant_param3'     => $booking->getbookingEndDate(),
            'merchant_param4'     => '',
            'merchant_param5'     => '',
            'promo_code'          => '',
            'customer_identifier' => '',
            'submit'              => 'Payment',
            'integration_type'    => 'iframe_normal',
        ];

        $merchant_data  = http_build_query($postFields);
        $encrypted_data = CCAvenuePaymentSetup::encrypt($merchant_data, $working_key); // Method for encrypting the data.

        $production_url = 'https://secure.ccavenue.ae/transaction/transaction.do?command=initiateTransaction&encRequest=' . $encrypted_data . '&access_code=' . $access_code;

        //we need to include the js ccAvenuePayment.js

        $result = [
            'additionalFacilities' => $property->getPropertyFacilities(true),
            'booking'              => $tempBooking,
            'production_url'       => $production_url
        ];
        return $result;

    }


    public function paymentFailed()
    {
        $th        = Loader::helper('text');
        $bookingNo = $th->sanitize($this->post('bookingNo'));

        $u = $this->validUser();

        $booking = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
            return $this->addError('Invalid Booking ID');
        }
        if ($u->getUserID() != $booking->getUID()) {
            return $this->addError('Authentication failed');
        }
        if ($this->hasErrors()) {
            return null;
        }

        $property = $booking->getProperty();

        Events::fire('payment_failed', $booking->getBID());
        $tempBooking = [
            'bID'                       => $booking->getBID(),
            'pID'                       => $property->getID(),
            'bookingNo'                 => $booking->getBookingNo(),
            'name'                      => $property->getName(),
            'caption'                   => $property->getCaption(),
            'startDate'                 => $booking->getBookingStartDate(),
            'endDate'                   => $booking->getbookingEndDate(),
            'bookingAdditionFacilities' => $booking->getBookingPropertyFacilities(true),
            'additionalRequests'        => $booking->getAdditionalRequests(),
            'subtotal'                  => $booking->getSubtotal(),
            'total'                     => $booking->getTotal(),
            'creditAmount'              => $booking->getCreditAmount(),
            'location'                  => $property->getLocation(),
            'avgRating'                 => $property->getAverageRating(),
            'reviews'                   => $property->getTotalRatings(),
            'thumbnail'                 => $property->getThumbnailPath(),
            'propertyRules'             => $property->getPropertyRules(true),
            'cancellationPolicy'        => $property->getCancellationPolicy(true),
            'guests'                    => $booking->getNoOfGuest(),
            'createdAt'                 => $booking->getCreatedAt()
        ];

        $result = [
            'additionalFacilities' => $property->getPropertyFacilities(true),
            'booking'              => $tempBooking
        ];
        return $result;

    }

    public function paymentCancelled()
    {
        $th        = Loader::helper('text');
        $bookingNo = $th->sanitize($this->post('orderNo'));

        $booking = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
            return $this->addError('Invalid Booking ID');
        }
        if ($this->hasErrors()) {
            return null;
        }

        $property = $booking->getProperty();

        Events::fire('payment_cancelled', $booking->getBID());
        $this->redirect('booking/cancelled/' . $bookingNo);

        $tempBooking = [
            'bID'                       => $booking->getBID(),
            'pID'                       => $property->getID(),
            'bookingNo'                 => $booking->getBookingNo(),
            'name'                      => $property->getName(),
            'caption'                   => $property->getCaption(),
            'startDate'                 => $booking->getBookingStartDate(),
            'endDate'                   => $booking->getbookingEndDate(),
            'bookingAdditionFacilities' => $booking->getBookingPropertyFacilities(true),
            'additionalRequests'        => $booking->getAdditionalRequests(),
            'subtotal'                  => $booking->getSubtotal(),
            'total'                     => $booking->getTotal(),
            'creditAmount'              => $booking->getCreditAmount(),
            'location'                  => $property->getLocation(),
            'avgRating'                 => $property->getAverageRating(),
            'reviews'                   => $property->getTotalRatings(),
            'thumbnail'                 => $property->getThumbnailPath(),
            'propertyRules'             => $property->getPropertyRules(true),
            'cancellationPolicy'        => $property->getCancellationPolicy(true),
            'guests'                    => $booking->getNoOfGuest(),
            'createdAt'                 => $booking->getCreatedAt()
        ];

        $result = [
            'additionalFacilities' => $property->getPropertyFacilities(true),
            'booking'              => $tempBooking
        ];
        return $result;

    }
}
