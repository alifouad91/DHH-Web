<?php

class UserAddressList extends DatabaseItemList
{

    protected $queryCreated;

    protected function setBaseQuery()
    {
        $this->setQuery("select * from UserAddress");

    }

    protected function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = true;
        }
    }

    public function filterByUserId($user_id)
    {
        $this->filter('uID', $user_id);
    }


    /** @return CoachMember[] */
    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $records   = parent::get($itemsToGet, $offset);
        $obj_array = [];
        foreach ($records as $row) {
            $ua          = new UserAddress($row);
            $obj_array[] = $ua;
        }
        return $obj_array;
    }
}
