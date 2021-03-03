<?php
/**
 * Responsible for loading the indexed search class and initiating the reindex command.
 */
defined('C5_EXECUTE') or die('Access Denied.');

class ClearBooking extends Job
{

//    public $jNotUninstallable = 1;

    public function getJobName()
    {
        return t('Clear incomplete bookings');
    }

    public function getJobDescription()
    {
        return t('Clear incomplete bookings.');
    }

    public function run()
    {
        /** @var DateHelper $dh */
        $dh          = Loader::helper('date');
        $currentTime = $dh->getSystemDateTime('now', 'Y-m-d H:i:s');
        $currentTime = new DateTime($currentTime);

        $bookingStatus = [Booking::PAYMENT_UNPAID, Booking::PAYMENT_FAILED, Booking::PAYMENT_CANCELLED];
        $bookingList   = new BookingList();
        $bookingList->filterByBookingStatus($bookingStatus);


        $bookings = $bookingList->get(0);

        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            BookingHelper::clearPendingBooking($booking);
        }
    }
}
