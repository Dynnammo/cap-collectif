// @flow
import React from 'react';
import { FormattedMessage } from 'react-intl';
import { connect, type MapStateToProps } from 'react-redux';
import { Field, reduxForm } from 'redux-form';
import renderInput from '../Form/Field';
import { editOpinionVersion as onSubmit } from '../../redux/modules/opinion';
import type { State } from '../../types';

export const formName = 'opinion-version-edit';

const validate = ({ confirm, title, comment }) => {
  const errors = {};
  if (!confirm) {
    errors.confirm = 'global.required';
  }
  if (title) {
    if (title.length <= 2) {
      errors.title = 'opinion.version.title_error';
    }
  } else {
    errors.title = 'global.required';
  }
  if (comment) {
    if ($(comment).text().length < 2) {
      errors.comment = 'opinion.version.comment_error';
    }
  } else {
    errors.comment = 'global.required';
  }
  return errors;
};

type Props = { versionId: string };

class OpinionVersionEditForm extends React.Component<Props> {
  render() {
    return (
      <form>
        <div className="alert alert-warning edit-confirm-alert">
          <Field
            name="confirm"
            type="checkbox"
            component={renderInput}
            children={<FormattedMessage id="opinion.version.confirm" />}
          />
        </div>
        <Field
          name="title"
          type="text"
          component={renderInput}
          label={<FormattedMessage id="opinion.version.title" />}
        />
        <Field
          name="body"
          type="editor"
          component={renderInput}
          label={<FormattedMessage id="opinion.version.body" />}
          help={<FormattedMessage id="opinion.version.body_helper" />}
        />
        <Field
          name="comment"
          type="editor"
          component={renderInput}
          label={<FormattedMessage id="opinion.version.comment" />}
          help={<FormattedMessage id="opinion.version.comment_helper" />}
        />
      </form>
    );
  }
}

const mapStateToProps: MapStateToProps<*, *, *> = (state: State) => ({
  initialValues: {
    title:
      state.opinion.currentVersionId &&
      state.opinion.versionsById[state.opinion.currentVersionId].title,
    body:
      state.opinion.currentVersionId &&
      state.opinion.versionsById[state.opinion.currentVersionId].body,
    comment:
      state.opinion.currentVersionId &&
      state.opinion.versionsById[state.opinion.currentVersionId].comment,
  },
  opinionId:
    state.opinion.currentVersionId &&
    state.opinion.versionsById[state.opinion.currentVersionId].parent.id,
  versionId: state.opinion.currentVersionId,
});

export default connect(mapStateToProps)(
  reduxForm({
    form: formName,
    onSubmit,
    validate,
  })(OpinionVersionEditForm),
);
