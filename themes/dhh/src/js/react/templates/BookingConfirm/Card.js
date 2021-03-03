import React from "react";
import moment from "moment";
import _ from "lodash";
import { Card, Icon } from "antd";
import config from "../../config";
import { ArrowLong } from "../../icons";
import ImageFit from "../../components/ImageFit";
import Rate from "../../components/Rate";

const CardTitle = (props) => {
  const { booking } = props;

  const { thumbnail, name, location, avgRating, reviews } = booking;
  return (
    <div className="page__reviewbooking__details__card__heading">
      <ImageFit alt={name} style={{ objectFit: "cover" }} src={thumbnail} />
      <div>
        <h6>{name}</h6>
        <p>{location}</p>
        <div className="property__card__rating">
          <Rate disabled value={avgRating} />
          <small>· {reviews}</small>
        </div>
      </div>
    </div>
  );
};

export default class PropertyCard extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const { propertyDetails, loading } = this.props;
    const { booking } = propertyDetails;
    const sDate = (booking && moment(booking.startDate)) || null;
    const eDate = (booking && moment(booking.endDate)) || null;
    return (
      <Card
        bordered={false}
        loading={loading}
        className={`page__reviewbooking__details__card ${!loading && "done"}`}
        title={booking && <CardTitle booking={booking} />}
      >
        <div className="dates">
          <div>
            <span>{booking && sDate.format(config.dateFormat)}</span>
            <Icon component={ArrowLong} />
            <span>{booking && eDate.format(config.dateFormat)}</span>
          </div>
          <div>
            {booking && (
              <span>
                {eDate.diff(sDate, "days")} nights • {booking && booking.guests}{" "}
                guests
              </span>
            )}
          </div>
        </div>
        {booking && booking.bookingAdditionFacilities.length ? (
          <ul className="additional">
            {_.map(booking.bookingAdditionFacilities, (facility) => {
              return (
                <li key={facility.id}>
                  <Icon type="sliders" theme="filled" />
                  <span className="label">{facility.value}</span>
                  <span className="price">+ {facility.price}</span>
                </li>
              );
            })}
          </ul>
        ) : null}
        <div className="computation">
          <div>
            <span>Total Amount</span>
            <span>{booking && booking.total}</span>
          </div>
          <div>
            <span>All fees and VAT already included</span>
          </div>
        </div>
      </Card>
    );
  }
}
