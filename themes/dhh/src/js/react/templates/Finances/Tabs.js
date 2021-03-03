import React from 'react';
import _ from 'lodash';
import { Tabs, Skeleton, Spin, Icon } from 'antd';
import Sort from './Sort';
import UtilityCard from '../../components/UtilityCard';
import EmptyMessage from '../../components/EmptyMessage';
import { InvoiceEmpty } from '../../icons';

const TabPane = Tabs.TabPane;

export default class FinancesTabs extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      loading: true,
      items: [],
      spinning: false,
      ascending: false,
      payment_bills: [],
      maintenance_bills: [],
      activeKey: 'payment_bills',
    };
  }

  componentWillMount() {}

  getItems = (key, refresh) => {
    this.setState({ items: this.state[key] });
  };

  componentWillReceiveProps(nextProps) {
    const { activeKey } = this.state;
    const { payment_bills, maintenance_bills } = nextProps;
    if (
      (!nextProps.loading && this.props.loading) ||
      (!nextProps.spinning && this.props.spinning)
    ) {
      this.setState({
        payment_bills,
        maintenance_bills,
        items: nextProps[activeKey],
      });
    }

    if (!this.props.spinning && nextProps.spinning) {
      this.clearSort();
    }
  }

  handleSort = key => {
    const { ascending, activeFilter, items } = this.state;

    let sorted = _.sortBy(items, property => property[key]);

    if (key === activeFilter) {
      this.setState({ ascending: !ascending });
    } else {
      this.setState({ activeFilter: key, ascending: true });
    }
    if (!this.state.ascending) {
      sorted = _.reverse(sorted);
    }
    this.setState({ items: sorted });
  };

  clearSort = () => {
    const { activeKey } = this.state;
    this.setState({ items: this.state[activeKey], activeFilter: '' });
  };

  renderSort = () => {
    const { loading, activeFilter, ascending } = this.state;
    return (
      <Sort
        activeFilter={activeFilter}
        ascending={ascending}
        loading={loading}
        handleSort={this.handleSort}
        clearSort={this.clearSort}
      />
    );
  };

  handleTabChange = e => {
    this.clearSort();
    this.props.handleTabChange(e);
    this.setState({ activeKey: e });
    this.getItems(e, true);
  };

  render() {
    const { items } = this.state;
    const { loading, spinning } = this.props;
    return (
      <div className="container-fluid">
        <div className="row">
          <div className="col-lg-offset-1 col-lg-10 pl-0 pr-0">
            <Tabs
              defaultActiveKey="payment_bills"
              animated={false}
              onChange={this.handleTabChange}
            >
              <TabPane tab="Payment Bills" key="payment_bills">
                <Spin spinning={spinning} tip="Getting Payment Invoices">
                  <Skeleton active loading={loading}>
                    {this.renderSort()}
                    {items.length ? (
                      _.map(items, (item, index) => {
                        return <UtilityCard key={index} data={item} />;
                      })
                    ) : (
                      <EmptyMessage
                        message="You have not recieved any invoices yet"
                        image={<Icon component={InvoiceEmpty} />}
                      />
                    )}
                  </Skeleton>
                </Spin>
              </TabPane>
              <TabPane tab="Maintenance" key="maintenance_bills">
                <Spin spinning={spinning} tip="Getting Maintenance Invoices">
                  {this.renderSort()}
                  {items.length ? (
                    _.map(items, (item, index) => {
                      return <UtilityCard key={index} data={item} />;
                    })
                  ) : (
                    <EmptyMessage
                      message="You have not recieved any invoices yet"
                      image={<Icon component={InvoiceEmpty} />}
                    />
                  )}
                </Spin>
              </TabPane>
            </Tabs>
          </div>
        </div>
      </div>
    );
  }
}
