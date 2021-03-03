import React from "react";
import moment from "moment";
import { Card, Icon, Modal, Input, Button, Form } from "antd";
import { TextInput, SaveButton } from "./FormElements";
import { Email, Download } from "../icons";
import { emailBill } from "../services";
import { objectToFormData } from "../utils";

const EmailModal = Form.create({})(props => {
  const { visible, handleCancel, loading, propertyName, form, success } = props;
  return (
    <Modal
      centered
      title={null}
      visible={visible}
      footer={null}
      width={420}
      onCancel={e => (loading ? null : handleCancel(e))}
    >
      <div className={`popup popup__${success ? "added" : null}`}>
        <div className="popup__title">
          <h4>{success ? "Bill successfully emailed!" : "Email your bill"}</h4>
          {!success ? (
            <>
              <p className="property">{propertyName}</p>
              <p>Leave the email field blank to send to your email</p>
            </>
          ) : null}
        </div>
        {success ? null : (
          <Form onSubmit={e => props.handleSubmit(e, form)}>
            <TextInput
              form={form}
              name="recipientEmail"
              placeholder="Enter your email"
              isEmail={true}
            />
            <SaveButton
              saveLabel="SEND"
              disabled={loading}
              saving={loading}
              className="w-100"
              savingLabel="Sending"
            />
          </Form>
        )}
      </div>
    </Modal>
  );
});

export default class UtilityCard extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      visible: false,
      loading: false,
      success: false
    };
  }

  handleModalOpen = () => {
    const { visible } = this.state;
    this.setState({ visible: !visible });
    setTimeout(() => {
      this.setState({ success: false });
    }, 500);
  };

  handleSubmit = (e, form) => {
    const { data } = this.props;
    e.preventDefault();
    form.validateFields((err, values) => {
      if (!err) {
        this.setState({ loading: true });
        values.billID = data.billID;
        const obj = _.pickBy(values, _.identity);
        emailBill(
          objectToFormData(obj),
          response => {
            if (response.status) {
              this.setState({ loading: false, success: true });
            }
          },
          err => {
            console.log(err);
            this.setState({ loading: false });
          }
        );
      }
    });
  };

  render() {
    const { visible, loading, success } = this.state;
    const { data } = this.props;
    const { date, propertyName, amount, currency, billID, billImage } = data;

    const d = moment(date);
    const month = d.format("MMMM DD");
    const year = d.format("YYYY");

    return (
      <div className="col-lg-9 pl-0 pr-0">
        <EmailModal
          handleCancel={this.handleModalOpen}
          visible={visible}
          propertyName={propertyName}
          handleSubmit={this.handleSubmit}
          loading={loading}
          success={success}
        />
        <Card hoverable bordered className="utility__card">
          {/* <ImageFit style={{ objectFit: "cover" }} src={billImage} /> */}

          {billImage ? (
            <object type="application/pdf" data={billImage}>
              <p>Cannot load pdf</p>
            </object>
          ) : null}
          <div className="utility__card__details">
            <h5>
              {month}, <span>{year}</span>
            </h5>
            <p className="small">{propertyName}</p>

            <div className="utility__card__amount">
              {currency}
              <span>{Number(amount)}</span>
            </div>

            <div className="utility__card__billing">
              Bill Number <span>{billID}</span>
            </div>

            <div className="utility__card__buttons">
              {billImage ? (
                <a download href={billImage} target="_blank">
                  <Icon component={Download} />
                </a>
              ) : null}
              <Icon component={Email} onClick={this.handleModalOpen} />
            </div>
          </div>
        </Card>
      </div>
    );
  }
}
