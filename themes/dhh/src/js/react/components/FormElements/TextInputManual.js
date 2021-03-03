import React, { Component } from "react";
import PropTypes from "prop-types";
import { Input, Form } from "antd";

class TextInputManual extends Component {
  constructor(props) {
    super(props);

    this.state = {
      validateStatus: "",
      errorMsg: null
    };
  }

  validate = value => {
    const { label, required, whitespace, max, min } = this.props;
    const isWhitespace = whitespace === undefined ? true : whitespace;
    const error = "error";
    if ((required && !value) || (isWhitespace && !value.trim())) {
      return {
        validateStatus: error,
        errorMsg: `${label} is required.`
      };
    }

    if (max && value.length > max) {
      return {
        validateStatus: error,
        errorMsg: `${label} cannot exceed ${max} characters`
      };
    }

    if (min && value.trim().length < min) {
      return {
        validateStatus: error,
        errorMsg: `${label} must be at least ${min} characters`
      };
    }

    return {
      validateStatus: "",
      errorMsg: undefined
    };
  };

  handleChange = event => {
    event.preventDefault();
    const result = this.validate(event.target.value);
    this.setState({
      ...result
    });
    this.props.onChange(event, result.errorMsg);
  };

  render() {
    const {
      value,
      required,
      name,
      label,
      placeholder,
      labelSpan,
      wrapperSpan,
      max,
      rows,
      extra,
      help,
      validateStatus,
      hasFeedback,
      error
    } = this.props;
    const maxLength = max || (name === "_id" ? 30 : 256);
    const status = error ? "error" : validateStatus;
    const helpMessage = error || help;
    const formItemLayout = {
      labelCol: { span: labelSpan || null },
      wrapperCol: { span: wrapperSpan || null }
    };
    const internalValidateStatus = this.state.validateStatus;
    const internalErrorMsg = this.state.errorMsg;

    return (
      <Form.Item
        {...formItemLayout}
        label={label}
        validateStatus={internalValidateStatus || status}
        help={internalErrorMsg || helpMessage}
        extra={extra}
        hasFeedback={hasFeedback}
        required={required}
      >
        {!rows ? (
          <Input
            name={name}
            value={value}
            placeholder={placeholder}
            maxLength={maxLength.toString()}
            onChange={this.handleChange}
          />
        ) : (
          <Input.TextArea
            name={name}
            value={value}
            placeholder={placeholder}
            maxLength={maxLength.toString()}
            rows={rows}
            onChange={this.handleChange}
          />
        )}
      </Form.Item>
    );
  }
}

TextInputManual.propTypes = {
  name: PropTypes.string.isRequired,
  label: PropTypes.string.isRequired,
  onChange: PropTypes.func,
  value: PropTypes.string,
  placeholder: PropTypes.string,
  help: PropTypes.string,
  extra: PropTypes.string,
  validateStatus: PropTypes.string,
  error: PropTypes.string,
  required: PropTypes.bool,
  whitespace: PropTypes.bool,
  hasFeedback: PropTypes.bool,
  max: PropTypes.number,
  min: PropTypes.number,
  rows: PropTypes.number,
  labelSpan: PropTypes.number,
  wrapperSpan: PropTypes.number
};

export default TextInputManual;
