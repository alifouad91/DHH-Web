<?php
defined('C5_EXECUTE') or die('Access Denied.');

class Concrete5_Controller_Dashboard_System_Permissions extends DashboardBaseController
{
    /**
     * Dashboard view - automatically redirects to a default
     * page in the category.
     */
    public function view()
    {
        $this->redirect('/dashboard/system/permissions/site');
    }
}
