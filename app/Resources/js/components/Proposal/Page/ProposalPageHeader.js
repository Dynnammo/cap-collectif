// @flow
import React from 'react';
import { FormattedMessage, FormattedDate } from 'react-intl';
import { connect, type MapStateToProps } from 'react-redux';
import classNames from 'classnames';
import moment from 'moment';
import { createFragmentContainer, graphql } from 'react-relay';
import UserAvatar from '../../User/UserAvatar';
import UserLink from '../../User/UserLink';
import ProposalVoteButtonWrapper from '../Vote/ProposalVoteButtonWrapper';
import ProposalFollowButton from '../Follow/ProposalFollowButton';
import type { ProposalPageHeader_proposal } from './__generated__/ProposalPageHeader_proposal.graphql';
import type { State } from '../../../types';

type Props = {
  proposal: ProposalPageHeader_proposal,
  className: string,
  referer: string,
  isAuthenticated: boolean,
};

export class ProposalPageHeader extends React.Component<Props> {
  static defaultProps = {
    className: '',
  };

  render() {
    const { proposal, className, referer, isAuthenticated } = this.props;
    const createdDate = (
      <FormattedDate
        value={moment(proposal.createdAt)}
        day="numeric"
        month="long"
        year="numeric"
        hour="numeric"
        minute="numeric"
      />
    );
    const updatedDate = (
      <FormattedDate
        value={moment(proposal.updatedAt)}
        day="numeric"
        month="long"
        year="numeric"
        hour="numeric"
        minute="numeric"
      />
    );

    const classes = {
      proposal__header: true,
      [className]: true,
    };

    return (
      <div className={classNames(classes)}>
        <div>
          <a style={{ textDecoration: 'none' }} href={referer || proposal.show_url}>
            <i className="cap cap-arrow-65-1 icon--black" />{' '}
            <FormattedMessage id="proposal.back" />
          </a>
        </div>
        <h1 className="consultation__header__title h1">{proposal.title}</h1>
        <div className="media mb-15">
          <UserAvatar className="pull-left" user={proposal.author} />
          <div className="media-body">
            <p className="media--aligned excerpt">
              <FormattedMessage
                id="proposal.infos.header"
                values={{
                  user: <UserLink user={proposal.author} />,
                  createdDate,
                }}
              />
              {moment(proposal.updatedAt).diff(proposal.createdAt, 'seconds') > 1 && (
                <span>
                  {' • '}
                  <FormattedMessage
                    id="global.edited_on"
                    values={{
                      updated: updatedDate,
                    }}
                  />
                </span>
              )}
            </p>
          </div>
        </div>
        {proposal.publicationStatus !== 'DRAFT' && (
          <ProposalVoteButtonWrapper
            id="proposal-vote-btn"
            proposal={proposal}
            className="pull-right btn-lg"
          />
        )}
        {proposal.publicationStatus !== 'DRAFT' && (
          /* $FlowFixMe https://github.com/cap-collectif/platform/issues/4973 */
          <ProposalFollowButton
            proposal={proposal}
            isAuthenticated={isAuthenticated}
            className="pull-right btn-lg"
          />
        )}
      </div>
    );
  }
}

const mapStateToProps: MapStateToProps<*, *, *> = (state: State) => {
  return {
    referer: state.proposal.referer,
    isAuthenticated: state.user.user !== null,
  };
};

const container = connect(mapStateToProps)(ProposalPageHeader);

export default createFragmentContainer(
  container,
  graphql`
    fragment ProposalPageHeader_proposal on Proposal {
      id
      ...ProposalFollowButton_proposal @arguments(isAuthenticated: $isAuthenticated)
      title
      theme {
        title
      }
      currentVotableStep {
        id
        ... on CollectStep {
          open
        }
        ... on SelectionStep {
          open
        }
      }
      author {
        username
        displayName
        media {
          url
        }
      }
      createdAt
      updatedAt
      publicationStatus
      show_url
    }
  `,
);
