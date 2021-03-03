<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class Referral
{
    protected $rID;
    protected $uID;
    protected $referrerEmail;
    protected $referredEmail;
    protected $creditSent;

    function __construct($row)
    {
        $this->rID                 = $row['rID'];
        $this->uID                = $row['uID'];
        $this->referrerEmail             = $row['referrerEmail'];
        $this->referredEmail         = $row['referredEmail'];
        $this->creditSent         = $row['$creditSent'];
    }

    /**
     * @param $uID
     * @param $referredEmail
     * @return $r
     */
    public static function add($uID,$referredEmail)
    {
        $u = UserInfo::getByID($uID);
        $referrerEmail = $u->getUserEmail();

        $db    = Loader::db();

        $query = "INSERT INTO Referral(uID,referrerEmail,referredEmail ) 
              VALUES ( ? , ?, ? ) ";
        $ret   = $db->Execute($query, [$uID, $referrerEmail, $referredEmail]);
        if ($ret) {
            $r = self::getByID($db->Insert_ID());
            return $r;
        }

        return null;
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
        $query  = "SELECT * FROM Referral WHERE {$condition_suffix}";
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
        $query = "SELECT * FROM Referral WHERE rID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['rID'])) {
            return new Referral($row);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->rID;
    }

    /**
     * @return mixed
     */
    public function getUID()
    {
        return $this->uID;
    }

    /**
     * @return mixed
     */
    public function getReferrerEmail()
    {
        return $this->referrerEmail;
    }

    /**
     * @return mixed
     */
    public function getReferredEmail()
    {
        return $this->referredEmail;
    }

    /**
     * @return mixed
     */
    public function getCreditSent()
    {
        return $this->creditSent;
    }

    public static function update($rID)
    {
        $db    = Loader::db();
        $query = "UPDATE Referral SET creditSent = 'YES' WHERE rID = ?";
        $ret   = $db->Execute($query, [$rID]);

        return null;
    }

}