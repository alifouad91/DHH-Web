import React from "react";
import { Card, Icon } from "antd";

export default class BookMonthly extends React.Component {
  handleModalOpen = () => {
    $.fancybox.open({
      src: "#book-monthly-form",
      opts: {
        touch: false
      }
    });
  };
  render() {
    const { monthlyPrice } = this.props;
    return (
      <Card className="card__secondary">
        <Icon type="home" theme="filled" />
        <div className="card__secondary__details">
          <div className="card__secondary__title">
            <span>Monthly Price</span>
            <span>{monthlyPrice}</span>
          </div>
          <p className="small">
            We provide special price for long-term rent, contact us if you
            interested in long stay booking
          </p>
          <a onClick={this.handleModalOpen}>Request for a monthly rental</a>
        </div>
      </Card>
    );
  }
}
