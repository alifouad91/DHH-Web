import React from "react";
import { Modal } from "antd";

export default class EmailModal extends React.Component {
  render() {
    const { visible } = this.state;
    return (
      <Modal
        centered
        title={null}
        visible={visible}
        footer={null}
        width={420}
        // onCancel={e => (loading ? null : handleCancel(e))}
      >
        Email me
      </Modal>
    );
  }
}
