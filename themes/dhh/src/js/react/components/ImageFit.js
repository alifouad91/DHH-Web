import * as React from "react";
import getStyleProp from "desandro-get-style-property";

const supportsObjectFit = !!getStyleProp("objectFit");

export default class Image extends React.Component {
  renderFallback = () => {
    const { style, src, ...restProps } = this.props;
    return (
      <div
        style={{
          ...style,
          backgroundImage: `url(${src})`,
          backgroundSize: style.objectFit,
          backgroundPosition: "center center",
          position: "relative"
        }}
      >
        <img
          style={{
            display: "block",
            position: "absolute",
            top: 0,
            left: 0,
            width: "100%",
            height: "100%",
            opacity: 0
          }}
          src={src}
          {...restProps}
        />
      </div>
    );
  };

  renderNative = () => {
    const { style, ...restProps } = this.props;
    return (
      <img
        style={{
          ...style,
          display: "block"
        }}
        {...restProps}
      />
    );
  };

  render() {
    const isUsingObjectFit = !!this.props.style.objectFit;

    return isUsingObjectFit && supportsObjectFit
      ? this.renderNative()
      : this.renderFallback();
  }
}
