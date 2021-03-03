<?php
/**
 * Responsible for loading the indexed search class and initiating the reindex command.
 */
defined('C5_EXECUTE') or die('Access Denied.');

class SendRateNotification extends Job
{

//    public $jNotUninstallable = 1;

    public function getJobName()
    {
        return t('Send rate notification');
    }

    public function getJobDescription()
    {
        return t('Send rate notification.');
    }

    public function run()
    {
        /** @var DateHelper $dh */
        $dh          = Loader::helper('date');

        $bookingList   = new BookingList();
        $bookingList->filterCompleted("+1 day");
        $bookingList->filterByRateNotified(0);

        $bookings = $bookingList->get(0);

        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            if ($booking->getProperty()) {
                Events::fire('rate_booking_email',$booking);
            }
            $booking->markNotifiedToRate();
        }
    }
}
