import React from 'react';
import { message } from 'antd';
import axios from 'axios';
import config from './config';

const { BASE_URL } = config;

let tokenInProgress = false;
// * GET, POST, DELETE, UPDATE
const initialToken = localStorage.getItem('access_token');
if (initialToken) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${initialToken}`;
}

const checkToken = new Promise((resolve, reject) => {
  axios.defaults.headers.common['token'] = document.getElementById(
    'api-token-csrf'
  ).value;
  if (localStorage.getItem('access_token')) {
    resolve(localStorage.getItem('access_token'));
  } else {
    axios.get(`${BASE_URL}/api/general/getLoggedInUser`).then((response) => {
      const { data } = response;
      if (response.data.success) {
        localStorage.setItem('access_token', data.data.token);
        axios.defaults.headers.common[
          'Authorization'
        ] = `Bearer ${localStorage.getItem('access_token')}`;
        resolve(data.data.token);
      }
      resolve(true);
    });
  }
});

const apiGet = ({ path, params, cb, err }) => {
  checkToken.then((token) => {
    axios
      .get(`${BASE_URL}${path}/`, {
        params,
        ...(typeof token === 'string'
          ? {
              headers: {
                Authorization: `Bearer ${token}`,
              },
            }
          : {}),
      })
      .then(function(response) {
        if (response.data.success && cb) {
          cb(response.data.data);
        } else {
          if (err) {
            err(response.data.errors);
          }
        }
      })
      .catch(function(error) {
        if (error && error.data && error.data.errors) {
          message.error(error.data.errors);
          if (err) {
            err(error.data.errors);
          }
        }
      });
  });
};

const apiPost = ({ path, params, cb, err }) => {
  checkToken.then(() => {
    axios
      .post(`${BASE_URL}${path}`, params)
      .then(function(response) {
        if (response.data.success) {
          cb(response.data.data);
        } else {
          err(response);
        }
      })
      .catch(function(error) {
        if (err) {
          err(error.data.errors);
        }
      });
  });
};

// * GENERAL

export const getStack = (stackName, cb, err) => {
  apiGet({
    path: '/api/general/getStack',
    params: {
      stackName,
    },
    cb,
    err,
  });
};

export const checkUserStatus = (cb, err) => {
  apiGet({
    path: '/api/general/checkStatus',
    cb,
    err,
  });
};

export const getCountries = (cb, err) => {
  apiGet({
    path: '/api/general/getCountries',
    cb,
    err,
  });
};

export const getLoggedInUser = (cb, err) => {
  apiGet({
    path: '/api/general/getLoggedInUser',
    cb,
    err,
  });
};

export const submitForm = (params, cb, err) => {
  apiPost({
    path: '/api/general/submitForm',
    params,
    cb,
    err,
  });
};

export const getCCMToken = (cb, err) => {
  apiGet({
    path: '/api/general/getCCMToken',
    cb,
    err,
  });
};

export const getHomePageItems = (params, cb, err) => {
  apiGet({
    path: '/api/general/getHomepageFilters',
    params,
    cb,
    err,
  });
};

export const getRandomGuestReviews = (cb, err) => {
  apiGet({
    path: '/api/general/getRandomGuestReviews',
    cb,
    err,
  });
};

export const getCurrencies = (cb, err) => {
  apiGet({
    path: '/api/general/getCurrencies',
    cb,
    err,
  });
};

export const setCurrency = (params, cb, err) => {
  apiPost({
    path: '/api/general/setCurrency',
    params,
    cb,
    err,
  });
};

// * BLOG APIS
export const getBlogItems = (cb, err) => {
  apiGet({
    path: '/api/general/getPageList',
    params: {
      pageHandle: 'blog_page',
    },
    cb,
    err,
  });
};

// * PROPERTY APIS
export const getFilters = (cb, err) => {
  apiGet({
    path: '/api/property/filters',
    cb,
    err,
  });
};

export const getProperties = (params, cb, err) => {
  apiGet({
    path: '/api/property',
    params,
    cb,
    err,
  });
};

export const getPropertyDetails = (id, cb, err) => {
  apiGet({
    path: '/api/property/detail',
    params: {
      propertyID: id,
    },
    cb,
    err,
  });
};

export const toggleFavourite = (params, cb, err) => {
  apiPost({
    path: '/api/property/favourite',
    params,
    cb,
    err,
  });
};

export const getDailyPrices = (params, cb, err) => {
  apiGet({
    path: '/api/property/pricePerDay',
    params,
    cb,
    err,
  });
};

export const getSubTotal = (params, cb, err) => {
  apiGet({
    path: '/api/property/getSubTotal',
    params,
    cb,
    err,
  });
};

// * BOOKING APIS

export const addBooking = (params, cb, err) => {
  apiPost({
    path: '/api/booking/add',
    params,
    cb,
    err,
  });
};

export const getBookingDetails = (params, cb, err) => {
  apiGet({
    path: '/api/booking/detail',
    params,
    cb,
    err,
  });
};

export const toggleAdditionalFacility = (params, cb, err) => {
  apiPost({
    path: '/api/booking/addAdditionalFacility',
    params,
    cb,
    err,
  });
};

export const addReview = (params, cb, err) => {
  apiPost({
    path: '/api/booking/addReview',
    params,
    cb,
    err,
  });
};

export const updateReview = (params, cb, err) => {
  apiPost({
    path: '/api/booking/updateReview',
    params,
    cb,
    err,
  });
};

export const applyCoupon = (params, cb, err) => {
  apiPost({
    path: '/api/booking/applyCoupon',
    params,
    cb,
    err,
  });
};

export const removeCouponDiscount = (params, cb, err) => {
  apiPost({
    path: '/api/booking/applyCoupon',
    params,
    cb,
    err,
  });
};

export const updateCouponDiscount = (params, cb, err) => {
  apiPost({
    path: '/api/booking/applyCoupon',
    params,
    cb,
    err,
  });
};

export const proceedPayment = (params, cb, err) => {
  apiPost({
    path: '/api/payment/paymentProcessing',
    params,
    cb,
    err,
  });
};

export const confirmPayment = (params, cb, err) => {
  apiPost({
    path: '/api/payment/paymentSuccess',
    params,
    cb,
    err,
  });
};

// * USER APIS

export const getToken = (params, cb, err) => {
  tokenInProgress = true;
  apiPost({
    path: '/api/general/getLoggedInUserFromHash',
    params,
    cb: (data) => {
      if (data.token) {
        localStorage.setItem('access_token', data.token);
      }
      if (cb) {
        cb();
      }
    },
    err: (err) => {
      console.log(err);
    },
  });
};

export const getUserDetails = (cb, err) => {
  apiGet({
    path: '/api/user',
    cb,
    err,
  });
};

export const updateUserDetails = (params, cb, err) => {
  apiPost({
    path: '/api/user/edit',
    params,
    cb,
    err,
  });
};

export const getFavourites = (params, cb, err) => {
  apiGet({
    path: '/api/user/myFavourites',
    params,
    cb,
    err,
  });
};

export const getMyBookings = (params, cb, err) => {
  apiGet({
    path: '/api/user/myBookings',
    params,
    cb,
    err,
  });
};

export const getMyReviews = (params, cb, err) => {
  apiGet({
    path: '/api/user/myReviews',
    params,
    cb,
    err,
  });
};

export const getPropertyReviews = (cb, err) => {
  apiGet({
    path: '/api/user/guestReviews',
    cb,
    err,
  });
};

export const getMyProperties = (cb, err) => {
  apiGet({
    path: '/api/user/myProperties',
    cb,
    err,
  });
};

export const getUserStatistics = (params, cb, err) => {
  apiGet({
    path: '/api/user/statistics',
    params,
    cb,
    err,
  });
};

export const setUserAvatar = (params, cb, err) => {
  apiPost({
    path: '/api/user/updateAvatar',
    params,
    cb,
    err,
  });
};

export const getNotifications = (params, cb, err) => {
  apiGet({
    path: '/api/user/notifications',
    params,
    cb,
    err,
  });
};

export const markNotificationAsRead = (params, cb, err) => {
  apiPost({
    path: '/api/user/markNotificationAsRead',
    params,
    cb,
    err,
  });
};

export const clearAllNotifications = (cb, err) => {
  apiPost({
    path: '/api/user/clearAllNotifications',
    cb,
    err,
  });
};
// * Utils API

export const getBills = (cb, err) => {
  apiGet({
    path: '/api/utility',
    cb,
    err,
  });
};

export const getPaymentBills = (params, cb, err) => {
  apiGet({
    path: '/api/utility/payment_bills',
    params,
    cb,
    err,
  });
};

export const getMaintenanceBills = (params, cb, err) => {
  apiGet({
    path: '/api/utility/maintenance_bills',
    params,
    cb,
    err,
  });
};

export const emailBill = (params, cb, err) => {
  apiPost({
    path: '/api/utility/sendAsEmail',
    params,
    cb,
    err,
  });
};

export const emailFriends = (params, cb, err) => {
  apiPost({
    path: '/api/general/sendInvite',
    params,
    cb,
    err,
  });
};
