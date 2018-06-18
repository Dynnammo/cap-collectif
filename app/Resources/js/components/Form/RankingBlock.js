import React from 'react';
import { FormattedMessage } from 'react-intl';
import { Row, Col, ListGroup } from 'react-bootstrap';
import { DropTarget, DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import ReactDOM from 'react-dom';
import RankingBox from './RankingBox';
import { ITEM_TYPE } from '../../constants/RankingConstants';

const itemTarget = {
  drop() {},
};

type Props = {
  field: Object,
  connectDropTarget: Function,
  onRankingChange: Function,
  disabled: boolean,
  onBlur: Function,
};

export class RankingBlock extends React.Component<Props> {
  static displayName = 'RankingBlock';

  static defaultProps = {
    disabled: false,
  };

  state = {
    items: {
      pickBox: this.props.field.choices,
      choiceBox: this.props.field.values || [],
    },
    choicesHeight: 'auto',
  };

  componentDidMount() {
    this.recalculateChoicesHeight();
  }

  moveItem = (atList, atIndex, it) => {
    const { onRankingChange, onBlur } = this.props;
    const { item, list, index } = this.findItem(it.id);
    const items = JSON.parse(JSON.stringify(this.state.items));
    items[list].splice(index, 1);
    items[atList].splice(atIndex, 0, item);
    this.setState(
      {
        items,
      },
      () => {
        onRankingChange(this.state.items.choiceBox);
        this.recalculateChoicesHeight();
      },
    );

    onBlur();
  };

  recalculateChoicesHeight() {
    const height = `${$(ReactDOM.findDOMNode(this.choiceBox)).height()}px`;
    if (height !== '0px') {
      this.setState({
        choicesHeight: height,
      });
    }
  }

  findItem(id) {
    const { items } = this.state;
    let itemList = null;
    let item = null;
    let itemIndex = null;
    Object.keys(items).map(listKey => {
      items[listKey].map((i, iKey) => {
        if (i.id === id) {
          itemList = listKey;
          item = i;
          itemIndex = iKey;
        }
      });
    });
    return {
      item,
      list: itemList,
      index: itemIndex,
    };
  }

  reset() {
    this.setState(this.getInitialState());
  }

  render() {
    if (__SERVER__) {
      return <span />;
    }
    const { field, connectDropTarget, disabled } = this.props;
    const { items, choicesHeight } = this.state;

    let spotsNb = field.choices.length;

    if (field.values) {
      spotsNb += field.values.length;
    }

    return connectDropTarget(
      <div>
        <Row>
          <Col xs={6}>
            <h5 className="h5">{<FormattedMessage id="global.form.ranking.pickBox.title" />}</h5>
            <ListGroup className="ranking__pick-box">
              <RankingBox
                ref={c => (this.pickBox = c)}
                items={items.pickBox}
                spotsNb={spotsNb}
                listType="pickBox"
                fieldId={field.id}
                moveItem={this.moveItem}
                disabled={disabled}
              />
            </ListGroup>
          </Col>
          <Col xs={6}>
            <h5 className="h5">{<FormattedMessage id="global.form.ranking.choiceBox.title" />}</h5>
            <ListGroup className="ranking__choice-box" style={{ height: choicesHeight }}>
              <RankingBox
                ref={c => (this.choiceBox = c)}
                items={items.choiceBox}
                spotsNb={spotsNb}
                listType="choiceBox"
                fieldId={field.id}
                moveItem={this.moveItem}
                disabled={disabled}
              />
              {items.choiceBox.length === 0 ? (
                <div
                  className="hidden-xs ranking__choice-box__placeholder"
                  style={{ height: `${spotsNb * 45}px` }}>
                  <span>{<FormattedMessage id="global.form.ranking.choiceBox.placeholder" />}</span>
                </div>
              ) : null}
            </ListGroup>
          </Col>
        </Row>
      </div>,
    );
  }
}

export default DragDropContext(HTML5Backend)(
  DropTarget(ITEM_TYPE, itemTarget, connect => ({
    // eslint-disable-line new-cap
    connectDropTarget: connect.dropTarget(),
  }))(RankingBlock),
);
