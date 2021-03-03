<?php
/**
 * Responsible for loading the indexed search class and initiating the reindex command.
 */
defined('C5_EXECUTE') or die('Access Denied.');

class AddReferralBooking extends Job
{

//    public $jNotUninstallable = 1;

    public function getJobName()
    {
        return t('Add Referral Credit');
    }

    public function getJobDescription()
    {
        return t('Clear incomplete bookings.');
    }

    public function run()
    {
        /** @var DateHelper $dh */
        $dh          = Loader::helper('date');
        $currentDate = $dh->getFormattedDate('now', 'Y-m-d');

        $bookingList = new BookingList();
        $bookingList->filterByBookingStatus(Booking::PAYMENT_COMPLETE);
        $bookingList->filterByCheckoutTime();
        $bookingList->filterReferredBooking();

        $bookings = $bookingList->get(0);

        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            $u  = $booking->getUID();
            $ui = UserInfo::getByID($u);
            if ($ui->getReferredBy()) {

                $referralList = new ReferralList();
                $referralList->filterByReferrerEmail($ui->getReferredBy());
                $referralList->filterByReferredEmail($ui->getUserEmail());
                $referralList->filterByCreditSent('NO');
                $referrals = $referralList->get();

                $referrer = UserInfo::getByEmail($ui->getReferredBy());
                if ($referrals && $referrer) {
                    $creditAmount = Config::get('REFERRAL_CREDIT') + $referrer->getCreditAmount();
                    $referrer->updateReferralCredit($creditAmount);
                }

                /** @var Referral $referral */
                foreach ($referrals as $referral) {
                    $referral->update($referral->getID());
                }

            }
        }
    }
}
