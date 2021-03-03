<?php
defined('C5_EXECUTE') or die("Access Denied.");

Class BookingController Extends Controller
{

    const ITEMS_TO_LOAD = 10;

    public function view()
    {
        $this->redirect('/properties');
    }

    public function review($bookingNo)
    {
        if (!$bookingNo) {
            $this->redirect('/properties');
        }
        $booking = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
            $this->redirect('/properties');
        }
        $this->set('booking', $booking);
        $this->set('task', 'review');
    }

    public function payment($bookingNo)
    {
        if (!$bookingNo) {
            $this->redirect('/properties');
        }
        $booking = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
            $this->redirect('/properties');
        }
        $this->set('booking', $booking);
        $this->set('task', 'payment');
    }

    public function confirm($bookingNo)
    {
        if (!$bookingNo) {
            $this->redirect('/properties');
        }

        $booking = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
            $this->redirect('/properties');
        }
        $this->set('booking', $booking);
        $this->set('task', 'confirm');
    }

    public function cancelled($bookingNo)
    {
        if (!$bookingNo) {
            $this->redirect('/properties');
        }

        $booking = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
            $this->redirect('/properties');
        }
        $this->set('booking', $booking);
        $this->set('task', 'cancelled');
    }

    public function failed($bookingNo)
    {
        if (!$bookingNo) {
            $this->redirect('/properties');
        }

        $booking = Booking::getByBookingNo($bookingNo);
        if (!$booking) {
            $this->redirect('/properties');
        }
        $this->set('booking', $booking);
        $this->set('task', 'failed');
    }

}
