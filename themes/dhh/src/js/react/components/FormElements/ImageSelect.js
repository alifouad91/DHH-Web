import React from "react";
import PropTypes from "prop-types";
import { Form, Button } from "antd";

const ImageSelect = ({
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
  thumbnail,
  onClick,
  altText,
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
        rules: [{ required, message: `${label} is required.` }],
      })(
        <div className="form-image-select">
          {/* <Icon
            type="form"
            onClick={onClick}
          /> */}
          <Button icon="form" type="primary" onClick={onClick}>
            Select Image
          </Button>
          {thumbnail || initialValue ? (
            <img src={thumbnail ? thumbnail : initialValue} alt={altText} />
          ) : null}
        </div>,
      )}
    </Form.Item>
  );
};

ImageSelect.propTypes = {
  form: PropTypes.object.isRequired,
  name: PropTypes.string.isRequired,
  label: PropTypes.string.isRequired,
  onClick: PropTypes.func.isRequired,
  placeholder: PropTypes.string,
  initialValue: PropTypes.string,
  help: PropTypes.string,
  extra: PropTypes.string,
  validateStatus: PropTypes.string,
  error: PropTypes.string,
  required: PropTypes.bool,
  hasFeedback: PropTypes.bool,
  labelSpan: PropTypes.number,
  wrapperSpan: PropTypes.number,
};

export default ImageSelect;
