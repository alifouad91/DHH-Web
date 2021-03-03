import React, { Component } from "react";
import { Button } from "antd";
import ReactBnbGallery from "react-bnb-gallery";
import { formatGalleryModalObject } from "../../utils";

export default class GalleryModal extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      galleryOpened: false,
      photos: formatGalleryModalObject(props.images)
    };
  }

  toggleGallery = () => {
    this.setState({ galleryOpened: !this.state.galleryOpened });
  };

  render() {
    const { extraImages, extraImagesThumb, isMobile } = this.props;
    return (
      <>
        {isMobile ? null : (
          <>
            <div
              className="page__propertydetails__viewphotos"
              onClick={this.toggleGallery}
              style={{ backgroundImage: `url(${extraImagesThumb})` }}
            >
              <span>+ {extraImages} MORE</span>
            </div>
            <ReactBnbGallery
              show={this.state.galleryOpened}
              photos={this.state.photos}
              onClose={this.toggleGallery}
            />
          </>
        )}
      </>
    );
  }
}
