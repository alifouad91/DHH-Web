import React from "react";
import ReactDOM from "react-dom";
import PropTypes from "prop-types";
import _ from "lodash";
import { Button } from "antd";
import PropertyCard from "../../components/PropertyCard";
import { sampleJsonFeatured } from "../../constants";
import { generateLoadingObject } from "../../utils";
import { getHomePageItems } from "../../services";

class FeaturedProperty extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: generateLoadingObject(3)
    };
  }
  componentWillMount() {
    this.getItems();
  }

  getItems = () => {
    const { filter, keywords } = this.props;
    getHomePageItems(
      {
        filter,
        keywords
      },
      data => {
        // console.log(data);
        this.setState({ items: data });
      },
      err => {
        console.log(err);
      }
    );
  };

  render() {
    const { title, count } = this.props;
    const { items } = this.state;
    return (
      <div className="container-fluid">
        <h4>{title}</h4>
        <div className="row">
          {_.map(items, (item, index) => {
            return (
              <PropertyCard
                key={index}
                index={index}
                data={item}
                itemsToShow={count}
              />
            );
          })}
        </div>
        <Button type="secondary">Show more</Button>
      </div>
    );
  }
}

FeaturedProperty.propTypes = {
  title: PropTypes.string.isRequired,
  // filter: PropTypes.oneOfType[(PropTypes.string, PropTypes.number)],
  count: PropTypes.number.isRequired
};

const $featured = $(".property__featured");
$featured.each((index, el) => {
  const $this = $(el);
  const title = $this.data("title");
  const filter = $this.data("filter");
  const count = Number($this.data("count"));
  ReactDOM.render(
    <FeaturedProperty
      title={title}
      filter={filter}
      count={count}
      key={index}
    />,
    el
  );
});
