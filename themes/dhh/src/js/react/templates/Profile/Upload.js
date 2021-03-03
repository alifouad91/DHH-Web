import React from "react";
import { Upload, message, Avatar, Icon, Spin } from "antd";
import { generateAvatarFromString } from "../../utils";
import config from "../../config";

export default class UploadAvatar extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      userGroup: $("#navigation__form").data("group"),
      uploading: false,
      newAvatar: null
    };
  }

  handleChange = info => {
    this.setState({ uploading: true });
    if (info.file.status === "done") {
      message.success(`Avatar uploaded successfully`);
      this.setState({
        uploading: false,
        newAvatar: info.file.response.data.avatar
      });
    } else if (info.file.status === "error") {
      message.error(`Avatar upload failed.`);
      this.setState({ uploading: false });
    }
  };

  render() {
    const { userGroup, uploading, newAvatar } = this.state;
    const { avatar, fullName } = this.props;
    const stringAvatar = generateAvatarFromString(fullName);

    const props = {
      name: "avatar",
      action: `${config.BASE_URL}/api/user/updateAvatar`,
      headers: {
        Authorization: `Bearer ${localStorage.getItem("access_token")}`
      },
      onChange: this.handleChange,
      showUploadList: false
    };

    return (
      <Upload {...props}>
        <div className="page__profile__header__avatar">
          <Spin spinning={uploading}>
            <Icon type="plus" />
            <span>UPLOAD</span>
          </Spin>
        </div>
        <Avatar
          size={60}
          src={newAvatar ? newAvatar : avatar ? avatar : null}
          style={{
            background:
              userGroup === "landlord"
                ? "radial-gradient(circle, #5C80FD 0%, #85A5F2 100%)"
                : "radial-gradient(circle, #FD5C63 0%, #FF927B 100%)"
          }}
        >
          {stringAvatar ? stringAvatar : "U"}
        </Avatar>
      </Upload>
    );
  }
}
