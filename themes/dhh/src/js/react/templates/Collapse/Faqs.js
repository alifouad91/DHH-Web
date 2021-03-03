import React from "react";
import ReactDOM from "react-dom";
import _ from "lodash";
import { Collapse, Icon } from "antd";
import { htmlToCollapseObject } from "../../utils";

const Panel = Collapse.Panel;

export default class Faqs extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      data: []
    };
  }
  componentWillMount() {
    this.setState({
      data: htmlToCollapseObject($(".faqs__concrete > div"))
    });
    // console.log(htmlToCollapseObject($(".collapse__faqs__items > div")));
  }

  render() {
    const { data } = this.state;
    return (
      <Collapse
        bordered={false}
        expandIcon={({ isActive }) => (
          <Icon type="caret-down" rotate={isActive ? 180 : 0} />
        )}
      >
        {_.map(data, (itm, index) => {
          return (
            <Panel header={itm.title} key={index}>
              <div dangerouslySetInnerHTML={{ __html: String(itm.content) }} />
            </Panel>
          );
        })}
      </Collapse>
    );
  }
}

const $el = $(".collapse__faqs");
if ($el.length && !$(".edit-mode").length) {
  $el.each((index, el) => {
    ReactDOM.render(<Faqs key={index} />, el);
  });
}
