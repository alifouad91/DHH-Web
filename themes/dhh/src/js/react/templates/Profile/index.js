import React from "react";
import ReactDOM from "react-dom";
import moment from "moment";
import { isValidPhoneNumber } from "react-phone-number-input";

import { Form, Spin, message } from "antd";
import { SaveButton } from "../../components/FormElements";
import {
  Header,
  SectionDivider,
  SectionDividerShort,
  PersonalForm,
  Subscription,
  SecurityForm,
} from "./views";
import { objectToFormData } from "../../utils";
import {
  getUserDetails,
  getCountries,
  updateUserDetails,
} from "../../services";
import config from "../../config";

class Profile extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      loading: true,
      updating: false,
      userDetails: {},
      countries: [],
      phone: null,
      phoneIsValid: false,
      isLandlord: $("header").data("group") === "landlord",
    };
  }

  componentWillMount() {
    const { userHash } = this.props;
    let data = new FormData();
    data.append("userHash", userHash);
    // getToken(data, () => {
    getUserDetails((data) => {
      // console.log(data);
      if (data) {
        this.setState({
          loading: false,
          userDetails: data,
          phone: data.phone,
          phoneIsValid: isValidPhoneNumber(data.phone),
        });
      }
    });
    // });

    getCountries((data) => {
      this.setState({ countries: data });
    });
  }

  handleSubmit = (e) => {
    e.preventDefault();
    const { userDetails, phoneIsValid } = this.state;
    const { userId } = this.props;

    this.props.form.validateFields((err, values) => {
      if (!err) {
        let obj = {
          ...userDetails,
          ..._.pickBy(values, _.identity),
          ...{ userId },
        };

        obj.dateOfBirth = moment(`${obj.day}-${obj.month}-${obj.year}`).format(
          config.apiBookingDateFormat
        );
        delete obj.day;
        delete obj.month;
        delete obj.year;
        if (!userDetails.isSocialLogin) {
          delete obj.uEmail;
        }

        if (
          moment().diff(
            moment(obj.dateOfBirth, config.apiBookingDateFormat),
            "years"
          ) < 18
        ) {
          message.error(`You must be 18 years old and above.`);
          return;
        }
        if (!phoneIsValid) {
          message.error(`Please enter a valid phone number`);
          return;
        }
        obj.phone = this.state.phone;
        obj.serviceNews = Number(obj.serviceNews);
        obj.dubaiAdvices = Number(obj.dubaiAdvices);
        obj.relatedProposal = Number(obj.relatedProposal);
        if (moment(obj.passportValidTill).isValid()) {
          obj.passportValidTill = moment(obj.passportValidTill).format(
            "YYYY-MM-DD"
          );
        }

        this.setState({ updating: true });
        updateUserDetails(
          objectToFormData(obj),
          (data) => {
            // console.log(data);
            this.setState({ updating: false, userDetails: data });
            message.success(`Successfully updated your profile`);
          },
          (err) => {
            message.error("Something went wrong");
            this.setState({ updating: false });
          }
        );
      }
    });
  };

  handlePhoneChange = (phone) => {
    // console.log(isValidPhoneNumber(phone))
    this.setState({ phone, phoneIsValid: isValidPhoneNumber(phone) });
  };

  handleLogout = (e) => {
    localStorage.removeItem("access_token");
    location.href = `${config.BASE_URL}/login/logout`;
  };

  render() {
    const { form } = this.props;
    const {
      loading,
      userDetails,
      countries,
      updating,
      phone,
      isLandlord,
    } = this.state;

    return (
      <div className="container-fluid">
        <Header
          isLandlord={isLandlord}
          handleLogout={this.handleLogout}
          details={userDetails}
        />
        <SectionDivider />
        <Spin
          spinning={loading || updating}
          tip={`${updating ? "Updating" : "Getting"} User Details`}
        >
          <Form className="page__profile__form" onSubmit={this.handleSubmit}>
            <PersonalForm
              form={form}
              details={userDetails}
              countries={countries}
              handlePhoneChange={this.handlePhoneChange}
              phoneNumber={phone}
            />
            <SectionDividerShort />
            <Subscription form={form} details={userDetails} />
            {userDetails.isSocialLogin ? null : (
              <SecurityForm form={form} details={userDetails} />
            )}
            <div className="row">
              <div className="col-md-5 col-md-offset-3">
                <SaveButton saveLabel="Save Changes" />
              </div>
            </div>
          </Form>
        </Spin>
      </div>
    );
  }
}

const ProfileForm = Form.create()(Profile);

const $el = $(".page__profile");
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const id = $this.data("id");
    const uid = $this.data("uid");
    ReactDOM.render(<ProfileForm userHash={id} userId={uid} />, el);
  });
}
