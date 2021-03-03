import React from 'react';
import PropTypes from 'prop-types';
import moment from 'moment';
import { DatePicker, Form } from 'antd';
const RangePicker = DatePicker.RangePicker;

function range(start, end) {
  const result = [];
  for (let i = start; i < end; i++) {
    result.push(i);
  }
  return result;
}

function disabledDate(current) {
  // Can not select days before today and today
  return current && current < moment().endOf('day');
}

function disabledRangeTime(_, type) {
  if (type === 'start') {
    return {
      disabledHours: () => range(0, 60).splice(4, 20),
      disabledMinutes: () => range(30, 60),
      disabledSeconds: () => [55, 56],
    };
  }
  return {
    disabledHours: () => range(0, 60).splice(20, 4),
    disabledMinutes: () => range(0, 31),
    disabledSeconds: () => [55, 56],
  };
}

const RangePickerInput = ({
    form,
    name,
    label,
    required,
    placeholder,
    initialValue,
    labelSpan,
    wrapperSpan,
    extra,
    help,
    validateStatus,
    hasFeedback,
    error,
    onChange,
    format,
    hasTime,
    size,
    className,
    dropdownClassName,
}) => {
    const status = error ? 'error' : validateStatus;
    const helpMessage = error || help;
    return (
        <Form.Item
            label={label}
            labelCol={{ span: labelSpan || null }}
            wrapperCol={{ span: wrapperSpan || null }}
            validateStatus={status}
            help={helpMessage}
            extra={extra}
            hasFeedback={hasFeedback}
        >
            {form.getFieldDecorator(name, {
                initialValue,
                rules: [{ required, message: `${label} is required.` }],
            })(
              <RangePicker
                disabledDate={disabledDate}
                disabledTime={disabledRangeTime}
                placeholder={placeholder} 
                showTime={false}
                className={className}
                dropdownClassName={dropdownClassName}
                onChange={onChange}
                size={size}
                format={`DD-MM-YYYY`}
              />
            )}
        </Form.Item>
    );
};

RangePickerInput.propTypes = {
    form: PropTypes.object.isRequired,
    name: PropTypes.string.isRequired,
    label: PropTypes.string,
    size: PropTypes.string,
    onChange: PropTypes.func,
    className: PropTypes.string,
    dropdownClassName: PropTypes.string,
    placeholder: PropTypes.oneOfType([
      PropTypes.string,
      PropTypes.array,
    ]),
    initialValue: PropTypes.object,
    help: PropTypes.string,
    extra: PropTypes.string,
    validateStatus: PropTypes.string,
    error: PropTypes.string,
    required: PropTypes.bool,
    hasFeedback: PropTypes.bool,
    labelSpan: PropTypes.number,
    wrapperSpan: PropTypes.number,
    hasTime: PropTypes.bool
};

RangePickerInput.defaultProps = {
    hasTime: false
};

export default RangePickerInput;
