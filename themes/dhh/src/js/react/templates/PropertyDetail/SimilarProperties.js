import React from 'react';
import PropTypes from 'prop-types';
import _ from 'lodash';
import { Button } from 'antd';
import PropertyCard from '../../components/PropertyCard';
import { generateLoadingObject } from '../../utils';
import config from '../../config';

export default class SimilarProperties extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: generateLoadingObject(4),
      loading: true,
    };
  }
  componentWillMount() {
    this.setState({ loading: this.props.loading });
  }
  componentDidMount() {
    this.setState({ items: generateLoadingObject(4) });
  }
  componentWillReceiveProps(nextProps) {
    const { loading } = this.state;

    if (loading && !nextProps.loading) {
      this.setState({ loading: nextProps.loading, items: nextProps.data });
    }
  }
  render() {
    const { items, loading } = this.state;
    const { similarPropertyCount, location, pID } = this.props;
    return (
      <div className='page__limit similar-properties block property__similar'>
        <div className='container-fluid'>
          <h4>Similar Properties</h4>
          <div className='row'>
            {_.map(items, (item, index) => {
              return <PropertyCard index={index} key={index} data={item} />;
            })}
          </div>
          {!loading && (
            <Button
              type='secondary'
              href={`${
                config.BASE_URL
              }/properties?locations=${location}&excludeIDs=${pID}`}
            >
              Explore All{' '}
              <span className='ant-btn-count'>{similarPropertyCount}</span>
            </Button>
          )}
        </div>
      </div>
    );
  }
}

// SimilarProperties.propTypes = {
// };
