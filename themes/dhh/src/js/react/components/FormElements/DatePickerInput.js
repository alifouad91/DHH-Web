import React from "react";
import PropTypes from "prop-types";
import { DatePicker, Form } from "antd";

const DatePickerInput = ({
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
  style,
  disabledDate,
  disabled,
  onOpenChange,
  open
}) => {
  const status = error ? "error" : validateStatus;
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
        rules: [{ required, message: `${label} is required.` }]
      })(
        <DatePicker
          style={style}
          placeholder={placeholder}
          onOpenChange={onOpenChange}
          onChange={onChange}
          format={format}
          showTime={hasTime}
          disabled={disabled}
          disabledDate={disabledDate}
          readonly="readonly"
        />
      )}
    </Form.Item>
  );
};

DatePickerInput.propTypes = {
  form: PropTypes.object.isRequired,
  name: PropTypes.string.isRequired,
  label: PropTypes.string,
  onChange: PropTypes.func,
  placeholder: PropTypes.string,
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

DatePickerInput.defaultProps = {
  hasTime: false
};

export default DatePickerInput;
