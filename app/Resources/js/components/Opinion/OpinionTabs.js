import OpinionArgumentsBox from './OpinionArgumentsBox';
import OpinionVersionsBox from './OpinionVersionsBox';
import OpinionSourcesBox from './OpinionSourcesBox';

const TabbedArea = ReactBootstrap.TabbedArea;
const TabPane = ReactBootstrap.TabPane;
const FormattedMessage = ReactIntl.FormattedMessage;

const OpinionTabs = React.createClass({
  propTypes: {
    opinion: React.PropTypes.object.isRequired,
    isReportingEnabled: React.PropTypes.bool.isRequired,
  },
  mixins: [ReactIntl.IntlMixin],

  getCommentSystem() {
    return this.props.opinion.parent ? this.props.opinion.parent.type.commentSystem : this.props.opinion.type.commentSystem;
  },

  getArgumentsTrad() {
    if (this.getCommentSystem() === 2) {
      return this.getIntlMessage('global.arguments');
    }
    return this.getIntlMessage('global.simple_arguments');
  },

  renderArgumentsContent() {
    return <OpinionArgumentsBox {...this.props} />;
  },

  renderVersionsContent() {
    return <OpinionVersionsBox isReportingEnabled={this.props.isReportingEnabled} opinionId={this.props.opinion.id} opinionBody={this.props.opinion.body} />;
  },

  renderSourcesContent() {
    return <OpinionSourcesBox {...this.props} />;
  },

  render() {
    const opinion = this.props.opinion;
    let tabNumber = this.isSourceable() ? 1 : 0;
    tabNumber += this.isCommentable() ? 1 : 0;
    tabNumber += this.isVersionable() ? 1 : 0;

    if (tabNumber > 1) {
      return (
        <TabbedArea defaultActiveKey={1} animation={false}>
          { this.isCommentable()
            ? <TabPane id="opinion__arguments" className="opinion-tabs" eventKey={1} tab={
                <FormattedMessage message={this.getArgumentsTrad()} num={this.props.opinion.arguments_count} />
              }>
                {this.renderArgumentsContent()}
              </TabPane>
            : null
          }
          { this.isVersionable()
            ? <TabPane id="opinion__versions" className="opinion-tabs" eventKey={2} tab={
                <FormattedMessage message={this.getIntlMessage('global.versions')} num={opinion.versions_count} />
              }>
                {this.renderVersionsContent()}
              </TabPane>
            : null
          }
          { this.isSourceable()
            ? <TabPane id="opinion__sources" className="opinion-tabs" eventKey={3} tab={
                <FormattedMessage message={this.getIntlMessage('global.sources')} num={opinion.sources_count} />
              }>
                {this.renderSourcesContent()}
              </TabPane>
            : null
          }
        </TabbedArea>
      );
    }

    if (this.isSourceable()) {
      return this.renderSourcesContent();
    }
    if (this.isVersionable()) {
      return this.renderVersionsContent();
    }
    if (this.isCommentable()) {
      return this.renderArgumentsContent();
    }

    return null;
  },

  isSourceable() {
    const type = this.props.opinion.parent ? this.props.opinion.parent.type : this.props.opinion.type;
    if (type !== 'undefined') {
      return type.sourceable;
    }
    return false;
  },

  isCommentable() {
    if (this.getCommentSystem() === 1 || this.getCommentSystem() === 2) {
      return true;
    }
    return false;
  },

  isVersionable() {
    const opinion = this.props.opinion;
    return !this.isVersion() && opinion.type !== 'undefined' && opinion.type.versionable;
  },

  isVersion() {
    return !!this.props.opinion.parent;
  },

});

export default OpinionTabs;
