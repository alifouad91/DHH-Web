<?php defined('C5_EXECUTE') or die('Access Denied.');
class User extends Concrete5_Model_User {

    public function getUserInfoObject()
    {

        return UserInfo::getByID($this->getUserID());

    }

    public function isLandLord()
    {
        return array_search(LANDLORD_GROUP_NAME, $this->getUserGroups(), true) !== false;
    }

    public function userGroups()
    {
        return $this->getUserGroups();
    }
}
