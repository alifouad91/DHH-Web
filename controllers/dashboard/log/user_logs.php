<?php
defined('C5_EXECUTE') or die('Access Denied.');
class DashboardLogUserLogsController extends DashboardBaseController
{
    protected $configURL = 'dashboard/log/user_logs/';

    public function view()
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');

        $keyword = $th->sanitize($this->request('keyword'));
        $userId = $th->sanitize($this->request('userId'));

        $userLogList = new UserLogsList();

        $userList = new UserList();

        $users = $userList->get();
        /** @var  $user UserInfo */
        $userArr = [];
        $userArr[] = 'All';
        foreach ($users as $user)
        {
            $userArr[$user->getUserID()] = $user->getUserName();
        }

        $userLogList->sortByCreatedAt();

        if ($keyword) {
            $userLogList->filterByKeyword($keyword);
        }

        if ($userId) {
            $userLogList->filterByUserID($userId);
        }

        $userLogs = $userLogList->get();
        $this->set('userLogList', $userLogList);
        $this->set('userLogs', $userLogs);
        $this->set('userArr', $userArr);
        $this->set('configURL', $this->configURL);
    }

    public function clear($token = '')
    {
        $valt = Loader::helper('validation/token');
        if ($valt->validate('', $token)) {
            UserLogs::clear();
            $this->redirect($this->configURL);
        } else {
            $this->redirect($this->configURL);
        }
    }
}

?>