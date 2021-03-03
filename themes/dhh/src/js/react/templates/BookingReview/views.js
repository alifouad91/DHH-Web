import React from 'react';
import moment from 'moment';
import _ from 'lodash';
import { Checkbox, Icon, Input, Button } from 'antd';
import ImageFit from '../../components/ImageFit';
import Rate from '../../components/Rate';
import { Cancel } from '../../icons';

export const DetailHeader = (props) => {
  const { propertyDetails } = props;
  const { booking } = propertyDetails;
  const { name, startDate, endDate } = booking;
  const sDate = moment(startDate);
  const eDate = moment(endDate);
  return (
    <div className='page__reviewbooking__details__header'>
      <h5>{name}</h5>
      <div className='dates'>
        <div className='date-wrapper'>
          <div className='date-wrapper-highlight'>
            <span>{sDate.format('MMM')}</span>
            <span>{sDate.format('DD')}</span>
          </div>
          <div>
            {sDate.format('dddd')} check-in <br />
            After 3PM
          </div>
        </div>
        <div className='date-wrapper'>
          <div className='date-wrapper-highlight'>
            <span>{eDate.format('MMM')}</span>
            <span>{eDate.format('DD')}</span>
          </div>
          <div>
            {eDate.format('dddd')} check-out <br />
            Before 12PM
          </div>
        </div>
      </div>
    </div>
  );
};

export const ExtraFacilities = (props) => {
  const { onChange, propertyDetails } = props;
  const { booking, additionalFacilities } = propertyDetails;
  const defaultProperties =
    booking && _.map(booking.bookingAdditionFacilities, 'id');
  return (
    <div className='page__reviewbooking__details__facilities'>
      <h5>Add extra facilities</h5>
      <Checkbox.Group value={defaultProperties}>
        {_.map(additionalFacilities, (facility) => {
          return (
            <Checkbox value={facility.id} key={facility.id} onChange={onChange}>
              <Icon type='sliders' theme='filled' />
              <span className='label'>{facility.value}</span>
              <span className='price'>+ {facility.price}</span>
            </Checkbox>
          );
        })}
      </Checkbox.Group>
    </div>
  );
};

export const AdditionalRequests = (props) => {
  const { onChange } = props;
  return (
    <div className='page__reviewbooking__details__requests'>
      <h5>Additional Requests</h5>
      <p>
        If you have any special requests, please leave a message below with
        details of your requests and we will try our best to accomodate them
      </p>
      <Input.TextArea
        placeholder='Your message'
        rows={6}
        style={{ height: 'auto' }}
        onChange={onChange}
      />
    </div>
  );
};

export const PropertyRules = (props) => {
  const { propertyRules } = props;
  return (
    <div className='page__reviewbooking__details__rules'>
      <h5>Property Rules</h5>
      <ul>
        {_.map(propertyRules, (rule, index) => {
          return <li key={index}>{rule}</li>;
        })}
      </ul>
    </div>
  );
};

export const CancellationPolicy = (props) => {
  const { cancellationPolicy } = props;
  return (
    <div className='page__reviewbooking__details__policy'>
      <div>
        <Icon component={Cancel} />
      </div>
      <div>
        {_.map(cancellationPolicy, (rule, index) => {
          return <p key={index}>{rule}</p>;
        })}
      </div>
    </div>
  );
};

export const Proceed = (props) => {
  return (
    <div className='page__reviewbooking__details__proceed'>
      <p className='small'>
        I agree to the{' '}
        <a href={`${location.origin}/behavior-rules`} target='_blank'>
          Rules
        </a>{' '}
        and the{' '}
        <a href={`${location.origin}/privacy-policy`} target='_blank'>
          Privacy Policy
        </a>
        . I also agree to pay the total amount shown, which includes Service
        Fees.
      </p>
      <Button
        type='primary'
        loading={props.loading}
        onClick={() => props.handleProceed(props.form)}
      >
        PROCEED TO PAYMENT
      </Button>
    </div>
  );
};

export const CardTitle = (props) => {
  const { booking } = props;

  const { thumbnail, name, location, avgRating, reviews } = booking;
  return (
    <div className='page__reviewbooking__details__card__heading'>
      <ImageFit alt={name} style={{ objectFit: 'cover' }} src={thumbnail} />
      <div>
        <h6>{name}</h6>
        <p>{location}</p>
        <div className='property__card__rating'>
          <Rate disabled value={Number(avgRating)} />
          <small>· {reviews}</small>
        </div>
      </div>
    </div>
  );
};
