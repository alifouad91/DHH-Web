import React from "react";
import _ from "lodash";
import { Divider, Icon } from "antd";

export default class Content extends React.Component {
  render() {
    const { data } = this.props;
    const {
      title,
      location,
      beds,
      bathrooms,
      guests,
      description,
      numberOfRooms,
      apartmentType,
      amenities,
      facilities,
    } = data;
    return (
      <div className="page__propertydetails__content">
        <span className="page__propertydetails__content__apartmentType">
          {apartmentType}
        </span>
        <h2 className="page__propertydetails__content__title">{title}</h2>
        <span className="page__propertydetails__content__location">
          {location}
        </span>
        <Divider />
        <span className="page__propertydetails__content__others">
          {numberOfRooms} bedroom{numberOfRooms > 1 ? "s" : ""} • {guests}{" "}
          guests • {bathrooms} bathroom • {beds} bed
          {beds > 1 ? "s" : ""}
        </span>
        <h6>Description</h6>
        <p>{description}</p>
        <div className="container-fluid">
          <div className="row">
            <div className="col-xs-12 col-sm-6 col-md-8 col-lg-8 pl-0 p-0-m">
              <h6>Facilities</h6>
              <ul className="row page__propertydetails__content__facilities">
                {_.map(amenities, (item) => {
                  return (
                    <li className="col-xs-6 col-sm-6 col-md-6" key={item.id}>
                      {/* TODO: FOLLOW UP BACKEND WHY ICONS ARE NOT UPDATING */}
                      <Icon
                        component={() =>
                          item.icon ? (
                            <img src={item.icon} />
                          ) : (
                            <Icon type="lock" />
                          )
                        }
                      />
                      {item.value}
                    </li>
                  );
                })}
              </ul>
            </div>
            <div className="col-xs-12 col-sm-6 col-md-4 p-0-m">
              <h6>
                Extras <span>(additional cost)</span>
              </h6>
              <ul>
                {_.map(facilities, (item) => {
                  return (
                    <li key={item.id}>
                        <Icon
                            component={() =>
                                item.icon ? (
                                    <img src={item.icon} />
                                ) : (
                                    <Icon type="lock" />
                                )
                            }
                        />
                      {item.value} {item.price ? `(${item.price})` : ""}
                    </li>
                  );
                })}
              </ul>
            </div>
          </div>
        </div>
      </div>
    );
  }
}
