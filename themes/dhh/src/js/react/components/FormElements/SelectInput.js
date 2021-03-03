import React, { Component } from "react";
import PropTypes from "prop-types";
import _ from "lodash";
import { Select, Form, Icon } from "antd";
const { Option, OptGroup } = Select;

class SelectInput extends Component {
  _createContent = (items, mode) => {
    switch (mode) {
      /**
       * - MULTIPLE MODE -
       * Expected items object structure
       * [
       *    {
       *      groupLabel: {string},
       *      groupValue: [
       *        {
       *          label: {string},
       *          value: {string}
       *        }, ...
       *      ]
       *    }, ...
       * ]
       */
      case "multiple":
        return _.map(items, (item, i) => {
          const { groupLabel, groupValue } = item;

          return (
            <OptGroup label={groupLabel} key={`${i}`}>
              {_.map(groupValue, (subItem, j) => {
                return (
                  <Option key={`${i}${j}`} value={subItem.value}>
                    {subItem.label}
                  </Option>
                );
              })}
            </OptGroup>
          );
        });

      /**
       * - TAGS | COMBOBOX MODE -
       * Expected items object structure
       * [
       *    {
       *      label: {string},
       *      value: {string}
       *    }, ...
       * ]
       */
      case "tags":
      case "combobox":
      default:
        return _.map(items, item => {
          const { label, value, val } = item;
          const itemVal = value ? value : val;
          return (
            <Option key={itemVal} value={itemVal}>
              {label ? label : itemVal}
            </Option>
          );
        });
    }
  };

  render() {
    const {
      form,
      name,
      label,
      required,
      initialValue,
      placeholder,
      labelSpan,
      wrapperSpan,
      style,
      extra,
      help,
      validateStatus,
      hasFeedback,
      error,
      onChange,
      list,
      mode,
      disabled,
      size,
      showSearch
    } = this.props;

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
          rules: [
            { required, message: `${label ? label : "Field"} is required.` }
          ]
        })(
          <Select
            style={style}
            onChange={onChange}
            placeholder={placeholder}
            mode={mode}
            disabled={disabled}
            size={size}
            suffixIcon={<Icon type="caret-down" />}
            showSearch={showSearch}
          >
            {list && this._createContent(list, mode)}
          </Select>
        )}
      </Form.Item>
    );
  }
}

SelectInput.propTypes = {
  form: PropTypes.object.isRequired,
  name: PropTypes.string.isRequired,
  label: PropTypes.string,
  initialValue: PropTypes.oneOfType([
    PropTypes.string,
    PropTypes.arrayOf(PropTypes.string)
  ]),
  size: PropTypes.string,
  onChange: PropTypes.func,
  placeholder: PropTypes.string,
  help: PropTypes.string,
  extra: PropTypes.string,
  validateStatus: PropTypes.string,
  error: PropTypes.string,
  required: PropTypes.bool,
  disabled: PropTypes.bool,
  hasFeedback: PropTypes.bool,
  style: PropTypes.object,
  labelSpan: PropTypes.number,
  wrapperSpan: PropTypes.number,
  list: PropTypes.oneOfType([
    PropTypes.arrayOf(
      PropTypes.shape({
        label: PropTypes.string,
        value: PropTypes.string
      })
    ),
    PropTypes.arrayOf(
      PropTypes.shape({
        groupLabel: PropTypes.string,
        groupValue: PropTypes.arrayOf(
          PropTypes.shape({
            label: PropTypes.string,
            value: PropTypes.string
          })
        )
      })
    )
  ]),
  mode: PropTypes.string
};

export default SelectInput;
