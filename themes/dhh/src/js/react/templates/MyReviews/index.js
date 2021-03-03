import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';
import { Skeleton, Spin, Icon } from 'antd';
import Sort from './Sort';
import MyReviewCard from '../../components/MyReviewCard';
import EmptyMessage from '../../components/EmptyMessage';
import ReviewModal from '../../components/ReviewModal';
import { ReviewsEmpty } from '../../icons';
import { getMyReviews } from '../../services';

export default class MyReviews extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			items: {},
			data: {},
			status: null,
			reviews: [],
			loading: true,
			spinning: false,
			sorting: false,
			activeFilter: '',
			ascending: false,
			visible: false,
			apiModified: false,
		};
	}

	componentWillMount() {
		this.getReviews();
	}

	getReviews = () => {
		const { userId } = this.props;
		getMyReviews(
			{ userId },
			data => {
				this.setState({
					loading: false,
					spinning: false,
					items: data,
					reviews: data.reviews,
				});
				$('.page__myreviews')
					.find('.sub-text')
					.html(`${data.totalReviews} reviews in total`);
			},
			err => {
				console.log(err);
			}
		);
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

	handleCancel = () => {
		if (this.state.apiModified) {
			this.clearSort();
			this.setState({ spinning: true });
			this.getReviews();
		}
		setTimeout(() => {
			this.setState({ status: null, data: null });
		}, 500);
		this.setState({ visible: false });
	};

	modifyApi = () => {
		this.setState({ apiModified: true });
	};

	changeStatus = (status, data) => {
		// console.log(status, data);
		this.setState({ status, data, visible: true });
	};

	render() {
		const { activeFilter, ascending, loading, reviews, visible, status, data, spinning } = this.state;
		return (
			<Spin spinning={spinning} tip="Updating Reviews">
				<div className={`container-fluid ${loading && 'loading'}`}>
					<Skeleton active loading={loading}>
						<ReviewModal
							status={status}
							visible={visible}
							handleCancel={this.handleCancel}
							data={data}
							changeStatus={this.changeStatus}
							modifyApi={this.modifyApi}
						/>
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
										return (
											<MyReviewCard item={item} key={item.bID} changeStatus={this.changeStatus} />
										);
									})}
								</div>
							</>
						) : (
							<EmptyMessage
								message="You have not written any reviews yet"
								image={<Icon component={ReviewsEmpty} />}
							/>
						)}
					</Skeleton>
				</div>
			</Spin>
		);
	}
}

const $el = $('.page__myreviews__render');
if ($el) {
	$el.each((index, el) => {
		const $this = $(el);
		const id = Number($this.data('id'));
		ReactDOM.render(<MyReviews userId={id} />, el);
	});
}
