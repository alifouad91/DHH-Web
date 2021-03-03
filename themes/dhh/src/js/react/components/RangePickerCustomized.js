import React from "react";
import moment from "moment";
import DayPickerInput from "react-day-picker/DayPickerInput";
import "react-day-picker/lib/style.css";

import { formatDate, parseDate } from "react-day-picker/moment";
import config from "../config";

export default class RangePicker extends React.Component {
  constructor(props) {
    super(props);
    this.handleFromChange = this.handleFromChange.bind(this);
    this.handleToChange = this.handleToChange.bind(this);
    this.state = {
      from: undefined,
      to: undefined
    };
  }

  componentWillMount() {
    const { sDate, eDate } = this.props;
    console.log(sDate, eDate);
    if(sDate && eDate) {
      const s = sDate.split('-')
      const e = eDate.split('-')
      this.setState({
        from: new Date(s[2], s[1] - 1, s[0]),
        to: new Date(e[2], e[1] - 1, e[0])
      })
    }
  }
  showFromMonth() {
    const { from, to } = this.state;
    if (!from) {
      return;
    }
    if (moment(to).diff(moment(from), "months") < 2) {
      this.to.getDayPicker().showMonth(from);
    }
  }
  handleFromChange(from) {
    const { to } = this.state;
    this.props.handleChange("startDate", moment(from).format(config.apiBookingDateFormat));
    this.setState({ from });
  }
  handleToChange(to) {
    this.props.handleChange("endDate", moment(to).format(config.apiBookingDateFormat));
    this.setState({ to }, this.showFromMonth);
  }
  render() {
    const { startPlaceHolder, endPlaceHolder, sDate, eDate, className } = this.props;
    const { from, to } = this.state;
    const modifiers = { start: from, end: to, disabled:  true };
    const today = new Date();
    return (
      <>
        <div className={`col-xs-6 col-sm-4 col-md-2 pr-0 form__element range__picker ${className}`}>
          <DayPickerInput
            value={from ? from : sDate}
            placeholder={startPlaceHolder ? startPlaceHolder : "Check In"}
            format={config.apiBookingDateFormat}
            formatDate={formatDate}
            parseDate={parseDate}
            dayPickerProps={{
              selectedDays: [from, { from, to }],
              disabledDays: { before: today, after: to },
              toMonth: to,
              modifiers,
              numberOfMonths: 1,
              onDayClick: () => this.to.getInput().focus()
            }}
            onDayChange={this.handleFromChange}
          />
        </div>
        <div className={`col-xs-6 col-sm-4 col-md-2 pr-0 form__element range__picker ${className} end`}>
          <DayPickerInput
            ref={el => (this.to = el)}
            value={to ? to : eDate}
            placeholder={endPlaceHolder ? endPlaceHolder : "Check Out"}
            format={config.apiBookingDateFormat}
            formatDate={formatDate}
            parseDate={parseDate}
            dayPickerProps={{
              selectedDays: [from, { from, to }],
              disabledDays: [from, { before: from }],
              modifiers,
              month: from,
              fromMonth: from,
              numberOfMonths: 1
            }}
            onDayChange={this.handleToChange}
          />
        </div>
      </>
    );
  }
}
