// @flow
import * as React from 'react';

type Props = {
  show?: boolean,
  children?: any,
};

export class Loader extends React.Component<Props> {
  static defaultProps = {
    show: true,
    children: null,
  };

  render() {
    const { children, show } = this.props;
    if (show) {
      return (
        <div className="col-xs-2 col-xs-offset-5 spinner-loader-container">
          <div className="spinner-loader" />
        </div>
      );
    }
    return Array.isArray(children) ? <div>{children}</div> : children;
  }
}

export default Loader;
