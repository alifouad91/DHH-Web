import React from 'react';
import _ from 'lodash';
import moment from 'moment';
import { Icon, Dropdown, List, Badge, Tooltip, Button, Spin } from 'antd';
import { Notification, Messages, EmptyNotification } from '../../icons';
import { getNotifications, markNotificationAsRead, clearAllNotifications } from '../../services';
import { objectToFormData } from '../../utils';
import config from '../../config';
import EmptyMessage from '../../components/EmptyMessage';

const NotificationItem = props => {
	const { item, markAsRead, activeId, handleLinkClick } = props;
	const { readStatus, nID, title, body, createdAt, link } = item;
	const isUnread = !Boolean(Number(readStatus));
	return (
		<List.Item>
			<Spin spinning={activeId === nID}>
				<a
					// href={`${config.BASE_URL}${link}`}
					onClick={e => handleLinkClick(e, link, nID, isUnread)}
					className={`notifications__item ${isUnread ? 'unread' : ''}`}
				>
					<div>
						<h6>{title}</h6>
						<span className="date">{moment(createdAt).format('MMM D')}</span>
						<Button type="primary" onClick={e => markAsRead(nID, e)}>
							Mark as read
						</Button>
						<Badge />
					</div>
					<p>{body}</p>
				</a>
			</Spin>
		</List.Item>
	);
};

const NotificationList = props => {
	const { notifications, loading, markAsRead, activeId, unreadCount, handleClearAll, handleLinkClick } = props;
	return (
		<List
			className="notifications__list"
			locale={{
				emptyText: (
					<EmptyMessage
						image={<Icon component={EmptyNotification} />}
						message={
							<>
								<span className="title">Nothing new</span>
								<span>Waiting for new adventures and bookings</span>
							</>
						}
					/>
				),
			}}
			header={
				<div className="notifications__header">
					<div>
						<h5>Notifications</h5>
						<span>{loading ? '' : unreadCount > 0 ? `${unreadCount} unread` : `Nothing New`}</span>
					</div>
					{notifications.length > 0 ? <span onClick={handleClearAll}>Clear All</span> : null}
				</div>
			}
			footer={null}
			// bordered
			dataSource={notifications}
			loading={loading}
			renderItem={item => (
				<NotificationItem
					handleLinkClick={handleLinkClick}
					item={item}
					activeId={activeId}
					markAsRead={markAsRead}
				/>
			)}
		/>
	);
};

export default class UserNotification extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			notifications: [],
			loading: true,
			activeId: null,
			clearing: false,
		};
	}
	componentWillMount() {
		this.fetchNotifications();
	}

	markAsRead = (nID, e, cb) => {
		e.preventDefault();
		this.setState({ activeId: nID });
		markNotificationAsRead(
			objectToFormData({ nID }),
			response => {
				let newNotifs = this.state.notifications;
				_.map(newNotifs, function(notif) {
					if (notif.nID === response.nID) {
						notif.readStatus = '1';
					}
				});
				this.setState({
					activeId: null,
					notifications: newNotifs,
				});
				if (cb) {
					cb();
				}
			},
			err => {
				console.log(err);
				this.setState({
					activeId: null,
				});
			}
		);
	};

	fetchNotifications = isNew => {
		const params = isNew ? { new: true } : {};
		getNotifications(
			params,
			response => {
				// let newNotifs = [...response, ...this.state.notifications];
				this.setState({
					notifications: _.sortBy([...this.state.notifications, ...response], 'nID'),
					loading: false,
				});
				setTimeout(() => {
					this.fetchNotifications(true);
				}, config.notificationInterval);
			},
			err => {}
		);
	};

	handleClearAll = () => {
		this.setState({ clearing: true });
		clearAllNotifications(
			response => {
				// console.log(response);
				this.setState({ notifications: [], clearing: false });
			},
			err => {
				console.log(err);
			}
		);
	};

	handleVisibleChange = flag => {
		this.setState({ visible: flag });
	};

	handleLinkClick = (e, link, nID, isUnread) => {
		e.preventDefault();
		// console.log(e, link, nID);
		if (isUnread) {
			this.markAsRead(nID, e, () => {
				location.href = `${config.BASE_URL}${link}`;
			});
		} else {
			location.href = `${config.BASE_URL}${link}`;
		}
	};

	render() {
		const { notifications, loading, activeId, clearing } = this.state;
		const unreadCount = _.filter(notifications, notif => notif.readStatus === '0').length;
		// console.log(unreadCount);
		return (
			<div className="navigation__form__notifications">
				<Dropdown
					overlay={
						<NotificationList
							loading={loading}
							notifications={notifications}
							markAsRead={this.markAsRead}
							activeId={activeId}
							unreadCount={unreadCount}
							handleClearAll={this.handleClearAll}
							handleLinkClick={this.handleLinkClick}
							clearing={clearing}
						/>
					}
					trigger={['click']}
					placement="bottomRight"
					onVisibleChange={this.handleVisibleChange}
				>
					<a className="ant-dropdown-link" href="#">
						<Icon component={Notification} className={unreadCount > 0 ? 'unread' : ''} />
						{unreadCount > 0 ? <Badge status="error" /> : null}
					</a>
				</Dropdown>
			</div>
		);
	}
}
