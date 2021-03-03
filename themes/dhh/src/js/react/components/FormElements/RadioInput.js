import React from "react";
import PropTypes from "prop-types";
import _ from "lodash";
import { Radio, Form } from "antd";
const RadioGroup = Radio.Group;

const RadioInput = ({
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
  items,
  buttonStyle,
  onChange,
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
        <RadioGroup
          disabled={disabled}
          buttonStyle={buttonStyle}
          onChange={onChange}
          style={style}
        >
          {_.map(items, item => {
            return (
              <Radio.Button key={item.val} value={item.val}>
                {item.label ? item.label : item.val}
              </Radio.Button>
            );
          })}
        </RadioGroup>
      )}
    </Form.Item>
  );
};

RadioInput.propTypes = {
  form: PropTypes.object.isRequired,
  name: PropTypes.string.isRequired,
  items: PropTypes.array.isRequired,
  label: PropTypes.string,
  onChange: PropTypes.func,
  initialValue: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  help: PropTypes.string,
  extra: PropTypes.string,
  validateStatus: PropTypes.string,
  error: PropTypes.string,
  required: PropTypes.bool,
  hasFeedback: PropTypes.bool,
  labelSpan: PropTypes.number,
  wrapperSpan: PropTypes.number
};

RadioInput.defaultProps = {
  buttonStyle: "solid"
};

export default RadioInput;
