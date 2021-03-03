import React from 'react';
import ReactDOM from 'react-dom';
import { Button } from 'antd';

import config from '../../config';

class BookingCancelled extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      propertyDetails: {},
      loading: true
    };
  }

  goToUrl = url => {
    location.href = `${config.BASE_URL}${url}`;
  };

  render() {
    const { title, subTitle } = this.props;
    return (
      <div className='page__confirmbooking__content'>
        <h4>{title}</h4>

        <p>{subTitle}</p>
        <div className='buttons'>
          <Button type='secondary' onClick={() => this.goToUrl('/')}>
            GO TO HOMEPAGE
          </Button>
          <Button
            type='primary'
            onClick={() => this.goToUrl('/profile/mybookings')}
          >
            VIEW MY BOOKINGS
          </Button>
        </div>
      </div>
    );
  }
}

const $el = $('.page__cancelledbooking__render');
if ($el) {
  $el.each((index, el) => {
    const $this = $(el);
    const title = $this.data('title');
    const subTitle = $this.data('sub');
    ReactDOM.render(<BookingCancelled title={title} subTitle={subTitle} />, el);
  });
}
