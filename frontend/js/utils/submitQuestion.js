// @flow
import type { responsesHelper_adminQuestion } from '~relay/responsesHelper_adminQuestion.graphql';

// Easyfix: We should rely on __typename MultipleChoiceQuestion instead
const multipleChoiceQuestions = ['button', 'radio', 'select', 'checkbox', 'ranking'];

export type QuestionsInReduxForm = $ReadOnlyArray<responsesHelper_adminQuestion>;

const convertJump = jump => ({
  id: jump.id,
  conditions:
    jump.conditions &&
    jump.conditions.filter(Boolean).map(condition => ({
      ...condition,
      question: condition.question.id,
      value: condition.value ? condition.value.id : null,
    })),
  origin: jump.origin.id,
  destination: jump.destination.id,
});

export const submitQuestion = (questions: QuestionsInReduxForm) =>
  // $FlowFixMe Missing type annotation for U.
  questions.filter(Boolean).map(question => {
    const questionInput = {
      question: {
        ...question,
        alwaysJumpDestinationQuestion: question.alwaysJumpDestinationQuestion
          ? question.alwaysJumpDestinationQuestion.id
          : null,
        rangeMax: question.rangeMax ? parseInt(question.rangeMax, 10) : undefined,
        rangeMin: question.rangeMin ? parseInt(question.rangeMin, 10) : undefined,
        jumps: question.jumps ? question.jumps.filter(Boolean).map(convertJump) : [],
        validationRule:
          question.validationRule && question.validationRule.type.length
            ? question.validationRule
            : question.__typename === 'MultipleChoiceQuestion'
            ? null
            : undefined,
        // Easyfix: this should be refactored
        otherAllowed: question.isOtherAllowed,
        isOtherAllowed: undefined,
        // List of not send properties to server
        __typename: undefined,
        kind: undefined,
        number: undefined,
        position: undefined,
        choices: undefined,
        destinationJumps: undefined,
      },
    };
    if (
      multipleChoiceQuestions.indexOf(question.type) !== -1 &&
      typeof question.choices !== 'undefined'
    ) {
      questionInput.question.choices = question.choices
        ? // $FlowFixMe question.choices is not an array in the query, don't have time to rewrite the whole type
          question.choices.map(choice => ({
            ...choice,
            // We only send ids to the server
            image: choice.image ? choice.image.id : null,
            color: choice.color || null,
            // List of not send properties to server
            kind: undefined,
          }))
        : [];
    }
    return questionInput;
  });
