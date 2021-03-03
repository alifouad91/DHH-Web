import React from "react";
import _ from "lodash";
import moment from "moment";
import { Avatar, Divider } from "antd";
import "react-phone-number-input/style.css";
import PhoneInput from "react-phone-number-input";
import Upload from "./Upload";
import {
  TextInput,
  SelectInput,
  SwitchInput,
  DatePickerInput,
} from "../../components/FormElements";
import { generateYears, generateDays, generateMonths } from "../../utils";

export const SectionDivider = () => {
  return (
    <div className="row">
      <div className="col-md-10 col-md-offset-1">
        <Divider />
      </div>
    </div>
  );
};

export const SectionDividerShort = () => {
  return (
    <div className="row">
      <div className="col-md-7 col-md-offset-1">
        <Divider />
      </div>
    </div>
  );
};

export const Header = (props) => {
  const { handleLogout, details, isLandlord } = props;
  return (
    <div className="row">
      <div className="col-md-10 col-md-offset-1 page__profile__header">
        {details.uEmail ? (
          <>
            <Upload fullName={details.fullName} avatar={details.avatar} />
            {/* <Avatar size={60} icon="user" />{" "} */}
          </>
        ) : null}
        {details.uEmail ? (
          <div>
            <h1>
              {details.fullName} <span>profile</span>
            </h1>
            {isLandlord ? null : (
              <div className="designation">
                <span>
                  {details.userBadge ? `${details.userBadge} • ` : null}
                </span>
                <span>
                  {details.bookingCount ? details.bookingCount : "No"} bookings
                </span>
              </div>
            )}
          </div>
        ) : null}
        <a onClick={handleLogout}>Logout</a>
      </div>
    </div>
  );
};

const FormDivider = () => {
  return (
    <div className="row">
      <div className="col-md-offset-3 col-md-5">
        <Divider />
      </div>
    </div>
  );
};

export const PersonalForm = (props) => {
  const { form, details, countries, handlePhoneChange, phoneNumber } = props;
  const dob = moment(details.dateOfBirth);
  const day = dob.format("D");
  const month = dob.format("MMMM");
  const year = dob.format("YYYY");
  return (
    <div className="container-fluid">
      <div className="row">
        <div className="col-md-2 col-md-offset-1">
          <h5>Personal</h5>
        </div>
        <div className="col-md-2">
          <span>Name in Passport</span>
        </div>
        <div className="col-md-3">
          <TextInput
            form={form}
            name="fullName"
            required={true}
            initialValue={details.fullName}
          />
        </div>
      </div>
      <div className="row">
        <div className="col-md-2 col-md-offset-3">
          <span>Birth date</span>
        </div>
        <div className="col-md-3">
          <div className="container-fluid pl-0">
            <div className="col-xs-4 pl-0">
              <SelectInput
                form={form}
                name="day"
                placeholder="Day"
                required={true}
                initialValue={details.dateOfBirth ? day : null}
                list={generateDays()}
                // initialValue={details.nationality}
              />
            </div>
            <div className="col-xs-4 pl-0">
              <SelectInput
                form={form}
                name="month"
                placeholder="Month"
                required={true}
                initialValue={details.dateOfBirth ? month : null}
                list={generateMonths()}
                // initialValue={details.nationality}
              />
            </div>
            <div className="col-xs-4 pl-0">
              <SelectInput
                form={form}
                name="year"
                placeholder="Year"
                required={true}
                initialValue={details.dateOfBirth ? year : null}
                list={generateYears()}
                // initialValue={details.nationality}
              />
            </div>
          </div>
        </div>
      </div>
      <div className="row">
        <div className="col-md-2 col-md-offset-3">
          <span>Nationality</span>
        </div>
        <div className="col-md-3">
          <SelectInput
            form={form}
            name="nationality"
            list={countries}
            required={true}
            showSearch={true}
            initialValue={details.nationality}
          />
        </div>
      </div>
      <FormDivider />
      <div className="row">
        <div className="col-md-2 col-md-offset-3">
          <span>Email</span>
        </div>
        <div className="col-md-3">
          <TextInput
            form={form}
            name="uEmail"
            type="email"
            required={true}
            disabled={!details.isSocialLogin}
            initialValue={details.uEmail}
          />
        </div>
      </div>
      <div className="row">
        <div className="col-md-2 col-md-offset-3">
          <span>Phone Number</span>
        </div>
        <div className="col-md-3">
          <PhoneInput
            country="AE"
            placeholder="Enter phone number"
            value={phoneNumber}
            onChange={(value) => handlePhoneChange(value)}
          />
        </div>
      </div>
      <FormDivider />
      <div className="row">
        <div className="col-xs-0 col-md-1 col-md-offset-2">
          <span className="optional">(optional)</span>
        </div>
        <div className="col-md-2">
          <span>Passport Number</span>
        </div>
        <div className="col-md-3">
          <TextInput
            form={form}
            name="passportNo"
            initialValue={details.passportNo}
          />
        </div>
      </div>
      <div className="row">
        <div className="col-xs-0 col-md-1 col-md-offset-2">
          <span className="optional">(optional)</span>
        </div>
        <div className="col-md-2">
          <span>Valid Until</span>
        </div>
        <div className="col-md-3">
          <DatePickerInput
            form={form}
            name="passportValidTill"
            initialValue={moment(details.passportValidTill, 'YYYY-MM-DD"')}
            format="YYYY-MM-DD"
          />
          {/* <TextInput
            form={form}
            name="passportValidTill"
            initialValue={details.passportValidTill}
          /> */}
        </div>
      </div>
    </div>
  );
};

export const Subscription = (props) => {
  const { form, details } = props;
  return (
    <div className="container-fluid">
      <div className="row">
        <div className="col-xs-12 col-md-2 col-md-offset-1">
          <h5>
            Email <br />
            Subscriptions
          </h5>
        </div>
        <div className="col-xs-6 col-md-2">
          <span>Service News</span>
        </div>
        <div className="col-xs-6 col-md-3 text-right">
          <SwitchInput
            form={form}
            name="serviceNews"
            initialValue={Boolean(Number(details.serviceNews))}
          />
        </div>
      </div>
      <div className="row">
        <div className="col-xs-6 col-md-2 col-md-offset-3">
          <span>Dubai Advices</span>
        </div>
        <div className="col-xs-6 col-md-3 text-right">
          <SwitchInput
            form={form}
            name="dubaiAdvices"
            initialValue={Boolean(Number(details.dubaiAdvices))}
          />
        </div>
      </div>
      <div className="row">
        <div className="col-xs-6 col-md-2 col-md-offset-3">
          <span>Related Proposals</span>
        </div>
        <div className="col-xs-6 col-md-3 text-right">
          <SwitchInput
            form={form}
            name="relatedProposal"
            initialValue={Boolean(Number(details.relatedProposal))}
          />
        </div>
      </div>
    </div>
  );
};

export const SecurityForm = (props) => {
  const { form } = props;
  return (
    <>
      <SectionDividerShort />
      <div className="container-fluid">
        <div className="row">
          <div className="col-md-2 col-md-offset-1">
            <h5>Security</h5>
          </div>
          <div className="col-md-2">
            <span>Old Password</span>
          </div>
          <div className="col-md-3">
            <TextInput form={form} name="uPasswordOld" type="password" />
          </div>
        </div>
        <div className="row">
          <div className="col-md-2 col-md-offset-3">
            <span>New Password</span>
          </div>
          <div className="col-md-3">
            <TextInput form={form} name="uPassword" />
          </div>
        </div>
        <div className="row">
          <div className="col-md-2 col-md-offset-3">
            <span>Confirm New Password</span>
          </div>
          <div className="col-md-3">
            <TextInput form={form} name="uPasswordConfirm" />
          </div>
        </div>
        <FormDivider />
      </div>
    </>
  );
};
