import React from 'react';
import _ from 'lodash';
import { Skeleton, Icon } from 'antd';
import Sort from './Sort';
import PropertyReviewCard from '../../components/PropertyReviewCard';
import EmptyMessage from '../../components/EmptyMessage';
import { ReviewsEmpty } from '../../icons';
import { getPropertyReviews } from '../../services';

// const sample = {
//   totalReviews: 1,
//   reviews: [
//     {
//       bID: "1",
//       pID: "1",
//       thumbnail: "http://localhost/dhh/files/properties/1_1549802794.png",
//       title: "Swan Apartments-3",
//       caption: "Wonderful View At Swan Appartments",
//       location: "Dubai",
//       perDayPrice: "250.0100",
//       startDate: "2019-02-25",
//       endDate: "2019-02-28",
//       guests: "3",
//       totalNights: "3",
//       reviewRating: "5",
//       reviewComment: "Awesome!",
//       createdAt: "2019-01-01",
//       userName: "admin",
//       profilePic: ""
//     },
//     {
//       bID: "2",
//       pID: "2",
//       thumbnail: "http://localhost/dhh/files/properties/1_1549802794.png",
//       title: "Swan Apartments",
//       caption: "Wonderful View At Swan Appartments",
//       location: "Dubai",
//       perDayPrice: "250.0100",
//       startDate: "2019-02-25",
//       endDate: "2019-02-28",
//       guests: "3",
//       totalNights: "3",
//       reviewRating: "5",
//       reviewComment: "Awesome!",
//       createdAt: "2019-01-01",
//       userName: "admin",
//       profilePic: ""
//     }
//   ]
// };

export default class Reviews extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: {},
      reviews: [],
      loading: true,
      sorting: false,
      activeFilter: '',
      ascending: false,
    };
  }

  componentWillMount() {
    this.getReviews();
  }

  componentWillReceiveProps(nextProps) {
    if (this.props.selectedProperty !== nextProps.selectedProperty) {
      this.filterReviews(nextProps.selectedProperty);
    }
  }

  getReviews = () => {
    const { updateReviews } = this.props;
    getPropertyReviews(
      data => {
        this.setState({
          loading: false,
          items: data,
          reviews: data.reviews,
        });
        updateReviews(data.reviews);
      },
      err => {
        console.log(err);
      },
    );
  };

  filterReviews = prop => {
    const { items } = this.state;
    this.clearSort();
    this.setState({
      reviews:
        prop === 'all'
          ? items.reviews
          : _.filter(items.reviews, review => {
              return review.title === prop;
            }),
    });
    // this.setState({ reviews: items });
  };

  handleSort = key => {
    const { ascending, activeFilter, reviews } = this.state;

    let sorted = _.sortBy(reviews, property => property[key]);

    if (key === activeFilter) {
      this.setState({ ascending: !ascending });
    } else {
      this.setState({ activeFilter: key, ascending: true });
    }
    if (!this.state.ascending) {
      sorted = _.reverse(sorted);
    }
    this.setState({ reviews: sorted });
  };

  clearSort = () => {
    const { items } = this.state;
    this.setState({ reviews: items.reviews, activeFilter: '' });
  };

  render() {
    const { activeFilter, ascending, loading, reviews } = this.state;
    return (
      <div className={`container-fluid ${loading && 'loading'}`}>
        <Skeleton active loading={loading}>
          {reviews.length ? (
            <>
              <div className="row">
                <Sort
                  activeFilter={activeFilter}
                  ascending={ascending}
                  loading={loading}
                  handleSort={this.handleSort}
                  clearSort={this.clearSort}
                />
              </div>
              <div className="row">
                {_.map(reviews, item => {
                  return <PropertyReviewCard item={item} key={item.bID} />;
                })}
              </div>
            </>
          ) : (
            <EmptyMessage
              message="You have not received any reviews yet"
              image={<Icon component={ReviewsEmpty} />}
            />
          )}
        </Skeleton>
      </div>
    );
  }
}
