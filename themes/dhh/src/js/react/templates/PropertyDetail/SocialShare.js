import React from 'react';
import { Breadcrumb, message } from 'antd';

export default class SocialShare extends React.Component {
	copyToClipboard = url => {
		const textField = document.createElement('textarea');
		textField.innerText = url;
		document.body.appendChild(textField);
		textField.select();
		document.execCommand('copy');
		textField.remove();
		message.success('Link copied to clipboard');
	};

	handleShare = (social, e) => {
		e.preventDefault();
		// console.log(social);
		switch (social) {
			case 'facebook':
				window.open(
					`https://www.facebook.com/sharer/sharer.php?u=${document.URL}`,
					'facebook-popup',
					'height=350,width=600'
				);
				break;
			case 'twitter':
				window.open(`https://twitter.com/share?url=${document.URL}`, 'twitter-popup', 'height=350,width=600');
				break;
			case 'whatsapp':
				window.open(
					`https://api.whatsapp.com/send?text=${document.URL}`,
					'whatsapp-popup',
					'height=350,width=600'
				);
				break;
			case 'copy':
				this.copyToClipboard(document.URL);
				// location.href = `mailto:name@email.com?subject=Driven Holiday Homes - ${this.props.subject}&body=${document.URL}`;
				break;
		}
	};
	render() {
		return (
			<div className="breadcrumb__share">
				<span className="breadcrumb__share__title">SHARE</span>
				<Breadcrumb>
					<Breadcrumb.Item>
						<a onClick={this.handleShare.bind(this, 'facebook')}>Facebook</a>
					</Breadcrumb.Item>
					<Breadcrumb.Item>
						<a onClick={this.handleShare.bind(this, 'twitter')}>Twitter</a>
					</Breadcrumb.Item>
					<Breadcrumb.Item>
						<a onClick={this.handleShare.bind(this, 'whatsapp')}>Whatsapp</a>
					</Breadcrumb.Item>
					<Breadcrumb.Item>
						<a onClick={this.handleShare.bind(this, 'copy')}>Copy Link</a>
					</Breadcrumb.Item>
				</Breadcrumb>
			</div>
		);
	}
}
