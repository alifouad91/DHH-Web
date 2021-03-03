import React from "react";
import { Card, Icon, Menu, Dropdown, Skeleton } from "antd";
import { YearlyOccupancy, PaymentEmpty } from "../../icons";
import EmptyMessage from "../../components/EmptyMessage";

export const Occupancy = (props) => {
  const { activeObject, empty, loadingStat } = props;
  return (
    <Card className="card__gray yearly__occupancy" bordered={false}>
      <Skeleton loading={loadingStat}>
        <span>Occupancy</span>
        {empty ? (
          <EmptyMessage
            image={<Icon component={YearlyOccupancy} />}
            message="Waiting for the first data"
          />
        ) : (
          <>
            <div className="text-center">
              <Icon component={YearlyOccupancy} />
            </div>
            <div className="yearly__occupancy__bottom">
              <span>NIGHTS</span>
              <span>{parseFloat(activeObject.avgNights).toFixed(2)}</span>
            </div>
          </>
        )}
      </Skeleton>
    </Card>
  );
};

export const Payments = (props) => {
  const { activeObject, empty, loadingStat } = props;
  let paidOut = parseFloat(activeObject.paidOut).toFixed(2);
  paidOut = paidOut.split(".");
  let expected = parseFloat(activeObject.expected).toFixed(2);
  expected = expected.split(".");
  return (
    <Card
      className={`card__gray payments ${empty && "empty"}`}
      bordered={false}
    >
      <Skeleton loading={loadingStat}>
        <div>
          <span>Payments</span>
        </div>
        {empty ? (
          <EmptyMessage
            message="Waiting for the first data"
            image={<Icon component={PaymentEmpty} />}
          />
        ) : (
          <>
            <div>
              <div className="heading">
                <span>PAID OUT</span> <span>AED</span>
              </div>
              <div className="body">
                <span>{paidOut[0]}</span>
                <span>{paidOut[1] ? `.${paidOut[1]}` : ".00"}</span>
              </div>
            </div>
            <div>
              <div className="heading">
                <span>EXPECTED</span> <span>AED</span>
              </div>
              <div className="body">
                <span>{expected[0]}</span>
                <span>{expected[1] ? `.${expected[1]}` : ".00"}</span>
              </div>
            </div>
          </>
        )}
      </Skeleton>
    </Card>
  );
};

const menu = (list, handleMenuChange, selectedKey) => {
  // console.log(list);
  return (
    <Menu
      defaultSelectedKeys={["all"]}
      selectedKeys={[selectedKey]}
      onClick={({ key, item }) => handleMenuChange(key, item)}
    >
      <Menu.Item key="all">All Properties</Menu.Item>
      {_.map(list, (property) => {
        return <Menu.Item key={property.id}>{property.title}</Menu.Item>;
      })}
    </Menu>
  );
};

export const DropMenu = (props) => {
  const { propertyList, handleMenuChange, selectedTitle, selectedKey } = props;
  return (
    <Dropdown
      overlay={menu(propertyList, handleMenuChange, selectedKey)}
      trigger={["click"]}
      placement="bottomRight"
    >
      <a className="ant-dropdown-link" href="#">
        {selectedTitle} <Icon type="caret-down" />
      </a>
    </Dropdown>
  );
};
