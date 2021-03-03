import React from "react";
import PropTypes from "prop-types";
import { Input, Form } from "antd";

const TextInputMultiple = ({
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
  type,
  className
}) => {
  const isWhitespace = whitespace === undefined ? true : whitespace;
  const maxLength = max || (name === "_id" ? 30 : 256);
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
      className={className}
      {...formItemLayout}
    >
      {form.getFieldDecorator(name, {
        initialValue,
        rules: [
          { required, message: `${label} is required.` },
          { whitespace: isWhitespace, message: `${label} is required.` },
          {
            max: maxLength,
            message: `${label} cannot exceed ${maxLength} characters`
          },
          { min, message: `${label} must be at least ${min} characters` },
          { ...email }
        ]
      })(
        !rows ? (
          <Input
            placeholder={placeholder}
            maxLength={maxLength.toString()}
            onChange={onChange}
            disabled={disabled}
            type={type}
          />
        ) : (
          <Input.TextArea
            placeholder={placeholder}
            maxLength={maxLength.toString()}
            autosize={{ minRows: rows }}
            rows={rows}
            onChange={onChange}
            disabled={disabled}
          />
        )
      )}
    </Form.Item>
  );
};

TextInputMultiple.propTypes = {
  form: PropTypes.object.isRequired,
  name: PropTypes.string.isRequired,
  label: PropTypes.string.isRequired,
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

export default TextInputMultiple;
