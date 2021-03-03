import React from "react";
import PropTypes from "prop-types";
import { Slider, Form } from "antd";

const SliderInput = ({
  form,
  name,
  label,
  required,
  initialValue,
  labelSpan,
  wrapperSpan,
  extra,
  help,
  validateStatus,
  hasFeedback,
  error,
  onChange,
  onAfterChange,
  range,
  step,
  min,
  max,
  disabled,
  style
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
        <Slider
          range={range}
          step={step}
          min={min}
          max={max}
          onChange={onChange}
          onAfterChange={onAfterChange}
          tipFormatter={null}
          disabled={disabled}
          style={style}
        />
      )}
    </Form.Item>
  );
};

SliderInput.propTypes = {
  form: PropTypes.object.isRequired,
  name: PropTypes.string.isRequired,
  step: PropTypes.number.isRequired,
  min: PropTypes.number.isRequired,
  max: PropTypes.number.isRequired,
  range: PropTypes.bool,
  label: PropTypes.string,
  onChange: PropTypes.func,
  initialValue: PropTypes.array,
  help: PropTypes.string,
  extra: PropTypes.string,
  validateStatus: PropTypes.string,
  error: PropTypes.string,
  required: PropTypes.bool,
  hasFeedback: PropTypes.bool,
  labelSpan: PropTypes.number,
  wrapperSpan: PropTypes.number
};

SliderInput.defaultProps = {
  range: true,
  min: 1,
  max: 2000
};

export default SliderInput;
