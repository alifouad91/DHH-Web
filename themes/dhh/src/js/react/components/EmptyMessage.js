import React from 'react';
import { Empty } from 'antd';

export default class EmptyMessage extends React.PureComponent {
  render() {
    const { message, image } = this.props;
    return <Empty description={<h6>{message}</h6>} image={image} />;
  }
}
