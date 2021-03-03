import React from 'react';
import moment from 'moment';
import { Card, Icon } from 'antd';
import ImageFit from './ImageFit';
import Rate from './Rate';
import { isDateSameYear, isDateSameMonth } from '../utils';
import config from '../config';

export default class BookingCard extends React.Component {
	// handleAmend = (date, title) => {
	//   $("#amend-booking-form")
	//     .find(".property")
	//     .html(`${title} · ${date}`);
	//   $.fancybox.open({
	//     src: "#amend-booking-form",
	//     opts: {
	//       touch: false
	//     }
	//   });
	// };

	// handleCancel = (date, title) => {
	//   $("#cancel-booking-form")
	//     .find(".property")
	//     .html(`${title} · ${date}`);
	//   $.fancybox.open({
	//     src: "#cancel-booking-form",
	//     opts: {
	//       touch: false
	//     }
	//   });
	// };

	render() {
		const { data, upcoming, ongoing, changeStatus } = this.props;
		const {
			thumbnail,
			title,
			location,
			avgRating,
			reviews,
			startDate,
			endDate,
			guests,
			totalNights,
			perDayPrice,
			rID,
			myRatings,
			path,
			editable,
			total,
		} = data;

		const sDate = moment(startDate);
		const eDate = moment(endDate);
		const sameYear = isDateSameYear(startDate, endDate);
		const sameMonth = isDateSameMonth(startDate, endDate);
		// {eDate.diff(sDate, "days")} nights
		const mDate = `${sDate.format('MMM D')} ${sameYear ? '' : `, ${sDate.format('YYYY')}`} - ${
			sameMonth ? eDate.format('D') : eDate.format('MMM D')
		},  ${eDate.format('YYYY')}`;
		// console.log(data);
		return (
			<div className="col-xs-12 col-sm-12 col-md-12 pl-0 pr-0 booking__card">
				<Card bordered={false} hoverable={true}>
					<ImageFit src={thumbnail} style={{ objectFit: 'cover' }} />
					<div className="booking__card__details">
						<div className={`booking__card__date ${!upcoming && 'done'}`}>
							<span className="date-1">
								{sDate.format('MMM D')}
								<em>{sameYear ? '' : `, ${sDate.format('YYYY')}`}</em>
							</span>
							<em>{` - `}</em>
							<span className="date-2">
								{sameMonth ? eDate.format('D') : eDate.format('MMM D')}{' '}
								<em>{`, ${eDate.format('YYYY')}`}</em>
							</span>
						</div>
						<h5>{title}</h5>
						<p>{location}</p>
						<div className="property__card__rating">
							<Rate disabled value={Number(avgRating)} />
							<small>· {reviews}</small> <Icon type="user" className="guest" />
							<span>{guests} guests</span>
						</div>
						<div className="booking__card__price">
							<span>{totalNights} nights for</span>
							<span>{total}</span>
						</div>
						<div className="booking__card__actions">
							{upcoming ? (
								!ongoing ? (
									<div>
										<a
											className="secondary"
											onClick={() => {
												data.mDate = mDate;
												changeStatus('amend', data);
											}}
										>
											Amend
										</a>
										<a
											className="secondary"
											onClick={() => {
												data.mDate = mDate;
												changeStatus('cancel', data);
											}}
										>
											Cancel
										</a>
										<a href={`${config.BASE_URL}/properties/${path}`} className="secondary">
											View Property
										</a>
									</div>
								) : (
									<a href={`${config.BASE_URL}/properties/${path}`} className="secondary">
										View Property
									</a>
								)
							) : (
								<div>
									{rID ? (
										<>
											<span className="secondary">
												{' '}
												Your rating: {myRatings} star{myRatings > 1 ? 's' : ''}
											</span>
											<a onClick={() => changeStatus('edit', data)} hidden={!editable}>
												Edit review
											</a>
										</>
									) : (
										<a onClick={() => changeStatus('rate', data)}>Rate Booking</a>
									)}
									<a href={`${config.BASE_URL}/properties/${path}`} style={{ marginLeft: 12 }}>
										Book again
									</a>
								</div>
							)}
						</div>
					</div>
				</Card>
			</div>
		);
	}
}
