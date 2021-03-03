import React from "react";
import PropTypes from "prop-types";
import { Upload, Button, Icon, Form } from "antd";

const ImageUpload = ({
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
  action,
  disabled,
  onSuccess,
  showUploadList,
  fileList,
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
          <Upload name={name} action={action} accept="image/*" listType="picture"
            onRemove={false}
            onChange={onChange}
            disabled={disabled}
            onSuccess={onSuccess}
            multiple={false}
            showUploadList={showUploadList}
            fileList={fileList}
            headers={{
              authorization: `Bearer ${localStorage.getItem('access_token')}`,
            }}>
          <Button disabled={disabled}>
            <Icon type="upload" /> Click to upload
          </Button>
        </Upload>
      )}
    </Form.Item>
  );
};

ImageUpload.propTypes = {
  form: PropTypes.object.isRequired,
  name: PropTypes.string.isRequired,
  label: PropTypes.string.isRequired,
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
};

export default ImageUpload;
