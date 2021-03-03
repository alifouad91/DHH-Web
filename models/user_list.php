<?php

defined('C5_EXECUTE') or die('Access Denied.');
class UserList extends Concrete5_Model_UserList {

    protected $joinUserDetails = 0;

    protected function setBaseQuery()
    {
        $this->setQuery('SELECT DISTINCT u.uID, u.uName, ud.facebookID, ud.googleID, ud.fullName FROM Users u ');
    }

    protected function createQuery()
    {
        parent::createQuery();
        if (!$this->joinUserDetails)
        {
            $this->addToQuery('LEFT JOIN UserDetails ud on ud.uID = u.uID');
            $this->joinUserDetails = 1;
        }
    }

    public function filterByKeywords($keywords)
    {
        $db             = Loader::db();
        $qkeywords      = $db->quote('%' . $keywords . '%');
        $keys           = UserAttributeKey::getSearchableIndexedList();
        $emailSearchStr = ' OR u.uEmail like ' . $qkeywords . ' ';
        $nameSearchStr = ' OR ud.fullName like ' . $qkeywords . ' ';
        $attribsStr     = '';
        foreach ($keys as $ak) {
            $cnt = $ak->getController();
            $attribsStr .= ' OR ' . $cnt->searchKeywords($keywords);
        }
        $this->filter(false, '( u.uName like ' . $qkeywords . $emailSearchStr . $attribsStr . $nameSearchStr . ')');
    }

    public function filterByUserIDs($userIds, $comparison = '=')
    {
        $this->filter('u.uID', $userIds, $comparison);
    }

    public function filterByFacebookID($facebookID, $comparison = '=')
    {
        $this->filter('ud.facebookID', $facebookID, $comparison);
    }

    public function filterByGoogleID($googleID, $comparison = '=')
    {
        $this->filter('ud.googleID', $googleID, $comparison);
    }

    public function filterByNotInGroup($groupName, $inGroup = true)
    {
        $group = Group::getByName($groupName);
        $tbl   = 'ug_' . $group->getGroupID();
        $this->addToQuery("left join UserGroups $tbl on {$tbl}.uID = u.uID ");
        if ($inGroup) {

            $this->filter(false, "{$tbl}.gID=" . intval($group->getGroupID()));
        } else {
            $this->filter(false, "{$tbl}.gID is null");
        }
    }
}
