import React from "react";
import PropTypes from "prop-types";
import { Form, InputNumber } from "antd";
import _ from "lodash";

const NumberInput = ({
  form,
  name,
  label,
  required,
  sign,
  placeholder,
  initialValue,
  labelSpan,
  wrapperSpan,
  width,
  max,
  min,
  precision,
  step,
  size,
  extra,
  help,
  validateStatus,
  hasFeedback,
  disabled,
  error,
  onChange,
  parser,
  transformNonZero = false,
  hasFormatter = true,
}) => {
  const status = error ? "error" : validateStatus;
  const helpMessage = error || help;
  const signFormat = sign || "$";
  const replaceRegEx = new RegExp(`\\${signFormat}\\s?|(,*)`, "g");
  function transformNaNToZero(value) {
    let num = _.toNumber(value);
    num = _.isNaN(num)
      ? 0
      : num;
    return num;
  }
  const formatter = (value) => {
    return `${signFormat} ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  };
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
        normalize: transformNonZero ? transformNaNToZero : undefined,
      })(
        <InputNumber
          style={{ width }}
          placeholder={placeholder}
          // formatter={ hasFormatter ? {value =>
          //   `${signFormat} ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ",")
          // } : null}
          formatter={hasFormatter ? formatter : null}
          parser={value => value.replace(replaceRegEx, "")}
          min={min}
          max={max}
          precision={precision === undefined ? 2 : precision}
          step={step}
          size={size}
          onChange={onChange}
          disabled={disabled}
        />,
      )}
    </Form.Item>
  );
};

NumberInput.propTypes = {
  form: PropTypes.object.isRequired,
  name: PropTypes.string.isRequired,
  label: PropTypes.string.isRequired,
  onChange: PropTypes.func,
  sign: PropTypes.string,
  placeholder: PropTypes.string,
  initialValue: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  help: PropTypes.string,
  extra: PropTypes.string,
  validateStatus: PropTypes.string,
  error: PropTypes.string,
  required: PropTypes.bool,
  hasFeedback: PropTypes.bool,
  width: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  max: PropTypes.number,
  min: PropTypes.number,
  precision: PropTypes.number,
  step: PropTypes.number,
  size: PropTypes.string,
  labelSpan: PropTypes.number,
  wrapperSpan: PropTypes.number,
};

export default NumberInput;
