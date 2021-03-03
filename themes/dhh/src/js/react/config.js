const isDev = location.hostname === 'localhost';
const BASE_URL = isDev ? 'http://localhost/driven-holiday-homes/index.php' : '';

export default {
  blogDateFormat: 'MMM YYYY',
  dateFormat: 'DD.MM.YYYY',
  apiBookingDateFormat: 'DD-MM-YYYY',
  BASE_URL,
  GOOGLE_MAPS_KEY: 'AIzaSyBI3hIe9rQR3cUGfPgZUl6TUcG0Ox4tp18',
  reviewBookingTime: 600, //In seconds
  maxGallerySliderThumbs: 3,
  defaultThumb:
    'https://gw.alipayobjects.com/zos/rmsportal/JiqGstEfoWAOHiTxclqi.png',
  ratingTooltips: [
    'Very Poor Experience',
    'Poor Experience',
    'Satisfied',
    'Good Service',
    'Excellent Service',
  ],
  amendBookingFields: {
    comment: 'comment-14',
    propertyName: 'property-name-36',
    bookingDate: 'booking-date-37',
    bookingEndDate: 'booking-end-date-40',
    name: 'name-41',
    email: 'email-42',
    phone: 'phone-number-43',
    formID: 4,
    bID: 121,
  },
  cancelBookingFields: {
    comment: 'comment-15',
    propertyName: 'property-name-38',
    bookingDate: 'booking-date-39',
    bookingEndDate: 'booking-end-date-44',
    name: 'name-45',
    email: 'email-46',
    phone: 'phone-47',
    formID: 5,
    bID: 125,
  },
  formValues: {
    cID: 182,
    resolution: '2560x1440',
    action: 'submit',
  },
  notificationInterval: 60000,
};
