import React from 'react';
import ReactDOM from 'react-dom';
import moment from 'moment';

import { Form, Icon, Menu, Dropdown } from 'antd';
import { DateRangePicker } from 'react-dates';

import {
  TextInput,
  SelectInput,
  RangePickerInput,
  SaveButton,
} from '../../components/FormElements';
import config from '../../config';
import { toQueryString } from '../../utils';
import { HomeBannerSearch } from '../../icons';
import { guestBanner } from '../../constants';
import { getFilters } from '../../services';
import { filter } from 'lodash';

class Banner extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      loading: false,
      startDate: null,
      endDate: null,
      filters: {},
      isMobile: $('header').hasClass('site-is-mobile'),
    };
  }

  componentDidMount() {
    getFilters(
      (filters) => {
        this.setState({ filters });
      },
      (err) => {
        console.log(err);
      }
    );
  }
  handleSubmit = (e) => {
    e.preventDefault();
    const { startDate, endDate } = this.state;
    this.props.form.validateFields((err, values) => {
      // console.log(err, values);
      if (!err) {
        this.setState({ loading: true });
        if (startDate) {
          values.startDate = moment(startDate).format(
            config.apiBookingDateFormat
          );
        }
        if (endDate) {
          values.endDate = moment(endDate).format(config.apiBookingDateFormat);
        }
        location.href = `${config.BASE_URL}/properties?${toQueryString(
          _.pickBy(values, _.identity)
        )}`;
      }
    });
  };

  handleChange = (key, val) => {
    this.setState({ [key]: val });
  };

  render() {
    const { form } = this.props;
    const { loading, isMobile, filters } = this.state;
    console.log(filters, 'filters');
    return (
      <div>
        <Form onSubmit={this.handleSubmit} layout='vertical'>
          <div className='row form form__large'>
            <div className='col-xs-12 col-sm-12 col-md-4 form__element'>
              <TextInput
                form={form}
                size='large'
                placeholder='Search by area name'
                name='keywords'
                prefix={<Icon component={HomeBannerSearch} />}
              />
            </div>
            <div className='col-xs-12 col-sm-12 col-md-4 p-0'>
              <DateRangePicker
                startDate={this.state.startDate}
                startDatePlaceholderText='Check In'
                endDatePlaceholderText='Check Out'
                startDateId='startDate'
                endDate={this.state.endDate}
                displayFormat={config.apiBookingDateFormat}
                endDateId='endDate'
                onDatesChange={({ startDate, endDate }) =>
                  this.setState({ startDate, endDate })
                }
                focusedInput={this.state.focusedInput}
                onFocusChange={(focusedInput) =>
                  this.setState({ focusedInput })
                }
                noBorder={true}
                transitionDuration={500}
                hideKeyboardShortcutsPanel={true}
                daySize={33}
                numberOfMonths={isMobile ? 1 : 2}
                readOnly={isMobile}
              />
            </div>
            <div className=' col-xs-12 col-sm-4 col-md-2 p-0 form__element'>
              <SelectInput
                form={form}
                name='guests'
                size='large'
                placeholder='Guests'
                list={guestBanner}
                initialValue={2}
              />
            </div>
            <div className='col-xs-12 col-sm-12 col-md-2 p-0 form__element'>
              <SaveButton
                saving={loading}
                savingLabel='Searching'
                disabled={loading}
                size='large'
                saveLabel='SEARCH'
              />
            </div>
          </div>
        </Form>
        {false && filters && filters.locations ? (
          <ul className='homepage-location-filter'>
            {filters.locations.map((location, index) =>
              index >= 5 ? null : (
                <li>
                  <a
                    href={`${config.BASE_URL}/properties?locations=${location}`}
                  >
                    {location}
                  </a>
                </li>
              )
            )}
            <li>
              <Dropdown
                overlay={
                  <Menu>
                    {filters.locations.map((location, index) =>
                      index >= 5 ? (
                        <Menu.Item>
                          <a
                            href={`${
                              config.BASE_URL
                            }/properties?locations=${location}`}
                          >
                            {location}
                          </a>
                        </Menu.Item>
                      ) : null
                    )}
                  </Menu>
                }
              >
                <a
                  className='ant-dropdown-link'
                  onClick={(e) => e.preventDefault()}
                >
                  More <Icon type='caret-down' />
                </a>
              </Dropdown>
            </li>
          </ul>
        ) : null}
      </div>
    );
  }
}

Banner.propTypes = {};
const BannerForm = Form.create()(Banner);

if (document.getElementById('banner-form')) {
  ReactDOM.render(<BannerForm />, document.getElementById('banner-form'));
}
