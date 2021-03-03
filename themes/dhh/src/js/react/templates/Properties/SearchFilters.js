import React from 'react';
import { DateRangePicker } from 'react-dates';

import _ from 'lodash';
import moment from 'moment';
import { Form, Icon, Divider } from 'antd';
import {
  DatePickerInput,
  SliderInput,
  RadioInput,
  SaveButton,
} from '../../components/FormElements';
import CustomPicker from '../../components/RangePickerCustomized';
import {
  guests,
  bedrooms,
  bedroomsMobile,
  guestsMobile,
  duration,
  propertyType,
} from '../../constants';
import CheckDropdown from '../../components/CheckDropdown';
import config from '../../config';

class SearchFilters extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      checked: [],
      locations: props.locations.length ? props.locations : [],
      otherFilters: [],
      price: [],
      isMobile: $('header').hasClass('site-is-mobile'),
      startDate: null,
      endDate: null,
    };
  }

  handleCheckboxChange = (key, item, initial) => {
    let newItems;
    if (this.state[key].indexOf(item) === -1) {
      newItems = [...this.state[key], item];
    } else {
      newItems = _.remove(this.state[key], (itm) => {
        return item !== itm;
      });
    }
    this.setState({ [key]: newItems });

    this.handleSubmit({ [key]: newItems });
  };

  handleSliderChange = (val) => {
    this.setState({ price: val });
  };

  handleSubmit = ({
    locations = this.state.locations,
    otherFilters = this.state.otherFilters,
    key,
    value,
    force,
    e,
  }) => {
    if (e) {
      e.preventDefault();
    }
    // console.log(...rest);
    const { submitFilter } = this.props;
    const { isMobile } = this.state;
    this.setState({
      [key]: value,
    });

    if (isMobile && !force) {
      return;
    }

    setTimeout(() => {
      this.props.form.validateFields((err, values) => {
        const { startDate, endDate } = this.state;
        if (startDate) {
          values.startDate = moment(startDate).format(
            config.apiBookingDateFormat
          );
        }
        if (endDate) {
          values.endDate = moment(endDate).format(config.apiBookingDateFormat);
        }
        if (locations && locations.length) {
          values.locations = locations.join(',');
        }

        if (otherFilters && otherFilters.length) {
          values.otherFilters = otherFilters.join(',');
        }
        submitFilter(values, this.state.checked);
      });
    }, 600);
  };

  disabledEndDate = (endValue) => {
    const { startDate } = this.state;
    if (!startDate) {
      return true;
    }
    return endValue.valueOf() <= moment(startDate.valueOf()).add(1, 'days');
  };

  handleChange = (key, val) => {
    this.setState({ [key]: val });
    if (key === 'endDate') {
      this.handleSubmit({});
    }
  };

  render() {
    const {
      form,
      monthly,
      disabled,
      startDate,
      endDate,
      filters,
      filterOpen,
      openFilters,
    } = this.props;

    const { price, isMobile } = this.state;
    const { locations, otherFilters, minPrice, maxPrice } = filters;
    // console.log(locations);
    return (
      <div className={`property__filter ${filterOpen ? 'filter-open' : ''}`}>
        <div className='property__filter__header'>
          <h5>Filters</h5>
          <div
            className='page__propertydetails__forms__close'
            onClick={openFilters}
          >
            <Icon type='close' />
          </div>
        </div>
        <Form onSubmit={(e) => this.handleSubmit({ e, force: true })}>
          <div className='property__filter__formitems'>
            <DateRangePicker
              displayFormat={config.apiBookingDateFormat}
              startDate={
                this.state.startDate
                  ? this.state.startDate
                  : startDate
                  ? moment(startDate, config.apiBookingDateFormat)
                  : null
              }
              startDatePlaceholderText='Start Date'
              endDatePlaceholderText='End Date'
              startDateId='startDate'
              endDate={
                this.state.endDate
                  ? this.state.endDate
                  : startDate
                  ? moment(endDate, config.apiBookingDateFormat)
                  : null
              }
              endDateId='endDate'
              onDatesChange={({ startDate, endDate }) =>
                this.setState({ startDate, endDate })
              }
              focusedInput={this.state.focusedInput}
              onFocusChange={(focusedInput) => this.setState({ focusedInput })}
              noBorder={true}
              customArrowIcon={<Divider />}
              disabled={disabled}
              onClose={() => {
                if (this.state.startDate || this.state.endDate) {
                  this.handleSubmit({});
                }
              }}
              hideKeyboardShortcutsPanel={true}
              transitionDuration={500}
              daySize={33}
              numberOfMonths={isMobile ? 1 : 2}
              readOnly={isMobile}
            />
            {/*<CustomPicker
              className="normal"
              handleChange={this.handleChange}
              sDate={startDate ? startDate : null}
              eDate={endDate ? endDate : null}
              startPlaceHolder="Start Date"
              endPlaceHolder="End Date"
            />*/}
            {/*<DatePickerInput
              form={form}
              name="startDate"
              placeholder="Start Date"
              initialValue={
                startDate
                  ? moment(startDate, config.apiBookingDateFormat)
                  : null
              }
              onChange={e => this.handleSubmit({ key: "startDate", value: e })}
              style={{ width: 120 }}
              disabled={disabled}
              disabledDate={startValue => {
                return startValue < moment().endOf("day");
              }}
            />
            <DatePickerInput
              form={form}
              name="endDate"
              placeholder="End Date"
              initialValue={
                endDate ? moment(endDate, config.apiBookingDateFormat) : null
              }
              onChange={e => this.handleSubmit({ key: "endDate", value: e })}
              style={{ width: 120 }}
              disabled={disabled}
              disabledDate={this.disabledEndDate}
            />*/}

            {/* <RangePickerInput
            form={form}
            placeholder={["Start Date", "End Date"]}
            name="date"
            dropdownClassName="banner__calendar"
            onChange={this.handleSubmit}
          /> */}
            <div className='property__filter__slider'>
              <span>
                From{' '}
                <b>{minPrice && !price.length ? Number(minPrice) : price[0]}</b>{' '}
                to{' '}
                <b>{maxPrice && !price.length ? Number(maxPrice) : price[1]}</b>{' '}
                per night
              </span>
              <SliderInput
                form={form}
                name='price'
                step={5}
                min={minPrice ? Number(minPrice) : 100}
                max={maxPrice ? Number(maxPrice) : 1000}
                initialValue={[
                  minPrice ? Number(minPrice) : 100,
                  maxPrice ? Number(maxPrice) : 1000,
                ]}
                onChange={this.handleSliderChange}
                onAfterChange={this.handleSubmit}
                disabled={disabled}
                style={{ width: 270 }}
              />
            </div>
            {!monthly ? (
              <RadioInput
                form={form}
                initialValue={1}
                name='duration'
                items={duration}
                onChange={this.handleSubmit}
                disabled={disabled}
                label='Duration'
              />
            ) : null}
            <RadioInput
              form={form}
              name='guests'
              items={isMobile ? guestsMobile : guests}
              initialValue={this.props.guests ? Number(this.props.guests) : 1}
              onChange={this.handleSubmit}
              disabled={disabled}
              label='Guests'
            />
            {/* <RadioInput
              form={form}
              initialValue={1}
              name="apartmentType"
              items={propertyType}
              onChange={this.handleSubmit}
              disabled={disabled}
              label="Property Type"
            /> */}
            <RadioInput
              form={form}
              name='bedrooms'
              label='Bedrooms'
              items={isMobile ? bedroomsMobile : bedrooms}
              onChange={this.handleSubmit}
              disabled={disabled}
            />
          </div>
          <div className='property__filter__dropdown'>
            <CheckDropdown
              label='Close to'
              items={locations ? locations : []}
              handleChange={(item) =>
                this.handleCheckboxChange('locations', item)
              }
              disabled={disabled}
              isMobile={isMobile}
              showBadge={this.state.locations.length}
              defaultChecked={this.props.locations}
            />
            <CheckDropdown
              label='More'
              items={otherFilters ? otherFilters : []}
              disabled={disabled}
              isMobile={isMobile}
              handleChange={(item) =>
                this.handleCheckboxChange('otherFilters', item)
              }
              showBadge={this.state.otherFilters.length}
            />
          </div>
          <SaveButton
            saveLabel='APPLY FILTERS'
            savingLabel='APPLYING FILTERS'
            saving={disabled}
          />
        </Form>
      </div>
    );
  }
}

SearchFilters.propTypes = {
  // bla: PropTypes.string,
};

const FilterForm = Form.create()(SearchFilters);

export default FilterForm;
