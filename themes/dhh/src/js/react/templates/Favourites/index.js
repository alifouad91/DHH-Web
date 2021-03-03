import React from "react";
import ReactDOM from "react-dom";
import { Spin } from "antd";
import PropertyCard from "../../components/PropertyCard";
import { generateLoadingObject } from "../../utils";
import { getFavourites } from "../../services";

export default class Favourites extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: generateLoadingObject(3),
      loading: true,
      spinning: false
    };
  }

  componentWillMount() {
    this.getList();
  }

  getList = out => {
    const { userId } = this.props;
    if (out) {
      this.setState({ spinning: true });
    }
    getFavourites(
      {
        userId
      },
      data => {
        this.setState({ items: data });
        if (out) {
          this.setState({ spinning: false });
        }
      },
      err => {
        console.log(err);
      }
    );
  };

  render() {
    const { items, spinning } = this.state;
    return (
      <div className="container">
        <div className="row">
          <Spin spinning={spinning} tip="Refreshing Items.">
            {items.length ? (
              _.map(items, (item, index) => {
                return (
                  <PropertyCard
                    index={index}
                    key={index}
                    data={item}
                    favHandle={this.getList}
                    itemsToShow={3}
                  />
                );
              })
            ) : (
              <div className="col-lg-12">
                <h3>No Items Available</h3>
              </div>
            )}
          </Spin>
        </div>
      </div>
    );
  }
}
const $el = $(".page__favourite__render");
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const id = Number($this.data("id"));
    ReactDOM.render(<Favourites userId={id} />, el);
  });
}
