<?php

class DashboardBookingDiscountCouponsController extends DashboardBaseController
{
    protected $configURL = 'dashboard/booking/discount-coupons';

    public function view($arg = false)
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');

        /** @var DateHelper $dh */
        $dh = Loader::helper('date');

        $keywords  = $th->sanitize($this->request('keywords'));
        $startDate = $th->sanitize($this->request('startDate'));
        $endDate   = $th->sanitize($this->request('endDate'));

        $discountList = new DiscountCouponList();

        if ($keywords) {
            $discountList->filterByKeyword($keywords);
        }
        if ($startDate) {
            $startDate = $dh->date('Y-m-d', strtotime($startDate));
            $discountList->filterByStartDate($startDate);
        }
        if ($endDate) {
            $endDate = $dh->date('Y-m-d', strtotime($endDate));
            $discountList->filterByEndDate($endDate);
        }

        $discounts = $discountList->get();

        $this->set('task', 'overview');
        $this->set('discounts', $discounts);
        $this->set('dc_type_options', $this->getDcTypeArray());
        $this->set('configURL', $this->configURL);
        if ($arg) {
            switch ($arg) {
                case 'deleted':
                    $this->set('message', 'Successfully deleted!');
                    break;
            }
        }
    }

    public function detail($id, $arg2 = false)
    {
        $discount_coupon = DiscountCoupon::getByID($id);
        $groups          = Group::getAllGroupsArray();
        $this->set('task', 'detail');
        $this->set('groups', $groups);
        $this->set('discount_coupon', $discount_coupon);
        $this->set('properties', $this->getPropertiesSelect());
        $this->set('dc_properties', $this->getDcPropertiesArray($discount_coupon));
        $this->set('dc_user_groups', $this->getDcUserGroupsArray($discount_coupon));
        $this->set('dc_type_options', $this->getDcTypeArray());
        $this->set('configURL', $this->configURL);
        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Successfully updated');
                    break;
                case 'saved':
                    $this->set('message', 'Successfully saved!');
                    break;
                case 'deleted':
                    $this->set('message', 'Successfully deleted!');
                    break;
            }
        }
    }

    public function add()
    {
        $groups = Group::getAllGroupsArray();
        $this->set('task', 'add');
        $this->set('groups', $groups);
        $this->set('properties', $this->getPropertiesSelect());
        $this->set('dc_type_options', $this->getDcTypeArray());
        $this->set('configURL', $this->configURL);
    }

    public function save()
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');
        /** @var $eh ValidationErrorHelper */
        $eh = Loader::helper('validation/error');


        $name          = $th->sanitize($this->post('name'));
        $couponCode    = $th->sanitize($this->post('couponCode'));
        $type          = $th->sanitize($this->post('type'));
        $value         = $th->sanitize($this->post('value'));
        $is_date_range = $th->sanitize($this->post('is_date_range'));
        $startDate_dt  = $th->sanitize($this->post('startDate_dt'));
        $startDate_h   = $th->sanitize($this->post('startDate_h'));
        $startDate_m   = $th->sanitize($this->post('startDate_m'));

        $endDate_dt = $th->sanitize($this->post('endDate_dt'));
        $endDate_h  = $th->sanitize($this->post('endDate_h'));
        $endDate_m  = $th->sanitize($this->post('endDate_m'));
        $startDate = '';
        $endDate = '';

        $is_user_groups      = $th->sanitize($this->post('is_user_groups'));
        $groups              = $this->post('groups');
        $properties          = $this->post('properties');
        $is_properties       = $th->sanitize($this->post('is_properties'));
        $timesUsableUser     = $th->sanitize($this->post('timesUsableUser'));
        $timesUsableProperty = $th->sanitize($this->post('timesUsableProperty'));
        $active              = $th->sanitize($this->post('active'));

        if (!$name) {
            $eh->add('Please enter a Name');
        }
        if (!$couponCode) {
            $eh->add('Please enter a Coupon Code');
        } else {
            $discountCouponList = new DiscountCouponList();
            $discountCouponList->filterByCouponCode($couponCode);
            $discountCoupons = $discountCouponList->get();
            if($discountCoupons) {
                $eh->add('Coupon Code already exist in the system');
            }


        }
        if (!$value) {
            $eh->add('Please enter Value');
        }

        if ($is_date_range) {
            //date range enabled
            $startDate_dt = DateTime::createFromFormat('d-m-Y H:i', $startDate_dt . ' ' . $startDate_h . ':' . $startDate_m);

            $startDate = $startDate_dt->format('Y-m-d H:i');

            if (!$startDate) {
                $eh->add('Please enter a valid Start Date');
            }

            $endDate_dt = DateTime::createFromFormat('d-m-Y H:i', $endDate_dt . ' ' . $endDate_h . ':' . $endDate_m);
            $endDate = $endDate_dt->format('Y-m-d H:i');
            if (!$endDate) {
                $eh->add('Please enter a valid End Date');
            }

            if ($startDate >= $endDate) {
                $eh->add('End Date should be greater than Start Date');
            }

        }


        if ($is_user_groups) {
            if (!$groups) {
                $eh->add('Please select users');
            }
        } else {
            $groups = [];
        }

        if ($is_properties) {
            if (!$properties) {
                $eh->add('Please select properties');
            }
        } else {
            $properties = [];
        }

        if(!$timesUsableUser) {
            $eh->add('Please enter Usable per User');
        }

        if (!$eh->has()) {

            $discount = DiscountCoupon::add($name, $couponCode, $type, $value, $startDate, $endDate, $timesUsableUser, $timesUsableProperty, $active);
            if ($is_user_groups) {
                $discount->updateUserGroups($groups);
            }
            if ($is_properties) {
                $discount->updateProperties($properties);
            }
            $this->redirect($this->configURL . '/detail/' . $discount->getID() . '/saved');
        }

        $this->set('error', $eh);
        $this->add();
    }

    public function update()
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');
        /** @var $eh ValidationErrorHelper */
        $eh = Loader::helper('validation/error');
        /** @var DateHelper $dh */
        $dh = Loader::helper('date');


        $dcID            = $th->sanitize($this->post('discountID'));
        $discount_coupon = DiscountCoupon::getByID($dcID);
        if ($discount_coupon === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $name          = $th->sanitize($this->post('name'));
            $couponCode    = $th->sanitize($this->post('couponCode'));
            $type          = $th->sanitize($this->post('type'));
            $value         = $th->sanitize($this->post('value'));
            $is_date_range = $th->sanitize($this->post('is_date_range'));
            $startDate_dt  = $th->sanitize($this->post('startDate_dt'));
            $startDate_h   = $th->sanitize($this->post('startDate_h'));
            $startDate_m   = $th->sanitize($this->post('startDate_m'));
            $startDate_a   = $th->sanitize($this->post('startDate_a'));

            $endDate_dt = $th->sanitize($this->post('endDate_dt'));
            $endDate_h  = $th->sanitize($this->post('endDate_h'));
            $endDate_m  = $th->sanitize($this->post('endDate_m'));
            $endDate_a  = $th->sanitize($this->post('endDate_a'));
            $startDate = '0000-00-00 00:00:00';
            $endDate = '0000-00-00 00:00:00';

            $is_user_groups      = $th->sanitize($this->post('is_user_groups'));
            $groups              = $this->post('groups');
            $properties          = $this->post('properties');
            $is_properties       = $th->sanitize($this->post('is_properties'));
            $timesUsableUser     = $th->sanitize($this->post('timesUsableUser'));
            $timesUsableProperty = $th->sanitize($this->post('timesUsableProperty'));
            $active              = $th->sanitize($this->post('active'));

            if (!$name) {
                $eh->add('Please enter a name');
            }
            if (!$couponCode) {
                $eh->add('Please enter a coupon code');
            } else {
                $discountCouponList = new DiscountCouponList();
                $discountCouponList->filterByCouponCode($couponCode);
                $discountCouponList->filterByNotInID($discount_coupon->getID());
                $discountCoupons = $discountCouponList->get();
                if($discountCoupons) {
                    $eh->add('Coupon Code already exist in the system');
                }


            }
            if (!$value) {
                $eh->add('Please enter value');
            }

            if ($is_date_range) {
                $startDate_dt = $dh->date('d-m-Y H:i', strtotime($startDate_dt . ' ' . $startDate_h . ':' . $startDate_m . ' ' . $startDate_a));
                $startDate_dt = DateTime::createFromFormat('d-m-Y H:i', $startDate_dt);
                $startDate = $startDate_dt->format('Y-m-d H:i');
                if (!$startDate) {
                    $eh->add('Please enter a valid start date');
                }

                $endDate_dt = $dh->date('d-m-Y H:i', strtotime($endDate_dt . ' ' . $endDate_h . ':' . $endDate_m . ' ' . $endDate_a));
                $endDate_dt = DateTime::createFromFormat('d-m-Y H:i', $endDate_dt);
                $endDate = $endDate_dt->format('Y-m-d H:i');
                if (!$endDate) {
                    $eh->add('Please enter a valid end date');
                }

                if ($startDate >= $endDate) {
                    $eh->add('End Date should be greater than Start Date');
                }

            }

            if ($is_user_groups) {
                if (!$groups) {
                    $eh->add('Please select users');
                }
            } else {
                $groups = [];
            }

            if ($is_properties) {
                if (!$properties) {
                    $eh->add('Please select properties');
                }
            } else {
                $properties = [];
            }

            if(!$timesUsableUser) {
                $eh->add('Please enter Usable per User');
            }

            if (!$eh->has()) {

                $discount = DiscountCoupon::update($dcID, $name, $couponCode, $type, $value, $startDate, $endDate, $timesUsableUser, $timesUsableProperty, $active);
                $discount->updateUserGroups($groups);
                $discount->updateProperties($properties);

                $this->redirect($this->configURL . '/detail/' . $discount->getID() . '/updated');
            }

            $this->set('error', $eh);
            $this->detail($discount_coupon->getID());
        }
    }

    public function delete($dcID)
    {
        if (!$dcID) {
            $this->redirect($this->configURL);
        }
        $discount = DiscountCoupon::getByID($dcID);

        $discount->delete();

        $this->redirect($this->configURL . '/deleted');
    }

    /* public function update */
    public function getPropertiesSelect()
    {
        $pl         = new PropertyList();
        $properties = $pl->get();
        $arr        = [];
        foreach ($properties as $property) {
            /** @var $property Property */
            $arr[$property->getID()] = $property->getName();
        }
        return $arr;
    }

    public function getDcUserGroupsArray(DiscountCoupon $discount_coupon)
    {
        /** @var $discountCouponUserGroup DiscountCouponUserGroups */
        $arr = [];
        foreach ($discount_coupon->getDiscountCouponUserGroups() as $discountCouponUserGroup) {
            $arr[] = $discountCouponUserGroup->getUserGroupID();
        }
        return $arr;
    }

    public function getDcPropertiesArray(DiscountCoupon $discount_coupon)
    {
        /** @var $discountCouponProperty DiscountCouponProperties */
        $arr = [];
        foreach ($discount_coupon->getDiscountCouponProperties() as $discountCouponProperty) {
            $arr[] = $discountCouponProperty->getPID();
        }

        return $arr;
    }

    public function getDcTypeArray()
    {
        $arr = [DiscountCoupon::TYPE_FIXED => 'Fixed', DiscountCoupon::TYPE_PERCENT => 'Percent'];
        return $arr;
    }

}