// @flow
import React, { useState } from 'react';
import { reduxForm, Field, submit, SubmissionError, clearSubmitErrors } from 'redux-form';
import { graphql, createFragmentContainer } from 'react-relay';
import { FormattedMessage, FormattedHTMLMessage } from 'react-intl';
import { Button, Alert } from 'react-bootstrap';
import { connect } from 'react-redux';
import AppDispatcher from '../../../dispatchers/AppDispatcher';
import component from '../../Form/Field';
import AddArgumentMutation from '../../../mutations/AddArgumentMutation';
import type { ArgumentType, State, Dispatch } from '../../../types';
import type { ArgumentCreate_argumentable } from '~relay/ArgumentCreate_argumentable.graphql';
import RequirementsFormModal from '../../Requirements/RequirementsModal';

type FormValues = { body: ?string };
type FormValidValues = { body: string };

type Props = {|
  ...ReduxFormFormProps,
  type: ArgumentType,
  argumentable: ArgumentCreate_argumentable,
  user: { id: string, isEmailConfirmed: boolean },
  submitting: boolean,
  form: string,
  dispatch: Dispatch,
|};

const onSubmit = (
  values: FormValidValues,
  dispatch: Dispatch,
  { argumentable, type, reset, user }: Props,
) => {
  const input = {
    argumentableId: argumentable.id,
    body: values.body,
    type: type === 'FOR' || type === 'SIMPLE' ? 'FOR' : 'AGAINST',
  };

  return AddArgumentMutation.commit({ input }, user.isEmailConfirmed)
    .then(res => {
      if (res.addArgument && res.addArgument.userErrors.length === 0) {
        AppDispatcher.dispatch({
          actionType: 'UPDATE_ALERT',
          alert: { bsStyle: 'success', content: 'alert.success.add.argument' },
        });
        reset();
        return;
      }
      if (res.addArgument) {
        for (const error of res.addArgument.userErrors) {
          if (error.message === 'You contributed too many times.') {
            throw new SubmissionError({ _error: 'publication-limit-reached' });
          }
        }
      }
      throw new SubmissionError({ _error: 'global.error.server.form' });
    })
    .catch(e => {
      if (e instanceof SubmissionError) {
        throw e;
      }
      throw new SubmissionError({ _error: 'global.error.server.form' });
    });
};

const validate = ({ body }: FormValues) => {
  const errors = {};
  if (!body || body.replace(/<\/?[^>]+(>|$)/g, '').length <= 2) {
    errors.body = 'argument.constraints.min';
  }
  if (body && body.length > 2000) {
    errors.body = 'argument.constraints.max';
  }
  return errors;
};

export const ArgumentCreate = ({
  user,
  argumentable,
  type,
  dispatch,
  form,
  submitting,
  error,
}: Props) => {
  const [showModal, setShowModal] = useState<boolean>(false);

  const openModal = () => {
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
  };

  const disabled = !argumentable.contribuable || !user;
  return (
    <div className="opinion__body box">
      {argumentable.step && (
        <RequirementsFormModal step={argumentable.step} handleClose={closeModal} show={showModal} />
      )}
      <div className="opinion__data" style={{ overflow: 'visible' }}>
        <form id={`argument-form--${type}`}>
          {error && (
            <Alert
              bsStyle="warning"
              onDismiss={() => {
                dispatch(clearSubmitErrors(form));
              }}>
              {error === 'publication-limit-reached' ? (
                <div>
                  <h4>
                    <strong>
                      <FormattedMessage id="publication-limit-reached" />
                    </strong>
                  </h4>
                  <FormattedMessage id="publication-limit-reached-argument-content" />
                </div>
              ) : (
                <FormattedHTMLMessage id="global.error.server.form" />
              )}
            </Alert>
          )}
          <Field
            name="body"
            component={component}
            id={`arguments-body-${type}`}
            type="textarea"
            rows={2}
            label={<FormattedMessage id={`argument.${type === 'AGAINST' ? 'no' : 'yes'}.add`} />}
            placeholder={`argument.${type === 'AGAINST' ? 'no' : 'yes'}.add`}
            labelClassName="sr-only"
            disabled={disabled}
          />
          {!disabled && (
            <Button
              disabled={submitting}
              onClick={() => {
                if (
                  argumentable.step &&
                  argumentable.step.requirements &&
                  !argumentable.step.requirements.viewerMeetsTheRequirements
                ) {
                  openModal();
                  return;
                }
                dispatch(submit(form));
              }}
              bsStyle="primary">
              <FormattedMessage id={submitting ? 'global.loading' : 'global.send'} />
            </Button>
          )}
        </form>
      </div>
    </div>
  );
};

const mapStateToProps = (state: State) => ({
  user: state.user.user,
});

const container = connect<any, any, _, _, _, _>(mapStateToProps)(
  reduxForm({
    onSubmit,
    validate,
  })(ArgumentCreate),
);

export default createFragmentContainer(container, {
  argumentable: graphql`
    fragment ArgumentCreate_argumentable on Argumentable
    @argumentDefinitions(isAuthenticated: { type: "Boolean!" }) {
      id
      contribuable
      ... on Opinion {
        step {
          requirements {
            viewerMeetsTheRequirements @include(if: $isAuthenticated)
          }
          ...RequirementsFormLegacy_step @arguments(isAuthenticated: $isAuthenticated)

          ...RequirementsModal_step @arguments(isAuthenticated: $isAuthenticated)
        }
      }
      ... on Version {
        step {
          requirements {
            viewerMeetsTheRequirements @include(if: $isAuthenticated)
          }
          ...RequirementsFormLegacy_step @arguments(isAuthenticated: $isAuthenticated)

          ...RequirementsModal_step @arguments(isAuthenticated: $isAuthenticated)
        }
      }
    }
  `,
});
