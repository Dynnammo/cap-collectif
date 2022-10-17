// @flow
import React from 'react';
import moment from 'moment';
import { graphql, createFragmentContainer } from 'react-relay';
import { FormattedDate } from 'react-intl';
import type { StyledComponent } from 'styled-components';
import styled from 'styled-components';
import Media from '../../Ui/Medias/Media/Media';
import UserAvatarDeprecated from '../../User/UserAvatarDeprecated';
import UserLink from '../../User/UserLink';
import UnpublishedLabel from '../../Publishable/UnpublishedLabel';
import type { ProposalPreviewUser_proposal } from '~relay/ProposalPreviewUser_proposal.graphql';

type Props = { proposal: ProposalPreviewUser_proposal };

const MediaBody: StyledComponent<{}, {}, typeof Media.Body> = styled(Media.Body)`
  overflow: visible;
  .cap-popover {
    button {
      background-color: transparent !important;
      border: none !important;
    }
  }
`;
export class ProposalPreviewUser extends React.Component<Props> {
  render() {
    const { proposal } = this.props;
    const date = proposal.publishedAt || proposal.createdAt;

    return (
      <Media>
        <Media.Left>
          {/* $FlowFixMe Will be a fragment soon */}
          <UserAvatarDeprecated user={proposal.author} />
        </Media.Left>
        <MediaBody>
          <UserLink className="excerpt" user={proposal.author} />
          <div className="excerpt small">
            <FormattedDate value={moment(date)} day="numeric" month="long" year="numeric" />
          </div>
          <UnpublishedLabel publishable={proposal} />
        </MediaBody>
      </Media>
    );
  }
}

export default createFragmentContainer(ProposalPreviewUser, {
  proposal: graphql`
    fragment ProposalPreviewUser_proposal on Proposal {
      ...UnpublishedLabel_publishable
      publishedAt
      createdAt
      author {
        ...UserAvatar_user
        ...UserLink_user
        id
        displayName
        url
        media {
          url
        }
      }
    }
  `,
});
