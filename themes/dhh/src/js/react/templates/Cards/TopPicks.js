import React from "react";
import ReactDOM from "react-dom";
import PropTypes from "prop-types";
import _ from "lodash";
import { Button } from "antd";
import PropertyCard from "../../components/PropertyCard";
import { sampleJsonTopPicks } from "../../constants";
import { generateLoadingObject } from "../../utils";

class TopPicks extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: []
    }
  }
  componentDidMount() {
    this.setState({ items: generateLoadingObject(4) });
    setTimeout(() => {
      this.setState({ items: sampleJsonTopPicks });
    }, 3000)
  }
  render() {
    const { items } = this.state;
    const { title } = this.props;
    return (
      <div className="container-fluid">
        <h4>{title}</h4>
        <div className="row">
          {_.map(items, (item, index) => {
            return <PropertyCard index={index} key={index} data={item} />;
          })}
        </div>
        <Button type="secondary">Show more</Button>
      </div>
    );
  }
}

TopPicks.propTypes = {
  title: PropTypes.string.isRequired,
};

const $top = $(".property__picks");
$top.each((index, el) => {
  const $this = $(el);
  const title = $this.data("title");
  ReactDOM.render(<TopPicks title={title} key={index} />, el);
});
