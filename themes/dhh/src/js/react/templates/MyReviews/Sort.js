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
          onClick={() => handleSort("reviewRating")}
          className={`sort sort__rating ${activeFilter === "reviewRating" &&
            "active"}`}
        >
          Rating
          {activeFilter === "reviewRating" && (
            <SortIcon ascending={ascending} />
          )}
        </span>
        <span
          onClick={() => handleSort("createdAt")}
          className={`sort sort__price ${activeFilter === "createdAt" &&
            "active"}`}
        >
          Date
          {activeFilter === "createdAt" && <SortIcon ascending={ascending} />}
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
