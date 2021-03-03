import React from 'react';
import moment from 'moment';
import _ from 'lodash';
import { Card, Icon } from 'antd';
import { CardTitle } from './views';
import config from '../../config';
import { ArrowLong } from '../../icons';

export default class PropertyCard extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const { propertyDetails, loading } = this.props;
    const { booking } = propertyDetails;
    const sDate = (booking && moment(booking.startDate)) || null;
    const eDate = (booking && moment(booking.endDate)) || null;
    const hasDiscount = (booking && Number(booking.discount) !== 0) || null;
    const perDayPrices = (booking && booking.perDayBreakdown) || null;
    const priceGroup =
      (booking && _.groupBy(booking.perDayBreakdown, 'price')) || {};
    let priceArray = [];
    if (priceGroup) {
      _.mapValues(priceGroup, (val, key) => {
        priceArray = [
          ...priceArray,
          { label: key, from: val[0].day, to: val[val.length - 1].day },
        ];
      });
    }
    // console.log(perDayPrices);
    return (
      <div className='col-lg-4'>
        <div className='container-fluid'>
          <Card
            bordered={false}
            loading={loading}
            className={`page__reviewbooking__details__card ${!loading &&
              'done'}`}
            title={booking && <CardTitle booking={booking} />}
          >
            <div className='dates'>
              <div>
                <span>{booking && sDate.format(config.dateFormat)}</span>
                <Icon component={ArrowLong} />
                <span>{booking && eDate.format(config.dateFormat)}</span>
              </div>
              <div>
                {booking && (
                  <span>
                    {/* {eDate.diff(sDate, "days") + 1} */}
                    {booking && booking.noOfDays} nights •{' '}
                    {booking && booking.guests} guests
                  </span>
                )}
              </div>
            </div>

            {booking && booking.bookingAdditionFacilities.length ? (
              <ul className='additional'>
                {_.map(booking.bookingAdditionFacilities, (facility) => {
                  return (
                    <li key={facility.id}>
                      <Icon type='sliders' theme='filled' />
                      <span className='label'>{facility.value}</span>
                      <span className='price'>+ {facility.price}</span>
                    </li>
                  );
                })}
              </ul>
            ) : null}

            {/* {booking && booking.perDayBreakdown ? (
							<ul className="additional">
								<li>
									<h6>Price per day breakdown</h6>
								</li>
								{_.map(priceArray, (item) => {
									let from = moment(item.from);
									let to = moment(item.to);
									const isSameMonth = from.format('MM') === to.format('MM');
									const isSameYear = from.format('YYYY') === to.format('YYYY');
									from = isSameYear ? from.format('MMM DD') : from.format('MMM DD, YYYY');
									to = isSameMonth ? to.format('DD, YYYY') : to.format('MMM DD, YYYY');
									return (
										<li key={item.label}>
											<span className="label">
												{from}
												{item.from !== item.to ? ` to ${to}` : null}
											</span>
											<span className="price">{item.label}</span>
										</li>
									);
								})}
							</ul>
						) : null} */}

            {perDayPrices ? (
              <ul className='additional'>
                <li>
                  <h6>Price per day breakdown</h6>
                </li>
                {_.map(perDayPrices, (item) => {
                  const day = moment(item.day, 'YYYY-MM-DD');
                  // let from = day;
                  // let to = day.add(1, 'day');
                  // console.log(day.add(1, 'day'));
                  // const isSameMonth = from.format('MM') === to.format('MM');
                  // const isSameYear = from.format('YYYY') === to.format('YYYY');
                  // from = isSameYear ? from.format('MMM DD') : from.format('MMM DD, YYYY');
                  // to = isSameMonth ? to.format('DD, YYYY') : to.format('MMM DD, YYYY');
                  return (
                    <li key={item.label}>
                      <span className='label'>
                        {day.format('DD/MM/YYYY')} -{' '}
                        {day.add(1, 'day').format('DD/MM/YYYY')}
                      </span>
                      <span className='price'>{item.price}</span>
                    </li>
                  );
                })}
              </ul>
            ) : null}

            {hasDiscount || (booking && booking.creditAmount) ? (
              <ul className='additional'>
                <li>
                  <h6>Savings</h6>
                </li>
                {hasDiscount ? (
                  <li>
                    {/* <Icon type="sliders" theme="filled" /> */}
                    <span className='label'>Coupon</span>
                    <span className='price'>- {booking.discount}</span>
                  </li>
                ) : null}
                {booking && booking.creditAmount ? (
                  <li>
                    <span className='label'>Referral Credit</span>
                    <span className='price'>
                      - {booking && booking.creditAmount}
                    </span>
                  </li>
                ) : null}
              </ul>
            ) : null}

            <ul className='additional'>
              <li>
                <h6>Other Fees</h6>
              </li>
              <li>
                <span className='label'>VAT</span>
                <span className='price'>{booking && booking.vatAmount}</span>
              </li>
              <li>
                <span className='label'>
                  Tourism Fee (x {booking && booking.noOfDays} nights)
                </span>
                <span className='price'>{booking && booking.dirhamFee}</span>
              </li>
            </ul>
            <div className='computation'>
              <div>
                <span>Total Amount</span>
                <span>{booking && booking.total}</span>
              </div>
              <div style={{ display: 'none' }}>
                <span>
                  {booking && booking.dirhamFee} Tourism fee and{' '}
                  {booking && booking.vatAmount} VAT already included
                </span>
              </div>
              {booking &&
              booking.AEDAmt &&
              document
                .getElementById('app-currency')
                .getAttribute('data-currency') !== 'ar_AE' ? (
                <div>
                  <span className='amount-in-aed'>
                    You will be paying {booking.AEDAmt} upon checkout
                  </span>
                </div>
              ) : null}
            </div>
          </Card>
        </div>
      </div>
    );
  }
}
