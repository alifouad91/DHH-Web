import React, { useState } from 'react';
import { Form, Input, Button } from 'antd';
import { applyCoupon } from '../../services';
import { objectToFormData } from '../../utils';

class DiscountForm extends React.Component {
  constructor(props) {
    super(props);
    const {
      propertyDetails: { booking },
    } = props;
    const hasDiscount = (booking && Number(booking.discount) !== 0) || null;

    this.state = {
      loading: false,
      status: hasDiscount ? 'valid' : 'none',
      couponCode: '',
      message: '',
    };
  }

  handleSubmit = (e) => {
    const { status, couponCode } = this.state;
    const {
      propertyDetails: { booking },
    } = this.props;
    const { bookingNo, pID } = booking;
    const obj = {
      bookingNo,
      pID,
      couponCode,
    };
    this.setState({ loading: true });
    switch (status) {
      case 'none':
      case 'not-applicable': {
        if (!couponCode) {
          return;
        }
        applyCoupon(
          objectToFormData(obj),
          this.handleCallback,
          this.handleError
        );
        break;
      }
      case 'valid':
        applyCoupon(
          objectToFormData({ bookingNo, pID, couponCode: '' }),
          this.handleCallback,
          this.handleError
        );
        break;
    }
  };

  handleError = (err) => {
    console.log(err);
  };

  handleCallback = (data) => {
    const { updateData } = this.props;
    const { status } = data;
    let message = '';
    switch (status) {
      case 'not-applicable':
        message = 'Coupon not applicable';
        break;
      case 'none':
        updateData(data.booking);
        break;
      case 'valid': {
        updateData(data.booking);
        break;
      }
    }
    this.setState({ loading: false, message, status });
  };

  onChange = (e) => {
    this.setState({
      couponCode: e.target.value,
    });
  };

  handleUpdateClick = () => {
    this.setState({ status: 'none', message: '', couponCode: '' });
  };

  handleCancelClick = () => {
    this.setState({ status: 'valid', message: '', couponCode: '' });
  };

  render() {
    const { couponCode, loading, message, status } = this.state;
    const {
      propertyDetails: { booking },
    } = this.props;
    const { discount } = booking;
    const hasDiscount = (booking && Number(booking.discount) !== 0) || null;

    return (
      <div className='review-booking-form discount-booking-section'>
        <h5>Coupon and Promotions</h5>
        <div className={`discount-form ${status}`}>
          {(() => {
            switch (status) {
              case 'none':
              case 'not-applicable':
                return (
                  <>
                    <Input
                      placeholder='Enter Coupon Code'
                      onChange={this.onChange}
                      onPressEnter={this.handleSubmit}
                      value={couponCode}
                    />
                    <div className='action-button'>
                      <Button
                        disabled={!couponCode.length}
                        type='primary'
                        loading={loading}
                        onClick={this.handleSubmit}
                      >
                        Apply
                      </Button>
                      {/* {hasDiscount } */}
                      <Button
                        disabled={!hasDiscount}
                        type='secondary'
                        onClick={this.handleCancelClick}
                      >
                        Cancel
                      </Button>
                    </div>
                    {message ? <p>{message}</p> : null}
                  </>
                );
              case 'valid':
                return (
                  <>
                    <p>You got a discount of {discount}</p>
                    <div className='action-button'>
                      <Button
                        type='primary'
                        disabled={loading}
                        onClick={this.handleUpdateClick}
                      >
                        Update
                      </Button>
                      <Button
                        onClick={this.handleSubmit}
                        type='secondary'
                        loading={loading}
                      >
                        Remove
                      </Button>
                    </div>
                  </>
                );
              default:
                return null;
            }
          })()}
        </div>
      </div>
    );
  }
}

export default Form.create({ name: 'discount' })(DiscountForm);
