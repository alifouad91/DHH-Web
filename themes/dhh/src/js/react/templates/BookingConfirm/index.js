import React from 'react';
import ReactDOM from 'react-dom';
import { Button } from 'antd';
import Card from './Card';
import { getBookingDetails, confirmPayment } from '../../services';
import { objectToFormData } from '../../utils';

import config from '../../config';

class BookingConfirm extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      propertyDetails: {},
      loading: true
    };
  }
  componentWillMount() {
    this.getBooking();
    // this.confirm();
  }
  confirm = () => {
    const { bookingNo } = this.props;
    confirmPayment(
      objectToFormData({ bookingNo }),
      data => {
        console.log(data);
      },
      err => {
        console.log(err);
      }
    );
  };
  getBooking = () => {
    const { bookingNo } = this.props;
    getBookingDetails(
      { bookingNo },
      data => {
        this.setState({ loading: false, propertyDetails: data });
      },
      err => {
        console.log(err);
      }
    );
  };

  goToUrl = url => {
    location.href = `${config.BASE_URL}${url}`;
  };

  render() {
    const { propertyDetails, loading } = this.state;
    return (
      <div className='page__confirmbooking__content'>
        <h4>{loading ? 'Confirming your Booking' : 'Booking Confirmed'}</h4>
        <p>
          We're glad you've chosen to stay with us. Please check your email for
          confirmation of this booking
        </p>
        <Card propertyDetails={propertyDetails} loading={loading} />
        <div className='buttons'>
          <Button type='secondary' onClick={() => this.goToUrl('/')}>
            GO TO HOMEPAGE
          </Button>
          <Button
            type='primary'
            onClick={() => this.goToUrl('/profile/mybookings')}
          >
            VIEW MY BOOKINGS
          </Button>
        </div>
      </div>
    );
  }
}

const $el = $('.page__confirmbooking__render');
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const id = Number($this.data('id'));
    const bookingnum = Number($this.data('bookingnum'));
    ReactDOM.render(<BookingConfirm bookingNo={bookingnum} bID={id} />, el);
  });
}
