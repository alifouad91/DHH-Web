import React from "react";
import PropTypes from "prop-types";
import { Form, Button } from "antd";

const SaveButton = ({
  saveLabel,
  saving,
  savingLabel,
  disabled,
  hidden,
  size,
  style,
  icon,
  className
}) => (
  <Form.Item>
    <Button
      type="primary"
      htmlType="submit"
      icon={icon}
      disabled={disabled}
      hidden={hidden}
      style={style}
      loading={saving}
      size={size}
      className={className}
    >
      {saving ? savingLabel : saveLabel || "Save"}
    </Button>
  </Form.Item>
);

SaveButton.propTypes = {
  cancelLink: PropTypes.string,
  span: PropTypes.number,
  offset: PropTypes.number,
  saveLabel: PropTypes.string,
  cancelLabel: PropTypes.string,
  saving: PropTypes.bool,
  disabled: PropTypes.bool,
  hidden: PropTypes.bool,
  size: PropTypes.string,
  icon: PropTypes.string
};
export default SaveButton;
