/* eslint-env jest */
/* @flow */
import React from 'react';
import { shallow } from 'enzyme';
import { UserAdminAccount } from './UserAdminAccount';
import { intlMock, formMock } from '../../../mocks';

describe('<UserAdminAccount/>', () => {
  const props1 = {
    ...formMock,
    intl: intlMock,
  };

  const userExpiredAndSubscribed = {
    subscribedToNewsLetterAt: '2018-05-03 11:11:11',
    expiredAt: '2018-06-03 11:11:11',
  };

  const userNotExpiredAndNotSubscribed = {
    subscribedToNewsLetterAt: null,
    expiredAt: null,
  };
  it('should render with user is admin or viewer', () => {
    const wrapper = shallow(
      <UserAdminAccount
        {...props1}
        isViewerOrSuperAdmin
        user={userExpiredAndSubscribed}
        userDeletedIsNotViewer
      />,
    );
    wrapper.setState({
      showDeleteAccountModal: false,
    });
    expect(wrapper).toMatchSnapshot();
  });

  it('should render with user is not admin or viewer ', () => {
    const wrapper = shallow(
      <UserAdminAccount
        {...props1}
        user={userNotExpiredAndNotSubscribed}
        isViewerOrSuperAdmin={false}
        userDeletedIsNotViewer={false}
      />,
    );
    wrapper.setState({
      showDeleteAccountModal: false,
    });
    expect(wrapper).toMatchSnapshot();
  });
});
