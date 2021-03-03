import React from "react";
import ReactDOM from "react-dom";
import PropTypes from "prop-types";
import _ from "lodash";
import { Button } from "antd";
import ReviewCard from "../../components/ReviewCard";

const sampleJSON = [
  {
    image: "https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",
    userName: "Andrew Hatfield",
    location: "Jumeirah Town",
    lengthOfStay: "6 nights",
    rating: 3,
    date: "09 Sep 2018",
    review:
      "Wonderful place! DnA were great host, the location, pool house, pool...amazing! Wonderful place! DnA were great host, the location"
  },
  {
    image: "https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",
    userName: "Andrew Hatfield",
    location: "Jumeirah Town",
    lengthOfStay: "6 nights",
    rating: 4,
    date: "09 Sep 2018",
    review:
      "Wonderful place! DnA were great host, the location, pool house, pool...amazing! Wonderful place! DnA were great host, the location"
  },
  {
    image: "https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",
    userName: "Andrew Hatfield",
    location: "Jumeirah Town",
    lengthOfStay: "6 nights",
    rating: 4,
    date: "09 Sep 2018",
    review:
      "Wonderful place! DnA were great host, the location, pool house, pool...amazing! Wonderful place! DnA were great host, the location"
  }
];

class TenantReviews extends React.Component {
  render() {
    return (
      <div className="row">
        {_.map(sampleJSON, (item, index) => {
          return (
            <ReviewCard key={index} index={index} data={item} itemsToShow={3} />
          );
        })}
      </div>
    );
  }
}

TenantReviews.propTypes = {
  // title: PropTypes.string.isRequired
};

const $el = $(".tenant__reviews");
$el.each((index, el) => {
  ReactDOM.render(<TenantReviews key={index} />, el);
});
