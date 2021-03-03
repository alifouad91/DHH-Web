<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class PropertyBlockDates
{
    protected $pbdID;
    protected $pID;
    protected $startDate;
    protected $endDate;
    protected $description;
    protected $price;
    protected $total;
    protected $year;
    protected $month;
    protected $avgNights;

    function __construct($row)
    {
        $this->pbdID                 = $row['pbdID'];
        $this->pID                = $row['pID'];
        $this->startDate             = $row['startDate'];
        $this->endDate         = $row['endDate'];
        $this->description         = $row['description'];
        $this->price         = $row['price'];
    }

    /**
     * @param $pID
     * @param $startDate
     * @param $endDate
     * @return $bd
     */
    public static function add($pID,$startDate, $endDate, $desc, $price)
    {
        $db    = Loader::db();
        $query = "INSERT INTO PropertyBlockDates(pID,startDate,endDate,description,price ) 
              VALUES ( ? , ?, ?, ?, ? ) ";
        $ret   = $db->Execute($query, [$pID, $startDate, $endDate, $desc, $price]);
        if ($ret) {
            $bd = self::getByID($db->Insert_ID());
            return $bd;
        }

        return null;
    }

    /**
     * @param $pID
     * @param $pbdID
     * @param $startDate
     * @param $endDate
     * @param $desc
     * @param $price
     * @return $bd
     */
    public static function update($pbdID,$pID,$startDate, $endDate, $desc, $price)
    {
        $db    = Loader::db();
        $query = "UPDATE PropertyBlockDates SET pID = ?,startDate = ?,endDate = ?, description = ?, price = ? WHERE pbdID = ?";
        $ret   = $db->Execute($query, [$pID,$startDate, $endDate, $desc, $price, $pbdID]);
        if ($ret) {
            return self::getByID($pbdID);
        }

        return null;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM PropertyBlockDates WHERE pbdID = ?";
        $ret   = $db->Execute($query, [$this->getID()]);

        return $ret;
    }

    public static function where($condition = null)
    {
        $condition_vars   = [];
        $condition_suffix = '1';

        if (!is_array($condition)) {
            if ($condition) {
                $condition_suffix = $condition;
            }
        } else {

            $condition_suffix = '';
            $last_key         = end(array_keys($condition));

            foreach ($condition as $key => $value) {

                if ($value === null) {
                    $condition_suffix .= $key . ' IS NULL';
                } else {
                    $condition_suffix .= $key . ' = ?';
                    $condition_vars[] = $value;
                }

                if ($key !== $last_key) {
                    $condition_suffix .= " AND ";
                }
            }
        }

        $db     = Loader::db();
        $query  = "SELECT * FROM PropertyBlockDates WHERE {$condition_suffix}";
        $result = $db->Execute($query, $condition_vars);

        $links = [];

        while ($row = $result->FetchRow()) {
            $links[] = new static($row);
        }

        return $links;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM PropertyBlockDates WHERE pbdID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['pbdID'])) {
            return new PropertyBlockDates($row);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->pbdID;
    }

    /**
     * @return mixed
     */
    public function getPID()
    {
        return $this->pID;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
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
}