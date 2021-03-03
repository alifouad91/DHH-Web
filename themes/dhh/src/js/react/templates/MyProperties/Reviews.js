import React from "react";
import PropTypes from "prop-types";
import _ from "lodash";
import { Button } from "antd";
import config from "../../config";
import ReviewCard from "../../components/ReviewCard";
import EmptyMessage from "../../components/EmptyMessage";
import { getPropertyReviews } from "../../services";
import { generateLoadingObject } from "../../utils";

export default class PropertyReview extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      reviews: generateLoadingObject(3),
      loading: true
    };
  }
  componentWillMount() {
    this.getReviews();
  }

  getReviews = () => {
    this.setState({ loading: true });
    getPropertyReviews(
      data => {
        // console.log(data);
        this.setState({ loading: false, reviews: data.reviews });
      },
      err => {
        setTimeout(() => {
          this.getReviews();
        }, 5000);
      }
    );
  };
  render() {
    const { reviews } = this.state;
    return (
      <div className="container-fluid">
        <h4>Guests Reviews</h4>
        <div className="row">
          {reviews.length ? (
            _.map(reviews, (item, index) => {
              return (
                <ReviewCard
                  key={index}
                  index={index}
                  data={item}
                  itemsToShow={3}
                />
              );
            })
          ) : (
            <EmptyMessage message="No Reviews available" />
          )}
        </div>
        {reviews.length ? <Button type="secondary" href={`${config.BASE_URL}/profile/property-reviews`}>
          Read all reviews
        </Button> : null}
      </div>
    );
  }
}

PropertyReview.propTypes = {};
