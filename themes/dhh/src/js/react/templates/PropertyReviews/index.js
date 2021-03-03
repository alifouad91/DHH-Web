import React from "react";
import ReactDOM from "react-dom";
import _ from "lodash";
import { Select, Icon } from "antd";
import Reviews from "./Reviews";

const Option = Select.Option;

class PropertyReview extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      properties: [],
      selectedProperty: "all",
      noOfReviews: 0,
      loading: true
    };
  }

  handleChange = e => {
    this.setState({ selectedProperty: e });
  };

  updateReviews = data => {
    // console.log(_.uniqBy(data, "title"));
    this.setState({
      noOfReviews: data.length,
      properties: _.uniqBy(data, "title"),
      loading: false
    });
  };

  render() {
    const { noOfReviews, properties, selectedProperty, loading } = this.state;
    return (
      <div className="page__section container-fluid">
        <div className="row">
          <div className="col-lg-8 col-lg-offset-1">
            <div className="container-fluid">
              <div className="row">
                <div className="page__section__header">
                  <h1>Reviews of my properties</h1>
                  <span className="sub-text">
                    {noOfReviews} reviews in total
                  </span>
                  <Select
                    defaultValue="all"
                    suffixIcon={<Icon type="caret-down" />}
                    className="w-100 "
                    onChange={this.handleChange}
                    disabled={loading}
                  >
                    <Option key="all">All Properties</Option>
                    {_.map(properties, property => {
                      return (
                        <Option key={property.title} value={property.title}>
                          {property.title}
                        </Option>
                      );
                    })}
                  </Select>
                </div>
              </div>
            </div>
          </div>
          <div className="col-lg-offset-1 col-lg-8 page__propertyreviews__render page__propertyreviews__details ">
            <Reviews
              updateReviews={this.updateReviews}
              selectedProperty={selectedProperty}
            />
          </div>
        </div>
      </div>
    );
  }
}

const $el = $(".page__propertyreviews__render");
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const id = Number($this.data("id"));
    ReactDOM.render(<PropertyReview userId={id} />, el);
  });
}
