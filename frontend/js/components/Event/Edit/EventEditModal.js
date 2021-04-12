// @flow
import * as React from 'react';
import { Modal } from 'react-bootstrap';
import { FormattedMessage, useIntl } from 'react-intl';
import { connect } from 'react-redux';
import { graphql, createFragmentContainer } from 'react-relay';
import { isInvalid, submit, isSubmitting } from 'redux-form';
import styled, { type StyledComponent } from 'styled-components';
import { formName } from '../Form/EventForm';
import CloseButton from '../../Form/CloseButton';
import SubmitButton from '../../Form/SubmitButton';
import type { State, Dispatch } from '../../../types';
import type { EventEditModal_query } from '~relay/EventEditModal_query.graphql';
import type { EventEditModal_event } from '~relay/EventEditModal_event.graphql';
import { EventFormInModal } from '../Create/EventCreateModal';

type RelayProps = {|
  query: EventEditModal_query,
  event: EventEditModal_event,
|};

type Props = {|
  ...RelayProps,
  show: boolean,
  submitting: boolean,
  dispatch: Dispatch,
  pristine: boolean,
  handleClose: () => void,
|};

const NotifyInfoMessage: StyledComponent<{}, {}, HTMLDivElement> = styled.div`
  font-size: 14px;
  font-style: italic;
  opacity: 0.8;
`;

export const EventEditModal = ({
  submitting,
  dispatch,
  show,
  pristine,
  query,
  event,
  handleClose,
}: Props) => {
  const intl = useIntl();

  return (
    <Modal
      animation={false}
      show={show}
      onHide={handleClose}
      bsSize="large"
      aria-labelledby="contained-modal-title-lg">
      <Modal.Header closeButton closeLabel={intl.formatMessage({ id: 'close.modal' })}>
        <Modal.Title id="contained-modal-title-lg">
          <FormattedMessage id="edit-event" />
        </Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <EventFormInModal query={query} event={event} isFrontendView />
      </Modal.Body>
      <Modal.Footer>
        {event && event.participants.totalCount > 0 && (
          <NotifyInfoMessage>
            <FormattedMessage id="event-modification-notification-to-members" />
          </NotifyInfoMessage>
        )}
        <div className="mt-10">
          <CloseButton onClose={handleClose} />
          <SubmitButton
            label="global.submit"
            id="confirm-event-submit"
            disabled={pristine || submitting}
            isSubmitting={submitting}
            onSubmit={() => {
              dispatch(submit(formName));
            }}
          />
        </div>
      </Modal.Footer>
    </Modal>
  );
};

const mapStateToProps = (state: State) => ({
  invalid: isInvalid(formName)(state),
  submitting: isSubmitting(formName)(state),
});

export const container = connect<any, any, _, _, _, _>(mapStateToProps)(EventEditModal);

export default createFragmentContainer(container, {
  query: graphql`
    fragment EventEditModal_query on Query
      @argumentDefinitions(isAuthenticated: { type: "Boolean!" }) {
      ...EventForm_query @include(if: $isAuthenticated)
    }
  `,
  event: graphql`
    fragment EventEditModal_event on Event {
      ...EventForm_event
      participants {
        totalCount
      }
    }
  `,
});
