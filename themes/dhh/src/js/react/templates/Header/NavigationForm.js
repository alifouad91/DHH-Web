import React from 'react';
import ReactDOM from 'react-dom';
// import { Spin, Button, Icon } from "antd";
import CurrencyDropdown from './CurrencyDropdown';
import UserDropdown from './UserDropdown';
import Notification from './Notification';
// import { getCurrencies } from "../../services";
import { currencies } from '../../constants';

class NavigationForm extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			isLoading: !props.loggedin,
			isLoggedIn: props.loggedin
		};
	}

	componentWillMount() {}

	render() {
		const { isLoggedIn } = this.state;
		const { userDetails } = this.props;

		return (
			<React.Fragment>
				<div className="navigation__form__currency">
					<span
						id="user-current-currency"
						className="navigation__form__currency__label"
						data-currency={currencies.filter(val => val.locale === userDetails.currentCurrency)[0].val}
					>
						Currency
					</span>
					<CurrencyDropdown selectedCurrency={userDetails.currentCurrency} />
				</div>
				{isLoggedIn && (
					<React.Fragment>
						{/* <Icon
              component={Notification}
              className="navigation__form__icons"
            /> */}
						<Notification />
						<UserDropdown userDetails={userDetails} />
					</React.Fragment>
				)}
			</React.Fragment>
		);
	}
}

const $el = $('#navigation__form');
if ($el) {
	$el.each((index, el) => {
		const $this = $(el);
		const loggedin = $this.data('loggedin');
		const bookingCount = $this.data('bcount');
		const reviewCount = $this.data('rcount');
		const favouriteCount = $this.data('fcount');
		const fullName = $this.data('name');
		const currentCurrency = $this.data('currentcurrency');
		const userGroup = $this.data('group');
		const userBadge = $this.data('badge');
		const avatar = $this.data('avatar');
		const obj = {
			bookingCount,
			reviewCount,
			favouriteCount,
			fullName,
			currentCurrency,
			userGroup,
			userBadge,
			avatar
		};
		ReactDOM.render(<NavigationForm userDetails={obj} loggedin={loggedin} />, el);
	});
}
