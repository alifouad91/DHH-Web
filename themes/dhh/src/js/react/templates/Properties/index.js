import React from 'react';
import ReactDOM from 'react-dom';
import BottomScrollListener from 'react-bottom-scroll-listener';
import { Spin, message, Button, Select, Icon } from 'antd';
import SearchFilters from './SearchFilters';
import SearchResults from './SearchResults';
import SearchSort from './SearchSort';
import EmptyMessage from '../../components/EmptyMessage';
import LoadMore from '../../components/LoadMore';
import { getProperties, getFilters } from '../../services';
import { FilterEmpty } from '../../icons';
import FeaturedProperty from '../Cards/HomePageProperties';
const Option = Select.Option;

export default class PropertyResults extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      locations: [],
      loadingInitial: true,
      loading: true,
      loadingMore: false,
      loadingFilters: true,
      filterOpen: false,
      filters: {},
      items: [],
      originalList: [],
      activeFilter: '',
      ascending: true,
      searchObj: {},
      pageNo: 1,
      currentFilters: {},
      toLoadMore: true,
      itemsPerPage: 12,
      keywords: '',
    };
  }

  componentWillMount() {
    let params = qs.get();
    if (params.keywords) {
      params.keywords = params.keywords.split('%20').join(' ');
      this.setState({
        keywords: params.keywords.split('%20').join(' '),
      });
    }

    if (params.locations) {
      params.locations = params.locations.split('%20').join(' ');
      this.setState({
        locations: [params.locations.split('%20').join(' ')],
      });
    }

    this.filterProperties(params, false, false, true);
    this.getFilter();
  }

  reset = () => {
    this.setState(
      {
        filterOpen: false,
        filters: {},
        items: [],
        originalList: [],
        activeFilter: '',
        searchObj: {},
        pageNo: 1,
        currentFilters: {},
        toLoadMore: true,
        itemsPerPage: 12,
        keywords: '',
      },
      this.filterProperties()
    );
  };

  getFilter = () => {
    this.setState({ loadingFilters: true });
    getFilters(
      (filters) => {
        this.setState({ loadingFilters: false, filters });
      },
      (err) => {
        console.log(err);
      }
    );
  };

  filterProperties = (
    params = {},
    append = false,
    isLoadMore = false,
    initial = false
  ) => {
    const { originalList, items, itemsPerPage, keywords } = this.state;
    if (keywords) {
      params.keywords = keywords;
    }
    this.setState({
      loading: isLoadMore ? false : true,
      loadingMore: isLoadMore ? true : false,
      currentFilters: params,
    });
    getProperties(
      params,
      (data) => {
        // console.log(data.length >= itemsPerPage);
        this.setState({
          originalList: !append ? data : [...originalList, ...data],
          items: !append ? data : [...items, ...data],
          loading: false,
          loadingMore: false,
          filterOpen: false,
          toLoadMore: data.length >= itemsPerPage,
        });
        $('body').removeClass('menu-open');
        if (initial) {
          this.setState({
            loadingInitial: false,
          });
        }
      },
      (err) => {
        console.log(err);
      }
    );
  };

  submitFilter = (val, checked) => {
    const { searchObj, activeFilter, ascending } = this.state;
    let obj = _.pickBy(val, _.identity);
    if (obj.price && obj.price.length) {
      obj.minPrice = obj.price[0];
      obj.maxPrice = obj.price[1];
      delete obj.price;
    }
    if (obj.apartmentType && typeof obj.apartmentType === 'number') {
      delete obj.apartmentType;
    }
    if (activeFilter === 'avgRating') {
      obj.ratingSort = ascending ? 'asc' : 'desc';
      obj.pageNo = 1;
    }

    if (activeFilter === 'propertyPrice') {
      obj.priceSort = ascending ? 'asc' : 'desc';
      obj.pageNo = 1;
    }

    if (!_.isEqual(obj, searchObj)) {
      if (this.props.monthly) {
        obj.monthly = true;
      }
      this.filterProperties(obj);
      this.setState({ searchObj: obj });
    }
  };

  handleSort = (key, reverse) => {
    const { ascending, activeFilter, loading } = this.state;

    if (loading) {
      message.warning('Sorting in progress', 1);
      return;
    }
    this.setState({ loading: true, pageNo: 1 });

    // let sorted = _.sortBy(items, (property) => property[key]);
    // if (reverse) {
    //   sorted = _.reverse(sorted);
    // }

    if (key === activeFilter) {
      this.setState({ ascending: !ascending });
    } else {
      this.setState({ activeFilter: key, ascending: true });
    }

    setTimeout(() => {
      //   if (this.state.ascending) {
      //     sorted = _.reverse(sorted);
      //   }

      setTimeout(() => {
        this.submitFilter();
      }, 500);
    }, 500);
  };

  handleSortMobile = (key, descending) => {
    // const { originalList } = this.state;
    // let sorted = _.sortBy(originalList, (property) => property[key]);

    // if (descending) {
    //   sorted = _.reverse(sorted);
    // }
    setTimeout(() => {
      this.submitFilter();
    }, 500);
  };

  clearSort = () => {
    this.setState({ items: this.state.originalList, activeFilter: '' });
  };

  openFilters = () => {
    this.setState({ filterOpen: !this.state.filterOpen });
    $('body').toggleClass('menu-open');
  };

  handleChange = (val) => {
    this.setState({ loading: true });
    switch (val) {
      case 'top':
        this.handleSort('avgRating', true);
        break;
      case 'plh':
        this.handleSort('propertyPrice', false);
        break;
      case 'phl':
        this.handleSort('propertyPrice', true);
        break;
    }
  };

  handleLoadMore = () => {
    const { loadingMore, currentFilters, pageNo, toLoadMore } = this.state;
    if (loadingMore || !toLoadMore) {
      return;
    }
    this.filterProperties(
      {
        ...currentFilters,
        pageNo: pageNo + 1,
      },
      true,
      true
    );
    this.setState({ pageNo: pageNo + 1 });
    console.log('Bottom');
  };

  render() {
    const {
      loading,
      items,
      activeFilter,
      ascending,
      loadingFilters,
      filters,
      filterOpen,
      loadingMore,
      locations,
      loadingInitial,
    } = this.state;
    const {
      userID,
      monthly,
      keywords,
      startDate,
      endDate,
      guests,
      featuredText,
      featuredId,
    } = this.props;
    return (
      <React.Fragment>
        <BottomScrollListener onBottom={this.handleLoadMore}>
          <div className='property__filter__btns'>
            <Select
              placeholder='Sort'
              suffixIcon={<Icon type='caret-down' />}
              onChange={this.handleChange}
            >
              <Option value='top'>Top Rated</Option>
              <Option value='plh'>Price Low - High</Option>
              <Option value='phl'>Price High - Low</Option>
            </Select>
            <Button type='secondary' onClick={this.openFilters}>
              Filters
            </Button>
          </div>
          {loadingInitial ? null : (
            <SearchFilters
              submitFilter={this.submitFilter}
              monthly={monthly}
              disabled={loading || loadingFilters}
              startDate={startDate}
              endDate={endDate}
              guests={guests}
              filters={filters}
              keywords={keywords}
              filterOpen={filterOpen}
              openFilters={this.openFilters}
              locations={locations}
            />
          )}
          <SearchSort
            activeFilter={activeFilter}
            ascending={ascending}
            itemLength={items.length}
            loading={loading}
            handleSort={this.handleSort}
            clearSort={this.clearSort}
            reset={this.reset}
          />

          <Spin spinning={loading}>
            {items.length ? (
              <SearchResults items={items} userID={userID} />
            ) : loading ? (
              <div
                style={{
                  padding: 25,
                }}
              />
            ) : (
              <div>
                <EmptyMessage
                  message={
                    <>
                      We currently have no properties matching your search.
                      <br />
                    </>
                  }
                  image={<Icon component={FilterEmpty} />}
                />
                <FeaturedProperty
                  title={featuredText}
                  id={featuredId}
                  count={4}
                />
              </div>
            )}
          </Spin>
          <LoadMore loading={loadingMore} />
        </BottomScrollListener>
      </React.Fragment>
    );
  }
}

const $el = $('#property-results');
const obj = qs.get();
const { keywords, startDate, endDate, guests } = obj;
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const id = $this.data('id');
    const monthly = $this.data('monthly');
    const featuredId = $this.data('featuredid');
    const featuredText = $this.data('featuredtext');
    ReactDOM.render(
      <PropertyResults
        keywords={keywords && keywords.split('%20').join(' ')}
        startDate={startDate}
        guests={guests}
        endDate={endDate}
        monthly={monthly}
        featuredId={featuredId}
        featuredText={featuredText}
        userID={id}
      />,
      el
    );
  });
}
