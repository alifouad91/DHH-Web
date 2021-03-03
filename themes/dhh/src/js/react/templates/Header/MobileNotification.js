import React from "react";
import ReactDOM from "react-dom";
import _ from "lodash";
import moment from "moment";
import { Icon, Dropdown, List, Badge, Tooltip, Button, Spin } from "antd";
import { Notification, Messages, EmptyNotification } from "../../icons";
import {
  getNotifications,
  markNotificationAsRead,
  clearAllNotifications,
} from "../../services";
import { objectToFormData } from "../../utils";
import config from "../../config";
import EmptyMessage from "../../components/EmptyMessage";

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
          className={`notifications__item ${isUnread ? "unread" : ""}`}
        >
          <div>
            <h6>{title}</h6>
            <span className="date">{moment(createdAt).format("MMM D")}</span>
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
  const {
    notifications,
    loading,
    markAsRead,
    activeId,
    unreadCount,
    handleClearAll,
    handleLinkClick,
    handleVisibility,
    className,
  } = props;
  return (
    <List
      className={`notifications__list ${className}`}
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
            <span>
              {loading
                ? ""
                : unreadCount > 0
                ? `${unreadCount} unread`
                : `Nothing New`}
            </span>
          </div>
          {notifications.length > 0 ? (
            <>
              <span onClick={handleClearAll}>Clear All</span>
            </>
          ) : null}
          <Icon type="double-right" onClick={handleVisibility} />
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

class MobileNotification extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      notifications: [],
      loading: true,
      activeId: null,
      clearing: false,
      visible: false,
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
            notif.readStatus = "1";
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
        let newNotifs = _.sortBy(
          _.uniqBy([...response, ...this.state.notifications], "createdAt"),
          "nID"
        );

        const unreadCount = _.filter(
          newNotifs,
          notif => notif.readStatus === "0"
        ).length;

        const $mobileMenu = $(".mobile-menu-icon");
        if (unreadCount > 0) {
          $mobileMenu.addClass("has-notif");
        } else {
          $mobileMenu.removeClass("has-notif");
        }
        this.setState({
          notifications: newNotifs,
          loading: false,
        });
        setTimeout(() => {
          this.fetchNotifications(true);
        }, 1000);
      },
      err => {
        console.log("err", err);
      }
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

  handleVisibility = e => {
    e.preventDefault();
    this.setState({ visible: !this.state.visible });
  };

  render() {
    const { notifications, loading, activeId, clearing, visible } = this.state;
    const unreadCount = _.filter(
      notifications,
      notif => notif.readStatus === "0"
    ).length;
    // console.log(unreadCount);
    return (
      <>
        <a onClick={this.handleVisibility}>Notifications</a>
        {loading ? null : (
          <>
            {unreadCount && unreadCount > 0 ? (
              <Badge dot={true} color="#fd6065" />
            ) : null}
            <span>{notifications.length}</span>
          </>
        )}
        <NotificationList
          loading={loading}
          notifications={notifications}
          markAsRead={this.markAsRead}
          activeId={activeId}
          unreadCount={unreadCount}
          handleClearAll={this.handleClearAll}
          handleLinkClick={this.handleLinkClick}
          clearing={clearing}
          className={visible ? "visible" : ""}
          handleVisibility={this.handleVisibility}
        />
      </>
    );
  }
}

const $el = $("#mobile-notifications");
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    ReactDOM.render(<MobileNotification />, el);
  });
}
