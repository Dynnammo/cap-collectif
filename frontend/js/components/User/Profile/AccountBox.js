// @flow
import React from 'react';
import { Panel } from 'react-bootstrap';
import { connect } from 'react-redux';
import { isInvalid } from 'redux-form';
import { FormattedMessage } from 'react-intl';
import { graphql, createFragmentContainer } from 'react-relay';
import AccountForm, { formName as accountForm } from './AccountForm';
import type { GlobalState, Dispatch } from '../../../types';
import type { AccountBox_viewer } from '~relay/AccountBox_viewer.graphql';
import type { LocaleMap } from '~ui/Button/SiteLanguageChangeButton';

type Props = {|
  viewer: AccountBox_viewer,
  dispatch: Dispatch,
  invalid: boolean,
  loginWithOpenId: boolean,
  +languageList: Array<LocaleMap>,
|};

export const AccountBox = ({ viewer, languageList, loginWithOpenId }: Props) => {
  return (
    <React.Fragment>
      <Panel>
        <Panel.Heading>
          <Panel.Title>
            <div className="panel-heading profile-header">
              <h1>
                <FormattedMessage id="profile.account.title" />
              </h1>
            </div>
          </Panel.Title>
        </Panel.Heading>
        <AccountForm
          languageList={languageList}
          viewer={viewer}
          loginWithOpenId={loginWithOpenId}
        />
      </Panel>
    </React.Fragment>
  );
};

const mapStateToProps = (state: GlobalState) => ({
  invalid: isInvalid(accountForm)(state),
});

const container = connect(mapStateToProps)(AccountBox);

export default createFragmentContainer(container, {
  viewer: graphql`
    fragment AccountBox_viewer on User {
      ...DeleteAccountModal_viewer
      ...AccountForm_viewer
    }
  `,
});
