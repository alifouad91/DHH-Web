import React from "react";
import ReactDOM from "react-dom";
import moment from "moment";
import { Spin } from "antd";
import { Fade } from "react-reveal";

import { getBlogItems } from "../services";
import config from "../config";

class BlogPage extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      loading: true,
      error: false,
      data: []
    };
  }

  componentWillMount() {
    getBlogItems(
      data => {
        console.log(data);
        this.setState({ data, loading: false });
      },
      err => {
        this.setState({ error: true, loading: false });
      }
    );
  }

  render() {
    const { data, loading } = this.state;
    return (
      <div className={`row ${loading ? "text-center" : ""}`}>
        {loading ? (
          <Spin size="large" tip="Loading blogs. Please wait." />
        ) : (
          _.map(data, (item, index) => {
            const { title, pageImage, category, author, date, url } = item;
            return (
              <a
                key={index}
                href={url}
                className={`page__blog__item__col ${
                  index === 0 || index > 3 ? "col-sm-12 col-lg-12 col-md-12" : "col-sm-12 col-md-4 col-lg-4"
                }`}
              >
                <Fade bottom>
                  <div className={`page__blog__item`}>
                    <div className="page__blog__item__image">
                      <img src={pageImage} alt="page-blog-image"/>
                    </div>
                    <div className="page__blog__item__details">
                      <p className="small">{category}</p>
                      <h3>{title}</h3>
                      <span>{`${author} · ${moment(date).format(
                        config.blogDateFormat
                      )}`}</span>
                    </div>
                  </div>
                </Fade>
              </a>
            );
          })
        )}
      </div>
    );
  }
}

const $el = $(".page__blog__items");
if ($el.length) {
  $el.each((index, el) => {
    ReactDOM.render(<BlogPage />, el);
  });
}
