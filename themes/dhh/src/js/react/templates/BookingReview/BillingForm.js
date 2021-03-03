import React from 'react';
import { Form, Row, Col } from 'antd';
import PhoneInput from 'react-phone-number-input';

import { TextInput, SelectInput } from '../../components/FormElements';
import { Proceed } from './views';
export default Form.create({ name: 'payment_details' })(
  ({
    form,
    handleProceed,
    submitting,
    propertyDetails: { booking },
    countries,
    handlePhoneChange,
    phoneNumber,
  }) => {
    const {
      billingDetails: {
        billing_first_name,
        billing_last_name,
        billing_phone,
        billing_email,
        billing_country,
        billing_city,
        billing_address,
      },
    } = booking;

    return (
      <div className='review-booking-form'>
        <h5>Billing Details</h5>
        <Form>
          <Row type='flex' gutter={25}>
            <Col xs={24} sm={12}>
              <TextInput
                label='First Name'
                form={form}
                name='billing_first_name'
                required={true}
                initialValue={billing_first_name}
              />
            </Col>
            <Col xs={24} sm={12}>
              <TextInput
                label='Last Name'
                form={form}
                name='billing_last_name'
                initialValue={billing_last_name}
                required={true}
              />
            </Col>
            <Col xs={24} sm={12}>
              <TextInput
                label='Email Address'
                form={form}
                isEmail={true}
                name='billing_email'
                required={true}
                initialValue={billing_email}
              />
            </Col>
            <Col xs={24} sm={12}>
              {/* <PhoneInput
								country="AE"
								placeholder="Enter phone number"
								value={billing_phone}
								onChange={(value) => handlePhoneChange(value)}
							/> */}
              <div class='ant-row ant-form-item'>
                <div class='ant-form-item-label'>
                  <label
                    for='payment_details_billing_last_name'
                    class='ant-form-item-required'
                    title='Last Name'
                  >
                    Phone Number
                  </label>
                </div>
                <div class='ant-form-item-control-wrapper'>
                  <div class='ant-form-item-control has-success'>
                    <span class='ant-form-item-children'>
                      <span class='ant-input-affix-wrapper'>
                        <PhoneInput
                          country='AE'
                          placeholder='Enter phone number'
                          value={phoneNumber}
                          onChange={(value) => handlePhoneChange(value)}
                        />
                      </span>
                    </span>
                  </div>
                </div>
              </div>

              {/* <TextInput
                label='Phone'
                form={form}
                name='billing_phone'
                required={true}
                initialValue={billing_phone}
              /> */}
            </Col>
            <Col xs={24} sm={12}>
              <TextInput
                label='Address'
                form={form}
                name='billing_address'
                required={true}
                initialValue={billing_address}
              />
            </Col>
            <Col xs={24} sm={12}>
              <TextInput
                label='City'
                form={form}
                name='billing_city'
                required={true}
                initialValue={billing_city}
              />
            </Col>
            <Col xs={24} sm={12}>
              <SelectInput
                form={form}
                label='Country'
                name='billing_country'
                list={countries}
                required={true}
                showSearch={true}
                initialValue={billing_country}
              />
              {/* <TextInput
								label="Country"
								form={form}
								name="billing_country"
								required={true}
								initialValue={billing_country}
							/> */}
            </Col>
          </Row>
        </Form>
        <Proceed
          handleProceed={handleProceed}
          loading={submitting}
          form={form}
        />
      </div>
    );
  }
);
