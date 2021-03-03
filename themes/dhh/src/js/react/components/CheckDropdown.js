import React from 'react';
import PropTypes from 'prop-types';
import { Menu, Dropdown, Icon, Checkbox, Badge } from 'antd';

export default class CheckDropdown extends React.PureComponent {
  renderMenu = () => {
    const { handleChange, items, defaultChecked } = this.props;
    const menu = (
      <Menu>
        {_.map(items, (item) => {
          return (
            <Menu.Item key={item}>
              <Checkbox
                defaultChecked={
                  defaultChecked && defaultChecked.indexOf(item) >= 0
                }
                value={item}
                onChange={() => handleChange(item)}
              >
                {item}
              </Checkbox>
            </Menu.Item>
          );
        })}
      </Menu>
    );
    return menu;
  };

  render() {
    const { label, disabled, showBadge, isMobile } = this.props;
    return (
      <>
        {isMobile ? (
          <>
            <div className='ant-col-null ant-form-item-label'>
              <label>{label}</label>
            </div>
            {this.renderMenu()}
          </>
        ) : (
          <Dropdown
            overlay={this.renderMenu}
            disabled={disabled}
            trigger={['click']}
            // placement="bottomRight"
            overlayClassName='check__slct'
          >
            <a className='ant-dropdown-link check__slct__link' href='#'>
              {showBadge ? <Badge status='error' /> : null}
              {label} <Icon type='caret-down' />
            </a>
          </Dropdown>
        )}
      </>
    );
  }
}

CheckDropdown.propTypes = {
  items: PropTypes.array.isRequired,
  handleChange: PropTypes.func.isRequired,
  label: PropTypes.string.isRequired,
};
