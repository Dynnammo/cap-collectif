// @flow
import React from 'react';
import { connect } from 'react-redux';
import { FormattedMessage } from 'react-intl';
import { ToggleButton, Button, Modal } from 'react-bootstrap';
import { reduxForm, Field, change } from 'redux-form';

import CloseButton from '../Form/CloseButton';
import SubmitButton from '../Form/SubmitButton';
import component from '../Form/Field';
import CreateQuestionnaireMutation from '../../mutations/CreateQuestionnaireMutation';

const formName = 'questionnaire-form-admin-create';

const validate = (values: Object) => {
  const errors = {};

  if (!values.title || values.title.length <= 2) {
    errors.title = 'title';
  }

  if (!values.type) {
    errors.type = 'type';
  }

  return errors;
};

const onSubmit = values => {
  CreateQuestionnaireMutation.commit({ input: values }).then(() => {
    window.location.reload();
  });
};

export type Props = {|
  ...ReduxFormFormProps,
  submitting: boolean,
  handleSubmit: () => void,
  submit: Function,
|};

type State = {
  showModal: boolean,
};

export class QuestionnaireAdminCreateButton extends React.Component<Props, State> {
  state = { showModal: false };

  render() {
    const { submitting, handleSubmit, submit, dispatch } = this.props;
    const { showModal } = this.state;
    return (
      <div>
        <Button
          id="add-questionnaire"
          bsStyle="default"
          style={{ marginTop: 10 }}
          onClick={() => {
            this.setState({ showModal: true });
          }}>
          <FormattedMessage id='global.add' />
        </Button>
        <Modal
          animation={false}
          show={showModal}
          onHide={() => {
            this.setState({ showModal: false });
          }}
          bsSize="large"
          aria-labelledby="contained-modal-title-lg">
          <Modal.Header closeButton>
            <Modal.Title id="contained-modal-title-lg">
              <FormattedMessage id="global.questionnaire" />
            </Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <form onSubmit={() => handleSubmit}>
              <Field type="radio-buttons" id="questionnaire_type" name="type" component={component}>
                <ToggleButton
                  id="type-voting"
                  onClick={() => dispatch(change(formName, 'type', 'VOTING'))}
                  value="VOTING">
                  <FormattedMessage id="voting" />
                </ToggleButton>
                <ToggleButton
                  id="type-questionnaire"
                  onClick={() => dispatch(change(formName, 'type', 'QUESTIONNAIRE'))}
                  value="QUESTIONNAIRE">
                  <FormattedMessage id="global.questionnaire" />
                </ToggleButton>
              </Field>
              <Field
                name="title"
                label={<FormattedMessage id='global.title' />}
                component={component}
                type="text"
                id="questionnaire_title"
              />
            </form>
          </Modal.Body>
          <Modal.Footer>
            <CloseButton
              onClose={() => {
                this.setState({ showModal: false });
              }}
            />
            <SubmitButton
              id="confirm-questionnaire-create"
              isSubmitting={submitting}
              onSubmit={() => {
                submit(formName);
              }}
            />
          </Modal.Footer>
        </Modal>
      </div>
    );
  }
}

const form = reduxForm({
  onSubmit,
  validate,
  form: formName,
})(QuestionnaireAdminCreateButton);

export default connect()(form);
