import React from "react";
import PropTypes from "prop-types";
import { Switch, Form } from "antd";

const SwitchInput = ({
  form,
  name,
  label,
  required = false,
  placeholder,
  initialValue,
  formItemLayout,
  whitespace,
  max,
  min,
  rows,
  extra,
  help,
  validateStatus,
  hasFeedback,
  error,
  onChange,
  isEmail,
  disabled,
  size,
  prefix
}) => {
  const isWhitespace = whitespace === undefined ? true : whitespace;
  const maxLength = max || (name === "_id" ? 30 : 1000);
  const status = error ? "error" : validateStatus;
  const helpMessage = error || help;
  const email = isEmail
    ? {
        type: "email",
        message: "The input is not valid E-mail!"
      }
    : {};
  return (
    <Form.Item
      label={label}
      validateStatus={status}
      help={helpMessage}
      extra={extra}
      hasFeedback={hasFeedback}
      {...formItemLayout}
    >
      {form.getFieldDecorator(name, {
        initialValue,
        valuePropName: "checked",
        rules: [{ required, message: `${label} is required.` }]
      })(<Switch />)}
    </Form.Item>
  );
};

SwitchInput.propTypes = {
  form: PropTypes.object.isRequired,
  name: PropTypes.string.isRequired,
  label: PropTypes.string,
  size: PropTypes.string,
  onChange: PropTypes.func,
  placeholder: PropTypes.string,
  initialValue: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  help: PropTypes.string,
  extra: PropTypes.string,
  validateStatus: PropTypes.string,
  error: PropTypes.string,
  required: PropTypes.bool,
  whitespace: PropTypes.bool,
  hasFeedback: PropTypes.bool,
  isEmail: PropTypes.bool,
  disabled: PropTypes.bool,
  max: PropTypes.number,
  min: PropTypes.number,
  rows: PropTypes.number,
  formItemLayout: PropTypes.object
};

export default SwitchInput;
