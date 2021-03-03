import React from 'react';
import { Icon } from 'antd';

export default class SortIcon extends React.PureComponent {
  render() {
    const { ascending } = this.props;
    return (
      <div className="sort__icon">
        <div>
          <Icon type="caret-up" className={ascending && 'active'}/>
          <Icon type="caret-down" className={!ascending && 'active'}/>
        </div>
      </div>
    )
  }
}