import React from 'react';
import { Icon } from 'antd';

const LoadingMore = ({ loading }) => (
	<div className={`load-more ${loading && 'active'}`}>
		<Icon type="loading" />
	</div>
);
export default LoadingMore;
