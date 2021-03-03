import React from "react";
import { Form, Button, Rate, Icon } from "antd";
import { RateInput, TextInput, SaveButton } from "../FormElements";
import { StarXL } from "../../icons";
import config from "../../config";

const RatingTooltips = props => {
  const { hovered } = props;
  return (
    <span className="popup__ratingtip">
      {config.ratingTooltips[hovered - 1]}
    </span>
  );
};

export const RateYourStay = Form.create({})(props => {
  const { form, hovered, onRateChange, loading, data } = props;
  return (
    <>
      <div className="popup__title">
        <h4>Rate your stay</h4>
        <p className="property">{data.title}</p>
        <p>
          Describe your stay at this property. Was it comfortable, clean, and
          cozy?
        </p>
      </div>
      <Form onSubmit={e => props.handleSubmit(e, form, "rate")}>
        <TextInput
          form={form}
          name="reviewComment"
          rows={7}
          placeholder="Tell us more about your stay…"
        />
        <RateInput
          form={form}
          name="reviewRating"
          initialValue={5}
          onChange={onRateChange}
        />
        <RatingTooltips hovered={hovered} />
        <SaveButton
          saveLabel="Rate stay"
          disabled={loading}
          saving={loading}
          savingLabel="Rating"
        />
      </Form>
    </>
  );
});

export const ReviewAdded = props => {
  const { data, updated, handleCancel, reEdit } = props;
  const { title, myComments, myRatings } = data;
  return (
    <>
      <div className="popup__title">
        <h4>Review {updated ? "updated" : "added"}</h4>
        <p className="property">{title}</p>
        <Rate
          className="ant-rate-form-element"
          disabled
          value={myRatings}
          character={<Icon component={StarXL} />}
        />
        <RatingTooltips hovered={myRatings} />
      </div>
      <div className="popup__body">
        <p>{myComments}</p>
        <Button type="secondary" onClick={reEdit}>
          EDIT REVIEW
        </Button>
        <Button type="primary" onClick={handleCancel}>
          CLOSE
        </Button>
      </div>
    </>
  );
};

export const ReviewEdit = Form.create({})(props => {
  const {
    form,
    hovered,
    onRateChange,
    data,
    loading,
    limitReached,
    handleCancel
  } = props;
  // console.log(data);
  return (
    <>
      <div className="popup__title">
        <h4>{limitReached ? "Your review" : "Edit review"} </h4>
        <p className="property">{data.title}</p>
        <p>
          {limitReached
            ? "You cannot update review anymore! Limit reached"
            : "Changed your mind?"}
        </p>
      </div>
      <Form onSubmit={e => props.handleSubmit(e, form, "edit")}>
        <TextInput
          form={form}
          name="reviewComment"
          rows={7}
          placeholder="Tell us more about your stay…"
          initialValue={data.myComments}
          readOnly={limitReached}
        />
        <RateInput
          form={form}
          name="reviewRating"
          initialValue={Number(data.myRatings)}
          onChange={onRateChange}
        />
        <RatingTooltips hovered={hovered} />
        <Button
          disabled={loading}
          hidden={limitReached}
          type="secondary"
          onClick={handleCancel}
        >
          Cancel
        </Button>
        <SaveButton
          saving={loading}
          savingLabel="Updating Review"
          disabled={loading}
          hidden={limitReached}
          saveLabel="Update Review"
        />
      </Form>
    </>
  );
});

export const AmendBooking = Form.create({})(props => {
  const { data, form, loading, handleCancel } = props;
  return (
    <>
      <div className="popup__title">
        <h4>Amend booking</h4>
        <p className="property">
          {data.title} · {data.mDate}
        </p>
        <p>
          Please tell us more about the changes you want to make, and one of our
          team members will reach out to you to assist in your request.
        </p>
      </div>
      <Form onSubmit={e => props.handleSubmit(e, form, "amend")}>
        <TextInput
          form={form}
          name={config.amendBookingFields.comment}
          rows={7}
          required
          placeholder="Write your comment or request here"
        />
        <Button disabled={loading} type="secondary" onClick={handleCancel}>
          Cancel
        </Button>
        <SaveButton disabled={loading} saveLabel="REQUEST TO AMEND" />
      </Form>
    </>
  );
});

export const Amended = props => {
  const { data, handleCancel, canceled } = props;
  const { title, message } = data;
  return (
    <>
      <div className="popup__title">
        <h4>{canceled ? "Cancellation" : "Amendment"} request sent</h4>
        <p className="property">{title}</p>
      </div>
      <div className="popup__body">
        <p>{message}</p>
        <Button type="primary" onClick={handleCancel}>
          CLOSE
        </Button>
      </div>
    </>
  );
};

export const CancelBooking = Form.create({})(props => {
  const { data, form, loading, handleCancel } = props;
  return (
    <>
      <div className="popup__title">
        <h4>Cancel booking</h4>
        <p className="property">
          {data.title} · {data.mDate}
        </p>
        <p>
          Please tell us more about why you wish to cancel your booking and one
          of our team members will reach out to assist you.
        </p>
      </div>
      <Form onSubmit={e => props.handleSubmit(e, form, "cancel")}>
        <TextInput
          form={form}
          name={config.cancelBookingFields.comment}
          rows={7}
          required
          placeholder="Write your comment here"
        />
        <Button disabled={loading} type="secondary" onClick={handleCancel}>
          CLOSE
        </Button>
        <SaveButton disabled={loading} saveLabel="REQUEST TO CANCEL" />
      </Form>
    </>
  );
});
