import React from "react";
import { Menu, Dropdown, Icon, message } from "antd";
import { currencies } from "../../constants";
import { setCurrency } from "../../services";
import { objectToFormData } from "../../utils";

export default class CurrencyDropdown extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      loading: false,
    };
  }

  handleClick = data => {
    if (this.props.selectedCurrency !== data.key) {
      const msg = message.loading("Setting Currency. Please wait.", 0);
      this.setState({ loading: true });
      setCurrency(
        objectToFormData({
          locale: data.key,
        }),
        data => {
          setTimeout(msg, 100);
          message.success(
            "Successfully updated currency! Page will reload.",
            0
          );
          // TODO: Check if we can just request the apis again
          location.reload();
        },
        err => {
          message.error(`Cannot set currency. Please retry.`);
          this.setState({ loading: false });
        }
      );
    }
  };

  renderMenu = () => {
    const menu = (
      <Menu
        onClick={this.handleClick}
        selectedKeys={[this.props.selectedCurrency]}
      >
        <Menu.ItemGroup title="Choose Currency">
          <Menu.Divider />
          {_.map(currencies, currency => {
            const { val, label, locale } = currency;
            return (
              <Menu.Item key={locale}>
                <span>{label}</span> <span>{val}</span>
              </Menu.Item>
            );
          })}
        </Menu.ItemGroup>
      </Menu>
    );
    return menu;
  };

  render() {
    const { selectedCurrency, placement } = this.props;
    const { loading } = this.state;
    return (
      <Dropdown
        overlay={this.renderMenu}
        trigger={["click"]}
        overlayClassName="currency__slct"
        placement={placement ? placement : "bottomRight"}
        disabled={loading}
      >
        <a className="ant-dropdown-link" href="#">
          {
            _.filter(currencies, currency => {
              return currency.locale === selectedCurrency;
            })[0].val
          }{" "}
          <Icon type="caret-down" />
        </a>
      </Dropdown>
    );
  }
}
