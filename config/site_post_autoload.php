<?php
defined('C5_EXECUTE') or die('Access Denied.');

$classes = [
    //Chat Server
//    'BasicMultiRoomServer'      => ['model', 'basic_multi_room_server'],

    //User Models
    'UserAddressList'           => ['model', 'user_address_list'],
    'UserAddress'               => ['model', 'user_address'],
    'UserDetails'               => ['model', 'user/userDetails'],

    //Property Models
    'Property'                  => ['model', 'property/property'],
    'PropertyList'              => ['model', 'property/propertyList'],
    'PropertySeasonList'        => ['model', 'property/propertySeasonList'],
    'PropertySeason'            => ['model', 'property/propertySeason'],
    'PropertyBlockDatesList'    => ['model', 'property/propertyBlockDatesList'],
    'PropertyBlockDates'        => ['model', 'property/propertyBlockDates'],
    'Facility'                  => ['model', 'property/facility'],
    'FacilityList'              => ['model', 'property/facilityList'],
    'PropertyFacilities'        => ['model', 'property/propertyFacilities'],
    'AreaType'                  => ['model', 'property/areaType'],
    'AreaTypeList'              => ['model', 'property/areaTypeList'],
    'ApartmentType'             => ['model', 'property/apartmentType'],
    'ApartmentTypeList'         => ['model', 'property/apartmentTypeList'],
    'Amenity'                   => ['model', 'property/amenity'],
    'AmenityList'               => ['model', 'property/amenityList'],
    'PropertyAmenities'         => ['model', 'property/propertyAmenities'],
    'Location'                  => ['model', 'property/location'],
    'LocationList'              => ['model', 'property/locationList'],
    'Images'                    => ['model', 'property/images'],
    'PropertyImages'            => ['model', 'property/propertyImages'],
    'ApartmentArea'             => ['model', 'property/apartmentArea'],
    'ApartmentAreaList'         => ['model', 'property/apartmentAreaList'],
    'Filters'                   => ['model', 'filters'],
    'HomePageFilters'           => ['model', 'property/homePageFilters'],
    'HomePageFiltersList'       => ['model', 'property/homePageFiltersList'],
    'PropertyHomePageFilters'   => ['model', 'property/propertyHomePageFilters'],
    'IPInfoDB'   => ['model', 'IPInfoDB'],

    //CCAvenue Model
    'CCAvenuePaymentSetup'      => ['model', 'ccavenue/ccavenue'],

    //Referral Models
    'Referral'                   => ['model', 'referral/referral'],
    'ReferralList'                   => ['model', 'referral/referralList'],

    //Payment Models
    'Payment'                   => ['model', 'payment/payment'],
    'PaymentList'               => ['model', 'payment/paymentList'],

    //User Logs Models
    'UserLogs'              => ['model', 'log/userLogs'],
    'UserLogsList'        => ['model', 'log/userLogsList'],

    //Booking Models
    'Booking'                   => ['model', 'booking/booking'],
    'BookingPropertyFacilities' => ['model', 'booking/bookingPropertyFacilities'],
    'BookingList'               => ['model', 'booking/bookingList'],
    'DiscountCouponList'        => ['model', 'discount_coupons/discount_coupon_list'],
    'DiscountCoupon'            => ['model', 'discount_coupons/discount_coupon'],
    'DiscountCouponProperties'  => ['model', 'discount_coupons/discount_coupon_properties'],
    'DiscountCouponUserGroups'  => ['model', 'discount_coupons/discount_coupon_user_groups'],

    //Utility Models
    'Bill'                      => ['model', 'utility/bill'],
    'BillList'                  => ['model', 'utility/billList'],

    //Models
    'Utils'                     => ['model', 'utils'],
    'App'                       => ['model', 'app'],
    'Review'                    => ['model', 'user/review'],
    'ReviewList'                => ['model', 'user/reviewList'],
    'UserFavourite'             => ['model', 'user/favourite'],
    'UserFavouriteList'         => ['model', 'user/favouriteList'],
    'StatisticList'             => ['model', 'statisticList'],
    'Statistic'                 => ['model', 'statistic'],
    'CurrencyRates'             => ['model', 'currencyRates'],
    'JWT'                       => ['model', 'app/jwt'],
    'Notification'              => ['model', 'notification/notification'],
    'NotificationList'          => ['model', 'notification/notificationList'],
    'SMS'                       => ['model', 'SMS'],

    // Social Logins
    'Facebook'                  => ['model', 'social_media/facebook'],
    'Google'                    => ['model', 'social_media/google'],
];

Loader::registerAutoload($classes);
