import ReactOnRails from 'react-on-rails';
import ProjectsListApp from '../js/startup/ProjectsListAppClient';
import CollectStepPageApp from '../js/startup/CollectStepPageApp';
import SelectionStepPageApp from '../js/startup/SelectionStepPageApp';
import NavbarApp from '../js/startup/NavbarAppClient';
import EmailNotConfirmedApp from '../js/startup/EmailNotConfirmedAppClient';
import NewOpinionApp from '../js/startup/NewOpinionAppClient';
import NewIdeaApp from '../js/startup/NewIdeaAppClient';
import PhoneProfileApp from '../js/startup/PhoneProfileApp';
import ProjectTrashButtonApp from '../js/startup/ProjectTrashButtonApp';
import OpinionPageApp from '../js/startup/OpinionPageApp';
import CommentSectionApp from '../js/startup/CommentSectionApp';
import SynthesisViewBoxApp from '../js/startup/SynthesisViewBoxApp';
import SynthesisEditBoxApp from '../js/startup/SynthesisEditBoxApp';
import ProposalPageApp from '../js/startup/ProposalPageApp';
import QuestionnaireStepPageApp from '../js/startup/QuestionnaireStepPageApp';
import ProjectStatsPageApp from '../js/startup/ProjectStatsPageApp';
import ProposalVoteBasketWidgetApp from '../js/startup/ProposalVoteBasketWidgetApp';
import AlertBoxApp from '../js/startup/AlertBoxApp';
import StepInfosApp from '../js/startup/StepInfosApp';
import ProposalsUserVotesPageApp from '../js/startup/ProposalsUserVotesPageApp';
import appStore from '../js/stores/AppStore';

const register = ReactOnRails.register;
const registerStore = ReactOnRails.registerStore;

// ((global) => {
  global.clearTimeout = global.clearTimeout || function () {};
  global.setTimeout = global.setTimeout || function () {};
  global.setInterval = global.setInterval || function () {};
  // }(this));


registerStore({ appStore });
register({ ProjectsListApp });
register({ CollectStepPageApp });
register({ SelectionStepPageApp });
register({ NavbarApp });
register({ EmailNotConfirmedApp });
register({ NewOpinionApp });
register({ NewIdeaApp });
register({ ProjectTrashButtonApp });
register({ OpinionPageApp });
register({ CommentSectionApp });
register({ SynthesisViewBoxApp });
register({ SynthesisEditBoxApp });
register({ ProposalPageApp });
register({ QuestionnaireStepPageApp });
register({ ProjectStatsPageApp });
register({ ProposalVoteBasketWidgetApp });
register({ AlertBoxApp });
register({ StepInfosApp });
register({ ProposalsUserVotesPageApp });
register({ PhoneProfileApp });
