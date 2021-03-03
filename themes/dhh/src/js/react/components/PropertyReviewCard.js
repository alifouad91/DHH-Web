import React from "react";
import moment from "moment";
import { Avatar } from "antd";
import ImageFit from "./ImageFit";
import Rate from "./Rate";
import { isDateSameYear, isDateSameMonth } from "../utils";

export default class MyReviewCard extends React.PureComponent {
  render() {
    const { item } = this.props;
    const {
      thumbnail,
      title,
      fullName,
      location,
      avgRating,
      startDate,
      endDate,
      totalNights,
      reviewComment,
      reviews,
      createdAt,
      reviewRating,
      avatar
    } = item;

    const sDate = moment(startDate);
    const eDate = moment(endDate);
    const sameYear = isDateSameYear(startDate, endDate);
    const sameMonth = isDateSameMonth(startDate, endDate);

    return (
      <div className="col-lg-12 myreview__card property">
        <div className="image">
          <ImageFit style={{ objectFit: "cover" }} src={thumbnail} />
          <p>{title}</p>
          <div className="property__card__rating">
            <Rate disabled value={Number(avgRating)} />
            <small>· {reviews}</small>
          </div>
        </div>
        <div className="myreview__card__details">
          <div className="avatar">
            <Avatar size={56} src={avatar ? avatar : "BM"} />
            <div>
              <h5>{fullName}</h5>
              <div className="myreview__card__date">
                <span className="date-1">
                  {sDate.format("MMM D")}{" "}
                  <em>{sameYear ? "" : `, ${sDate.format("YYYY")}`}</em>
                </span>
                <em>{` - `}</em>
                <span className="date-2">
                  {sameMonth ? eDate.format("D") : eDate.format("MMM D")}{" "}
                  <em>{`, ${eDate.format("YYYY")}`}</em>
                </span>
                <span> • ({totalNights} nights)</span>
              </div>
            </div>
          </div>
          <div className="myreview__card__rating">
            <span>Rating </span>
            <div>
              <Rate disabled value={Number(reviewRating)} />
              <span>{reviewRating}</span>
            </div>
          </div>
          <p>{reviewComment}</p>
          <span className="myreview__card__reviewdate">
            {moment(createdAt).format("MMM DD, YYYY")}
          </span>
        </div>
      </div>
    );
  }
}
