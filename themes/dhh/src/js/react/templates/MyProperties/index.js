import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';
import Chart from './Chart';
import Reviews from './Reviews';
import { Occupancy, Payments, DropMenu } from './views';
import PropertyCard from '../../components/PropertyCard';

import { getMyProperties, getUserStatistics } from '../../services';
import { generateLoadingObject } from '../../utils';

class MyProperties extends React.Component {
  constructor(props) {
    super(props);
    const now = new Date().getUTCFullYear();
    let years = Array(now - 2015)
      .fill('')
      .map((v, idx) => now - idx)
      .reverse();

    years = [...years, now + 1];
    console.log(years);

    this.state = {
      activePropertyCalendar: '',
      items: generateLoadingObject(3),
      loadingStat: true,
      loading: true,
      empty: false,
      statistics: [],
      dates: years,
      activeDate: now,
      activeObject: {},
      propertyList: [],
      selectedKey: 'all',
      selectedTitle: 'All Properties',
    };
  }

  componentWillMount() {
    this.getProperties();
    this.getStats();
  }

  getProperties = () => {
    getMyProperties(
      (data) => {
        // console.log(data);
        let propertyList = [];
        _.map(data, (item) => {
          propertyList = [
            ...propertyList,
            {
              id: item.pID,
              title: item.caption,
            },
          ];
        });
        this.setState({ items: data, propertyList, loading: false });
        setTimeout(() => {
          $('.horizontal-scroll').mCustomScrollbar({
            axis: 'x',
            theme: 'dark-thin',
            scrollInertia: 100,
            advanced: { autoExpandHorizontalScroll: true },
          });
        }, 1000);
      },
      (err) => {
        console.log(err);
      }
    );
  };

  getStats = (propertyID, date) => {
    const { activeDate } = this.state;
    const year = date ? date : activeDate;
    this.setState({ loadingStat: true });
    getUserStatistics(
      { propertyID, year },
      (data) => {
        if (_.isEmpty(data)) {
          this.setState({
            empty: true,
            loadingStat: false,
            statistics: [],
            activeObject: {},
          });
        } else {
          this.setState({
            activeDate: year,
            activeObject: data[year],
            loadingStat: false,
            empty: false,
            statistics: data,
          });
        }
      },
      (err) => {
        console.log(err);
      }
    );
  };

  handleCalendarToggle = (id, e) => {
    const { activePropertyCalendar } = this.state;
    e.preventDefault();
    e.stopPropagation();
    this.setState({
      activePropertyCalendar: activePropertyCalendar === id ? '' : id,
    });
  };

  handleMenuChange = (key, item) => {
    this.getStats(key === 'all' ? null : key);
    this.setState({ selectedKey: key, selectedTitle: item.props.children });
  };

  handleDateChange = (direction, disabled) => {
    if (disabled) {
      return;
    }
    const { dates, activeDate } = this.state;
    const index = _.indexOf(dates, activeDate);
    console.log(dates, index);
    switch (direction) {
      case 'left':
        this.setState({
          activeDate: dates[index - 1],
        });
        setTimeout(() => this.getStats(), 500);
        break;
      case 'right':
        this.setState({
          activeDate: dates[index + 1],
          // activeObject: statistics[dates[index + 1]],
        });
        setTimeout(() => this.getStats(), 500);
        break;
    }
  };

  render() {
    const {
      activePropertyCalendar,
      loadingStat,
      empty,
      selectedKey,
      selectedTitle,
      propertyList,
      activeObject,
      statistics,
      activeDate,
      dates,
      loading,
      items,
    } = this.state;
    console.log(dates);
    return (
      <div className='page__section container-fluid'>
        <div className='row'>
          <div className='page__section__header'>
            <h1>My Properties</h1>
            {loading ? null : (
              <span className='sub-text'>{items.length} properties</span>
            )}
          </div>
        </div>
        <div className='row page__myproperties__list'>
          {_.map(items, (item, index) => {
            return (
              <PropertyCard
                className='my-properties'
                key={index}
                index={index}
                data={item}
                itemsToShow={3}
                special={true}
                handleCalendarToggle={this.handleCalendarToggle}
                activePropertyCalendar={activePropertyCalendar}
              />
            );
          })}
        </div>
        <div className='row statistics-title'>
          <div className='col-xs-6'>
            <h4>Statistics</h4>
          </div>
          <div className='col-xs-6 text-right'>
            <DropMenu
              selectedKey={selectedKey}
              selectedTitle={selectedTitle}
              propertyList={propertyList}
              dates={dates}
              activeDate={activeDate}
              handleMenuChange={this.handleMenuChange}
            />
          </div>
        </div>
        <div className='row charts'>
          <div className='col-xs-12 col-sm-12 col-md-8 pl-0'>
            <Chart
              activeObject={activeObject}
              empty={empty}
              loadingStat={loadingStat}
              statistics={statistics}
              activeDate={activeDate}
              dates={dates}
              handleDateChange={this.handleDateChange}
            />
          </div>
          <div className='col-xs-12 col-sm-6 col-md-2'>
            <Occupancy
              activeObject={activeObject}
              empty={empty}
              loadingStat={loadingStat}
            />
          </div>
          <div className='col-xs-12 col-sm-6 col-md-2'>
            <Payments
              activeObject={activeObject}
              empty={empty}
              loadingStat={loadingStat}
            />
          </div>
        </div>
        <div className='row'>
          <Reviews />
        </div>
      </div>
    );
  }
}

const $el = $('.page__myproperties__render');
if ($el) {
  $el.each((index, el) => {
    ReactDOM.render(<MyProperties />, el);
  });
}
