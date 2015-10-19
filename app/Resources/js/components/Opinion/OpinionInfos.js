const FormattedDate = ReactIntl.FormattedDate;
const FormattedMessage = ReactIntl.FormattedMessage;

const OpinionInfos = React.createClass({
  propTypes: {
    opinion: React.PropTypes.object.isRequired,
    rankingThreshold: React.PropTypes.number,
    opinionTerm: React.PropTypes.number,
  },
  mixins: [ReactIntl.IntlMixin],

  isVersion() {
    return this.props.opinion.parent ? true : false;
  },

  renderDate() {
    if (!Modernizr.intl) {
      return null;
    }
    return (
      <span className="excerpt">
        <FormattedDate
            value={moment(this.props.opinion.created_at)}
            day="numeric" month="long" year="numeric"
            hour="numeric" minute="numeric"
         />
      </span>
    );
  },

  renderEditionDate() {
    if (!Modernizr.intl) {
      return null;
    }

    if (moment(this.props.opinion.updated_at).diff(this.props.opinion.created_at, 'seconds') <= 1) {
      return null;
    }

    return (
      <span className="excerpt">
        { ' - ' }
        { this.getIntlMessage('global.edited') }
        { ' ' }
        <FormattedDate
          value={moment(this.props.opinion.updated_at)}
          day="numeric" month="long" year="numeric"
          hour="numeric" minute="numeric"
        />
      </span>
    );
  },

  renderAuthorName() {
    if (this.props.opinion.author) {
      return (
        <a href={this.props.opinion.author._links.profile}>
          { this.props.opinion.author.username }
        </a>
      );
    }

    return <span>{ this.props.opinion.author_name }</span>;
  },

  renderRankingLabel() {
    const opinion = this.props.opinion;
    if (this.props.rankingThreshold !== null && opinion.ranking !== null && opinion.ranking <= this.props.rankingThreshold) {
      return (
        <span className="opinion__label opinion__label--green">
          <i className="cap cap-trophy"></i>
          {this.isVersion()
            ? <FormattedMessage
            message={this.getIntlMessage('opinion.ranking.versions')}
            max={this.props.rankingThreshold}
            />
            : this.props.opinionTerm === 0
              ? <FormattedMessage
                message={this.getIntlMessage('opinion.ranking.opinions')}
                max={this.props.rankingThreshold}
                />
              : <FormattedMessage
                message={this.getIntlMessage('opinion.ranking.articles')}
                max={this.props.rankingThreshold}
                />
          }
        </span>
      );
    }

    return null;
  },

  render() {
    return (
      <p className="h5 opinion__user">
        { this.renderAuthorName() }
        { ' • ' }
        { this.renderDate() }
        { this.renderEditionDate() }
        { this.renderRankingLabel() }
      </p>
    );
  },

});

export default OpinionInfos;
