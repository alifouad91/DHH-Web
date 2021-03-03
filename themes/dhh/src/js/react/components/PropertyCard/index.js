import React from 'react';
import PropTypes from 'prop-types';
import { Card, Skeleton, Spin } from 'antd';
import { Reveal, Fade } from 'react-reveal';

import {
  PropertyCover,
  Tags,
  FavIcon,
  Details,
  Calendar,
  CalendarDates,
} from './views';
import { toggleFavourite } from '../../services';
import config from '../../config';
import { displayLoginMessage, displayLandlordMessage } from '../../utils';

export default class PropertyCard extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isFav: false,
      spinning: false,
      changed: false,
      isLoggedIn: $('header').hasClass('logged-in'),
      isMobile: $('header').hasClass('site-is-mobile'),
      isLandlord: $('header').data('group') === 'landlord',
    };
  }

  handleFavClick = (id, e) => {
    const { userID, favHandle } = this.props;
    const { isLoggedIn, isLandlord } = this.state;
    e.stopPropagation();
    if (!isLoggedIn) {
      displayLoginMessage();
      return;
    }
    if (isLandlord) {
      displayLandlordMessage(`add favourites`);
      return;
    }
    // console.log("clicked", id, userID);
    let data = new FormData();
    // data.append("userID", userID);
    data.append('propertyID', id);
    this.setState({ spinning: true });
    toggleFavourite(
      data,
      (data) => {
        this.setState({
          isFav: data.favourited,
          spinning: false,
          changed: true,
        });
        if (favHandle) {
          favHandle(true);
        }
      },
      (err) => {
        console.log(err);
      }
    );
  };

  handleItemClick = (url) => {
    const { special } = this.props;
    // if (special) {
    //   return;
    // }
    location.href = `${config.BASE_URL}/properties/${url}`;
  };

  getDates = (dates) => {
    return _.map(dates, (date) => {
      return new Date(date);
    });
  };

  render() {
    const { isFav, spinning, changed, isMobile } = this.state;
    const {
      itemsToShow,
      data,
      index,
      special,
      className,
      handleCalendarToggle,
      activePropertyCalendar,
    } = this.props;
    const { thumbnail, isLoading, path, isFavorite, pID } = data;
    const isFeatured = itemsToShow < 4;
    let grid = `col-xs-12 col-sm-6 col-md-${12 / itemsToShow} col-lg-${12 /
      itemsToShow} col-xlg-${12 / itemsToShow} col-fhd-${12 / itemsToShow - 1}`;
    const showFav = changed ? isFav : isFavorite;
    const showCalendar = Number(activePropertyCalendar) === Number(pID);

    return (
      <div
        className={`${grid} property__card ${className} ${
          showCalendar ? 'show-calendar' : ''
        }`}
        onClick={this.handleItemClick.bind(this, path)}
      >
        {special && (
          <CalendarDates
            showCalendar={showCalendar}
            blockedDates={this.getDates(data.blockedDates)}
          />
        )}
        {isLoading ? (
          <Skeleton active loading={isLoading} paragraph={{ rows: 2 }} />
        ) : (
          <Fade>
            <Spin spinning={spinning}>
              <Card
                bordered={false}
                hoverable={false}
                className={special && 'special'}
                cover={<PropertyCover thumbnail={thumbnail} data={data} />}
              >
                <Tags data={data} special={special} />
                {!special && (
                  <FavIcon
                    handleFavClick={this.handleFavClick}
                    showFav={showFav}
                    id={pID}
                  />
                )}
                <Details data={data} />
                {special && (
                  <Calendar
                    id={pID}
                    handleCalendarToggle={handleCalendarToggle}
                    showCalendar={showCalendar}
                  />
                )}
              </Card>
            </Spin>
          </Fade>
        )}
      </div>
    );
  }
}

PropertyCard.propTypes = {
  data: PropTypes.object.isRequired,
  itemsToShow: PropTypes.number,
};

PropertyCard.defaultProps = {
  itemsToShow: 4,
};
