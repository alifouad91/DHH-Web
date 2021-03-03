import React from "react";
import PropTypes from "prop-types";
import ImageGallery from "react-image-gallery";
import { Icon } from "antd";

import { formatImageGalleryObject } from "../../utils";
import { SliderArrowLeft, SliderArrowRight } from "../../icons";

export default class ImageSlider extends React.Component {
  render() {
    const { images, isMobile } = this.props;
    const items = formatImageGalleryObject(images);
    return (
      <ImageGallery
        items={items}
        autoPlay={true}
        slideInterval={6000}
        slideDuration={800}
        lazyLoad={true}
        showThumbnails={!isMobile}
        showIndex={isMobile}
        renderItem={item => {
          return (
            <div className="image-gallery-image">
              <div
                className="image-gallery-bg"
                style={{ backgroundImage: `url(${item.original})` }}
              />
            </div>
          );
        }}
        renderRightNav={onClick => {
          return (
            <div className="image-gallery-nav image-gallery-nav-right">
              <Icon component={SliderArrowRight} onClick={onClick} />
            </div>
          );
        }}
        renderLeftNav={onClick => {
          return (
            <div className="image-gallery-nav image-gallery-nav-left">
              <Icon component={SliderArrowLeft} onClick={onClick} />
            </div>
          );
        }}
      />
    );
  }
}

ImageSlider.propTypes = {
  images: PropTypes.array.isRequired
};
