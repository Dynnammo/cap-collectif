import {RECEIVE_COMMENTS, CREATE_COMMENT} from '../constants/CommentConstants';
import BaseStore from './BaseStore';

class CommentStore extends BaseStore {

  constructor() {
    super();
    this.register(this._registerToActions.bind(this));
    this._comments = [];
    this._commentsCount = 0;
    this._commentsAndAnswersCount = 0;
    this._isReportingEnabled = true;
    this._isSync = true;
  }

  _registerToActions(action) {
    switch(action.actionType) {
      case RECEIVE_COMMENTS:
        this._comments = action.comments;
        this._commentsCount = action.comments_count;
        this._commentsAndAnswersCount = action.comments_total;
        this._commentsAndAnswersCount = action.comments_and_answers_count;
        this._isReportingEnabled = action.is_reporting_enabled;
        this._isSync = true;
        this.emitChange();
        break;
      case CREATE_COMMENT:
        this._isSync = false;
        this.emitChange();
        break;
      default:
        break;
    };
  }

  get isSync() {
    return this._isSync;
  }

  get isReportingEnabled() {
    return this._isReportingEnabled;
  }

  get comments() {
    return this._comments;
  }

  get countWithAnswers() {
    return this._commentsAndAnswersCount;
  }

  get count() {
    return this._commentsCount;
  }

}

export default new CommentStore();
