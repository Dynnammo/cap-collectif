import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import { IntlMixin } from 'react-intl';
import { Button } from 'react-bootstrap';
import DeepLinkStateMixin from '../../../utils/DeepLinkStateMixin';
import { changeTerm, loadProposals } from '../../../redux/modules/proposal';
import Input from '../../Form/Input';

const ProposalListSearch = React.createClass({
  propTypes: {
    dispatch: PropTypes.func.isRequired,
  },
  mixins: [
    IntlMixin,
    DeepLinkStateMixin,
  ],

  getInitialState() {
    return {
      value: '',
    };
  },

  handleSubmit(e) {
    e.preventDefault();
    let value = this._input.getValue();
    value = value.length > 0 ? value : null;
    this.props.dispatch(changeTerm(value));
    this.props.dispatch(loadProposals());
  },

  render() {
    return (
      <form onSubmit={this.handleSubmit}>
        <Input
          id="proposal-search-input"
          type="text"
          ref={c => this._input = c}
          placeholder={this.getIntlMessage('proposal.search')}
          buttonAfter={
            <Button id="proposal-search-button" type="submit">
              <i className="cap cap-magnifier"></i>
            </Button>
          }
          valueLink={this.linkState('value')}
          groupClassName="proposal-search-group pull-right"
        />
      </form>
    );
  },
});

export default connect()(ProposalListSearch);
