import React from "react";
import PropTypes from "prop-types";
import moment from "moment";
import { Card, Avatar, Skeleton } from "antd";
import { Fade } from "react-reveal";
import Rate from "./Rate";
import { generateAvatarFromString } from "../utils";

export default class ReviewCard extends React.Component {
  render() {
    const { data, propertyDetail, itemsToShow, full } = this.props;
    const {
      reviewRating,
      avatar,
      fullName,
      location,
      totalNights,
      createdAt,
      reviewComment,
      isLoading,
      isLandLord,
      designation,
      path,
      title,
    } = data;
    const date = isLandLord
      ? moment(createdAt, "DD-MM-YYYY")
      : moment(createdAt);
    const stringAvatar = generateAvatarFromString(fullName);

    return (
      <div
        className={`col-sm-12 col-md-6 col-lg-${12 / itemsToShow} col-xlg-${
          isLoading ? 12 / itemsToShow : 12 / itemsToShow - 1
        } review__card`}
      >
        {isLoading ? (
          <Skeleton active loading={true} />
        ) : (
          <Fade>
            <a
              href={
                full ? null : `${window.location.origin}/properties/${path}`
              }
            >
              <Card hoverable={!full}>
                <div className="review__card__profile">
                  {/* <Avatar
                  size={56}
                  src={avatar}
                  style={{ marginRight: 12 }}
                /> */}
                  <Avatar
                    size={56}
                    src={avatar ? avatar : null}
                    style={{
                      marginRight: 12,
                      minWidth: 56,
                      background: isLandLord
                        ? "radial-gradient(circle, #5C80FD 0%, #85A5F2 100%)"
                        : "radial-gradient(circle, #FD5C63 0%, #FF927B 100%)",
                    }}
                  >
                    {stringAvatar ? stringAvatar : "U"}
                  </Avatar>{" "}
                  <div className="review__card__profile__info">
                    <span className="sub-header-2">{fullName}</span>
                    <p className="small">
                      {date.format("DD MMM YYYY")}{" "}
                      {propertyDetail && ` • ${totalNights} night(s)`}
                    </p>
                    {!isLandLord && !propertyDetail && (
                      <p className="small">
                        {title} • {totalNights} night(s)
                      </p>
                    )}
                  </div>
                </div>
                <div className={`review__card__text ${full ? "full" : ""}`}>
                  <p className="small f">{reviewComment}</p>
                </div>
                <div className="review__card__rating">
                  {isLandLord ? (
                    <>
                      <p className="landlord-details">
                        {location} <span>{title ? designation : "owner"}</span>
                      </p>
                    </>
                  ) : (
                    <>
                      <Rate disabled defaultValue={Number(reviewRating)} />
                      <p>{reviewRating}</p>
                    </>
                  )}
                </div>
              </Card>
            </a>
          </Fade>
        )}
      </div>
    );
  }
}

ReviewCard.propTypes = {
  data: PropTypes.object.isRequired,
};
