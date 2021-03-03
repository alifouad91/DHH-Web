import React from 'react';
import { Form, Modal, Card, Icon, Button, message } from 'antd';
import { InviteFriends } from '../icons';
import { TextInput, SaveButton } from './FormElements';
import { emailFriends } from '../services';
import { objectToFormData } from '../utils';

const SubmitForm = ({ onSubmit, form, loading }) => {
	return (
		<Form onSubmit={(e) => onSubmit(e, form)}>
			<div>
				<p>We offer a special referral discount for our clients for introducing our services to a friend or relative. If you refer a friend to book from us, you will get a special discount including, privileged additional services like late checkout, complimentary breakfast, free airport transfer service or exclusive free 1-night stay</p>
				<TextInput
					form={form}
					name="email"
					placeholder="Enter email address of your friend"
					isEmail={true}
					required={true}
				/>
				<p className="small">
					Separate adresses by comma, if you want to send invitations to more than one friend
				</p>
				<SaveButton className="w-100" form={form} saving={loading} saveLabel="SEND INVITATION" />
			</div>
		</Form>
	);
};
const EmailForm = Form.create()(SubmitForm);

export default class InviteModal extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			status: 'invite',
			loading: false,
		};
	}

	handleSubmit = (e, form) => {
		e.preventDefault();
		form.validateFields((err, values) => {
			if (!err) {
				this.sendEmail(values);
			}
		});
	};

	sendEmail = (values) => {
		this.setState({ loading: true });
		emailFriends(
			objectToFormData(values),
			(response) => {
				console.log(response);
				this.setState({ loading: false, status: 'invited' });
			},
			(err) => {
				message.error(err.data.errors[0]);
				this.setState({ loading: false });
			}
		);
	};

	inviteMore = () => {
		this.setState({ status: 'invite' });
	};

	render() {
		const { status, loading } = this.state;
		const { visible, handleCancel } = this.props;
		return (
			<Modal
				centered
				title={null}
				visible={visible}
				footer={null}
				width={500}
				onCancel={(e) => (loading ? null : handleCancel(e))}
				className="popup__invitefriends"
			>
				<div className={`popup  popup__${status}`}>
					<Card
						title={
							<div>
								Invite Friends <Icon component={InviteFriends} />
							</div>
						}
						bordered={false}
					>
						{
							{
								invite: <EmailForm onSubmit={this.handleSubmit} loading={loading} />,
								invited: (
									<div>
										<h5>Invitations sent</h5>
										<p>
											We offer a special referral discount for our clients for introducing our
											services to a friend or relative. If you refer a friend to book from us, you
											will get a special discount including, privileged additional services like
											late checkout, complimentary breakfast, free airport transfer service or
											exclusive free 1-night stay.
										</p>
										<Button type="primary" className="w-100" onClick={this.inviteMore}>
											INVITE MORE
										</Button>
									</div>
								),
							}[status]
						}
					</Card>
				</div>
			</Modal>
		);
	}
}
