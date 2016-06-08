import React from 'react';
import { IntlMixin } from 'react-intl';
import classNames from 'classnames';
import UserAvatar from './UserAvatar';
import UserLink from './UserLink';

const UserPreview = React.createClass({
  propTypes: {
    user: React.PropTypes.object,
    username: React.PropTypes.string,
    className: React.PropTypes.string,
    style: React.PropTypes.object,
  },
  mixins: [IntlMixin],

  getDefaultProps() {
    return {
      user: null,
      username: null,
      className: '',
      style: {},
    };
  },

  render() {
    const user = this.props.user;
    const username = this.props.username === 'ANONYMOUS' ? this.getIntlMessage('global.anonymous') : this.props.username;
    if (!user && !username) {
      return null;
    }
    const classes = {
      'media': true,
      'media--user-thumbnail': true,
      'box': true,
      [this.props.className]: true,
    };

    return (
      <div className={classNames(classes)} style={this.props.style}>
        <UserAvatar user={user} className="pull-left" />
        <div className="media-body">
          <p className="media--aligned media--macro__user  small">
            {
              user
              ? <UserLink user={user} />
              : <span>{username}</span>
            }
          </p>
        </div>
      </div>
    );
  },

});

export default UserPreview;
