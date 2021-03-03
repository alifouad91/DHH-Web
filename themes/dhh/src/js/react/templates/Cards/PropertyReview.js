import React from "react";
import ReactDOM from "react-dom";
import PropTypes from "prop-types";
import _ from "lodash";
import { Button } from "antd";
import ReviewCard from "../../components/ReviewCard";
import { getRandomGuestReviews } from "../../services";
import { generateLoadingObject } from "../../utils";

class PropertyReview extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: generateLoadingObject(3),
      loading: true,
    };
  }
  componentWillMount() {
    this.getReviews();
  }

  getReviews = () => {
    this.setState({ items: generateLoadingObject(3), loading: true });
    getRandomGuestReviews(
      (data) => {
        this.setState({ items: data, loading: false });
      },
      (err) => {
        console.log(err);
      }
    );
  };

  render() {
    const { items, loading } = this.state;
    const { title, count } = this.props;
    return (
      <div className="container-fluid">
        <h4>{title}</h4>
        <div className="row">
          {_.map(items, (item, index) => {
            return (
              <ReviewCard
                key={index}
                index={index}
                data={item}
                itemsToShow={3}
              />
            );
          })}
        </div>
        {!loading && count >= items.length ? (
          <Button type="secondary" onClick={this.getReviews}>
            Show more
          </Button>
        ) : null}
      </div>
    );
  }
}

PropertyReview.propTypes = {
  title: PropTypes.string,
};

const $el = $(".property__reviews");
$el.each((index, el) => {
  const $this = $(el);
  const title = $this.data("title");
  const count = $this.data("count");
  ReactDOM.render(
    <PropertyReview count={count} title={title} key={index} />,
    el
  );
});
