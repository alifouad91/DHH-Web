<?php
defined('C5_EXECUTE') or die("Access Denied.");

class DatabaseHelper extends Concrete5_Helper_File
{
    private $numbers = '1234567890';

    function quoteCommaSeparatedValues($string)
    {
        $db        = Loader::db();
        $values = explode(',',$string);
        if (is_array($values))
        {
            $result = [];
            foreach ($values as $value)
            {
                $result[] = $db->Quote($value);
            }
            return implode(',',$result);
        }
        return $db->Quote($string);
    }


    public function generate($table, $key, $length = 12, $lowercase = false)
    {
        $foundHash = false;
        $db        = Loader::db();
        while ($foundHash == false) {
            $string = $this->getString($length);
            if ($lowercase) {
                $string = strtolower($string);
            }
            $cnt = $db->GetOne('select count(' . $key . ') as total from ' . $table . ' where ' . $key . ' = ?', array($string));
            if ($cnt < 1) {
                $foundHash = true;
            }
        }

        return (int)$string;
    }

    public function getString($length = 12)
    {
        $str  = str_repeat($this->numbers, 10);
        $hash = substr(str_shuffle($str), 0, $length);

        return $hash;
    }
}
