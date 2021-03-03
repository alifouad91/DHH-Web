import React from 'react';
import moment from 'moment';
import _ from 'lodash';
import { Tooltip, Card, Form, Divider, message, Spin } from 'antd';
import { DateRangePicker } from 'react-dates';
import { SelectInput, SaveButton } from '../../components/FormElements';
import { guests } from '../../constants';
import {
  stripCurrencySymbol,
  displayLoginMessage,
  displayLandlordMessage,
} from '../../utils';
import config from '../../config';
import { addBooking, getSubTotal } from '../../services';

const DayRender = ({ day, dailyPrices, perDayPrice, isDayBlocked }) => {
  const amount = _.find(dailyPrices, ['day', day.format('YYYY-MM-DD')]);
  return isDayBlocked ? (
    day.format('D')
  ) : (
    <Tooltip title={amount && amount.price ? amount.price : perDayPrice}>
      {day.format('D')}
    </Tooltip>
  );
};

class BookForm extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      numOfDays: 0,
      spinning: false,
      success: false,
      calculating: false,
      subTotalData: {},
      startValue: '',
      endValue: '',
      isLoggedIn: $('header').hasClass('logged-in'),
      isLandlord: $('header').data('group') === 'landlord',
      isMobile: $('header').hasClass('site-is-mobile'),
      startDate: null,
      endDate: null,
    };
  }

  handleSubmit = (e) => {
    e.preventDefault();
    const { uID, pID, form } = this.props;
    const { isLoggedIn, isLandlord } = this.state;

    if (!isLoggedIn) {
      displayLoginMessage();
      return;
    }
    if (isLandlord) {
      displayLandlordMessage(`book a property`);
      return;
    }
    form.validateFields((err, values) => {
      if (!err) {
        let data = new FormData();
        const { startDate, endDate } = this.state;
        data.append(
          'bookingStartDate',
          moment(startDate).format(config.apiBookingDateFormat)
        );
        data.append(
          'bookingEndDate',
          moment(endDate).format(config.apiBookingDateFormat)
        );
        data.append('uID', uID);
        data.append('pID', pID);
        data.append('noOfGuest', values.noOfGuest);
        data.append('noOfChildren', 0);
        this.setState({ spinning: true });
        // return;
        addBooking(
          data,
          (data) => {
            this.setState({ spinning: false, success: true });
            message.success(
              'Booking Successfully added. You will be redirected to review page.'
            );
            setTimeout(() => {
              location.href = `${config.BASE_URL}/booking/review/${
                data.booking.bookingNo
              }`;
            }, 2000);
          },
          (err) => {
            const { data } = err;
            this.setState({ spinning: false });
            message.error(data.errors[0]);
          }
        );
      }
    });
  };

  handleChange = (field, val) => {
    // console.log(field, val);
    this.setState({ [field]: val });
    setTimeout(() => {
      this.calculateDiff();
    }, 500);
  };

  calculateDiff = () => {
    const { startValue, endValue } = this.state;
    if (startValue && endValue) {
      this.setState({ numOfDays: endValue.diff(startValue, 'days') });
    }
  };

  isDayBlocked = (day) => {
    const { startDate } = this.state;
    const { blockedDates } = this.props.data;
    let nextBlockedDate = null;
    _.forEach(blockedDates, (date) => {
      const currentDate = moment(date);
      if (currentDate.isAfter(startDate)) {
        nextBlockedDate = currentDate;
        return false;
      }
    });

    const today = moment();
    const diff = today.diff(day, 'day');
    const endDate = today.add('5', 'months').format('YYYY-MM-DD');
    return (
      blockedDates.indexOf(day.format('YYYY-MM-DD')) >= 0 ||
      diff === 0 ||
      (startDate && day.isAfter(nextBlockedDate)) ||
      (startDate && day.isAfter(endDate)) ||
      day.isAfter(endDate)
    );
  };

  calculateTotal = () => {
    const { startDate, endDate } = this.state;
    this.setState({
      calculating: true,
    });
    getSubTotal(
      {
        propertyID: this.props.pID,
        startDate: startDate.format('YYYY-MM-DD'),
        endDate: endDate.format('YYYY-MM-DD'),
        locale: document
          .getElementById('app-currency')
          .getAttribute('data-currency'),
      },
      (data) => {
        this.setState({
          calculating: false,
          subTotalData: data,
        });
      },
      (err) => {
        console.log('err', err);
        message.error(err[0]);
        this.setState({
          calculating: false,
          subTotalData: {},
        });
      }
    );
  };

  render() {
    const {
      form,
      perDayPrice,
      dailyPrices,
      data: { vatAmount, dihramFee, monthlyPrice, minNights },
    } = this.props;
    const {
      numOfDays,
      spinning,
      success,
      isMobile,
      startDate,
      endDate,
      subTotalData,
      calculating,
    } = this.state;
    const hideMonthly = Number(monthlyPrice) === 0;
    return (
      // <Affix offsetTop={140}>
      <Card className='card__book' bordered={false}>
        <Spin spinning={spinning || calculating}>
          <h4>Book this apartment</h4>
          <p>From {perDayPrice} per night</p>
          <p>
            <span
              style={{
                fontSize: 10,
                opacity: 0.4,
              }}
            >
              + {vatAmount} VAT & {dihramFee} Tourism Fee
            </span>
          </p>
          <p>
            <span
              style={{
                fontSize: 10,
                opacity: 0.4,
              }}
            >
              * Minimum {minNights} Nights Stay
            </span>
          </p>
          <Form onSubmit={this.handleSubmit} layout='vertical'>
            {startDate || endDate ? (
              <span
                className='clear-dates'
                onClick={() =>
                  this.setState({
                    startDate: null,
                    endDate: null,
                    numOfDays: 0,
                    subTotalData: {},
                  })
                }
              >
                Clear Dates
              </span>
            ) : null}

            <DateRangePicker
              startDate={this.state.startDate}
              minimumNights={Number(minNights)}
              startDatePlaceholderText='Start Date'
              endDatePlaceholderText='End Date'
              startDateId='startDate'
              endDate={this.state.endDate}
              displayFormat='DD.MM.YYYY'
              endDateId='endDate'
              onDatesChange={({ startDate, endDate }) => {
                if (endDate) {
                  this.setState(
                    {
                      numOfDays: endDate.diff(startDate, 'day'),
                      endDate,
                    },
                    this.calculateTotal
                  );
                  // console.log(endDate.diff(startDate, 'day'), endDate);
                }
                this.setState({ startDate, endDate });
              }}
              focusedInput={this.state.focusedInput}
              onFocusChange={(focusedInput) => this.setState({ focusedInput })}
              noBorder={true}
              customArrowIcon={<Divider type='vertical' />}
              transitionDuration={500}
              hideKeyboardShortcutsPanel={true}
              required={true}
              isDayBlocked={this.isDayBlocked}
              daySize={33}
              numberOfMonths={isMobile ? 1 : 2}
              readOnly={isMobile}
              renderDayContents={(day) => (
                <DayRender
                  isDayBlocked={this.isDayBlocked(day)}
                  day={day}
                  dailyPrices={dailyPrices}
                  perDayPrice={perDayPrice}
                />
              )}
              showClearDate={true}
              isOutsideRange={(date) =>
                date.isBefore(moment(), 'day') ||
                date.isAfter(moment().add(5, 'months'), 'day')
              }
            />
            <SelectInput
              form={form}
              name='noOfGuest'
              placeholder='Guests'
              initialValue={1}
              list={guests}
            />
            {numOfDays && subTotalData.subtotal ? (
              <>
                <div className='computation'>
                  <span>{numOfDays} nights</span>
                  <span>{subTotalData.subtotal}</span>
                </div>
                <ul className='additional-details'>
                  <li>
                    <h6>Price Breakdown</h6>
                  </li>
                  <li>
                    <span className='label'>Subtotal</span>
                    <span className='price'>{subTotalData.subtotal}</span>
                  </li>
                  {subTotalData.creditAmount ? (
                    <li>
                      <span className='label'>Referral Credit</span>
                      <span className='price'>
                        - {subTotalData.creditAmount}
                      </span>
                    </li>
                  ) : null}
                  <li>
                    <span className='label'>VAT</span>
                    <span className='price'>{subTotalData.VAT}</span>
                  </li>
                  <li>
                    <span className='label'>
                      Tourism Fee (x {subTotalData.noOfDays} nights)
                    </span>
                    <span className='price'>{subTotalData.tourismFee}</span>
                  </li>

                  <li>
                    <span className='label'>Total Price</span>
                    <span className='price'>{subTotalData.total}</span>
                  </li>
                </ul>
                <p className='small' style={{ margin: 0 }} />
                <Divider />
                <p className='small'>
                  {numOfDays >= 30
                    ? 'Properties are not allowed to be booked for 30 days or more.'
                    : ''}{' '}
                  {!hideMonthly && numOfDays >= 30
                    ? 'Please use the monthly booking form below.'
                    : ''}
                </p>
              </>
            ) : null}
            <SaveButton
              saving={spinning}
              savingLabel='BOOKING PROPERTY'
              saveLabel={success ? 'BOOKED' : 'BOOK'}
              disabled={success || numOfDays >= 30}
            />
          </Form>
        </Spin>
      </Card>
      // </Affix>
    );
  }
}
const BookingForm = Form.create()(BookForm);
export default BookingForm;
