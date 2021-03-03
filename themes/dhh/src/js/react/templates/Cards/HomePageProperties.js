import React from "react";
import ReactDOM from "react-dom";
import PropTypes from "prop-types";
import _ from "lodash";
import { Button } from "antd";
// import { Fade } from "react-reveal";
import PropertyCard from "../../components/PropertyCard";
import { generateLoadingObject } from "../../utils";
import { getHomePageItems } from "../../services";

export default class FeaturedProperty extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: generateLoadingObject(props.count),
      loading: true,
      activeItems: [],
      hideShowMore: false,
    };
  }
  componentWillMount() {
    this.getItems();
  }

  getItems = () => {
    const { filter, keywords, count } = this.props;
    getHomePageItems(
      {
        filter,
        keywords,
        count: count * 2,
      },
      (data) => {
        const itemCount = data.length;
        this.setState({
          items: data,
          activeItems: _.slice(data, 0, count),
          loading: false,
        });
      },
      (err) => {
        console.log(err);
      }
    );
  };

  handleShowMore = () => {
    this.setState({ activeItems: this.state.items, hideShowMore: true });
  };

  render() {
    const { title, count } = this.props;
    const { items, loading, activeItems, hideShowMore } = this.state;
    const itemsToMap = loading ? items : activeItems;
    return (
      <div className="container-fluid">
        <h4>{title}</h4>
        <div className="row">
          {_.map(itemsToMap, (item, index) => {
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
        {itemsToMap.length &&
        items.length > count &&
        !hideShowMore &&
        !loading ? (
          <Button type="secondary" onClick={this.handleShowMore}>
            Show more
          </Button>
        ) : null}
      </div>
    );
  }
}

FeaturedProperty.propTypes = {
  title: PropTypes.string.isRequired,
  count: PropTypes.number.isRequired,
};

const $el = $(".property__home");
$el.each((index, el) => {
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
