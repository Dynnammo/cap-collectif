// @flow
import * as React from 'react';
import { IntlProvider } from 'react-intl-redux';
// import { type IntlShape } from 'react-intl';

// eslint-disable-next-line no-console
const onError = (e: Error) => console.log(e);

type Props = {|
  timeZone: string,
  children: React.Node,
|};

const CapcoIntlProvider = ({ children }: Props) => (
  <IntlProvider textComponent="span" onError={onError}>
    {children}
  </IntlProvider>
);

export default CapcoIntlProvider;
