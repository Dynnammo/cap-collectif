// @flow
import * as React from 'react';
import { useIntl } from 'react-intl';
import moment from 'moment';
import { graphql, commitLocalUpdate, useFragment } from 'react-relay';
import { ConnectionHandler, fetchQuery_DEPRECATED } from 'relay-runtime';
import {
  Modal,
  Button,
  Heading,
  CapUIModalSize,
  MultiStepModal,
  Flex,
  Tag,
  CapUIIcon,
  InfoMessage,
} from '@cap-collectif/ui';
import { useDispatch, useSelector } from 'react-redux';
import { useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import { Box } from 'reakit';
import { isPristine, submit } from 'redux-form';
import { closeVoteModal, vote } from '~/redux/modules/proposal';
import ProposalsUserVotesTable, { getFormName } from '../../Project/Votes/ProposalsUserVotesTable';
import environment from '~/createRelayEnvironment';
import type { GlobalState } from '~/types';
import type { ProposalVoteModal_proposal$key } from '~relay/ProposalVoteModal_proposal.graphql';
import type { ProposalVoteModal_step$key } from '~relay/ProposalVoteModal_step.graphql';
import WYSIWYGRender from '../../Form/WYSIWYGRender';
import { isInterpellationContextFromStep } from '~/utils/interpellationLabelHelper';
import usePrevious from '~/utils/hooks/usePrevious';
import ResetCss from '~/utils/ResetCss';
import ProposalVoteRequirementsModal from './ProposalVoteModals/ProposalVoteRequirementsModal';
import ProposalVoteConfirmationModal from './ProposalVoteModals/ProposalVoteConfirmationModal';

import invariant from '~/utils/invariant';
import UpdateProposalVotesMutation from '~/mutations/UpdateProposalVotesMutation';
import { refetchViewer } from '~/components/Requirements/RequirementsFormLegacy';
import type { ProposalVoteModal_viewer$key } from '~relay/ProposalVoteModal_viewer.graphql';
import getInitialValues from '~/components/Proposal/Vote/utils/getInitialValues';
import generateValidationSchema from '~/components/Proposal/Vote/utils/generateValidationSchema';
import {
  ProposalVoteModalContainer,
  ProposalVoteMultiModalContainer,
} from './ProposalVoteModal.style';
import VoteMinAlert from '~/components/Project/Votes/VoteMinAlert';
import formatPhoneNumber from '~/utils/formatPhoneNumber';
import CookieMonster from '~/CookieMonster';

type Props = {
  proposal: ProposalVoteModal_proposal$key,
  step: ProposalVoteModal_step$key,
  viewer: ProposalVoteModal_viewer$key,
};

type RequirementType = {|
  +__typename: string,
  +id: string,
  +viewerMeetsTheRequirement?: boolean,
  +viewerDateOfBirth?: ?string,
  +viewerAddress?: ?{|
    +formatted: ?string,
    +json: string,
  |},
  +viewerValue?: ?string,
  +label?: string,
|};

const PROPOSAL_FRAGMENT = graphql`
  fragment ProposalVoteModal_proposal on Proposal {
    id
  }
`;

const STEP_FRAGMENT = graphql`
  fragment ProposalVoteModal_step on ProposalStep
  @argumentDefinitions(isAuthenticated: { type: "Boolean!" }, token: { type: "String" }) {
    id
    votesRanking
    votesHelpText
    ...VoteMinAlert_step @arguments(token: $token)
    ... on RequirementStep {
      requirements {
        edges {
          node {
            __typename
            id
            viewerMeetsTheRequirement @include(if: $isAuthenticated)
            ... on DateOfBirthRequirement {
              viewerDateOfBirth @include(if: $isAuthenticated)
            }
            ... on PostalAddressRequirement {
              viewerAddress @include(if: $isAuthenticated) {
                formatted
                json
              }
            }
            ... on FirstnameRequirement {
              viewerValue @include(if: $isAuthenticated)
            }
            ... on LastnameRequirement {
              viewerValue @include(if: $isAuthenticated)
            }
            ... on PhoneRequirement {
              viewerValue @include(if: $isAuthenticated)
            }
            ... on IdentificationCodeRequirement {
              viewerValue @include(if: $isAuthenticated)
            }
            ... on FranceConnectRequirement {
              viewerValue @include(if: $isAuthenticated)
            }
            ... on CheckboxRequirement {
              label
              viewerMeetsTheRequirement
              id
            }
            ... on PhoneVerifiedRequirement {
              viewerMeetsTheRequirement
            }
          }
        }
      }
    }
    isSecretBallot
    canDisplayBallot
    publishedVoteDate
    ...interpellationLabelHelper_step @relay(mask: false)
    ...ProposalsUserVotesTable_step @arguments(token: $token)
    viewerVotes(orderBy: { field: POSITION, direction: ASC }, token: $token)
      @include(if: $isAuthenticated) {
      ...ProposalsUserVotesTable_votes
      totalCount
    }
  }
`;
const VIEWER_FRAGMENT = graphql`
  fragment ProposalVoteModal_viewer on User {
    phone
    phoneConfirmed
    ...ProposalVoteConfirmationModal_viewer
  }
`;

export const ProposalVoteModal = ({
  proposal: proposalRef,
  step: stepRef,
  viewer: viewerRef,
}: Props) => {
  const intl = useIntl();
  const proposal = useFragment(PROPOSAL_FRAGMENT, proposalRef);
  const step = useFragment(STEP_FRAGMENT, stepRef);
  const viewer = useFragment(VIEWER_FRAGMENT, viewerRef);
  const localState = useSelector((state: GlobalState) => state);
  const { currentVoteModal } = useSelector((state: GlobalState) => state.proposal);
  const showModal = !!currentVoteModal && currentVoteModal === proposal.id;
  const prevShowModal = usePrevious(showModal);
  const { user } = useSelector((state: GlobalState) => state.user);
  const viewerIsConfirmedByEmail = user && user.isEmailConfirmed;
  const isAuthenticated = !!user;
  const dispatch = useDispatch();
  const [isLoading, setIsLoading] = React.useState<boolean>(false);
  const pristine = isPristine(getFormName(step))(localState);
  const token = CookieMonster.getAnonymousAuthenticatedWithConfirmedPhone();

  // Create temp vote to display Proposal in ProposalsUserVotesTable
  const createTmpVote = React.useCallback(() => {
    commitLocalUpdate(environment, store => {
      const dataID = `client:newTmpVote:${proposal.id}`;

      let newNode = store.get(dataID);
      if (!newNode) {
        newNode = store.create(dataID, 'ProposalVote');
      }
      newNode.setValue(viewerIsConfirmedByEmail, 'published');
      if (!viewerIsConfirmedByEmail) {
        newNode.setValue('WAITING_AUTHOR_CONFIRMATION', 'notPublishedReason');
      }
      newNode.setValue(false, 'anonymous');
      newNode.setValue(null, 'id'); // This will be used to know that this is the tmp vote

      // $FlowFixMe Cannot call newNode.setLinkedRecord with store.get(...) bound to record
      newNode.setLinkedRecord(store.get(proposal.id), 'proposal');

      // Create a new edge
      const edgeID = `client:newTmpEdge:${proposal.id}`;
      let newEdge = store.get(edgeID);
      if (!newEdge) {
        newEdge = store.create(edgeID, 'ProposalVoteEdge');
      }
      newEdge.setLinkedRecord(newNode, 'node');

      const stepProxy = store.get(step.id);
      if (!stepProxy) return;

      const args = token
        ? { orderBy: { field: 'POSITION', direction: 'ASC' }, token }
        : { orderBy: { field: 'POSITION', direction: 'ASC' } };

      const connection = stepProxy.getLinkedRecord('viewerVotes', args);
      if (!connection) {
        return;
      }
      ConnectionHandler.insertEdgeAfter(connection, newEdge);
    });
  }, [proposal.id, step.id, viewerIsConfirmedByEmail, token]);

  const deleteTmpVote = React.useCallback(() => {
    commitLocalUpdate(environment, store => {
      const dataID = `client:newTmpVote:${proposal.id}`;
      const stepProxy = store.get(step.id);
      if (!stepProxy) return;
      const args = token
        ? { orderBy: { field: 'POSITION', direction: 'ASC' }, token }
        : { orderBy: { field: 'POSITION', direction: 'ASC' } };
      const connection = stepProxy.getLinkedRecord('viewerVotes', args);
      if (connection) {
        ConnectionHandler.deleteNode(connection, dataID);
      }
      store.delete(dataID);
    });
  }, [proposal.id, step.id, token]);

  React.useEffect(() => {
    if (!prevShowModal && showModal) {
      createTmpVote();
    } else if (!showModal && prevShowModal) {
      deleteTmpVote();
    }
  }, [prevShowModal, showModal, deleteTmpVote, createTmpVote]);

  const getModalVoteTitleTranslation = () => {
    const isInterpellation = isInterpellationContextFromStep(step);
    if (step.votesRanking) {
      if (isInterpellation) {
        return 'project.supports.title';
      }

      return 'proposal.validate.vote';
    }
    if (isInterpellation) {
      return 'global.support.for';
    }

    return 'global.vote.for';
  };

  const onSubmit = (values: { votes: Array<{ public: boolean, id: string }> }) => {
    const tmpVote = values.votes.filter(v => v.id === null)[0];
    if (!tmpVote) return;
    // First we add the vote
    return vote(dispatch, step.id, proposal.id, !tmpVote.public, intl).then(data => {
      if (
        !data ||
        !data.addProposalVote ||
        !data.addProposalVote.voteEdge ||
        !data.addProposalVote.voteEdge.node ||
        typeof data.addProposalVote.voteEdge === 'undefined'
      ) {
        invariant(false, 'The vote id is missing.');
      }
      tmpVote.id = data.addProposalVote.voteEdge.node.id;

      // If the user didn't reorder
      // or update any vote privacy
      // we are clean
      if (!step.votesRanking && pristine) {
        return true;
      }

      // Otherwise we update/reorder votes
      return UpdateProposalVotesMutation.commit(
        {
          input: {
            step: step.id,
            votes: values.votes
              .filter(voteFilter => voteFilter.id !== null)
              .map(v => ({ id: v.id, anonymous: !v.public })),
          },
          stepId: step.id,
          isAuthenticated,
          token: null,
        },
        { id: null, position: -1, isVoteRanking: step.votesRanking },
      );
    });
  };

  const onHide = () => {
    dispatch(closeVoteModal());
  };

  const keyTradForModalVoteTitle = getModalVoteTitleTranslation();

  let votesHelpText =
    step.isSecretBallot && !step.publishedVoteDate && !step.canDisplayBallot
      ? intl.formatMessage({ id: 'publish-ballot-no-date-help-text' })
      : '';
  votesHelpText =
    step.isSecretBallot && step.publishedVoteDate && !step.canDisplayBallot
      ? intl.formatMessage(
          { id: 'publish-ballot-date-help-text' },
          {
            date: moment(step.publishedVoteDate).format('DD/MM/YYYY'),
            time: moment(step.publishedVoteDate).format('HH:mm'),
          },
        )
      : votesHelpText;
  if (step.votesHelpText) {
    votesHelpText = votesHelpText
      ? `${votesHelpText} ${step.votesHelpText}`
      : `${step.votesHelpText}`;
  }

  const requirements: RequirementType[] =
    step.requirements?.edges
      ?.filter(Boolean)
      .map(edge => edge.node)
      .filter(Boolean) || [];

  // Check if only Phone Number verification is required to vote
  const isPhoneVerificationOnly =
    requirements.filter(
      requirement =>
        requirement.__typename !== 'PhoneVerifiedRequirement' &&
        requirement.__typename !== 'PhoneRequirement',
    ).length === 0;

  const hasPhoneRequirements =
    requirements.filter(
      requrement =>
        requrement.__typename === 'PhoneVerifiedRequirement' ||
        requrement.__typename === 'PhoneRequirement',
    ).length === 2;

  const initialValues = getInitialValues(
    requirements,
    isPhoneVerificationOnly,
    hasPhoneRequirements,
    viewer,
  );

  const requirementsFormSchema = generateValidationSchema(initialValues, isAuthenticated, intl);

  const requirementsForm = useForm<any>({
    mode: 'onChange',
    defaultValues: initialValues,
    resolver: yupResolver(requirementsFormSchema),
  });

  const validationForm = useForm<any>({
    mode: 'onChange',
    defaultValues: { code: 0 },
  });

  /* # CHECK PHONE REQUIREMENT # */
  const phoneFieldValue = requirementsForm.watch('PhoneVerifiedRequirement.phoneNumber');
  const hasFieldPhoneUnchanged = user?.phone
    ? formatPhoneNumber(user.phone) === phoneFieldValue
    : false;
  const hasViewerPhoneRequirementVerified =
    requirements.find(requirement => requirement.__typename === 'PhoneVerifiedRequirement')
      ?.viewerMeetsTheRequirement || false;

  const needToVerifyPhone =
    hasPhoneRequirements && (!hasFieldPhoneUnchanged || !hasViewerPhoneRequirementVerified);

  const allRequirementsMet = requirements?.every(
    requirement => requirement.viewerMeetsTheRequirement,
  );

  return !allRequirementsMet ? (
    step.requirements ? (
      <ProposalVoteMultiModalContainer
        baseId="proposal-vote-modal"
        id="proposal-vote-modal"
        onClose={onHide}
        aria-labelledby="contained-modal-title-lg"
        size={CapUIModalSize.Md}
        fullSizeOnMobile
        show={showModal}>
        <ProposalVoteRequirementsModal
          modalTitle={keyTradForModalVoteTitle}
          isPhoneVerificationOnly={isPhoneVerificationOnly}
          initialValues={initialValues}
          hasPhoneRequirements={hasPhoneRequirements}
          requirementsForm={requirementsForm}
          isLoading={isLoading}
          setIsLoading={setIsLoading}
          needToVerifyPhone={needToVerifyPhone}
        />
        {needToVerifyPhone && (
          <ProposalVoteConfirmationModal
            viewer={viewer}
            setIsLoading={setIsLoading}
            validationForm={validationForm}
            isLoading={isLoading}
            needToVerifyPhone={needToVerifyPhone}
            modalTitle={keyTradForModalVoteTitle}
          />
        )}
        <>
          <ResetCss>
            <MultiStepModal.Header>
              <Heading>{intl.formatMessage({ id: keyTradForModalVoteTitle })}</Heading>
            </MultiStepModal.Header>
          </ResetCss>
          <MultiStepModal.Body>
            <Box id="proposal-validate-vote-modal">
              <Flex direction="column" align="flex-start" spacing={6}>
                <ProposalsUserVotesTable onSubmit={onSubmit} step={step} votes={step.viewerVotes} />
                {votesHelpText && (
                  <InfoMessage variant="info" width="100%">
                    <InfoMessage.Title>
                      {intl.formatMessage({
                        id: isInterpellationContextFromStep(step)
                          ? 'admin.fields.step.supportsHelpText'
                          : 'admin.fields.step.votesHelpText',
                      })}
                    </InfoMessage.Title>
                    <InfoMessage.Content>
                      <WYSIWYGRender value={votesHelpText} />
                    </InfoMessage.Content>
                  </InfoMessage>
                )}
              </Flex>
            </Box>
          </MultiStepModal.Body>
          <MultiStepModal.Footer>
            <Button
              id="confirm-proposal-vote"
              variant="primary"
              variantColor="primary"
              variantSize="big"
              onClick={() => {
                dispatch(submit(getFormName(step)));
                fetchQuery_DEPRECATED(environment, refetchViewer, {
                  stepId: step.id,
                  isAuthenticated,
                });
                onHide();
              }}>
              {intl.formatMessage({ id: 'proposal.validate.vote' })}
            </Button>
          </MultiStepModal.Footer>
        </>
      </ProposalVoteMultiModalContainer>
    ) : null
  ) : (
    <ProposalVoteModalContainer
      baseId="proposal-vote-modal"
      id="proposal-vote-modal"
      onClose={onHide}
      ariaLabel="contained-modal-title-lg"
      size={CapUIModalSize.Md}
      fullSizeOnMobile
      show={showModal}>
      {({ hide }) => (
        <>
          <ResetCss>
            <Modal.Header>
              <Heading>{intl.formatMessage({ id: 'proposal.validate.votes' })}</Heading>
            </Modal.Header>
          </ResetCss>

          <Modal.Body>
            <Flex direction="column" align="flex-start" spacing={6}>
              {requirements?.length ? (
                <Tag variantColor="green">
                  <Tag.LeftIcon name={CapUIIcon.Check} />
                  <Tag.Label>{intl.formatMessage({ id: 'vote.conditions.met' })}</Tag.Label>
                </Tag>
              ) : null}
              <VoteMinAlert step={step} translationKey={getModalVoteTitleTranslation()} />
              <ProposalsUserVotesTable onSubmit={onSubmit} step={step} votes={step.viewerVotes} />
              {votesHelpText && (
                <InfoMessage variant="info" width="100%">
                  <InfoMessage.Title>
                    {intl.formatMessage({
                      id: isInterpellationContextFromStep(step)
                        ? 'admin.fields.step.supportsHelpText'
                        : 'admin.fields.step.votesHelpText',
                    })}
                  </InfoMessage.Title>
                  <InfoMessage.Content>
                    <WYSIWYGRender value={votesHelpText} />
                  </InfoMessage.Content>
                </InfoMessage>
              )}
            </Flex>
          </Modal.Body>
          <Modal.Footer>
            <Button
              variant="primary"
              variantColor="primary"
              variantSize="big"
              id="confirm-proposal-vote"
              onClick={() => {
                dispatch(submit(getFormName(step)));
                fetchQuery_DEPRECATED(environment, refetchViewer, {
                  stepId: step.id,
                  isAuthenticated,
                });
                hide();
              }}>
              {intl.formatMessage({ id: 'proposal.validate.vote' })}
            </Button>
          </Modal.Footer>
        </>
      )}
    </ProposalVoteModalContainer>
  );
};

export default ProposalVoteModal;
