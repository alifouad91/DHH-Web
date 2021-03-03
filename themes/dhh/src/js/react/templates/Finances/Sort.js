import React from "react";
import PropTypes from "prop-types";
import SortIcon from "../../components/SortIcon";

export default class SearchSort extends React.PureComponent {
  render() {
    const { handleSort, activeFilter, ascending, clearSort } = this.props;
    return (
      <div className="property__sort">
        <span>Sort by: </span>
        <span
          onClick={() => handleSort("amount")}
          className={`sort sort__rating ${activeFilter === "amount" &&
            "active"}`}
        >
          Amount
          {activeFilter === "amount" && <SortIcon ascending={ascending} />}
        </span>
        <span
          onClick={() => handleSort("date")}
          className={`sort sort__price ${activeFilter === "date" && "active"}`}
        >
          Date
          {activeFilter === "date" && <SortIcon ascending={ascending} />}
        </span>
        {activeFilter && (
          <span onClick={() => clearSort()} className="sort">
            Clear Sort
          </span>
        )}
      </div>
    );
  }
}

SearchSort.propTypes = {
  handleSort: PropTypes.func.isRequired,
  clearSort: PropTypes.func.isRequired,
  ascending: PropTypes.bool.isRequired,
  activeFilter: PropTypes.string,
  itemLength: PropTypes.number
};
