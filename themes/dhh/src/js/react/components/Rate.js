import React from "react";
import { Rate, Icon } from "antd";
import { Star, StarLarge } from "../icons";

export default class RateComponent extends React.PureComponent {
  render() {
    return (
      <Rate
        character={<Icon component={this.props.large ? StarLarge : Star} />}
        {...this.props}
      />
    );
  }
}
