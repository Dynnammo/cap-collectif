// @flow
import * as React from 'react';
import { connect, type MapStateToProps } from 'react-redux';
import { formValueSelector, arrayPush, arrayMove } from 'redux-form';
import { FormattedMessage, injectIntl, type IntlShape } from 'react-intl';
import { bindActionCreators } from 'redux';
import { ListGroup, ListGroupItem, ButtonToolbar, Button, Row, Col } from 'react-bootstrap';
import {
  DragDropContext,
  Droppable,
  Draggable,
  DropResult,
  DraggableProvided,
  DraggableStyle,
} from 'react-beautiful-dnd';
import ProposalFormAdminQuestionModal from './ProposalFormAdminQuestionModal';
import type { GlobalState, Dispatch } from '../../types';
import { ProposalFormAdminDeleteQuestionModal } from './ProposalFormAdminDeleteQuestionModal';
import { QuestionItem } from '../Question/QuestionItem';
import SectionAdminForm from './Section/SectionAdminForm';

type Props = {
  dispatch: Dispatch,
  fields: { length: number, map: Function, remove: Function },
  questions: Array<Object>,
  formName: string,
  intl: IntlShape,
  arrayPush: Function,
  arrayMove: Function,
};

type State = {
  editIndex: ?number,
  editIndexSection: ?number,
  showDeleteModal: boolean,
  deleteIndex: ?number,
};

const getItemStyle = (isDragging: boolean, draggableStyle: DraggableStyle) => ({
  userSelect: 'none',
  padding: 2,
  margin: `0 0 8px 0`,
  borderRadius: '8px',

  // change background colour if dragging
  background: isDragging ? 'lightgreen' : '',

  ...draggableStyle,
});

export class ProposalFormAdminQuestions extends React.Component<Props, State> {
  constructor(props: Props) {
    super(props);

    this.state = {
      editIndex: null,
      editIndexSection: null,
      deleteIndex: null,
      showDeleteModal: false,
    };
  }

  onDragEnd = (result: DropResult) => {
    // dropped outside the list
    if (!result.destination) {
      return;
    }

    this.props.arrayMove(
      this.props.formName,
      'questions',
      result.source.index,
      result.destination.index,
    );
  };

  handleClose = (index: number) => {
    const { fields, questions } = this.props;
    if (!questions[index].id) {
      fields.remove(index);
    }
    this.handleSubmit();
  };

  handleDelete = (index: number) => {
    this.setState({
      showDeleteModal: true,
      deleteIndex: index,
    });
  };

  handleDeleteAction = () => {
    const { deleteIndex } = this.state;
    const { fields } = this.props;

    fields.remove(deleteIndex);

    this.setState({
      showDeleteModal: false,
      deleteIndex: null,
    });
  };

  handleEdit = (index: number, type: string) => {
    if (type === 'section') {
      this.setState({ editIndexSection: index });
    } else {
      this.setState({ editIndex: index });
    }
  };

  handleSubmit = () => {
    this.setState({ editIndex: null });
    this.setState({ editIndexSection: null });
  };

  handleCreateQuestion = () => {
    const { fields, formName } = this.props;

    this.props.arrayPush(formName, 'questions', {
      private: false,
      required: false,
    });

    this.setState({ editIndex: fields.length });
  };

  handleCreateSection = () => {
    const { fields, formName } = this.props;

    this.props.arrayPush(formName, 'questions', {
      private: false,
      required: false,
      type: 'section',
    });
    this.setState({ editIndexSection: fields.length });
  };

  handleCancelModal = () => {
    this.setState({
      showDeleteModal: false,
      deleteIndex: null,
    });
  };

  render() {
    const { fields, questions, formName } = this.props;
    const { editIndex, showDeleteModal, editIndexSection } = this.state;
    return (
      <div className="form-group" id="proposal_form_admin_questions_panel_personal">
        <ProposalFormAdminDeleteQuestionModal
          isShow={showDeleteModal}
          cancelAction={this.handleCancelModal}
          deleteAction={this.handleDeleteAction}
        />
        <ListGroup>
          <DragDropContext onDragEnd={this.onDragEnd}>
            <Droppable droppableId="droppable">
              {(provided: DraggableProvided) => (
                <div ref={provided.innerRef}>
                  {fields.map((member, index) => (
                    <Draggable
                      key={questions[index].id}
                      draggableId={questions[index].id}
                      index={index}>
                      {(providedDraggable: DraggableProvided, snapshot) => (
                        <div
                          ref={providedDraggable.innerRef}
                          {...providedDraggable.draggableProps}
                          {...providedDraggable.dragHandleProps}
                          style={getItemStyle(
                            snapshot.isDragging,
                            providedDraggable.draggableProps.style,
                          )}>
                          <ListGroupItem key={index}>
                            <ProposalFormAdminQuestionModal
                              isCreating={!!questions[index].id}
                              onClose={this.handleClose.bind(this, index)}
                              onSubmit={this.handleSubmit}
                              member={member}
                              show={index === editIndex}
                              formName={formName}
                            />
                            <SectionAdminForm
                              show={index === editIndexSection}
                              member={member}
                              isCreating={!!questions[index].id}
                              onClose={this.handleClose.bind(this, index)}
                              onSubmit={this.handleSubmit}
                              formName={formName}
                            />
                            <Row>
                              <Col xs={8} style={{ display: 'flex' }}>
                                <QuestionItem question={questions[index]} />
                              </Col>
                              <Col xs={4}>
                                <ButtonToolbar className="pull-right">
                                  <Button
                                    bsStyle="warning"
                                    className="btn-outline-warning"
                                    onClick={this.handleEdit.bind(
                                      this,
                                      index,
                                      questions[index].type,
                                    )}>
                                    <i className="fa fa-pencil" />{' '}
                                    <FormattedMessage id="global.edit" />
                                  </Button>
                                  <Button
                                    bsStyle="danger"
                                    className="btn-outline-danger"
                                    onClick={this.handleDelete.bind(this, index)}>
                                    <i className="cap cap-times" />{' '}
                                    <FormattedMessage id="global.delete" />
                                  </Button>
                                </ButtonToolbar>
                              </Col>
                              {provided.placeholder}
                            </Row>
                          </ListGroupItem>
                        </div>
                      )}
                    </Draggable>
                  ))}
                </div>
              )}
            </Droppable>
          </DragDropContext>
        </ListGroup>
        <Button
          bsStyle="primary"
          className="btn-outline-primary box-content__toolbar"
          onClick={this.handleCreateSection}>
          <i className="cap cap-folder-add" /> <FormattedMessage id="create-section" />
        </Button>
        <Button
          bsStyle="primary"
          className="btn-outline-primary box-content__toolbar"
          onClick={this.handleCreateQuestion}>
          <i className="cap cap-bubble-add-2" />{' '}
          <FormattedMessage id="question_modal.create.title" />
        </Button>
      </div>
    );
  }
}

const mapStateToProps: MapStateToProps<*, *, *> = (state: GlobalState, props: Props) => {
  const selector = formValueSelector(props.formName);
  return {
    questions: selector(state, 'questions'),
  };
};

const mapDispatchToProps = dispatch => bindActionCreators({ arrayPush, arrayMove }, dispatch);

export default connect(
  mapStateToProps,
  mapDispatchToProps,
)(injectIntl(ProposalFormAdminQuestions));
