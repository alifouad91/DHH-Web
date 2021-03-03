import React from "react";

class Clock extends React.Component {
  format(time) {
    let seconds = time % 60;
    let minutes = Math.floor(time / 60);
    minutes = minutes.toString().length === 1 ? "0" + minutes : minutes;
    seconds = seconds.toString().length === 1 ? "0" + seconds : seconds;
    return minutes + ":" + seconds;
  }
  render() {
    const { time } = this.props;
    return <span className="app__timer">{this.format(time)}</span>;
  }
}

export default class Timer extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      count: props.count
    };
  }
  componentWillMount() {
    this.handleStart();
  }
  handleStart() {
    const { callBack } = this.props;
    this.timer = setInterval(() => {
      const newCount = this.state.count - 1;
      const count = newCount >= 0 ? newCount : 0;
      callBack(count);
      this.setState({ count });
    }, 1000);
  }
  render() {
    const { count } = this.state;

    return <Clock time={count} />;
  }
}
