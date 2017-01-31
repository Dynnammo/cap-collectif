// @flow
import React, { PropTypes } from 'react';
import { Panel, Button } from 'react-bootstrap';
import { connect } from 'react-redux';
import { IntlMixin } from 'react-intl';
import { submit } from 'redux-form';
import AccountForm from './AccountForm';

export const AccountBox = React.createClass({
  propTypes: {
    user: PropTypes.object.isRequired,
    submitting: PropTypes.bool.isRequired,
    dispatch: PropTypes.func.isRequired,
  },
  mixins: [IntlMixin],

  render() {
    const { submitting, dispatch } = this.props;
    const footer = (
      <Button
        id="edit-account-profile-button"
        //  dispatch(submit('account'))
        onClick={() => dispatch(confirmPassword())}
        disabled={submitting}
        bsStyle="primary"
      >
        {
          submitting
          ? this.getIntlMessage('global.loading')
          : this.getIntlMessage('global.save_modifications')
        }
      </Button>
    );
    return (
      <Panel
        header={this.getIntlMessage('profile.account.title')}
        footer={footer}
      >
        <AccountForm />
      </Panel>
    );
  },

});

const mapStateToProps = (state) => {
  return {
    user: state.default.user,
    submitting: state.user.isSubmittingAccountForm,
  };
};

export default connect(mapStateToProps)(AccountBox);
