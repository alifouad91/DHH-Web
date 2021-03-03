import React from "react";
import ReactDOM from "react-dom";
import _ from "lodash";
import { Tabs, Collapse, Icon } from "antd";
import { htmlToTabObject } from "../../utils";

const TabPane = Tabs.TabPane;
const Panel = Collapse.Panel;

const CollapseItem = ({ item }) => {
  return (
    <div>
      <h3>{item.title}</h3>
      {item.content && (
        <div dangerouslySetInnerHTML={{ __html: String(item.content) }} />
      )}
      <Collapse
        bordered={false}
        expandIcon={({ isActive }) => (
          <Icon type="caret-down" rotate={isActive ? 180 : 0} />
        )}
      >
        {_.map(item.collapse, (itm, index) => {
          return (
            <Panel header={itm.title} key={index}>
              <div dangerouslySetInnerHTML={{ __html: String(itm.content) }} />
            </Panel>
          );
        })}
      </Collapse>
    </div>
  );
};

export default class StaticTab extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      data: [],
      isMobile: $("header").hasClass("site-is-mobile")
    };
  }
  componentWillMount() {
    this.setState({ data: htmlToTabObject($(".tabs__concrete > div")) });
    $(window).on("resize", e => {
      const $this = $(e.currentTarget);
      this.setState({ isMobile: $this.width() <= 768 });
    });
  }

  renderTabContent = item => {
    if (item.collapse.length) {
      return <CollapseItem item={item} />;
    } else {
      return <div dangerouslySetInnerHTML={{ __html: String(item.content) }} />;
    }
  };

  render() {
    const { data, isMobile } = this.state;
    return (
      <Tabs
        defaultActiveKey="1"
        tabPosition={isMobile ? "top" : "left"}
        style={{ height: 220 }}
        type="card"
      >
        {_.map(data, (item, index) => {
          return (
            <TabPane tab={item.title} key={index}>
              {this.renderTabContent(item)}
            </TabPane>
          );
        })}
      </Tabs>
    );
  }
}

const $el = $(".tabs__static");
if ($el.length && !$(".edit-mode").length) {
  $el.each((index, el) => {
    ReactDOM.render(<StaticTab />, el);
  });
}
