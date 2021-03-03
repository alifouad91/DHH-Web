import React from "react";
import moment from "moment";
import PropTypes from "prop-types";
import { DatePicker, Form } from "antd";
import DatePickerInput from "./DatePickerInput";

export default class DateRange extends React.Component {
  state = {
    startValue: null,
    endValue: null,
    endOpen: false
  };

  disabledStartDate = startValue => {
    const { blockedDates } = this.props;
    return blockedDates.indexOf(startValue.format("YYYY-MM-DD")) >= 0
      ? startValue
      : startValue < moment().endOf("day");
  };

  disabledEndDate = endValue => {
    const { blockedDates } = this.props;
    const { startValue } = this.state;
    if (!startValue) {
      return true;
    }
    return blockedDates.indexOf(endValue.format("YYYY-MM-DD")) >= 0
      ? endValue
      : endValue.valueOf() <= moment(startValue.valueOf()).add(1, "days");
  };

  onChange = (field, value) => {
    this.setState({
      [field]: value
    });
    this.props.onChange(field, value);
  };

  onStartChange = value => {
    this.onChange("startValue", value);
    // this.props.form.setFieldsValue({
    //   startDate: value ? moment(value.valueOf()) : null
    // });
  };

  onEndChange = value => {
    this.onChange("endValue", value);
    // this.props.form.setFieldsValue({
    //   endDate: value ? moment(value.valueOf()) : null
    // });
  };

  handleStartOpenChange = open => {
    const { startValue } = this.state;
    setTimeout(() => {
      if (!startValue) {
        return;
      }
      if (!open) {
        this.setState({ endOpen: true });
      }
    }, 400);
  };

  handleEndOpenChange = open => {
    this.setState({ endOpen: open });
  };

  render() {
    const { startValue, endValue, endOpen } = this.state;
    const {
      form,
      startLabel,
      endLabel,
      initialValue,
      required,
      startPlaceholder,
      endPlaceholder
    } = this.props;
    return (
      <div>
        <Form.Item label={startLabel}>
          {form.getFieldDecorator("bookingStartDate", {
            initialValue,
            rules: [{ required, message: `${startLabel} is required.` }]
          })(
            <DatePicker
              disabledDate={this.disabledStartDate}
              // disabledDate={val => console.log(val.format())}
              format="YYYY-MM-DD"
              // value={startValue}
              placeholder={startPlaceholder}
              onChange={this.onStartChange}
              onOpenChange={this.handleStartOpenChange}
              readonly="readonly"
            />
          )}
        </Form.Item>
        <Form.Item label={endLabel}>
          {form.getFieldDecorator("bookingEndDate", {
            initialValue,
            rules: [{ required, message: `${endLabel} is required.` }]
          })(
            <DatePicker
              disabledDate={this.disabledEndDate}
              format="YYYY-MM-DD"
              // value={endValue}
              placeholder={endPlaceholder}
              onChange={this.onEndChange}
              open={endOpen}
              onOpenChange={this.handleEndOpenChange}
              readonly="readonly"
            />
          )}
        </Form.Item>
      </div>
    );
  }
}

DateRange.defaultProps = {
  hasTime: false,
  required: true,
  startLabel: "Start Date",
  endLabel: "End Date",
  startPlaceholder: "",
  endPlaceholder: ""
};
