<?php

class Utils
{

    public static function getNoOfDaysBetweenDates($start_date_str, $end_date_str)
    {

        $start_date = new DateTime($start_date_str);
        $end_date   = new DateTime($end_date_str);
        $noOfDays   = $end_date->diff($start_date)->format("%a");
        return $noOfDays;
    }

    //To get date time from concrete datatime form element
    //input - elem name
    //return - str - 2018-6-2 16:20:00
    public static function getConcreteDateTimeString($post_name)
    {

        /** @var DateHelper $dh */
        $dh = Loader::helper('date');
                                                                    // 2/6/2019
        $bookingDate_date = $dh->date('Y-m-d', strtotime($_POST[$post_name.'_dt'])); //2019-6-2
        $booking_time_str_am_pm = $_POST[$post_name.'_h'] .':'.$_POST[$post_name.'_m'].' '.$_POST[$post_name.'_a'] ;  //11:00 AM
        $time_24_hr_format =  date("H:i", strtotime($booking_time_str_am_pm)); // 16:20
        $bookingStartDate = $bookingDate_date.' '.$time_24_hr_format; // 2018-6-2 16:20:00
        return $bookingStartDate;

    }


}

?>