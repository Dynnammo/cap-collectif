// @flow
import * as React from 'react';
import { connect } from 'react-redux';
import { Field, FieldArray, formValueSelector } from 'redux-form';
import { FormattedMessage } from 'react-intl';
// TODO https://github.com/cap-collectif/platform/issues/7774
// eslint-disable-next-line no-restricted-imports
import { ListGroup, Button } from 'react-bootstrap';
import QuestionJumpConditionsAdminForm from './QuestionJumpConditionsAdminForm';
import type { GlobalState } from '../../types';
import component from '../Form/Field';
import type { responsesHelper_adminQuestion } from '~relay/responsesHelper_adminQuestion.graphql';
import type { QuestionnaireAdminConfigurationForm_questionnaire } from '~relay/QuestionnaireAdminConfigurationForm_questionnaire.graphql';

type ParentProps = {
  formName: string,
  oldMember: string,
};
type Props = ParentProps & {
  fields: { length: number, map: Function, remove: Function, push: Function },
  jumps: $PropertyType<responsesHelper_adminQuestion, 'jumps'>,
  questions: $PropertyType<QuestionnaireAdminConfigurationForm_questionnaire, 'questions'>,
  formName: string,
  currentQuestion: Object,
};

export class QuestionsJumpAdminForm extends React.Component<Props> {
  handleAlwaysJumpChange(e: SyntheticInputEvent<HTMLSelectElement>) {
    console.log(e.target.value);
  }

  render() {
    const { fields, jumps, questions, oldMember, formName, currentQuestion } = this.props;
    console.log(questions);
    return (
      <div className="form-group" id="questions_choice_panel_personal">
        <ListGroup>
          {fields.map((member, index) => (
            <div className="panel-custom panel panel-default">
              <div className="panel-heading">
                <i
                  className="cap cap-android-menu"
                  style={{ color: '#0388CC', fontSize: '20px' }}
                />
                <h3 className="panel-title">
                  <FormattedMessage id="answering-this-question" />
                </h3>
                <button
                  type="button"
                  style={{ border: 'none', fontSize: '20px', backgroundColor: '#f5f5f5' }}
                  onClick={() => fields.remove(index)}>
                  X
                </button>
              </div>
              <div className="panel-body">
                <FieldArray
                  name={`${member}.conditions`}
                  component={QuestionJumpConditionsAdminForm}
                  formName={formName}
                  member={member}
                />
              </div>
            </div>
          ))}
        </ListGroup>
        <Button
          bsStyle="primary"
          className="btn--outline box-content__toolbar"
          onClick={() => {
            fields.push({
              always: false,
              origin: {
                id: currentQuestion.id,
              },
              conditions: [
                {
                  question: {
                    id: currentQuestion.id,
                  },
                  value: currentQuestion.choices[0],
                  operator: 'IS',
                },
              ],
              destination: {
                id: currentQuestion.id,
              },
            });
          }}>
          <i className="fa fa-plus-circle" /> <FormattedMessage id="global.add" />
        </Button>
        <div className="movable-element">
          <div className="mb-10">
            <h4 className="panel-title">
              <FormattedMessage
                id={jumps && jumps.length === 0 ? 'always-go-to' : 'jump-other-goto'}
              />
            </h4>
            <Field
              id={`${oldMember}.alwaysJump`}
              name={`${oldMember}.alwaysJump`}
              type="select"
              normalize={val => (val !== '' ? val : null)}
              onChange={this.handleAlwaysJumpChange}
              component={component}>
              <option value="" />
              {questions.map((question, i) => (
                <option value={question.id}>{`${i}. ${question.title}`}</option>
              ))}
            </Field>
          </div>
        </div>
      </div>
    );
  }
}

const mapStateToProps = (state: GlobalState, props: ParentProps) => {
  const selector = formValueSelector(props.formName);
  return {
    currentQuestion: selector(state, `${props.oldMember}`),
    jumps: selector(state, `${props.oldMember}.jumps`),
    questions: selector(state, 'questions'),
  };
};

export default connect(mapStateToProps)(QuestionsJumpAdminForm);
