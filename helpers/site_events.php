<?php
defined('C5_EXECUTE') or die('Access Denied.');

class SiteEventsHelper
{
    /**
     * When Notifications is added from the dashboard,
     * trigger push notifications to notify users
     *
     * @param Notification $notification
     */
    public function on_notification_add(Notification $notification)
    {
//        PushNotificationManager::on_notification_add($notification);
    }

    /**
     * When an app user registers
     * send email to that user for verification
     *
     * @param $uHash
     * @param $email
     */
    public function on_app_user_registration($uHash, $email)
    {
        /** @var MailHelper $mh */
        $mh = Loader::helper('mail');
        if (ADMIN_EMAIL_NOTIFICATIONS) {
            $mh->from(EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
            $mh->to($email);
            $mh->addParameter('uEmail', $email);
            $mh->addParameter('uHash', $uHash);
            $mh->load('validate_user_email');
            $mh->setSubject('Verify your account');
            $mh->sendMail();
        }
    }

    public function on_email_verification_success($ui)
    {
        /** @var MailHelper $mh */
        /** @var UserInfo $ui */
        $mh = Loader::helper('mail');
        if (ADMIN_EMAIL_NOTIFICATIONS) {
            $mh->from(EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
            $mh->to($ui->getUserEmail());
            $mh->addParameter('uEmail', $ui->getUserEmail());
            $mh->addParameter('first_name', $ui->getUserName());
            $mh->addParameter('uName', $ui->getFirstName());
            $mh->load('welcome_mail');
            $mh->setSubject('Welcome to DHH');
            $mh->sendMail();
        }
    }

    public function on_booking_delete($bookingID,$ui)
    {
        /** @var MailHelper $mh */
        /** @var UserInfo $ui */
        $booking  = Booking::getByID($bookingID);
        $mh = Loader::helper('mail');
        $mh->from(EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
        $mh->to($ui->getUserEmail());
	    $mh->cc('hello@dhh.ae');
        $mh->addParameter('uEmail', $ui->getUserEmail());
        $mh->addParameter('uName', $ui->getFirstName());
        $mh->addParameter('booking', $booking);
        $mh->load('cancel_booking');
        $mh->setSubject('Oh no! Booking cancelled with DHH');
        $mh->sendMail();
        Log::addEntry(var_export($mh->getBodyHtml(), true), 'Oh no! Booking cancelled with DHH');
    }

    /**
     * @param $emails
     * @param User $u
     * @param $registerLink
     */
    public function on_send_invite($emails, $u)
    {
        /** @var MailHelper $mh */
        /** @var UserInfo $ui */
        $ui = $u->getUserInfoObject();

        if ($ui) {
            $userEmail = $ui->getUserEmail();
            $userName = $ui->getFullName();
            foreach ($emails as $email) {
                $mh = Loader::helper('mail');
//                $mh->from($userEmail, $userName);
                $mh->from('hello@dhh.ae', EMAIL_DEFAULT_FROM_NAME);
                $mh->to($email);
                $mh->addParameter('registerLink', BASE_URL.View::url('register/'.$ui->getUniqueToken()));
                $mh->addParameter('userName', $userName);
                $mh->load('invite');
                $mh->setSubject('DHH Invite');
                $mh->sendMail();
            }
        }
    }

    public function send_bill_as_email($billID,$email)
    {
        /** @var MailHelper $mh */
        /** @var UserInfo $ui */
        /** @var MimeHelper $mimeHelper */
        $bill = Bill::getByID($billID);
        $filename = end(explode('/',$bill->getPDFPath()));
        $mh = Loader::helper('mail');
        $mimeHelper = Loader::helper('mime');
        $mh->from(EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
        $mh->to($email);
        $mh->addParameter('billID', $billID);
        $mh->load('bill_email');
        $mh->setSubject('Bill No '.$billID);
        //$mh->addAttachment($bill->getPDFPath(), $filename, mime_content_type ( $filename ));
//        if($bill->getPDFPath(true)) {
//            $mh->addAttachment(
//                $bill->getPDFPath(true),
//                $filename,
//                $mimeHelper->mimeFromExtension(pathinfo($filename, PATHINFO_EXTENSION))
//            );
//        }
        $mh->sendMail();
    }

    /**
     * @param $bookingID
     */
    public function on_property_booked($bookingID)
    {
        $booking  = Booking::getByID($bookingID);
        if (!$booking)
        {
            return;
        }

        $u = new User();
        $ui = $u->getUserInfoObject();
        /** @var MailHelper $mh */
        /** @var UserInfo $ui */
        $mh = Loader::helper('mail');
        $mh->from(EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
        $mh->to($ui->getUserEmail());
	    $mh->cc('hello@dhh.ae');
        $mh->addParameter('uName', $ui->getFirstName());
        $mh->addParameter('booking', $booking);
        $mh->load('booking_confirmed');
        $mh->setSubject('Booking confirmed!');

        $mh->sendMail();

        $property = $booking->getPID();
        $property = Property::getByID($property);
        $Owner = $property->getOwnerID();

        $category = Notification::CATEGORY_PROPERTY_BOOKED;
        $type     = Notification::TYPE_BOOKING;
        Notification::addIfNew($Owner, $category, $type, $booking->getBID(), '/profile/my-properties');
        Notification::addIfNew($ui->getUserID(), $category, $type, $booking->getBID(),null,null,null,'Booking Successfull');
    }

    /**
     * @param Booking $booking
     */
    public function rate_booking_email($booking)
    {
        $ui = UserInfo::getByID($booking->getUID());
        /** @var MailHelper $mh */
        /** @var UserInfo $ui */
        $mh = Loader::helper('mail');
        $mh->from(EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
        $mh->to($ui->getUserEmail());
        $mh->addParameter('booking', $booking);
        $mh->addParameter('uName', $ui->getFirstName());
        $mh->load('rate_booking');
        $mh->setSubject('Rate your stay');
        $mh->sendMail();
    }

    /**
     * @param $bookingID
     */
    public function upcoming_booking($bookingID)
    {
        $booking  = Booking::getByID($bookingID);
        if (!$booking)
        {
            return;
        }
        $category = Notification::CATEGORY_UPCOMING_BOOKING;
        $type     = Notification::TYPE_BOOKING;
        Notification::addIfNew('', $category, $type, $booking->getBID());
    }

    /**
     * @param $bookingID
     */
    public function past_booking($bookingID)
    {
        $booking  = Booking::getByID($bookingID);
        if (!$booking)
        {
            return;
        }
        $category = Notification::CATEGORY_PAST_BOOKING;
        $type     = Notification::TYPE_BOOKING;
        Notification::addIfNew('', $category, $type, $booking->getBID());
    }

    /**
     * @param $bookingID
     */
    public function payment_success($bookingID)
    {
        $booking  = Booking::getByID($bookingID);
        if (!$booking)
        {
            return;
        }
        $noOfDays = $booking->getNoOfDays();
        $checkIn = $booking->getBookingStartDate();

        $user = UserDetails::getByID($booking->getUID());
        if(Config::get('ENABLE_SMS')) {
            $body = 'Hi, your booking for '.$noOfDays.' days with Driven Holiday Homes is confirmed. Your check-in date is '.$checkIn.'.For assistance, visit https://bit.ly/dhhsupport.';
            SMS::send($user->getBillingPhone(),Config::get('SMS_API_FROM_NUMBER'),$body);
        }

        $booking->updatePaymentStatus(Booking::PAYMENT_COMPLETE);
    }

    /**
     * @param $bookingID
     */
    public function payment_failed($bookingID)
    {

        $booking  = Booking::getByID($bookingID);
        if (!$booking) {
            return;
        }
        $user = UserDetails::getByID($booking->getUID());
        if(Config::get('ENABLE_SMS')) {
            $body = 'We are sorry for the in convenience.Your booking was not successful.Please contact us to complete your booking. Reference No :' . $booking->getBID().'.';
            SMS::send($user->getBillingPhone(),Config::get('SMS_API_FROM_NUMBER'),$body);
        }

        $booking->updatePaymentStatus(Booking::PAYMENT_FAILED);
    }

    /**
     * @param $bookingID
     */
    public function payment_cancelled($bookingID)
    {
        $booking  = Booking::getByID($bookingID);
        if (!$booking)
        {
            return;
        }
        $booking->updatePaymentStatus(Booking::PAYMENT_CANCELLED);
    }

    /**
     * @param $bookingID
     */
    public function payment_processing($bookingID)
    {
        $booking  = Booking::getByID($bookingID);
        if (!$booking)
        {
            return;
        }
        $booking->updatePaymentStatus(Booking::PAYMENT_PROCESSING);
    }

    /**
     * @param $reviewID
     */
    public function on_property_reviewed($reviewID)
    {
        $review  = Review::getByID($reviewID);
        if (!$review)
        {
            return;
        }
        $category = Notification::CATEGORY_PROPERTY_REVIEW;
        $type     = Notification::TYPE_REVIEW;
        Notification::addIfNew('', $category, $type, $review->getId());
    }

    /**
     * @param $billID
     */
    public function on_utility_add($billID)
    {
        $bill  = Bill::getByID($billID);
        if (!$bill)
        {
            return;
        }
        $category = Notification::CATEGORY_UTILITY;
        $type     = Notification::TYPE_UTILITY;
        Notification::addIfNew('', $category, $type, $bill->getId());
    }

}
