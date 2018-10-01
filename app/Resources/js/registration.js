// @flow
import moment from 'moment';
import React from 'react';
import ReactDOM from 'react-dom';
import ReactOnRails from 'react-on-rails';
import { addLocaleData } from 'react-intl';
import 'moment/locale/fr';
import frLocaleData from 'react-intl/locale-data/fr';
import 'moment/locale/en-gb';
import enLocaleData from 'react-intl/locale-data/en';
import 'moment/locale/es';
import esLocaleData from 'react-intl/locale-data/es';

import ProjectsListApp from './startup/ProjectsListAppClient';
import ProposalStepPageApp from './startup/ProposalStepPageApp';
import NavbarApp from './startup/NavbarAppClient';
import EmailNotConfirmedApp from './startup/EmailNotConfirmedAppClient';
import NewOpinionApp from './startup/NewOpinionAppClient';
import ProjectTrashButtonApp from './startup/ProjectTrashButtonApp';
import ProjectStepTabsApp from './startup/ProjectStepTabsApp';
import CarouselApp from './startup/CarouselApp';
import OpinionPageApp from './startup/OpinionPageApp';
import CommentSectionApp from './startup/CommentSectionApp';
import SynthesisViewBoxApp from './startup/SynthesisViewBoxApp';
import SynthesisEditBoxApp from './startup/SynthesisEditBoxApp';
import ProposalPageApp from './startup/ProposalPageApp';
import QuestionnaireStepPageApp from './startup/QuestionnaireStepPageApp';
import ProjectStatsPageApp from './startup/ProjectStatsPageApp';
import ProposalVoteBasketWidgetApp from './startup/ProposalVoteBasketWidgetApp';
import AlertBoxApp from './startup/AlertBoxApp';
import ConsultationPageApp from './startup/ConsultationPageApp';
import ProposalListApp from './startup/ProposalListApp';
import ProposalsUserVotesPageApp from './startup/ProposalsUserVotesPageApp';
import AccountProfileApp from './startup/AccountProfileApp';
import ShareButtonDropdownApp from './startup/ShareButtonDropdownApp';
import ProposalCreateFusionButtonApp from './startup/ProposalCreateFusionButtonApp';
import ProposalFormCreateButtonApp from './startup/ProposalFormCreateButtonApp';
import ProjectListPageApp from './startup/ProjectListPageApp';
import ProposalAdminPageApp from './startup/ProposalAdminPageApp';
import ProposalFormAdminPageApp from './startup/ProposalFormAdminPageApp';
import QuestionnaireAdminPageApp from './startup/QuestionnaireAdminPageApp';
import RegistrationAdminApp from './startup/RegistrationAdminApp';
import AdminModalsApp from './startup/AdminModalsApp';
import ShieldApp from './startup/ShieldApp';
import GroupAdminPageApp from './startup/GroupAdminPageApp';
import GroupCreateButtonApp from './startup/GroupCreateButtonApp';
import EvaluationsIndexPageApp from './startup/EvaluationsIndexPageApp';
import ChooseAUsernameApp from './startup/ChooseAUsernameApp';
import ParisUserNotValidApp from './startup/ParisUserNotValidApp';
import AccountProfileFollowingsApp from './startup/AccountProfileFollowingsApp';
import UserAdminCreateButtonApp from './startup/UserAdminCreateButtonApp';
import EditProfileApp from './startup/EditProfileApp';
import CookieApp from '../js/startup/CookieApp';
import UserAdminPageApp from '../js/startup/UserAdminPageApp';
import ProjectRestrictedAccessAlertApp from '../js/startup/ProjectRestrictedAccessAlertApp';
import ProjectRestrictedAccessApp from '../js/startup/ProjectRestrictedAccessApp';
import QuestionnaireCreateButtonApp from '../js/startup/QuestionnaireCreateButtonApp';
import ArgumentListApp from './startup/ArgumentListApp';
import VoteListApp from './startup/VoteListApp';

import appStore from '../js/stores/AppStore';

if (process.env.NODE_ENV === 'development') {
  if (new URLSearchParams(window.location.search).get('axe')) {
    global.axe(React, ReactDOM, 1000);
  }
}

const locale = window.locale;
if (locale === 'fr-FR') {
  addLocaleData(frLocaleData);
  moment.locale('fr-FR');
}
if (locale === 'en-GB') {
  addLocaleData(enLocaleData);
  moment.locale('en-GB');
}
if (locale === 'es-ES') {
  addLocaleData(esLocaleData);
  moment.locale('es-ES');
}

window.__SERVER__ = false;

ReactOnRails.registerStore({ appStore });

ReactOnRails.register({
  AccountProfileFollowingsApp,
  AdminModalsApp,
  RegistrationAdminApp,
  ChooseAUsernameApp,
  ParisUserNotValidApp,
  ShieldApp,
  ProjectListPageApp,
  ProposalFormCreateButtonApp,
  ProposalAdminPageApp,
  ProposalCreateFusionButtonApp,
  ProposalStepPageApp,
  NavbarApp,
  EmailNotConfirmedApp,
  NewOpinionApp,
  AccountProfileApp,
  ProjectTrashButtonApp,
  ProjectStepTabsApp,
  CarouselApp,
  EvaluationsIndexPageApp,
  OpinionPageApp,
  CommentSectionApp,
  SynthesisViewBoxApp,
  SynthesisEditBoxApp,
  ProposalPageApp,
  QuestionnaireStepPageApp,
  ProjectStatsPageApp,
  ProposalVoteBasketWidgetApp,
  AlertBoxApp,
  ConsultationPageApp,
  ProposalListApp,
  ProposalsUserVotesPageApp,
  ShareButtonDropdownApp,
  ProposalFormAdminPageApp,
  QuestionnaireAdminPageApp,
  QuestionnaireCreateButtonApp,
  GroupAdminPageApp,
  GroupCreateButtonApp,
  UserAdminCreateButtonApp,
  EditProfileApp,
  CookieApp,
  UserAdminPageApp,
  ProjectRestrictedAccessAlertApp,
  ProjectRestrictedAccessApp,
  ArgumentListApp,
  VoteListApp,
});
