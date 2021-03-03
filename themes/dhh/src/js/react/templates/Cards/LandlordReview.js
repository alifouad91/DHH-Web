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
      "Wonderful place! DnA were great host, the location, pool house, pool...amazing! Wonderful place! DnA were great host, the location",
    isLandLord: true
  },
  {
    image: "https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",
    userName: "Andrew Hatfield",
    location: "Jumeirah Town",
    lengthOfStay: "6 nights",
    rating: 4,
    date: "09 Sep 2018",
    review:
      "Wonderful place! DnA were great host, the location, pool house, pool...amazing! Wonderful place! DnA were great host, the location",
    isLandLord: true
  },
  {
    image: "https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",
    userName: "Andrew Hatfield",
    location: "Jumeirah Town",
    lengthOfStay: "6 nights",
    rating: 4,
    date: "09 Sep 2018",
    review:
      "Wonderful place! DnA were great host, the location, pool house, pool...amazing! Wonderful place! DnA were great host, the location",
    isLandLord: true
  },
  {
    image: "https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",
    userName: "Andrew Hatfield",
    location: "Jumeirah Town",
    lengthOfStay: "6 nights",
    rating: 4,
    date: "09 Sep 2018",
    review:
      "Wonderful place! DnA were great host, the location, pool house, pool...amazing! Wonderful place! DnA were great host, the location",
    isLandLord: true
  },
  {
    image: "https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",
    userName: "Andrew Hatfield",
    location: "Jumeirah Town",
    lengthOfStay: "6 nights",
    rating: 4,
    date: "09 Sep 2018",
    review:
      "Wonderful place! DnA were great host, the location, pool house, pool...amazing! Wonderful place! DnA were great host, the location",
    isLandLord: true
  }
];

class LandlordReview extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: []
    };
  }
  componentWillMount() {
    const $el = document.querySelectorAll(".landlord-review-item");
    let items = [];
    _.map($el, item => {
      const $this = $(item);
      items = [
        ...items,
        {
          avatar: $this.data("image"),
          fullName: $this.data("username"),
          createdAt: $this.data("createdat"),
          location: $this.data("location"),
          designation: $this.data("designation"),
          reviewComment: $this.data("reviewcomment"),
          isLandLord: true
        }
      ];
    });
    // console.log(items);
    this.setState({ items });
  }
  render() {
    const { items } = this.state;
    return (
      <div className="row">
        {_.map(items, (item, index) => {
          return (
            <ReviewCard key={index} index={index} data={item} itemsToShow={3} />
          );
        })}
      </div>
    );
  }
}

LandlordReview.propTypes = {
  // title: PropTypes.string.isRequired
};

const $el = $(".landlord__reviews");
$el.each((index, el) => {
  ReactDOM.render(<LandlordReview key={index} />, el);
});
