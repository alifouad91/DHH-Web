import React from 'react';
import { Radio } from 'antd';
import _ from 'lodash';
import ReviewCard from '../../components/ReviewCard';
import EmptyMessage from '../../components/EmptyMessage';
import Rate from '../../components/Rate';
import { filterReviews, createReviewTabOject } from '../../utils';

const RadioButton = Radio.Button;
const RadioGroup = Radio.Group;

export default class Reviews extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			reviewItems: [],
			tabOject: {},
		};
	}

	componentDidMount() {
		const { reviews } = this.props;
		this.setState({
			reviewItems: reviews,
			tabObject: createReviewTabOject(reviews),
		});
	}
	handleReviewChange = e => {
		const { reviews } = this.props;
		const val = e.target.value;
		this.setState({
			reviewItems: val === 'all' ? reviews : filterReviews(reviews, val),
		});
	};
	render() {
		const { avgRating } = this.props;
		const { reviewItems, tabObject } = this.state;
		const ratingTab = _.range(1, 6);
		// console.log(tabObject);
		return (
			<div className="page__propertydetails__reviews">
				<div className="page__propertydetails__reviews__title">
					<h4>
						Reviews from guests <span>{reviewItems.length}</span>
					</h4>
					<div>
						<Rate value={Number(avgRating)} disabled large />
						<h5>{Number(avgRating)}</h5>
					</div>
				</div>
				<div>
					<RadioGroup onChange={this.handleReviewChange} defaultValue="all">
						<RadioButton value="all">All Reviews</RadioButton>
						{reviewItems.length
							? _.map(ratingTab, val => {
									return (
										<RadioButton value={val} key={val} disabled={!tabObject[val]}>
											<Rate disabled value={val} /> · {tabObject[val] ? tabObject[val] : 0}
										</RadioButton>
									);
							  })
							: null}
					</RadioGroup>
				</div>
				<div className="container-fluid">
					<div className="row">
						{reviewItems.length ? (
							_.map(reviewItems, (item, index) => {
								return (
									<ReviewCard
										key={index}
										index={index}
										data={item}
										full={true}
										itemsToShow={1}
										propertyDetail={true}
									/>
								);
							})
						) : (
							<EmptyMessage message="No Available Reviews" />
						)}
					</div>
				</div>
			</div>
		);
	}
}
