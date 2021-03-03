import React from "react";
import PropTypes from "prop-types";
import SortIcon from "../../components/SortIcon";

export default class SearchSort extends React.PureComponent {
  render() {
    const {
      handleSort,
      activeFilter,
      itemLength,
      ascending,
      clearSort,
      reset,
    } = this.props;
    return (
      <div className="property__sort">
        <span>At the beginning: </span>
        <span
          onClick={() => handleSort("avgRating", false, "ratingSort")}
          className={`sort sort__rating ${activeFilter === "avgRating" &&
            "active"}`}
        >
          Top Rated
          {activeFilter === "avgRating" && <SortIcon ascending={ascending} />}
        </span>
        <span
          onClick={() => handleSort("propertyPrice", true, "priceSort")}
          className={`sort sort__price ${activeFilter === "propertyPrice" &&
            "active"}`}
        >
          Price
          {activeFilter === "propertyPrice" && (
            <SortIcon ascending={ascending} />
          )}
        </span>
        {activeFilter && (
          <span onClick={() => clearSort()} className="sort">
            Clear Sort
          </span>
        )}
        <span className="props-found">{itemLength} properties found</span>
        <div className="filters" onClick={reset}>
          Clear Filters
        </div>
      </div>
    );
  }
}

SearchSort.propTypes = {
  handleSort: PropTypes.func.isRequired,
  clearSort: PropTypes.func.isRequired,
  ascending: PropTypes.bool.isRequired,
  activeFilter: PropTypes.string,
  itemLength: PropTypes.number,
};
