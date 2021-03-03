import React from "react";
import ReactDOM from "react-dom";
import _ from "lodash";
import { Select } from "antd";
import CurrencyDropdown from "./CurrencyDropdown";
import { currencies } from "../../constants";

const Option = Select.Option;
class MobileCurrencyDropdown extends React.Component {
  render() {
    const { currentCurrency } = this.props;
    return (
      <>
        <span className="sub-header-2">Currency</span>
        <CurrencyDropdown selectedCurrency={currentCurrency} placement="topRight"/>
      </>
    )
  }
}

const $el = $(".mobile__curency");
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const currentCurrency = $this.data("currentcurrency");
    ReactDOM.render(<MobileCurrencyDropdown currentCurrency={currentCurrency}/>, el);
  });
}
