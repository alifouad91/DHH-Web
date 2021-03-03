import React from 'react';
import { message, Modal } from 'antd';
import { RateYourStay, ReviewAdded, ReviewEdit, AmendBooking, Amended, CancelBooking } from './views';
import { addReview, updateReview, submitForm } from '../../services';
import config from '../../config';
import { objectToFormData } from '../../utils';

export default class Popups extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			hovered: 5,
			loading: false,
			limitReached: false,
		};
	}

	componentWillReceiveProps(nextProps) {
		const { data } = nextProps;
		if (data && data.myRatings) {
			this.setState({ hovered: data.myRatings });
		}
	}
	handleSubmit = (e, form, status) => {
		e.preventDefault();
		form.validateFields((err, values) => {
			if (!err) {
				this.setState({ loading: true });
				switch (status) {
					case 'rate':
						this.add(values);
						break;
					case 'edit':
						this.update(values);
						break;
					case 'amend':
						this.amend(values);
						break;
					case 'cancel':
						this.cancel(values);
						break;
				}
			}
		});
	};

	onRateChange = (e) => {
		if (e && e !== this.state.hovered) {
			this.setState({ hovered: e });
		}
	};

	add = (values) => {
		const { data, changeStatus, modifyApi } = this.props;
		values.bookingNo = data.bookingNo;
		addReview(
			objectToFormData(values),
			(response) => {
				this.setState({ loading: false, rating: values.reviewRating });
				data.rID = response.rID;
				data.myRatings = values.reviewRating;
				data.myComments = values.reviewComment;
				changeStatus('added', data);
				modifyApi();
			},
			(err) => {
				console.log(err);
			}
		);
	};

	update = (values) => {
		const { data, changeStatus, modifyApi } = this.props;
		values.rID = data.rID;
		updateReview(
			objectToFormData(values),
			(response) => {
				this.setState({ loading: false, rating: values.reviewRating });
				data.myRatings = values.reviewRating;
				data.myComments = values.reviewComment;
				changeStatus('updated', data);
				modifyApi();
			},
			(err) => {
				console.log(err);
				message.error(err.data.errors[0]);
				this.setState({
					limitReached: true,
					loading: false,
				});

				this.props.handleCancel();
			}
		);
	};

	amend = (values) => {
		const { data, changeStatus, ccm_token } = this.props;
		const { amendBookingFields, formValues } = config;
		values.ccm_token = ccm_token;
		values.formID = amendBookingFields.formID;
		values.bID = amendBookingFields.bID;
		values.form_type = 'amend';
		values.bookingNo = data.bookingNo;
		values[amendBookingFields.propertyName] = data.title;
		values[amendBookingFields.bookingDate] = data.startDate;
		values[amendBookingFields.bookingEndDate] = data.endDate;
		values[amendBookingFields.name] = document.getElementById('navigation__form').dataset.name;
		values[amendBookingFields.email] = document.getElementById('navigation__form').dataset.email;
		values[amendBookingFields.phone] = document.getElementById('navigation__form').dataset.phone;
		submitForm(
			objectToFormData({ ...values, ...formValues }),
			(response) => {
				this.setState({ loading: false });
				data.message = response.message;
				changeStatus('amended', data);
			},
			(err) => {
				console.log(err);
			}
		);
	};

	cancel = (values) => {
		const { data, changeStatus, ccm_token } = this.props;
		const { cancelBookingFields, formValues } = config;
		values.ccm_token = ccm_token;
		values.formID = cancelBookingFields.formID;
		values.bID = cancelBookingFields.bID;
		values.bookingNo = data.bookingNo;
		values.form_type = 'cancel';
		values[cancelBookingFields.propertyName] = data.title;
		values[cancelBookingFields.bookingDate] = data.startDate;

		values[cancelBookingFields.bookingEndDate] = data.endDate;
		values[cancelBookingFields.name] = document.getElementById('navigation__form').dataset.name;
		values[cancelBookingFields.email] = document.getElementById('navigation__form').dataset.email;
		values[cancelBookingFields.phone] = document.getElementById('navigation__form').dataset.phone;
		submitForm(
			objectToFormData({ ...values, ...formValues }),
			(response) => {
				this.setState({ loading: false });
				data.message = response.message;
				changeStatus('canceled', data);
			},
			(err) => {
				console.log(err);
			}
		);
	};

	reEdit = () => {
		const { changeStatus, data } = this.props;
		changeStatus('edit', data);
	};

	render() {
		const { hovered, limitReached, loading } = this.state;
		const { visible, status, handleCancel, data, changeStatus } = this.props;
		return (
			<Modal
				centered
				title={null}
				visible={visible}
				footer={null}
				width={420}
				onCancel={(e) => (loading ? null : handleCancel(e))}
			>
				<div className={`popup popup__${status}`}>
					{
						{
							rate: (
								<RateYourStay
									handleSubmit={this.handleSubmit}
									onRateChange={this.onRateChange}
									data={data}
									hovered={hovered}
									loading={loading}
								/>
							),
							added: (
								<ReviewAdded
									hovered={hovered}
									data={data}
									handleCancel={handleCancel}
									reEdit={this.reEdit}
								/>
							),
							updated: (
								<ReviewAdded
									hovered={hovered}
									updated={true}
									data={data}
									handleCancel={handleCancel}
									reEdit={this.reEdit}
								/>
							),
							edit: (
								<ReviewEdit
									handleSubmit={this.handleSubmit}
									hovered={hovered}
									onRateChange={this.onRateChange}
									data={data}
									limitReached={limitReached}
									loading={loading}
									handleCancel={handleCancel}
								/>
							),
							amend: (
								<AmendBooking
									handleSubmit={this.handleSubmit}
									data={data}
									loading={loading}
									handleCancel={handleCancel}
								/>
							),
							amended: <Amended data={data} handleCancel={handleCancel} />,
							cancel: (
								<CancelBooking
									handleSubmit={this.handleSubmit}
									data={data}
									loading={loading}
									handleCancel={handleCancel}
								/>
							),
							canceled: <Amended data={data} handleCancel={handleCancel} canceled={true} />,
						}[status]
					}
				</div>
			</Modal>
		);
	}
}
