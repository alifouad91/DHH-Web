import React from 'react';
import _ from 'lodash';
import { Icon, Skeleton } from 'antd';
import { Bar } from 'react-chartjs-2';

import EmptyMessage from '../../components/EmptyMessage';
import { ChartNavLeft, ChartNavRight, GraphEmpty } from '../../icons';

let chartOptions = {
  maintainAspectRatio: false,
  barThickness: 28,
  legend: {
    display: false,
  },
  scales: {
    xAxes: [
      {
        maxBarThickness: 28,
        ticks: {
          min: 0,
          max: 15,
          // forces step size to be 5 units
          stepSize: 3,
        },
        gridLines: {
          color: 'rgba(0, 0, 0, 0)',
          zeroLineColor: 'rgba(0, 0, 0, 0)',
          zeroLineWidth: 0,
        },
      },
    ],
    yAxes: [
      {
        display: true,
        gridLines: {
          color: '#E2E2E3',
          // color: "rgba(0, 0, 0, 0)",
          zeroLineWidth: 3,
          zeroLineColor: 'rgba(0, 0, 0, 1)',
        },
        ticks: {
          min: 0,
          max: 15,
          // forces step size to be 5 units
          stepSize: 3,
        },
      },
    ],
  },
};
export default class Chart extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      settings: {},
    };
  }

  componentWillReceiveProps(nextProps) {
    if (!nextProps.loadingStat && this.props.loadingStat) {
      this.formData();
    }
  }
  formData = () => {
    const { activeObject, statistics } = this.props;
    const { statistic } = activeObject;
    if (!statistic) {
      return;
    }
    let cSettings = {
      labels: [
        'JAN',
        'FEB',
        'MAR',
        'APR',
        'MAY',
        'JUN',
        'JUL',
        'AUG',
        'SEPT',
        'OCT',
        'NOV',
        'DEC',
      ],
      datasets: [
        {
          backgroundColor: '#FD6065',
          borderColor: '#FD6065',
          borderWidth: 1,
          hoverBackgroundColor: '#FD6065',
        },
      ],
    };

    let settings = _.map(cSettings.labels, (label, index) => {
      if (!!statistic[label]) {
        cSettings.labels[index] = `${label} • (${statistic[label].nights})`;
        return Number(statistic[label].monthlyTotal);
      } else {
        return 0;
      }
    });

    cSettings.datasets[0].data = settings;
    const statEmpty = _.isEmpty(statistic);
    const maxValue = statEmpty ? 0 : _.maxBy(settings);
    chartOptions.scales.yAxes[0].ticks.max = maxValue;
    chartOptions.scales.yAxes[0].ticks.stepSize = Math.round(maxValue / 3);
    return cSettings;
  };
  render() {
    const {
      loadingStat,
      activeObject,
      activeDate,
      dates,
      empty,
      handleDateChange,
    } = this.props;
    const disableLeft = _.indexOf(dates, activeDate) === 0;
    const disableRight = _.indexOf(dates, activeDate) === dates.length - 1;
    return (
      <div className='statistics'>
        <Skeleton loading={loadingStat}>
          {empty ? (
            <EmptyMessage
              message='Waiting for the first data'
              image={<Icon component={GraphEmpty} />}
            />
          ) : (
            <>
              <div className='heading'>
                <span className='heading__left parent__span'>
                  Earned/Expected, <span>AED</span>
                </span>
                <div className='heading__yearselect'>
                  <Icon
                    component={ChartNavLeft}
                    onClick={() => handleDateChange('left', disableLeft)}
                    className={disableLeft ? 'disabled' : ''}
                  />
                  <span>{activeDate}</span>
                  <Icon
                    component={ChartNavRight}
                    onClick={() => handleDateChange('right', disableRight)}
                    className={disableRight ? 'disabled' : ''}
                  />
                </div>
                <div className='heading__earnings'>
                  <span className='parent__span'>
                    {parseFloat(activeObject.yearlyTotal).toFixed(2)}{' '}
                    <span>earned in {activeDate}, AED</span>
                  </span>
                </div>
              </div>
              <div style={{ height: 250 }}>
                <Bar
                  data={this.formData()}
                  width={100}
                  height={50}
                  options={chartOptions}
                />
              </div>
            </>
          )}
        </Skeleton>
      </div>
    );
  }
}
