<?php
defined('C5_EXECUTE') or die('Access Denied.');

class ProfileController extends Concrete5_Controller_Profile
{
    public function __construct()
    {
        $u = new User();
        if (User::isLoggedIn()) {
            $profile = UserInfo::getByID($u->getUserID());
            if (!is_object($profile)) {
                throw new Exception('Invalid User ID.');
            }
        } else {
            $this->set('intro_msg', t('You must sign in order to access this page!'));
            Loader::controller('/login');
            $this->render('/login');
        }
        $this->set('profile', $profile);
        $this->set('u', $u);

    }

    public function view($task = null)
    {
        if ($task)
        {
            $task = str_replace('-','_',$task);
            $this->{$task}();
        }
        else {
            $this->set('task', 'profile');
            $this->set('t', Loader::helper('text'));
        }
    }

    public function favourites()
    {
        /** @var User $u */
        $u = $this->get('u');
        if ($u->isLandLord())
        {
            $this->redirect('profile/my-properties');
        }
        $this->set('task','favourites');
    }

    public function my_bookings()
    {
        /** @var User $u */
        $u = $this->get('u');
        if ($u->isLandLord())
        {
            $this->redirect('profile/my-properties');
        }
        $this->set('task','myBookings');
    }

    public function my_reviews()
    {
        /** @var User $u */
        $u = $this->get('u');
        if ($u->isLandLord())
        {
            $this->redirect('profile/my-properties');
        }
        $this->set('task','myReviews');
    }

    public function my_properties()
    {
        /** @var User $u */
        $u = $this->get('u');
        if (!$u->isLandLord())
        {
            $this->redirect('/profile');
        }

        $this->set('task','myProperties');
    }

    public function property_reviews()
    {
        /** @var User $u */
        $u = $this->get('u');
        if (!$u->isLandLord())
        {
            $this->redirect('/profile');
        }

        $this->set('task','propertyReviews');
    }

    public function finances()
    {
        /** @var User $u */
        $u = $this->get('u');
        if (!$u->isLandLord())
        {
            $this->redirect('/profile');
        }

        $this->set('task','finances');
    }
}
