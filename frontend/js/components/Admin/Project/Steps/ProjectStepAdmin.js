// @flow
import React, { useState } from 'react';
import { FieldArray } from 'redux-form';
import { createFragmentContainer, graphql } from 'react-relay';
import { ButtonToolbar } from 'react-bootstrap';
import { FormattedMessage, type IntlShape } from 'react-intl';
import ProjectStepAdminList from './ProjectStepAdminList';
import ProjectAdminStepFormModal from '../Step/ProjectAdminStepFormModal';
import {
  ProjectBoxHeader,
  StepMenuItem,
  ProjectBoxContainer,
  StepCreateButton,
} from '../Form/ProjectAdminForm.style';
import { STEP_TYPES } from '~/constants/StepTypeConstants';
import { type StepTypes } from '../Form/ProjectAdminForm';
import type { ProjectStepAdmin_project } from '~relay/ProjectStepAdmin_project.graphql';

type Props = {|
  ...ReduxFormFormProps,
  form: string,
  intl: IntlShape,
  project: ProjectStepAdmin_project,
|};

export const validate = ({ steps }: StepTypes) => {
  const errors = {};

  /* AbstractStep */
  const titles = steps.map(s => s.title.toLowerCase());

  if (titles.some((item, index) => titles.indexOf(item) !== index)) {
    errors.steps = { _error: 'unique-step-title' };
  }

  /* QuestionnaireStep */
  const questionnaires = steps
    .filter(s => s.type === 'QuestionnaireStep')
    .map(s => s.questionnaire.value);

  if (questionnaires.some((item, index) => questionnaires.indexOf(item) !== index)) {
    errors.steps = { _error: 'duplicate-questionnaire' };
  }

  return errors;
};

export const ProjectStepAdmin = ({ form, project }: Props) => {
  const [stepType, setStepType] = useState('OtherStep');
  const [showAddStepModal, displayAddStepModal] = useState(false);

  return (
    <div className="col-md-12">
      <ProjectBoxContainer className="box container-fluid">
        <ProjectBoxHeader>
          <h4>
            <FormattedMessage id="project.show.meta.step.title" />
          </h4>
        </ProjectBoxHeader>
        <div className="box-content">
          <div className="form-group" id="project_form_admin_steps_panel">
            <FieldArray
              name="steps"
              component={ProjectStepAdminList}
              formName={form}
              project={project}
            />
            <ButtonToolbar>
              <ProjectAdminStepFormModal
                onClose={() => displayAddStepModal(false)}
                step={null}
                isCreating
                type={stepType}
                show={showAddStepModal}
                form={form}
                project={project}
              />
              <StepCreateButton
                id="js-btn-create-step"
                bsStyle="primary"
                title={
                  <>
                    <i className="fa fa-plus-circle" /> <FormattedMessage id="global.add" />
                  </>
                }>
                {STEP_TYPES.map((st, idx) => (
                  <StepMenuItem
                    key={idx}
                    id={st.label}
                    onClick={() => {
                      setStepType(st.value);
                      displayAddStepModal(true);
                    }}>
                    <FormattedMessage id={st.label} />
                  </StepMenuItem>
                ))}
              </StepCreateButton>
            </ButtonToolbar>
          </div>
        </div>
      </ProjectBoxContainer>
    </div>
  );
};

export default createFragmentContainer(ProjectStepAdmin, {
  project: graphql`
    fragment ProjectStepAdmin_project on Project {
      ...ProjectAdminStepFormModal_project
      ...ProjectStepAdminList_project
    }
  `,
});
