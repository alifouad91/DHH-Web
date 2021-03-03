import React from "react";
import _ from "lodash";
import { Menu, Dropdown, Icon, Avatar } from "antd";
import config from "../../config";
import { generateAvatarFromString } from "../../utils";
import InviteModal from "../../components/InviteFriends";

export default class CurrencyDropdown extends React.Component {
  constructor(props) {
    super(props);
    let locPath = location.pathname.split("/");
    locPath = _.filter(locPath);
    const selectedKeys = [locPath[locPath.length - 1]];

    this.state = {
      activeKey: selectedKeys,
      showModal: false,
    };
  }

  handleClick = (data) => {
    // const loc = `${config.BASE_URL}/${data.key}`;
    if (this.state.activeKey.indexOf(data.key) === -1) {
      switch (data.key) {
        case "login/logout":
          localStorage.removeItem("access_token");
          location.href = `${config.BASE_URL}/${data.key}`;
          break;
        case "profile":
          location.href = `${config.BASE_URL}/${data.key}`;
          break;
        case "invite":
          console.log("invite");
          this.handleModalClick();
          break;
        default:
          location.href = `${config.BASE_URL}/profile/${data.key}`;
      }
    }
  };

  renderMenu = () => {
    const { userDetails } = this.props;
    const isLandlord = userDetails.userGroup === "landlord";
    const title = (
      <>
        <h6>{userDetails.fullName}</h6>
        {isLandlord ? "" : <span>{userDetails.userBadge}</span>}
      </>
    );
    const menu = (
      <Menu onClick={this.handleClick} selectedKeys={this.state.activeKey}>
        <Menu.ItemGroup title={title}>
          <Menu.Divider />
          {!isLandlord && (
            <Menu.Item key="mybookings">
              <span>My Bookings</span> <span>{userDetails.bookingCount}</span>
            </Menu.Item>
          )}
          {!isLandlord && (
            <Menu.Item key="myreviews">
              <span>My Reviews</span> <span>{userDetails.reviewCount}</span>
            </Menu.Item>
          )}
          {!isLandlord && (
            <Menu.Item key="favourites">
              <span>Favourites</span> <span>{userDetails.favouriteCount}</span>
            </Menu.Item>
          )}
          {isLandlord && (
            <Menu.Item key="my-properties">
              <span>My Properties</span>
            </Menu.Item>
          )}
          {isLandlord && (
            <Menu.Item key="property-reviews">
              <span>Reviews</span>{" "}
            </Menu.Item>
          )}
          {isLandlord && (
            <Menu.Item key="finances">
              <span>Finances</span>
            </Menu.Item>
          )}

          <Menu.Item key="profile">
            <span>Edit Profile</span>
          </Menu.Item>
          <Menu.Divider />
          <Menu.Item key="invite">
            <span>Invite Friends</span>
          </Menu.Item>
          <Menu.Divider />
          <Menu.Item key="login/logout">
            <span style={{ color: "#E6474D" }}>Logout</span>
          </Menu.Item>
        </Menu.ItemGroup>
      </Menu>
    );
    return menu;
  };

  handleModalClick = () => {
    this.setState({ showModal: !this.state.showModal });
  };

  render() {
    const { showModal } = this.state;
    const { userDetails } = this.props;
    const { avatar } = userDetails;
    const stringAvatar = generateAvatarFromString(userDetails.fullName);

    return (
      <>
        <InviteModal
          visible={showModal}
          loading={false}
          handleCancel={this.handleModalClick}
        />
        <Dropdown
          overlay={this.renderMenu}
          trigger={["click"]}
          overlayClassName="userdropdown__slct"
          placement="bottomRight"
        >
          <a className="ant-dropdown-link" href="#">
            <Avatar
              size={40}
              src={avatar ? avatar : null}
              style={{
                background:
                  userDetails.userGroup === "landlord"
                    ? "radial-gradient(circle, #5C80FD 0%, #85A5F2 100%)"
                    : "radial-gradient(circle, #FD5C63 0%, #FF927B 100%)",
              }}
            >
              {stringAvatar ? stringAvatar : "U"}
            </Avatar>{" "}
            <Icon type="caret-down" />
          </a>
        </Dropdown>
      </>
    );
  }
}
