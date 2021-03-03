<?php
defined('C5_EXECUTE') or die('Access Denied.');
class BookingHelper extends Concrete5_Helper_Date {
    /**
     * @param $booking
     * @param $clearAll
     * @return mixed|string
     */
    public static function clearPendingBooking(Booking $booking, $clearAll = false)
    {
        /** @var TextHelper $th */
        /** @var DateHelper $dh */
        $th               = Loader::helper('text');
        $dh               = Loader::helper('date');
        $currentTime = $dh->getSystemDateTime('now', 'Y-m-d H:i:s');
        $currentTime = new DateTime($currentTime);

        $createdTime = $dh->getFormattedDate($booking->getCreatedAt(), 'Y-m-d H:i:s');
        $createdTime = new DateTime($createdTime);
        $diff        = $currentTime->diff($createdTime);
        if ($diff->i > 15 || $diff->h > 0 || $clearAll) {
            $uID = $booking->getUID();
            $ui = UserInfo::getByID($uID);
            if ($ui) {
                $bookCreditAmount = (double)$booking->getCreditAmount();
                $userCreditAmount = (double)$ui->getCreditAmount();
                $creditAmount = $bookCreditAmount + $userCreditAmount;
                $ui->updateReferralCredit($creditAmount);
            }
            $booking->delete();
        }


    }
}
