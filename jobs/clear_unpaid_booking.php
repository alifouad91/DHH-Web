<?php
/**
 * Responsible for loading the indexed search class and initiating the reindex command.
 */
defined('C5_EXECUTE') or die('Access Denied.');

class ClearUnpaidBooking extends Job
{

//    public $jNotUninstallable = 1;

    public function getJobName()
    {
        return t('Clear unpaid bookings');
    }

    public function getJobDescription()
    {
        return t('Clear unpaid bookings.');
    }

    public function run()
    {
        /** @var DateHelper $dh */
        $dh          = Loader::helper('date');
        $currentTime = $dh->getSystemDateTime('now', 'Y-m-d H:i:s');
        $currentTime = new DateTime($currentTime);

        $bookingStatus = ['payment_processing'];
        $bookingList   = new BookingList();
        $bookingList->filterByBookingStatus($bookingStatus);

        $bookings = $bookingList->get(0);

        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            $updatedAt   = $dh->getFormattedDate($booking->getUpdatedAt(), 'Y-m-d H:i:s');
            $updatedTime = new DateTime($updatedAt);
            $diff        = $currentTime->diff($updatedTime);
            if ($diff->i > 5 || $diff->h > 0) {
                $createdAt   = $dh->getFormattedDate($booking->getCreatedAt(), 'Y-m-d H:i:s');
                $createdTime = new DateTime($createdAt);
                $diff        = $currentTime->diff($createdTime);
                if ($diff->i > 20 || $diff->h > 0) {
                    $uID = $booking->getUID();
                    $ui = UserInfo::getByID($uID);
                    if ($ui) {
                        $bookCreditAmount = $booking->getCreditAmount();
                        $userCreditAmount = $ui->getCreditAmount();
                        $creditAmount = $bookCreditAmount + $userCreditAmount;
                        $ui->updateReferralCredit($creditAmount);
                    }
                    $booking->delete();
                }
                else{
                    $booking->updatePaymentStatus(Booking::PAYMENT_UNPAID);
                }
            }

        }
    }
}
