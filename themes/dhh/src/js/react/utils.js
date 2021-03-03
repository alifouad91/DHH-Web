import React from "react";
import _ from "lodash";
import moment from "moment";
import { message } from "antd";
import config from "./config";

export const displayLandlordMessage = (msg) => {
  message.warning(
    `You are not allowed to ${msg} because you are a landlord.`,
    5
  );
};

export const displayLoginMessage = () => {
  // const Msg = () => {
  //   return (
  //     <span>
  //       You are not logged in. Click this{" "}
  //       <a href={`${config.BASE_URL}/login`}>link</a> to login.
  //     </span>
  //   );
  // };
  // message.warning(<Msg />, 5);
  $.fancybox.open({
    src: "#login-popup-form",
    opts: {
      touch: false,
      beforeClose: () => {
        sessionStorage.removeItem("redirectPath");
      },
      beforeShow: () => {
        sessionStorage.setItem("redirectPath", location.href);
      },
    },
  });
};

export const generateLoadingObject = (num) => {
  let obj = [];
  _.range(0, num).forEach((current, index, range) => {
    obj = [...obj, { isLoading: true }];
  });
  return obj;
};

export const htmlToTabObject = (element) => {
  let arr = [];
  element.each((index, item) => {
    const $this = $(item);
    const title = $this.data("title");
    const content = $this.html();
    // console.log(content);
    let obj = {
      collapse: [],
    };
    let string = "";
    obj["title"] = title;
    // obj['content'] = content;

    $(content).each((indx, child) => {
      const $ch = $(child);
      const isCollapse = $ch.data("collapse");
      if (isCollapse) {
        const cTitle = $ch.find("span").html();
        const cContent = $ch.find("> div").html();
        obj["collapse"] = [
          ...obj["collapse"],
          {
            title: cTitle,
            content: cContent,
          },
        ];
      } else {
        if ($ch.html()) {
          string += $ch.html();
        }
      }
    });
    obj["content"] = string;
    arr = [...arr, obj];
  });

  return arr;
};

export const htmlToCollapseObject = (element) => {
  let arr = [];
  element.each((index, item) => {
    const $this = $(item);
    const title = $this.find("span").html();
    const content = $this.find("> div").html();
    arr = [
      ...arr,
      {
        title,
        content,
      },
    ];
  });
  return arr;
};

export const formatImageGalleryObject = (arr) => {
  let obj = [];
  _.map(arr, (item) => {
    obj = [
      ...obj,
      {
        original: item,
        thumbnail: item,
      },
    ];
  });
  return obj;
};

export const formatGalleryModalObject = (arr) => {
  let obj = [];
  _.map(arr, (item) => {
    obj = [
      ...obj,
      {
        photo: item,
      },
    ];
  });
  return obj;
};

export const filterReviews = (reviews, rating) => {
  return _.filter(reviews, (review) => {
    return Number(review.reviewRating) === Number(rating);
  });
};

export const createReviewTabOject = (reviews) => {
  return _.countBy(reviews, "reviewRating");
};

export const toQueryString = (obj) => {
  return Object.entries(obj)
    .map(([key, val]) => `${key}=${val}`)
    .join("&");
};

export const isDateSameYear = (d1, d2) => {
  const y1 = moment(d1).format("YYYY");
  const y2 = moment(d2).format("YYYY");
  return y1 === y2;
};

export const isDateSameMonth = (d1, d2) => {
  const y1 = moment(d1).format("MMM");
  const y2 = moment(d2).format("MMM");
  return y1 === y2;
};

export const stripCurrencySymbol = (num) => {
  return Number(num.replace(/[^0-9.-]+/g, ""));
};

export const generateYears = () => {
  let current = new Date();
  current = current.getFullYear();
  current = _.range(current, current - 100);
  return _.map(current, (val) => {
    return { value: val };
  });
};

export const generateDays = () => {
  const current = _.range(1, 32);
  return _.map(current, (val) => {
    return { value: val };
  });
};

export const generateMonths = () => {
  const months = moment.months();
  return _.map(months, (val) => {
    return { value: val };
  });
};

export const objectToFormData = (obj) =>
  Object.keys(obj).reduce((formData, key) => {
    formData.append(key, obj[key]);
    return formData;
  }, new FormData());

export const generateAvatarFromString = (str) => {
  return str
    ? str
        .split(" ")
        .map((x) => x[0])
        .join("")
        .substring(0, 2)
    : "U";
};
