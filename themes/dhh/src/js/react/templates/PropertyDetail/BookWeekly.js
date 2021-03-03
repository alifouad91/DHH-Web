import React from "react";
import { Card, Icon } from "antd";

export default class BookWeekly extends React.Component {
  handleModalOpen = () => {
    $.fancybox.open({
      src: "#book-weekly-form",
      opts: {
        touch: false
      }
    });
  };
  render() {
    const { weeklyPrice } = this.props;
    return (
      <Card className="card__secondary">
        <Icon type="home" theme="filled" />
        <div className="card__secondary__details">
          <div className="card__secondary__title">
            <span>Weekly Price</span>
            <span>{weeklyPrice}</span>
          </div>
          <p className="small">
            We provide special price for long-term rent, contact us if you
            interested in long stay booking
          </p>
          <a onClick={this.handleModalOpen}>Request for a weekly rental</a>
        </div>
      </Card>
    );
  }
}
