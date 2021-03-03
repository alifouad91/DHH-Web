import React from 'react';
import PropTypes from 'prop-types';
import { Input, Form } from 'antd';
// import { parsePhoneNumberFromString } from "libphonenumber-js";

const TextInput = ({
  form,
  name,
  label,
  required,
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
  prefix,
  readOnly,
  phone,
  className
}) => {
  const isWhitespace = whitespace === undefined ? true : whitespace;
  const maxLength = max || (name === '_id' ? 30 : 1000);
  const status = error ? 'error' : validateStatus;
  const helpMessage = error || help;
  const email = isEmail
    ? {
        type: 'email',
        message: 'The input is not valid E-mail!'
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
        rules: [
          { required, message: `${label ? label : 'Field'} is required.` },
          {
            whitespace: isWhitespace,
            message: `${label ? label : 'Field'} is required.`
          },
          {
            max: maxLength,
            message: `${
              label ? label : 'Field'
            } cannot exceed ${maxLength} characters`
          },
          {
            min,
            message: `${
              label ? label : 'Field'
            } must be at least ${min} characters`
          },
          { ...email }
        ]
      })(
        !rows ? (
          <Input
            placeholder={placeholder}
            maxLength={maxLength}
            className={className}
            onChange={onChange}
            disabled={disabled}
            size={size}
            prefix={prefix}
            readOnly={readOnly}
          />
        ) : (
          <Input.TextArea
            placeholder={placeholder}
            maxLength={maxLength}
            rows={rows}
            onChange={onChange}
            disabled={disabled}
            readOnly={readOnly}
            size={size}
          />
        )
      )}
    </Form.Item>
  );
};

TextInput.propTypes = {
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

export default TextInput;
