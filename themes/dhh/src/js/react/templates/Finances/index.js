import React from 'react';
import ReactDOM from 'react-dom';
import { Select, Icon } from 'antd';
import Tabs from './Tabs';
import EmailModal from './EmailModal';
import {
  getBills,
  getPaymentBills,
  getMaintenanceBills,
  emailBill,
} from '../../services';

const Option = Select.Option;

class Finances extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      properties: [],
      payment_bills: [],
      maintenance_bills: [],
      selectedProperty: 'all',
      loading: true,
      activeTab: 'payment_bills',
      spinning: false,
    };
  }

  componentWillMount() {
    this.getAllBills();
  }

  getAllBills = () => {
    this.setState({ loading: true });
    getBills(
      data => {
        const { properties, payment_bills, maintenance_bills } = data;
        this.setState({
          properties,
          payment_bills,
          maintenance_bills,
          loading: false,
        });
      },
      err => {
        console.log(err);
      },
    );
  };

  getFinances = pID => {
    this.setState({ spinning: true });
    Promise.all([
      new Promise((resolve, reject) => {
        getMaintenanceBills(
          {
            pID,
          },
          data => {
            resolve(data);
          },
        );
      }),
      new Promise((resolve, reject) => {
        getPaymentBills(
          {
            pID,
          },
          data => {
            resolve(data);
          },
        );
      }),
    ]).then(data => {
      this.setState({
        maintenance_bills: data[0],
        payment_bills: data[1],
        spinning: false,
      });
    });
  };

  sendAsEmail = params => {
    // emailBill()
    console.log(params);
  };

  handleChange = e => {
    this.getFinances(e);
    this.setState({ selectedProperty: e });
  };

  handleTabChange = key => {
    this.setState({ activeTab: key });
  };

  render() {
    const {
      properties,
      selectedProperty,
      loading,
      payment_bills,
      maintenance_bills,
      spinning,
    } = this.state;

    return (
      <div className="page__section container-fluid page__finances">
        <div className="row">
          <div className="col-lg-offset-1 col-lg-8">
            <div className="container-fluid">
              <div className="row">
                <div className="col-lg-6 pl-0">
                  <div className="page__section__header">
                    <h1>Utilities Invoices</h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="col-lg-2 text-right">
            <Select
              defaultValue="all"
              suffixIcon={<Icon type="caret-down" />}
              className="w-100 "
              onChange={this.handleChange}
              disabled={loading}
            >
              <Option key="all">All Properties</Option>
              {_.map(properties, property => {
                return (
                  <Option key={property.pID} value={property.pID}>
                    {property.propertyName}
                  </Option>
                );
              })}
            </Select>
          </div>
          <div className="col-lg-12 page__finances__render">
            <Tabs
              selectedProperty={selectedProperty}
              payment_bills={payment_bills}
              maintenance_bills={maintenance_bills}
              loading={loading}
              spinning={spinning}
              handleTabChange={this.handleTabChange}
            />
          </div>
        </div>
      </div>
    );
  }
}

const $el = $('.page__finances__render');
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const id = Number($this.data('id'));
    ReactDOM.render(<Finances userId={id} />, el);
  });
}
