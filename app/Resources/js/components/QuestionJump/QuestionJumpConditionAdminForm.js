// @flow
import * as React from 'react';
import { connect, type MapStateToProps } from 'react-redux';
import { formValueSelector, Field, arrayRemove } from 'redux-form';
import { FormattedMessage } from 'react-intl';
import type { GlobalState, Dispatch } from '../../types';
import component from '../Form/Field';

type RelayProps = { selectedQuestion: any };
type Props = RelayProps & {
  fields: { length: number, map: Function, remove: Function },
  questions: Object,
  formName: string,
  member: string,
  index: number,
  dispatch: Dispatch,
  oldMember: string
};

type State = {
  currentQuestion: ?number,
};

export class QuestionJumpConditionAdminForm extends React.Component<Props, State> {
  state = {
    currentQuestion: null,
  };

  handleQuestionChange = (e: $FlowFixMe) => {
    this.setState({ currentQuestion: e.target.value });
  };

  render() {
    const { index, questions, selectedQuestion, member, dispatch, formName, oldMember} = this.props;
    const { currentQuestion } = this.state;
    const arrayQuestions = [];
    questions.map(question => {
      if(question.kind !== 'simple') {
        arrayQuestions[question.id] = question.questionChoices;
      }
    });
    return (
      <div className="movable-element" key={index}>
        <div style={{marginBottom:'10px'}}>
          <h4 className="panel-title">
            <i className="cap cap-android-menu" style={{ color: 'rgb(3, 136, 204)', fontSize:'15px', marginRight:'10px' }} />
            Si la réponse à la question :
            <button type="button" style={{ border: 'none', float:'right', backgroundColor:'#f5f5f5' }} title="Remove Member" onClick={() => dispatch(arrayRemove(formName, `${oldMember}.conditions`, index))}>X</button>
          </h4>
        </div>
        <Field
          id={`${member}.question.id`}
          name={`${member}.question.id`}
          normalize={val => val && parseInt(val, 10)}
          className={'col-md-8'}
          type="select"
          onChange={e => {
            this.handleQuestionChange(e);
          }}
          component={component}>
          {questions.map((question, questionIndex) => {
            if(question.kind !== 'simple') {
              return (
              <option value={question.id}>
                {questionIndex}. {question.title}
              </option>
              );
            }
          })}
        </Field>
        <Field
          id={`${member}.operator`}
          name={`${member}.operator`}
          className={'col-md-3'}
          type="select"
          component={component}>
          <option value="IS">
            <FormattedMessage id="is" />
          </option>
          <option value="IS_NOT">
            <FormattedMessage id="is-not" />
          </option>
        </Field>
        <Field
          id={`${member}.value.id`}
          name={`${member}.value.id`}
          type="select"
          component={component}>
          {/* $FlowFixMe */}
          {currentQuestion !== null || selectedQuestion !== undefined
            ? arrayQuestions[currentQuestion !== null ? currentQuestion : selectedQuestion].map(
                (questionChoice, questionChoiceIndex) => (
                  <option value={questionChoice.id}>
                    {questionChoiceIndex}. {questionChoice.title}
                  </option>
                ),
              )
            : ''}
        </Field>
      </div>
    );
  }
}

const mapStateToProps: MapStateToProps<*, *, *> = (state: GlobalState, props: Props) => {
  const selector = formValueSelector(props.formName);
  return {
    selectedQuestion: selector(state, `${props.member}.question.id`),
  };
};

export default connect(mapStateToProps)(QuestionJumpConditionAdminForm);
