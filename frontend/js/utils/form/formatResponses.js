// @flow
import type {
  Question,
  Questions,
  QuestionType,
  ResponseInReduxForm,
  ResponsesInReduxForm,
} from '~/components/Form/Form.type';
import type { MultipleChoiceQuestionValidationRulesTypes } from '~relay/responsesHelper_question.graphql';

export type FormattedResponse = {|
  idQuestion: string,
  type: QuestionType,
  value: ?string | ?Array<string>,
  otherValue: ?string,
  hidden?: boolean,
  required: boolean,
  validationRule?: ?{|
    +type: MultipleChoiceQuestionValidationRulesTypes,
    +number: number,
  |},
  constraintes?: ?{|
    +isRangeBetween: boolean,
    +rangeMin: ?number,
    +rangeMax: ?number,
  |},
|};

const formatResponses = (
  questions: Questions = [],
  responses: ResponsesInReduxForm = [],
): Array<FormattedResponse> =>
  responses.reduce(
    (formattedResponses: Array<FormattedResponse>, response: ResponseInReduxForm) => {
      const { question: idQuestion, value } = response;

      const questionOfResponse: ?Question = questions.find(q => q.id === idQuestion);

      if (!questionOfResponse) {
        return [];
      }

      const {
        type,
        required,
        validationRule,
        isOtherAllowed,
        hidden,
        isRangeBetween,
        rangeMin,
        rangeMax,
      } = questionOfResponse;

      if (value) {
        let formattedValue: ?string | ?Array<string> = null;
        let otherValue: ?string = null;

        if (type === 'select' && value.value) {
          // $FlowFixMe
          formattedValue = value.value;
        } else if ((type === 'checkbox' || type === 'radio') && value.labels) {
          // $FlowFixMe
          formattedValue = value.labels;
          // $FlowFixMe
          otherValue = isOtherAllowed && value.other ? value.other : null;
        } else if (type === 'ranking' || type === 'siren' || type === 'medias') {
          // $FlowFixMe
          formattedValue = value;
        } else {
          // $FlowFixMe
          formattedValue = value;
        }

        formattedResponses.push({
          idQuestion,
          type,
          // $FlowFixMe
          value: formattedValue,
          hidden,
          otherValue,
          required,
          validationRule,
          constraintes: isRangeBetween
            ? {
                isRangeBetween,
                rangeMin,
                rangeMax,
              }
            : null,
        });
      } else {
        formattedResponses.push({
          idQuestion,
          type,
          value: null,
          hidden,
          otherValue: null,
          required,
          validationRule,
        });
      }

      return formattedResponses;
    },
    [],
  );

export default formatResponses;
