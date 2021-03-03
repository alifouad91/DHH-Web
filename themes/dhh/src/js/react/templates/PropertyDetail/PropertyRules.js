import React from "react";
import _ from "lodash";

export default class PropertyRules extends React.Component {
  render() {
    const { rules } = this.props;
    return (
      <div className="page__propertydetails__rules">
        <h4>Property Rules</h4>
        <ul>
          {_.map(rules, (rule, index) => (
            <li key={index}>{rule}</li>
          ))}
        </ul>
      </div>
    );
  }
}
