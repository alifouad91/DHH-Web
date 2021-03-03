import React from "react";
import PropTypes from "prop-types";
import PropertyCard from "../../components/PropertyCard";

class SearchhResults extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      hasError: false
    };
  }

  render() {
    const { items, userID } = this.props;
    if (this.state.hasError) {
      return <h1>Something went wrong.</h1>;
    }
    return (
      <div className="container-fluid property__results">
        <div className="row">
          {_.map(items, (item, index) => {
            return (
              <PropertyCard
                index={index}
                userID={userID}
                key={index}
                data={item}
              />
            );
          })}
        </div>
      </div>
    );
  }
}

SearchhResults.propTypes = {
  items: PropTypes.array.isRequired
};

SearchhResults.defaultProps = {
  // bla: 'test',
};

export default SearchhResults;
