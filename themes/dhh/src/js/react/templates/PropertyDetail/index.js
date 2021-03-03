import React from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { Fade } from 'react-reveal';
import _ from 'lodash';
import moment from 'moment';
import { Skeleton, Spin, Icon, Button, message } from 'antd';
import Slider from './Slider';
import Content from './Content';
import BookingForm from './PropertyBookForm';
import BookMonthly from './BookMonthly';
import BookWeekly from './BookWeekly';
import SocialShare from './SocialShare';
import Reviews from './Reviews';
import PropertyRules from './PropertyRules';
import DistrictArea from './DistrictArea';
import SimilarProperties from './SimilarProperties';
import GalleryModal from './GalleryModal';
import Maps from './Maps';
import {
  getPropertyDetails,
  getDailyPrices,
  toggleFavourite,
} from '../../services';
import config from '../../config';
import { NonFavourite, Favourite } from '../../icons';
import { displayLoginMessage, displayLandlordMessage } from '../../utils';

class PropertyDetail extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isFav: false,
      spinning: false,
      changed: false,
      loading: true,
      data: {},
      dailyPrices: [],
      mobileBookOpen: false,
      isMobile: $('header').hasClass('site-is-mobile'),
      isLoggedIn: $('header').hasClass('logged-in'),
      isLandlord: $('header').data('group') === 'landlord',
    };
  }

  componentWillMount() {
    const { pID } = this.props;
    const propertyID = pID;
    getPropertyDetails(
      propertyID,
      (data) => {
        this.setState({ data, loading: false });
        setTimeout(() => {
          this.bindWindowEvent();
        }, 500);
      },
      (err) => {
        console.log(err);
      }
    );
    const today = moment();
    const startDate = today.format('YYYY-MM-DD');
    const endDate = today.add('5', 'months').format('YYYY-MM-DD');
    getDailyPrices(
      {
        propertyID: this.props.pID,
        startDate,
        endDate,
        locale: document
          .getElementById('app-currency')
          .getAttribute('data-currency'),
      },
      (data) => {
        this.setState({ dailyPrices: data });
      },
      (err) => {
        console.log(err);
      }
    );
  }

  bindWindowEvent = () => {
    const $bookCard = $('.page__propertydetails__forms > div');
    if (!$bookCard) {
      return;
    }
    const $window = $(window);
    const stickOffset = 100;
    const stickyTop = $bookCard.offset().top;
    const stopper = $('.property__similar').offset().top;
    $window.on('scroll', (e) => {
      if ($window.width() <= 768) {
        return;
      }
      const $this = $(e.currentTarget);
      const topOffset = $this.scrollTop();
      const bookHeight = $bookCard.height();

      if ($bookCard.offset()) {
        if (topOffset + bookHeight >= stopper) {
          $bookCard.css({
            position: 'absolute',
            top: stopper - (stickyTop + bookHeight),
            // (stickyTop + bookHeight) + stickOffset
          });
        } else {
          $bookCard.css({ position: '', top: '' });
          if (topOffset > stickyTop - stickOffset) {
            $bookCard.addClass('active');
            if ($bookCard.parent().width() !== $bookCard.width()) {
              $bookCard.css({ width: $bookCard.parent().width() });
            }
          } else {
            $bookCard.removeClass('active');
            $bookCard.css({ width: 'auto' });
          }
        }
      }
    });
  };

  handleMobileBook = () => {
    this.setState({ mobileBookOpen: !this.state.mobileBookOpen });
    $('body').toggleClass('menu-open');
    $('.mobile-menu-icon').toggleClass('hide');
  };

  handleFavClick = (id, e) => {
    const { isLoggedIn, isLandlord, spinning } = this.state;
    e.stopPropagation();
    if (spinning) {
      return;
    }
    if (!isLoggedIn) {
      displayLoginMessage();
      return;
    }
    if (isLandlord) {
      displayLandlordMessage(`add favourites`);
      return;
    }
    // console.log("clicked", id, userID);
    let data = new FormData();
    // data.append("userID", userID);
    data.append('propertyID', id);
    this.setState({ spinning: true });
    toggleFavourite(
      data,
      (data) => {
        this.setState({
          isFav: data.favourited,
          spinning: false,
          changed: true,
        });
        message.success(
          `Successfully ${
            data.favourited ? 'added to' : 'removed from'
          } favourites.`
        );
      },
      (err) => {
        console.log(err);
      }
    );
  };

  render() {
    const {
      data,
      loading,
      mobileBookOpen,
      isMobile,
      dailyPrices,
      changed,
      isFav,
    } = this.state;
    const { pID, uID } = this.props;
    const { maxGallerySliderThumbs } = config;
    const sliderImages = isMobile
      ? data.images
      : _.slice(data.images, 0, maxGallerySliderThumbs);
    const extraImages =
      data && data.images ? data.images.length - maxGallerySliderThumbs : 0;
    const extraImagesThumb = extraImages
      ? data.images[maxGallerySliderThumbs]
      : null;
    const showExtra = extraImages > 0;
    const hideMonthly = Number(data.monthlyPrice) === 0;
    const hideWeekly = Number(data.weeklyPrice) === 0;
    const showFav = changed ? isFav : data.isFavorite;
    return (
      <>
        {!loading && (
          <div className='page__propertydetails__mobile-book'>
            <div>
              <h5>{data.perDayPrice}</h5>
              <span>per night</span>
            </div>
            <Button type='primary' onClick={this.handleMobileBook}>
              BOOK
            </Button>
          </div>
        )}
        <div className='page__propertydetails'>
          <div className='container-fluid page__propertydetails__slider'>
            <div className='row'>
              {loading ? (
                <Spin tip='Loading images' size='large' />
              ) : (
                <React.Fragment>
                  <Fade>
                    <Slider isMobile={isMobile} images={sliderImages} />
                    {showExtra ? (
                      <GalleryModal
                        extraImages={extraImages}
                        extraImagesThumb={extraImagesThumb}
                        images={data.images}
                        isMobile={isMobile}
                      />
                    ) : null}
                  </Fade>
                  <div className='page__propertydetails__fav'>
                    <Icon
                      onClick={(e) => this.handleFavClick(data.id, e)}
                      component={showFav ? Favourite : NonFavourite}
                    />
                  </div>
                </React.Fragment>
              )}
            </div>
          </div>
          <div className='container'>
            <div className='row'>
              <div className='col-sm-12 col-md-8 col-lg-8 p-0-m page__propertydetails__main'>
                <SocialShare subject={data.title} />
                {/* Content */}
                {loading ? (
                  <Skeleton active />
                ) : (
                  <Fade>
                    <Content data={data} />
                  </Fade>
                )}
                {/* Reviews */}
                {loading ? (
                  <div style={{ margin: '100px 0' }}>
                    <Skeleton loading={loading} active avatar />
                    <Skeleton loading={loading} active avatar />
                    <Skeleton loading={loading} active avatar />
                  </div>
                ) : (
                  <Fade>
                    <Reviews
                      avgRating={data.avgRating}
                      reviews={data.reviews}
                    />
                  </Fade>
                )}
                {/* Property Rules */}
                {/* District and Area */}
                {/* Maps */}
                {loading ? (
                  <Skeleton active paragraph={{ rows: 8 }} />
                ) : (
                  <Fade>
                    <PropertyRules rules={data.propertyRules} />
                    <DistrictArea districtArea={data.locationDescription} />
                    <Maps lat={data.lat} lng={data.long} />
                  </Fade>
                )}
              </div>
              <div
                className={`col-md-4 col-lg-4  page__propertydetails__forms ${
                  mobileBookOpen ? 'open' : ''
                }`}
              >
                {/* Book Form */}
                <Fade>
                  <div>
                    <div
                      className='page__propertydetails__forms__close'
                      onClick={this.handleMobileBook}
                    >
                      <Icon type='close' />
                    </div>
                    {loading ? (
                      <Skeleton
                        active
                        paragraph={{ rows: 8 }}
                        style={{ margin: '100px 0' }}
                      />
                    ) : (
                      <BookingForm
                        dailyPrices={dailyPrices}
                        perDayPrice={data.perDayPrice}
                        data={data}
                        pID={pID}
                        uID={uID}
                      />
                    )}
                    {/* Monhtly */}
                    {!hideMonthly && !loading && (
                      <BookMonthly monthlyPrice={data.monthlyPrice} />
                    )}
                    {/* Weekly */}
                    {!hideWeekly && !loading && (
                      <BookWeekly weeklyPrice={data.weeklyPrice} />
                    )}
                    {/* Social Share*/}
                    <SocialShare subject={data.title} />
                  </div>
                </Fade>
              </div>
            </div>
          </div>
          {/* Similar apartments */}
          <SimilarProperties
            loading={loading}
            location={data && data.location}
            pID={pID}
            data={data && data.similarProperties ? data.similarProperties : []}
            similarPropertyCount={
              data && data.similarPropertyCount ? data.similarPropertyCount : ''
            }
          />
        </div>
      </>
    );
  }
}

PropertyDetail.propTypes = {
  pID: PropTypes.number.isRequired,
  uID: PropTypes.number.isRequired,
};

const $el = $('.page__propertyitem');
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const pID = Number($this.data('pid'));
    const uID = Number($this.data('uid'));
    ReactDOM.render(<PropertyDetail uID={uID} pID={pID} />, el);
  });
}
