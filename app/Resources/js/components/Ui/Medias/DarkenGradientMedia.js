// @flow
import React, { PureComponent } from 'react';
import styled from 'styled-components';

type Props = {
  children?: any,
  url: string,
  width?: string,
  height?: string,
  linearGradient?: boolean,
  alt?: string,
};

export const Container = styled.div`
  background: ${props =>
    props.linearGradient
      ? `linear-gradient(to top, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0)),
  url(${props.url})`
      : `url(${props.url})`};
  background-size: cover;
  background-repeat: no-repeat;
  width: ${props => props.width};
  height: ${props => props.height};
`;

export default class DarkenGradientMedia extends PureComponent<Props> {
  static defaultProps = {
    linearGradient: true,
    width: '100%',
    height: '100%',
  };

  render() {
    const { url, width, height, linearGradient, alt, children } = this.props;

    return (
      <Container
        url={url}
        width={width}
        height={height}
        linearGradient={linearGradient}
        role={alt ? 'img' : null}
        aria-label={alt}>
        {children}
      </Container>
    );
  }
}
