<?php

use Concrete\Core\Localization\Service\Date;

/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 18/2/19
 * Time: 11:26 AM
 */

class Statistic
{
    protected $year;
    protected $month;
    protected $total;
    protected $expected;
    protected $paidOut;
    protected $avgNights;
    protected $bookingStartDate;
    protected $bookingEndDate;
    protected $bID;
    protected $bookingStatus;

    public function __construct($row)
    {
        $this->setPropertiesFromArray($row);
    }

    public function setPropertiesFromArray($arr)
    {
        foreach ($arr as $key => $prop) {
            $this->{$key} = $prop;
        }
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param mixed $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getExpected()
    {
        return $this->expected;
    }

    /**
     * @param mixed $expected
     */
    public function setExpected($expected)
    {
        $this->expected = $expected;
    }

    /**
     * @return mixed
     */
    public function getPaidOut()
    {
        return $this->paidOut;
    }

    /**
     * @param mixed $paidOut
     */
    public function setPaidOut($paidOut)
    {
        $this->paidOut = $paidOut;
    }

    /**
     * @return mixed
     */
    public function getAvgNights()
    {
        return $this->avgNights;
    }

    /**
     * @param mixed $avgNights
     */
    public function setAvgNights($avgNights)
    {
        $this->avgNights = $avgNights;
    }

    /**
     * @return mixed
     */
    public function getBookingStartDate()
    {
        return $this->bookingStartDate;
    }

    /**
     * @return mixed
     */
    public function getBookingEndDate()
    {
        return $this->bookingEndDate;
    }

    /**
     * @return mixed
     */
    public function getBID()
    {
        return $this->bID;
    }

    /**
     * @return mixed
     */
    public function getBookingStatus()
    {
        return $this->bookingStatus;
    }



}