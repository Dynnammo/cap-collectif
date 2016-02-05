import React from 'react';
import { IntlMixin, FormattedMessage } from 'react-intl';
import OpinionActions from '../../../actions/OpinionActions';
import LoginStore from '../../../stores/LoginStore';
import { Modal, Button } from 'react-bootstrap';
import CloseButton from '../../Form/CloseButton';
import SubmitButton from '../../Form/SubmitButton';

const OpinionDelete = React.createClass({
  propTypes: {
    opinion: React.PropTypes.object.isRequired,
  },
  mixins: [IntlMixin],

  getIntialState() {
    return {
      showModal: false,
      isSubmitting: false,
    };
  },

  showModal() {
    this.setState({
      showModal: true,
    });
  },

  hideModal() {
    this.setState({
      hideModal: true,
    });
  },

  isVersion() {
    return !!this.props.opinion.parent;
  },

  delete() {
    this.setState({ isSubmitting: true });
    if (this.isVersion()) {
      OpinionActions.deleteVersion(this.props.opinion.id, this.props.opinion.parent.id)
        .then(() => {
          window.location.href = this.props.opinion._links.parent;
        })
      ;
    } else {
      OpinionActions.deleteOpinion(this.props.opinion.id)
        .then(() => {
          window.location.href = this.props.opinion._links.type;
        })
      ;
    }
  },

  isTheUserTheAuthor() {
    if (this.props.opinion.author === null || !LoginStore.isLoggedIn()) {
      return false;
    }
    return LoginStore.user.uniqueId === this.props.opinion.author.uniqueId;
  },

  render() {
    if (this.isTheUserTheAuthor()) {
      return (
        <div>
          <Button
            id="opinion-delete"
            className="pull-right btn--outline btn-danger"
            onClick={this.showModal}
            style={{ marginLeft: '5px' }}
          >
            <i className="cap cap-bin-2"></i> {this.getIntlMessage('global.remove')}
          </Button>
          <Modal
            animation={false}
            show={this.state.showModal}
            onHide={this.hideModal}
            bsSize="large"
            aria-labelledby="contained-modal-title-lg"
          >
            <Modal.Header closeButton>
              <Modal.Title id="contained-modal-title-lg">
                { this.getIntlMessage('global.remove') }
              </Modal.Title>
            </Modal.Header>
            <Modal.Body>
              <p>
                <FormattedMessage
                  message={this.getIntlMessage('opinion.delete.confirm')}
                  title={this.props.opinion.title}
                />
              </p>
            </Modal.Body>
            <Modal.Footer>
              <CloseButton onClose={this.hideModal} />
              <SubmitButton
                id="confirm-opinion-delete"
                isSubmitting={this.state.isSubmitting}
                onSubmit={this.delete}
                label="global.remove"
                bsStyle="danger"
              />
            </Modal.Footer>
          </Modal>
        </div>
      );
    }

    return null;
  },

});

export default OpinionDelete;
