import React from "react";
import ReactDOM from "react-dom";
import { Skeleton, Spin } from "antd";
import BookingCard from "../../components/BookingCard";
import EmptyMessage from "../../components/EmptyMessage";
import ReviewModal from "../../components/ReviewModal";
import { generateLoadingObject } from "../../utils";
import { getMyBookings, getCCMToken } from "../../services";

export default class MyBookings extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: generateLoadingObject(3),
      loading: true,
      spinning: false,
      visible: false,
      status: null,
      data: null,
      apiModified: false,
      ccm_token: null
    };
  }

  componentWillMount() {
    this.getBookings();
    this.getToken();
  }

  getToken = () => {
    getCCMToken(
      data => {
        this.setState({ ccm_token: data.ccm_token });
      },
      err => {
        setTimeout(() => {
          this.getToken();
        }, 3000);
      }
    );
  };

  getBookings = () => {
    getMyBookings(
      {},
      data => {
        this.setState({
          items: data,
          loading: false,
          apiModified: false,
          spinning: false
        });
        $(".page__mybookings")
          .find(".sub-text")
          .html(`${data.upcomingTotal} upcoming  •  ${data.pastTotal} past`);
      },
      err => {
        console.log(err);
      }
    );
  };

  changeStatus = (status, data) => {
    this.setState({ status, data, visible: true });
  };

  handleCancel = () => {
    this.setState({ visible: false });
    if (this.state.apiModified) {
      this.setState({ spinning: true });
      this.getBookings();
    }
    setTimeout(() => {
      this.setState({ status: null, data: null });
    }, 500);
  };

  modifyApi = () => {
    this.setState({ apiModified: true });
  };

  render() {
    const {
      items,
      loading,
      visible,
      status,
      data,
      spinning,
      ccm_token
    } = this.state;

    return (
      <div className="container-fluid">
        <Spin spinning={spinning} tip="Updating bookings please wait">
          <ReviewModal
            status={status}
            visible={visible}
            handleCancel={this.handleCancel}
            data={data}
            changeStatus={this.changeStatus}
            modifyApi={this.modifyApi}
            ccm_token={ccm_token}
          />
          <h6>ONGOING</h6>
          <Skeleton active loading={loading}>
            <div className="row">
              {items.inProgress && items.inProgress.length ? (
                _.map(items.inProgress, item => {
                  return (
                    <BookingCard
                      changeStatus={this.changeStatus}
                      upcoming={true}
                      ongoing={true}
                      key={item.bID}
                      data={item}
                    />
                  );
                })
              ) : (
                <EmptyMessage message="No Ongoing Bookings" />
              )}
            </div>
          </Skeleton>
          <h6 style={{ marginTop: 53 }}>UPCOMING</h6>
          <Skeleton active loading={loading}>
            <div className="row">
              {items.upcomingTotal > 0 ? (
                _.map(items.upcomingBookings, item => {
                  return (
                    <BookingCard
                      changeStatus={this.changeStatus}
                      upcoming={true}
                      key={item.bID}
                      data={item}
                    />
                  );
                })
              ) : (
                <EmptyMessage message="No Upcoming Bookings" />
              )}
            </div>
          </Skeleton>
          <h6 style={{ marginTop: 53 }}>PAST</h6>
          <Skeleton active loading={loading}>
            <div className="row">
              {items.pastTotal > 0 ? (
                _.map(items.pastBookings, item => {
                  return (
                    <BookingCard
                      changeStatus={this.changeStatus}
                      upcoming={false}
                      key={item.bID}
                      data={item}
                    />
                  );
                })
              ) : (
                <EmptyMessage message="No Past Bookings" />
              )}
            </div>
          </Skeleton>
        </Spin>
      </div>
    );
  }
}
const $el = $(".page__mybookings__render");
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const id = Number($this.data("id"));
    ReactDOM.render(<MyBookings userId={id} />, el);
  });
}
