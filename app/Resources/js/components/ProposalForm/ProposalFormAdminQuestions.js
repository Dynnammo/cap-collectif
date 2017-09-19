// @flow
import * as React from 'react';
import { connect } from 'react-redux';
import { formValueSelector, arrayPush } from 'redux-form';
import { FormattedMessage } from 'react-intl';
import {
  ListGroup,
  ListGroupItem,
  ButtonToolbar,
  Button,
  Row,
  Col,
  Glyphicon,
} from 'react-bootstrap';
import ProposalFormAdminQuestionModal from './ProposalFormAdminQuestionModal';
import type { GlobalState, Dispatch } from '../../types';

const formName = 'proposal-form-admin-configuration';
const selector = formValueSelector(formName);

type Props = {
  dispatch: Dispatch,
  fields: { length: number, map: Function, remove: Function },
  questions: Array<Object>,
};
type State = { editIndex: ?number };

export class ProposalFormAdminQuestions extends React.Component<Props, State> {
  state = {
    editIndex: null,
  };

  handleClose = (index: number) => {
    const { fields, questions } = this.props;
    if (!questions[index].id) {
      fields.remove(index);
    }
    this.handleSubmit();
  };

  handleSubmit = () => {
    this.setState({ editIndex: null });
  };

  render() {
    const { dispatch, fields, questions } = this.props;
    const { editIndex } = this.state;
    return (
      <div className="form-group">
        <label style={{ marginBottom: 15, marginTop: 15 }}>Liste des autres champs</label>
        <ListGroup>
          {fields.map((member, index) => (
            <ListGroupItem key={index}>
              <ProposalFormAdminQuestionModal
                isCreating={!!questions[index].id}
                onClose={() => {
                  this.handleClose(index);
                }}
                onSubmit={this.handleSubmit}
                member={member}
                show={index === editIndex}
              />
              <Row>
                <Col xs={8}>
                  <div>
                    <strong>{questions[index].name}</strong>
                    <p>{questions[index].inputType}</p>
                  </div>
                </Col>
                <Col xs={4}>
                  <ButtonToolbar className="pull-right">
                    <Button
                      bsStyle="warning"
                      onClick={() => {
                        this.setState({ editIndex: index });
                      }}>
                      <Glyphicon glyph="pencil" /> <FormattedMessage id="global.edit" />
                    </Button>
                    <Button
                      bsStyle="danger"
                      onClick={() => {
                        // eslint-disable-next-line no-confirm
                        if (
                          window.confirm(
                            'Êtes-vous sûr de vouloir supprimer cette catégorie ?',
                            'Les propositions liées ne seront pas supprimées. Cette action est irréversible.',
                          )
                        ) {
                          fields.remove(index);
                        }
                      }}>
                      <Glyphicon glyph="trash" />
                    </Button>
                  </ButtonToolbar>
                </Col>
              </Row>
            </ListGroupItem>
          ))}
        </ListGroup>
        <Button
          style={{ marginBottom: 10 }}
          bsStyle="primary"
          onClick={() => {
            dispatch(arrayPush(formName, 'customFields', {}));
            this.setState({ editIndex: fields.length });
          }}>
          <Glyphicon glyph="plus" /> <FormattedMessage id="global.add" />
        </Button>
      </div>
    );
  }
}

const mapStateToProps = (state: GlobalState) => ({
  questions: selector(state, 'customFields'),
});

export default connect(mapStateToProps)(ProposalFormAdminQuestions);
