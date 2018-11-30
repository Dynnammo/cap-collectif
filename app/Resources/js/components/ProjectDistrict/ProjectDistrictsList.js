// @flow
import * as React from 'react';
import { graphql, createRefetchContainer, type RelayRefetchProp } from 'react-relay';
import { ListGroup, ListGroupItem, Row, Col, ButtonToolbar } from 'react-bootstrap';
import type { ProjectDistrictsList_districts } from './__generated__/ProjectDistrictsList_districts.graphql';
import EditButton from '../Ui/Button/EditButton';
import DeleteButtonPopover from '../Ui/Button/DeleteButtonPopover';
import AddButton from '../Ui/Button/AddButton';
import ProjectDistrictForm from './ProjectDistrictForm';
import DeleteProjectDistrictMutation from '../../mutations/DeleteProjectDistrictMutation';

type RelayProps = {
  districts: ProjectDistrictsList_districts,
};

type Props = RelayProps & {
  relay: RelayRefetchProp,
};

type State = {
  isModalOpen: boolean,
  isCreating: boolean,
  editDistrictId: ?string,
};

export class ProjectDistrictsList extends React.Component<Props, State> {
  constructor(props: Props) {
    super(props);

    this.state = {
      isModalOpen: false,
      isCreating: true,
      editDistrictId: null,
    };
  }

  openModal = () => {
    this.setState({
      isModalOpen: true,
      editDistrictId: null,
    });
  };

  closeModal = () => {
    this.setState({
      isModalOpen: false,
      isCreating: true,
      editDistrictId: null,
    });
  };

  handleCreate = () => {
    this.setState({
      isModalOpen: true,
      isCreating: true,
      editDistrictId: null,
    });
  };

  handleDelete = (deleteId: string) => {
    const input = {
      id: deleteId,
    };

    DeleteProjectDistrictMutation.commit({ input }).then(() => {
      this._refetch();
    });
  };

  handleEdit = (editeId: string) => {
    this.setState({
      isCreating: false,
      isModalOpen: true,
      editDistrictId: editeId,
    });
  };

  _refetch = () => {
    const { refetch } = this.props.relay;
    refetch({ refetch: true });
  };

  render() {
    const { districts } = this.props;
    const { isModalOpen, isCreating, editDistrictId } = this.state;

    return (
      <>
        <ProjectDistrictForm
          member="projectDistrict"
          show={isModalOpen}
          isCreating={isCreating}
          handleClose={this.closeModal}
          handleRefresh={this._refetch}
          district={districts.filter(district => district.id === editDistrictId).shift()}
        />
        <ListGroup>
          {districts.map(district => (
            <ListGroupItem key={district.id}>
              <Row>
                <Col xs={8}>
                  <strong>{district.name}</strong>
                </Col>
                <Col xs={4}>
                  <ButtonToolbar className="pull-right">
                    <EditButton onClick={() => this.handleEdit(district.id)} />
                    <DeleteButtonPopover handleValidate={() => this.handleDelete(district.id)} />
                  </ButtonToolbar>
                </Col>
              </Row>
            </ListGroupItem>
          ))}
        </ListGroup>
        <AddButton onClick={this.handleCreate} />
      </>
    );
  }
}

export default createRefetchContainer(
  ProjectDistrictsList,
  {
    districts: graphql`
      fragment ProjectDistrictsList_districts on ProjectDistrict @relay(plural: true) {
        id
        name
        geojson
        displayedOnMap
        border {
          isEnable
          color
          opacity
          size
        }
        background {
          isEnable
          color
          opacity
        }
      }
    `,
  },
  graphql`
    query ProjectDistrictsListQuery {
      districts: projectDistricts {
        ...ProjectDistrictsList_districts
      }
    }
  `,
);
