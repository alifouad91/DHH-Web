import React from 'react';
import { Form, Divider, Skeleton, message } from 'antd';
import PhoneInput, { isValidPhoneNumber } from 'react-phone-number-input';

import { TextInput } from '../../components/FormElements';
import {
  DetailHeader,
  ExtraFacilities,
  AdditionalRequests,
  PropertyRules,
  CancellationPolicy,
  Proceed,
} from './views';
import BillingForm from './BillingForm';
import DiscountForm from './DiscountForm';
import { proceedPayment, getCountries } from '../../services';
import { objectToFormData } from '../../utils';

class Details extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      facilities: [],
      additionalRequests: '',
      submitting: false,
      showIframe: false,
      iframeUrl: '',
      countries: [],
      phone: null,
      phoneIsValid: false,
    };
  }

  componentWillMount() {
    const { propertyDetails } = this.props;
    this.setState({
      phone:
        propertyDetails.booking &&
        propertyDetails.booking.billingDetails.billing_phone
          ? propertyDetails.booking.billingDetails.billing_phone
          : null,
      phoneIsValid:
        propertyDetails.booking &&
        propertyDetails.booking.billingDetails.billing_phone
          ? isValidPhoneNumber(
              propertyDetails.booking.billingDetails.billing_phone
            )
          : false,
    });
    getCountries((data) => {
      this.setState({ countries: data });
    });
  }

  handlePhoneChange = (phone) => {
    this.setState({ phone, phoneIsValid: isValidPhoneNumber(phone) });
  };

  handleCheck = (checkedValues) => {
    this.setState({ facilities: checkedValues });
  };

  handleChange = (e) => {
    this.setState({ additionalRequests: e.target.value });
  };

  handleProceed = (form) => {
    const { propertyDetails } = this.props;
    let obj = {
      bookingNo: propertyDetails.booking.bookingNo,
      additionalRequests: this.state.additionalRequests,
    };
    form.validateFields((errors, values) => {
      if (!errors) {
        obj = { ...obj, ...values };
        if (obj.billing_email) {
          obj.billing_email = encodeURIComponent(obj.billing_email);
        }
        if (!this.state.phoneIsValid) {
          message.error(
            `Please enter a valid phone number. Phone numbers should start with + or 0.`
          );
          return;
        }
        obj.billing_phone = this.state.phone;
        this.setState({ submitting: true });
        proceedPayment(
          objectToFormData(obj),
          (response) => {
            if (response) {
              // Modal.success({
              //   title: 'Success!',
              //   content:
              //     'You successfully booked the property. Close this popup to see the confirmation',
              //   onOk: () => {
              //     location.href = `${config.BASE_URL}/booking/confirm/${response.booking.bookingNo}`;
              //   },
              // });
              $('.wrapper').append(`<iframe
								src="${response.production_url}"
								id="paymentFrame"
								frameborder="0"
								scrolling="No"
							></iframe>`);
            }
            this.setState({ submitting: false });
          },
          (err) => {
            console.log(err);
          }
        );
      }
    });
  };

  render() {
    const { submitting, countries } = this.state;
    const {
      propertyDetails,
      loading,
      handleExtraFacilities,
      form,
      pID,
      bookingNo,
      updateData,
    } = this.props;
    return (
      <>
        <div className='col-lg-offset-1 col-lg-6'>
          <div className=''>
            <Skeleton active loading={loading}>
              <DetailHeader propertyDetails={propertyDetails} />
            </Skeleton>
            <Divider />
            <Skeleton active loading={loading}>
              <ExtraFacilities
                propertyDetails={propertyDetails}
                onChange={handleExtraFacilities}
                loading={loading}
              />
            </Skeleton>
            <Divider />
            <Skeleton active loading={loading}>
              <AdditionalRequests
                onChange={this.handleChange}
                loading={loading}
              />
            </Skeleton>
            <Divider />
            <Skeleton active loading={loading}>
              <PropertyRules
                propertyRules={
                  propertyDetails &&
                  propertyDetails.booking &&
                  propertyDetails.booking.propertyRules
                }
              />
            </Skeleton>
            <Divider />
            <Skeleton active loading={loading}>
              <CancellationPolicy
                cancellationPolicy={
                  propertyDetails &&
                  propertyDetails.booking &&
                  propertyDetails.booking.cancellationPolicy
                }
              />
            </Skeleton>
            <Divider />
            <Skeleton active loading={loading}>
              <DiscountForm
                updateData={updateData}
                propertyDetails={propertyDetails}
              />
            </Skeleton>
            <Divider />
            <Skeleton active loading={loading}>
              <BillingForm
                handleProceed={this.handleProceed}
                loading={submitting}
                countries={countries}
                propertyDetails={propertyDetails}
                handlePhoneChange={this.handlePhoneChange}
                phoneNumber={this.state.phone}
              />
            </Skeleton>
          </div>
        </div>
      </>
    );
  }
}

export default Details;
