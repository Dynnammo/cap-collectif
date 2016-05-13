import ReactOnRails from 'react-on-rails';
import ProjectsListApp from './startup/ProjectsListAppClient';
import CollectStepPageApp from './startup/CollectStepPageApp';
import SelectionStepPageApp from './startup/SelectionStepPageApp';
import NavbarApp from './startup/NavbarAppClient';
import EmailNotConfirmedApp from './startup/EmailNotConfirmedAppClient';
import NewOpinionApp from './startup/NewOpinionAppClient';
import NewIdeaApp from './startup/NewIdeaAppClient';
import ProjectTrashButtonApp from './startup/ProjectTrashButtonApp';
import OpinionPageApp from './startup/OpinionPageApp';
import CommentSectionApp from './startup/CommentSectionApp';
import SynthesisViewBoxApp from './startup/SynthesisViewBoxApp';
import SynthesisEditBoxApp from './startup/SynthesisEditBoxApp';
import ProposalPageApp from './startup/ProposalPageApp';
import QuestionnaireStepPageApp from './startup/QuestionnaireStepPageApp';
import ProjectStatsPageApp from './startup/ProjectStatsPageApp';
import ProposalVoteBasketWidgetApp from './startup/ProposalVoteBasketWidgetApp';
import AlertBoxApp from './startup/AlertBoxApp';
import StepInfosApp from './startup/StepInfosApp';
import ProposalsUserVotesPageApp from './startup/ProposalsUserVotesPageApp';
import PhoneProfileApp from '../js/startup/PhoneProfileApp';
import appStore from '../js/stores/AppStore';

const register = ReactOnRails.register;
const registerStore = ReactOnRails.registerStore;

window.__SERVER__ = false;

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
