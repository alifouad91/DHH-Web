<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class PropertySeason
{
    protected $psID;
    protected $seasonName;
    protected $seasonStartDate;
    protected $seasonEndDate;
    protected $seasonPrice;
    protected $status;
    protected $minNightsSeason;

    function __construct($row)
    {
        $this->psID               = $row['psID'];
        $this->seasonName         = $row['seasonName'];
        $this->seasonStartDate    = $row['seasonStartDate'];
        $this->seasonEndDate      = $row['seasonEndDate'];
        $this->seasonPrice        = $row['seasonPrice'];
        $this->status             = $row['status'];
        $this->minNightsSeason    = $row['minNightsSeason'];
    }


    /**
     * @param $pID
     * @param $seasonName
     * @param $seasonStartDate
     * @param $seasonEndDate
     * @param $seasonPrice
     * @param $seasonStatus
     * @param $minNightsSeason
     * @return $pr
     */
    public static function add($pID,$seasonName, $seasonStartDate, $seasonEndDate, $seasonPrice, $seasonStatus, $minNightsSeason)
    {
        $db    = Loader::db();
        $query = "INSERT INTO PropertySeasons(pID,seasonName,seasonStartDate,seasonEndDate,seasonPrice,status, minNightsSeason ) 
              VALUES ( ? , ? , ? , ? , ?, ?, ?  ) ";
        $ret   = $db->Execute($query, [$pID, $seasonName, $seasonStartDate, $seasonEndDate, $seasonPrice, $seasonStatus, $minNightsSeason]);
        if ($ret) {
            $pr = self::getByID($db->Insert_ID());
            return $pr;
        }

        return null;
    }

    public function update($pID,$psID,$seasonName, $seasonStartDate, $seasonEndDate, $seasonPrice, $status, $minNightsSeason)
    {
        $db    = Loader::db();
        $query = "UPDATE PropertySeasons SET pID = ?,seasonName = ?,seasonStartDate = ?,seasonEndDate = ?,seasonPrice = ?, status = ?, minNightsSeason = ? WHERE psID = ?";
        $ret   = $db->Execute($query, [$pID,$seasonName, $seasonStartDate, $seasonEndDate, $seasonPrice, $status, $minNightsSeason, $psID]);
        if ($ret) {
            return self::getByID($psID);
        }

        return null;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM PropertySeasons WHERE psID = ?";
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
        $query  = "SELECT * FROM PropertySeasons WHERE {$condition_suffix}";
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
        $query = "SELECT * FROM PropertySeasons WHERE psID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['psID'])) {
            return new PropertySeason($row);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->psID;
    }

    /**
     * @return string
     */
    public function getSeasonName()
    {
        return $this->seasonName;
    }

    /**
     * @return date
     */
    public function getSeasonStartDate()
    {
        return $this->seasonStartDate;
    }

    /**
     * @return date
     */
    public function getSeasonEndDate()
    {
        return $this->seasonEndDate;
    }

    /**
     * @return mixed
     */
    public function getSeasonPrice()
    {
        return $this->seasonPrice;
    }

    /**
     * @return int
     */
    public function getSeasonStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getMinNightsSeason()
    {
        return $this->minNightsSeason;
    }
}