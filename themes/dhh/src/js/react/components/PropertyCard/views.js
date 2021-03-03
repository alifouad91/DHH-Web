import React from 'react';
import { Tag, Icon, Card } from 'antd';
import DayPicker from 'react-day-picker';

import ImageFit from '../ImageFit';
import Rate from '../Rate';
import { NonFavourite, Favourite, Share, CalendarIcon } from '../../icons';

export const PropertyCover = ({ thumbnail, data }) => {
  //   console.log(props);
  return (
    <ImageFit
      alt={data.title ? data.title : 'DHH'}
      // className={isFeatured ? "featured" : ""}
      style={{ objectFit: 'cover' }}
      src={thumbnail}
    />
  );
};

export const Tags = (props) => {
  const { data, special } = props;
  const { bookingStatus, monthlyPrice, weeklyPrice } = data;
  const hideMonthly = Number(monthlyPrice) === 0;
  const hideWeekly = Number(weeklyPrice) === 0;
  const booked = bookingStatus === 'Booked';
  let specialTag = '';
  if (special) {
    specialTag = (
      <Tag className={`${booked && 'booked'} ${special && 'special'}`}>
        {bookingStatus}
      </Tag>
    );
  }
  let monthlyTag = '';
  if (!hideMonthly) {
    monthlyTag = (
      <Tag className={`${booked && 'booked'} ${special && 'special'}`}>
        & Monthly
      </Tag>
    );
  }

  let weeklyTag = '';
  if (!hideWeekly) {
    weeklyTag = (
      <Tag className={`${booked && 'booked'} ${special && 'special'}`}>
        & Weekly
      </Tag>
    );
  }

  return (
    <div className={'tag-group'}>
      {monthlyTag} {specialTag} {weeklyTag}
    </div>
  );
};

export const FavIcon = (props) => {
  const { handleFavClick, showFav, id } = props;
  return (
    <Icon
      onClick={(e) => handleFavClick(id, e)}
      component={showFav ? Favourite : NonFavourite}
      className={`${showFav ? 'fav' : 'non-fav'} favourites`}
    />
  );
};

export const Details = (props) => {
  const { data } = props;
  const {
    title,
    location,
    perDayPrice,
    avgRating,
    reviews,
    vatAmount,
    dihramFee,
  } = data;
  return (
    <>
      <span className='sub-header-2'>{title}</span>
      <p className='small'>{location ? location : ' '}</p>
      <p>
        <span>From</span> <b>{perDayPrice}</b> <span>per night</span>
      </p>
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
      <div className='property__card__rating'>
        <Rate disabled value={Number(avgRating)} />
        <small>· {reviews}</small>
      </div>
    </>
  );
};

export const Calendar = (props) => {
  const { id, handleCalendarToggle, showCalendar } = props;
  return (
    <div className='property__card__calendar'>
      {/* <Icon component={Share} /> */}
      <Icon
        className={showCalendar && 'active'}
        component={CalendarIcon}
        onClick={(e) => handleCalendarToggle(id, e)}
      />
    </div>
  );
};

export const CalendarDates = (props) => {
  const { showCalendar, blockedDates, id } = props;
  const today = new Date();
  let bDates = blockedDates;
  bDates.push({ before: today });

  return (
    <div
      className={`property__card__calendardates reveal-top-right  ${
        showCalendar ? 'reveal-top-right-active' : ''
      }`}
    >
      <Card hoverable={false} bordered={false} className='hover-off'>
        <div className={`horizontal-scroll ${id}-horizontal-scroll`}>
          <DayPicker numberOfMonths={5} disabledDays={bDates} />
        </div>
      </Card>
    </div>
  );
};
