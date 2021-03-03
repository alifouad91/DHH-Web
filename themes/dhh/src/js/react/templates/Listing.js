import React from 'react';
import ReactDOM from 'react-dom';
import { Form, Card, Button, Select, Radio, Icon } from 'antd';
import { WhatsApp } from '../icons';
import config from '../config';

const Option = Select.Option;
const RadioButton = Radio.Button;
const RadioGroup = Radio.Group;

class Listing extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			apartmentType: 'flat',
			bedroom: 1,
			totalEarnings: 8736,
			data: {
				flat: [
					{
						bedroom: 1,
						price: 8736,
					},
					{
						bedroom: 2,
						price: 14564,
					},
					{
						bedroom: 3,
						price: 19217,
					},
					{
						bedroom: '4+',
						price: 25954,
					},
				],
				villa: [
					{
						bedroom: 3,
						price: 40178,
					},
					{
						bedroom: 4,
						price: 50854,
					},
					{
						bedroom: '5+',
						price: 65227,
					},
				],
			},
		};
	}

	handleApartmentChange = (e) => {
		const { bedroom, data } = this.state;
		const val = e.target.value;
		this.setState({ apartmentType: val, bedroom: data[val][0].bedroom });
	};

	handleBedroomChange = (e) => {
		const { apartmentType } = this.state;
		this.setState({ bedroom: e, totalEarnings: e * apartmentType });
	};

	calculateEarning = () => {
		const { apartmentType, bedroom } = this.state;
		this.setState({ totalEarnings: apartmentType * bedroom });
	};

	render() {
		const { bedroom, apartmentType, totalEarnings, data } = this.state;
		const bedRoomMap = data[apartmentType];
		const earnings = bedRoomMap.filter((item) => item.bedroom === bedroom)[0];
		return (
			<>
				<Card bordered={false}>
					<h4>Find out how much you can get</h4>
					<Form>
						<Form.Item>
							<RadioGroup onChange={this.handleApartmentChange} value={apartmentType}>
								<RadioButton value="flat">Flat</RadioButton>
								<RadioButton value="villa">Villa</RadioButton>
							</RadioGroup>
						</Form.Item>
						<Form.Item>
							<Select value={bedroom} onChange={this.handleBedroomChange}>
								{bedRoomMap.map((item) => (
									<Option value={item.bedroom}>{item.bedroom} bedroom</Option>
								))}
							</Select>
						</Form.Item>
					</Form>
					<div className="ant-card-footer">
						<div className="ant-card-footer-content">
							<span>You can earn</span>
							<span>aed/month</span>
							<span>{earnings.price}.00</span>
						</div>
						<Button
							type="primary"
							onClick={() => {
								location.href = `${config.BASE_URL}/become-host`;
							}}
						>
							Request to List Property
						</Button>
					</div>
				</Card>
				<a target="_blank" href={document.getElementById('listing-form').getAttribute('data-link')}>
					<Button type="secondary" className="whatsapp-button">
						<Icon component={WhatsApp} /> Or write to WhatsApp
					</Button>
				</a>
			</>
		);
	}
}

if (document.getElementById('listing-form')) {
	ReactDOM.render(<Listing />, document.getElementById('listing-form'));
}
