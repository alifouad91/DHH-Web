import React from "react";
import _ from "lodash";

export default class DistrictArea extends React.Component {
  render() {
    const { districtArea } = this.props;
    return (
      <div className="page__propertydetails__area">
        <h4>Disctrict and area</h4>
        {_.map(districtArea, (rule, index) => (
          <p key={index}>{rule}</p>
        ))}
      </div>
    );
  }
}
