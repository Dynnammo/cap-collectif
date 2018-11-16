// @flow
import React from 'react';
import { FormattedMessage } from 'react-intl';
import { Button, Modal, ListGroup } from 'react-bootstrap';
import CloseButton from '../../Form/CloseButton';
import ProjectStatsListItem from './ProjectStatsListItem';
import ProjectStatsActions from '../../../actions/ProjectStatsActions';
import Loader from '../../Ui/FeedbacksIndicators/Loader';

type Props = {
  type: string,
  stepId: string,
  icon: string,
  label: string,
  data: Object,
  showPercentage: boolean,
  isCurrency: boolean,
  theme: ?string,
  district: ?string,
};

type State = {
  showModal: boolean,
  isLoading: boolean,
  data: Object,
};

class ProjectStatsModal extends React.Component<Props, State> {
  constructor(props: Props) {
    super(props);
    const { data } = props;

    this.state = {
      showModal: false,
      isLoading: true,
      data,
    };
  }

  componentDidMount() {
    this.loadData();
  }

  componentWillReceiveProps(nextProps: Props) {
    const { district, theme } = this.props;
    if (theme !== nextProps.theme || district !== nextProps.district) {
      this.loadData();
    }
  }

  loadData = () => {
    const { district, stepId, theme, type } = this.props;
    ProjectStatsActions.load(stepId, type, null, theme, district).then(response => {
      this.setState({
        data: response.data,
        isLoading: false,
      });
    });
  };

  showModal = () => {
    this.setState({
      showModal: true,
    });
  };

  hideModal = () => {
    this.setState({
      showModal: false,
    });
  };

  render() {
    const { icon, isCurrency, label, showPercentage, stepId, type } = this.props;
    const id = `project-stats-modal-${stepId}-${type}`;
    return (
      <div>
        <Button
          onClick={this.showModal}
          disabled={this.state.showModal}
          bsStyle="primary"
          className="btn--outline stats__all-button">
          {<FormattedMessage id="project.stats.display.all" />}
        </Button>
        <Modal
          id={`stats-modal-${stepId}-${type}`}
          animation={false}
          show={this.state.showModal}
          onHide={this.hideModal}
          aria-labelledby={`${id}-title`}>
          <Modal.Header closeButton>
            <Modal.Title id={`${id}-title`}>
              <i className={icon} /> {<FormattedMessage id={label} />}
            </Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <Loader show={this.state.isLoading}>
              <ListGroup className="stats__list">
                {this.state.data.values.length > 0 &&
                  this.state.data.values.map((row, index) => (
                    <ProjectStatsListItem
                      key={index}
                      item={row}
                      showPercentage={showPercentage}
                      isCurrency={isCurrency}
                    />
                  ))}
              </ListGroup>
            </Loader>
          </Modal.Body>
          <Modal.Footer>
            <CloseButton onClose={this.hideModal} label="project.stats.modal.close" />
          </Modal.Footer>
        </Modal>
      </div>
    );
  }
}

export default ProjectStatsModal;
