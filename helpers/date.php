<?php
defined('C5_EXECUTE') or die('Access Denied.');
class DateHelper extends Concrete5_Helper_Date {
    /**
     * @param $startDate
     * @param $endDate
     * @return mixed|string
     */
    public function getNoOfNights($startDate, $endDate)
    {

        $datetime1 = new DateTime($startDate);
        $datetime2 = new DateTime($endDate);

        $interval = $datetime1->diff($datetime2);
        return $interval->format('%a');


    }

    public function subtractNight($date,$interval)
    {
        $datetime1 = new DateTime($date);

        $ret = $datetime1->sub($interval);
        return $ret->format('Y-m-d');
    }

    public function getDatesFromRange($start, $end, $format = 'Y-m-d') {
        $array = array();
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }
}
