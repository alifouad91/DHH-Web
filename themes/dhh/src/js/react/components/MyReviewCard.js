import React from "react";
import moment from "moment";
import { Button } from "antd";
import ImageFit from "./ImageFit";
import Rate from "./Rate";
import { isDateSameYear, isDateSameMonth } from "../utils";

export default class MyReviewCard extends React.PureComponent {
  render() {
    const { item, changeStatus } = this.props;
    const {
      thumbnail,
      title,
      location,
      startDate,
      endDate,
      totalNights,
      myComments,
      createdAt,
      myRatings,
      editable
    } = item;

    const sDate = moment(startDate);
    const eDate = moment(endDate);
    const sameYear = isDateSameYear(startDate, endDate);
    const sameMonth = isDateSameMonth(startDate, endDate);

    return (
      <div className="col-lg-12 myreview__card">
        <ImageFit style={{ objectFit: "cover" }} src={thumbnail} />
        <div className="myreview__card__details">
          <h5>{title}</h5>
          <p className="small">{location}</p>
          <div className="myreview__card__rating">
            <span>Your Rating </span>
            <div>
              <Rate disabled value={Number(myRatings)} />
              <span>{myRatings}</span>
            </div>
          </div>
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
          <p>{myComments}</p>
          <span className="myreview__card__reviewdate">
            {moment(createdAt).format("MMM DD, YYYY")}
          </span>
          <div className="myreview__card__buttons">
            {/* <Button type="secondary">Delete</Button> */}
            <Button
              type="secondary"
              disabled={!editable}
              onClick={() => changeStatus("edit", item)}
            >
              {editable ? "Edit" : "Edit limit reached!"}
            </Button>
          </div>
        </div>
      </div>
    );
  }
}
