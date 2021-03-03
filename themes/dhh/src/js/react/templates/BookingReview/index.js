import React from 'react';
import ReactDOM from 'react-dom';
import moment from 'moment';
import 'moment-timezone';

import { Spin, message, Modal } from 'antd';
import Details from './Details';
import Card from './Card';
import Timer from '../../components/Timer';
import { getBookingDetails, toggleAdditionalFacility } from '../../services';
import config from '../../config';

export default class ReviewBooking extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      propertyDetails: {},
      loading: true,
      loaded: false,
      spinning: false,
      expired: false,
      message: '',
    };
  }

  componentWillMount() {
    this.getBooking();
    moment.tz.setDefault('Asia/Dubai');
    // this.handleExtraFacilities();
  }

  getBooking = (cb) => {
    const { bookingNo } = this.props;
    getBookingDetails(
      { bookingNo, api: 'bookingReview' },
      (data) => {
        if (cb) {
          this.setState({
            spinning: false,
            loaded: true,
            propertyDetails: data,
          });
          message.success(`Facility successfully ${this.state.message}`);
        } else {
          this.setState({
            loading: false,
            loaded: true,
            propertyDetails: data,
          });
        }
        this.checkIfExpired(data);
      },
      (err) => {
        console.log(err);
        if (err.indexOf('booking_confirmed') >= 0) {
          Modal.error({
            title: 'Booking confirmed',
            content:
              'This booking has already been confirmed. You will be redirected to your bookings page after this message.',
            onOk: () => {
              location.href = `${config.BASE_URL}/profile/mybookings`;
            },
          });
        }
      }
    );
  };

  handleExtraFacilities = (e) => {
    const { bookingNo } = this.props;
    const data = new FormData();
    data.append('bookingNo', bookingNo);
    data.append('pfID', e.target.value);
    this.setState({ spinning: true });
    toggleAdditionalFacility(
      data,
      (data) => {
        this.getBooking(true);
        this.setState({ message: data.status });
      },
      (err) => {
        console.log(err);
      }
    );
  };

  getDateDiff = (propertyDetails) => {
    if (propertyDetails && propertyDetails.booking) {
      const bDate = moment(propertyDetails.booking.createdAt);
      const now = moment();
      // console.log(bDate.format(), now.format());
      let diff = now.diff(bDate, 'seconds');
      diff = config.reviewBookingTime - diff;
      return diff;
    }
  };

  checkIfExpired = (propertyDetails) => {
    if (this.getDateDiff(propertyDetails) <= 0) {
      this.showExpiredMessage();
    }
  };

  showExpiredMessage = () => {
    const { propertyDetails } = this.state;
    if (!this.state.expired) {
      this.setState({ expired: true });
      Modal.error({
        title: 'Booking has expired!',
        content:
          'Your booking has expired. You will be redirected after this message.',
        onOk: () => {
          location.href = `${config.BASE_URL}/properties/${
            propertyDetails.booking.path
          }`;
        },
      });
    }
  };

  handleTimer = (time) => {
    if (time <= 0 && !this.state.expired) {
      this.showExpiredMessage();
    }
  };

  updateData = (booking) => {
    let newDetails = this.state.propertyDetails;
    newDetails.booking = { ...newDetails.booking, ...booking };
    this.setState({ propertyDetails: newDetails });
  };

  render() {
    const { propertyDetails, loading, spinning, expired, loaded } = this.state;
    const diff = this.getDateDiff(propertyDetails);
    return (
      <Spin
        spinning={spinning || expired || loading}
        tip={
          !expired
            ? loading
              ? 'Getting Booking Details. Please Wait.'
              : `Updating Booking Details. Please Wait.`
            : `Booking Expired`
        }
      >
        {!loading ? (
          <div className='booking__timer'>
            {!loaded && !expired && diff <= 0 ? null : (
              <Timer count={diff} callBack={this.handleTimer} />
            )}
            <span className='remains'>remains</span>
            <span className='to-complete'>to complete booking</span>
          </div>
        ) : null}
        {loading ? (
          <div style={{ padding: 50 }} />
        ) : (
          <div className='row property-booking-row'>
            <Details
              propertyDetails={propertyDetails}
              loading={loading}
              handleExtraFacilities={this.handleExtraFacilities}
              updateData={this.updateData}
            />
            <Card propertyDetails={propertyDetails} loading={loading} />
          </div>
        )}
      </Spin>
    );
  }
}
const $el = $('.page__reviewbooking__render');
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const id = Number($this.data('id'));
    const bookingnum = Number($this.data('bookingnum'));
    ReactDOM.render(<ReviewBooking bookingNo={bookingnum} bID={id} />, el);
  });
}
