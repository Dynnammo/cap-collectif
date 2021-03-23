@core @project
Feature: Project

Scenario: Can not sort or filter if feature projects_form is disabled
  Given I visited "projects page"
  Then I should not see "project-theme"

@elasticsearch
Scenario: Project can be sorted by published date
  Given feature "projects_form" is enabled
  And I visited "projects page"
  And I wait "[id='project-preview-UHJvamVjdDpwcm9qZWN0MQ==']" to appear on current page
  And I select "opinion.sort.last" from "project-sorting"
  And I wait "[id='project-preview-UHJvamVjdDpwcm9qZWN0MQ==']" to appear on current page
  Then "Transformation numérique des relations" should be before "Projet vide" for selector ".project-preview .card__title a"

@elasticsearch
Scenario: Project can be sorted by contributions number
  Given feature "projects_form" is enabled
  And I visited "projects page"
  And I select "opinion.sort.last" from "project-sorting"
  And I wait "[id='project-preview-UHJvamVjdDpwcm9qZWN0MQ==']" to appear on current page
  And I select "argument.sort.popularity" from "project-sorting"
  And I wait "[id='project-preview-UHJvamVjdDpwcm9qZWN0NQ==']" to appear on current page
  Then "Croissance, innovation, disruption" should be before "Projet de loi Renseignement" for selector ".project-preview .card__title a"

Scenario: Project can be filtered by theme
  Given feature "themes" is enabled
  And feature "projects_form" is enabled
  And I visited "projects page"
  And I wait ".project-preview" to appear on current page
  # /!\ this test the number of projects visible set in the admin.
  Then I should see 16 ".project-preview" elements
  Then I should see 16 ".progress" elements
  And I should see "project.preview.action.participe"
  And I should see "project.preview.action.seeResult"
  And I click the "#project-button-filter" element
  And I wait "#project-theme" to appear on current page
  And I select "Transport" from react "#project-theme"
  And I wait ".project-preview" to appear on current page
  Then I should see 11 ".project-preview" elements
  And I should see "Projet vide"
  And I should see "Dépot avec selection vote budget"
  And I should not see "Croissance, innovation, disruption"

Scenario: Project can be filtered with theme page
  Given feature "themes" is enabled
  And feature "projects_form" is enabled
  When I go to a theme page
  And I wait ".project-preview" to appear on current page
  Then I should see 7 ".project-preview" element

Scenario: Project can be filtered by theme and sorted by contributions number at the same time
  Given feature "themes" is enabled
  And feature "projects_form" is enabled
  And I visited "projects page"
  And I wait "#project-button-filter" to appear on current page
  And I click the "#project-button-filter" element
  And I wait "#project-theme" to appear on current page
  And I select "Transport" from react "#project-theme"
  And I wait ".project-preview" to appear on current page
  And I select "argument.sort.popularity" from "project-sorting"
  And I wait ".project-preview" to appear on current page
  Then I should see 11 ".project-preview" elements
  And I should see "Projet de loi Renseignement"
  And I should see "Budget Participatif Rennes"
  And I should not see "Croissance, innovation, disruption"
  And "Stratégie technologique de l'Etat et services publics" should be before "Projet vide" for selector ".project-preview .card__title a"

Scenario: Project can be filtered by type and sorted by contributions number at the same time
  And feature "projects_form" is enabled
  And I visited "projects page"
  And I wait "#project-button-filter" to appear on current page
  And I click the "#project-button-filter" element
  And I wait "#project-type" to appear on current page
  And I select "global.consultation" from react "#project-type"
  And I wait ".project-preview" to appear on current page
  And I select "argument.sort.popularity" from "project-sorting"
  And I wait ".project-preview" to appear on current page
  Then I should see 7 ".project-preview" elements
  And I should see "Projet de loi Renseignement"
  And I should see "Stratégie technologique de l'Etat et services publics"
  And I should not see "Croissance, innovation, disruption"
  And "Stratégie technologique de l'Etat et services publics" should be before "Projet vide" for selector ".project-preview .card__title a"

Scenario: Project can be filtered by title
  Given feature "projects_form" is enabled
  And I visited "projects page"
  And I wait "#project-search-button" to appear on current page
  When I fill in the following:
    | project-search-input | innovation |
  And I wait ".project-preview" to appear on current page
  And I click on button "#project-search-button"
  And I wait "Projet vide" to disappear on current page
  And I wait ".project-preview" to appear on current page 1 times
  Then I should see "Croissance, innovation, disruption"
  And I should not see "Stratégie technologique de l'Etat et services publics"
  And I should not see "Projet vide"

Scenario: Project can be filtered by status
  And feature "projects_form" is enabled
  And I visited "projects page"
  And I wait "#project-button-filter" to appear on current page
  And I click the "#project-button-filter" element
  And I wait "#project-status" to appear on current page
  And I select "step.status.open" from react "#project-status"
  And I wait ".project-preview" to appear on current page
  Then I should see 16 ".project-preview" elements
  And I select "step.status.closed" from react "#project-status"
  And I wait ".project-preview" to appear on current page
  Then I should see 7 ".project-preview" elements
  And I select "ongoing-and-future" from react "#project-status"
  Then I should see 1 ".project-preview" elements

@read-only
Scenario: Restricted project should display in projects list
  Given feature "projects_form" is enabled
  And I am logged in as super admin
  And I visited "projects page"
  And I wait "#project-search-button" to appear on current page
  And I wait "#project-button-filter" to appear on current page
  And I wait ".loader" to disappear on current page
  When I fill in the following:
    | project-search-input | custom |
  And I focus the "#project-search-input" element
  And I click the "#project-search-input" element
  And I click the "#project-search-button" element
  And I wait ".loader" to appear on current page
  And I wait ".project-preview" to appear on current page
  Then I should see 1 ".project-preview" elements
  And I should see "Un avenir meilleur pour les nains de jardins (custom access)"
  And I should not see "Stratégie technologique de l'Etat et services publics"
  And I should not see "Croissance, innovation, disruption"

Scenario: Presentation step should display correct number of element
  Given feature "calendar" is enabled
  And feature "blog" is enabled
  And I visited "consultation page" with:
    | projectSlug | croissance-innovation-disruption |
    | stepSlug    | collecte-des-avis                |
  And I follow "Présentation"
  Then I should see 1 ".media--news" elements
  And I wait "#PresentationStepEvents" to appear on current page
  And I should see 4 ".eventPreview" elements

Scenario: Posts menu for project should display correct number of posts
  Given feature "blog" is enabled
  And I visited "project posts page" with:
    | projectSlug | croissance-innovation-disruption |
  And I should see 5 ".media--news" elements

Scenario: Project header should display correct number of votes
  Given I visited "consultation page" with:
    | projectSlug | croissance-innovation-disruption |
    | stepSlug    | collecte-des-avis                |
  Then I should see "10" in the "#votes-counter-pill" element

Scenario: Can not have access to download if export is disabled
  Given I visited "consultation page" with:
    | projectSlug   | strategie-technologique-de-letat-et-services-publics |
    | stepSlug      | collecte-des-avis-pour-une-meilleur-strategie        |
  Then I should not see "project.download.button" in the "#main" element

Scenario: Can not download a project if export is disabled
  Given I visited "home page"
  When I try to download "/projets/strategie-technologique-de-letat-et-services-publics/projet/collecte-des-avis-pour-une-meilleur-strategie/download"
  Then I should see "error.404.title"

Scenario: Can not access trash if feature is disabled
  Given I am logged in as user
  And I visited "consultation page" with:
    | projectSlug | croissance-innovation-disruption |
    | stepSlug    | collecte-des-avis                |
  Then I should not see "project.trash" in the "#main" element

Scenario: Users can't see privates project
  Given feature "projects_form" is enabled
  And I visited "projects page"
  Then I should not see "Qui doit conquérir le monde ? | Visible par les admins seulement"

Scenario: Anonymous can't access to a private project
  Given I go to "https://capco.test/project/qui-doit-conquerir-le-monde-visible-par-les-admins-seulement/collect/collecte-des-propositions-pour-conquerir-le-monde"
  Then I should see "unauthorized-access"
  And I should see "restricted-access"
  When I follow "error.to_homepage"
  Then I should be redirected to "/"

Scenario: Anonymous try to access to a wrong page
  Given I go to "https://capco.test/project/qui-doit-conquerir-fautedefrappe-visible-par-les-admins-seulement-seulement/collect/collecte-des-propositions-pour-conquerir-le-monde"
  Then I should see "error.404.title"

Scenario: Anonymous try to access to a project with restricted access
  Given I go to "https://capco.test/project/un-avenir-meilleur-pour-les-nains-de-jardins-custom-access/collect/collecte-des-propositions-liberer-les-nains-de-jardin"
  And I wait ".error-page" to appear on current page
  Then I should see 'restricted-access'

Scenario: Not allowed user can't access to a private project
  Given I am logged in as user
  And I go to "https://capco.test/project/qui-doit-conquerir-le-monde-visible-par-les-admins-seulement/collect/collecte-des-propositions-pour-conquerir-le-monde"
  And I wait ".error-page" to appear on current page
  Then I should see 'restricted-access'
  When I follow "error.report"
  Then I should be redirected to "/contact"

Scenario: User try to access to a project with restricted access
  Given I am logged in as user
  And I go to "https://capco.test/project/un-avenir-meilleur-pour-les-nains-de-jardins-custom-access/collect/collecte-des-propositions-liberer-les-nains-de-jardin"
  And I wait ".error-page" to appear on current page
  Then I should see 'restricted-access'

Scenario: Super Admin can access to all private projects
  Given feature "projects_form" is enabled
  And I am logged in as super admin
  And I visited "collect page" with:
    | projectSlug | qui-doit-conquerir-le-monde-visible-par-les-admins-seulement |
    | stepSlug    | collecte-des-propositions-pour-conquerir-le-monde            |
  Then I should see "Collecte des propositions pour conquérir le monde"
  And I wait ".alert-dismissible" to appear on current page
  And I should see "only-visible-by-administrators"
  When I visited "collect page" with:
    | projectSlug | project-pour-la-creation-de-la-capcobeer-visible-par-admin-seulement |
    | stepSlug    | collecte-des-propositions-pour-la-capcobeer                          |
  Then I should see "Collecte des propositions pour la capcoBeer"
  And I wait ".alert-dismissible" to appear on current page
  And I should see "global.draft.only_visible_by_you"
  When I visited "collect page" with:
    | projectSlug | project-pour-la-force-visible-par-mauriau-seulement |
    | stepSlug    | collecte-des-propositions-pour-la-force             |
  Then I should see "Collecte des propositions pour La Force"
  And I wait ".alert-dismissible" to appear on current page
  And I should see "global.draft.only_visible_by_you"
  When I visited "collect page" with:
    | projectSlug | un-avenir-meilleur-pour-les-nains-de-jardins-custom-access |
    | stepSlug    | collecte-des-propositions-liberer-les-nains-de-jardin      |
  Then I should see "Un avenir meilleur pour les nains de jardins (custom access)"

Scenario: An admin can't access a private project of an other admin
  Given I am logged in as admin
  And I go to "https://capco.test/project/project-pour-la-force-visible-par-mauriau-seulement/collect/collecte-des-propositions-pour-la-force"
  Then I should see 'restricted-access'

Scenario: Admin access to a project accessible for admins only
  Given I am logged in as admin
  And I visited "collect page" with:
    | projectSlug | qui-doit-conquerir-le-monde-visible-par-les-admins-seulement |
    | stepSlug    | collecte-des-propositions-pour-conquerir-le-monde            |
  Then I should see "Collecte des propositions pour conquérir le monde"
  And I should see "only-visible-by-administrators"

Scenario: Admin access to his project and click to edit it
  Given I am logged in as admin
  When I visited "collect page" with:
    | projectSlug | project-pour-la-creation-de-la-capcobeer-visible-par-admin-seulement |
    | stepSlug    | collecte-des-propositions-pour-la-capcobeer                          |
  Then I should see "Collecte des propositions pour la capcoBeer"
  And I should see "admin" in the "#project-header #authors-credit" element
  And I should see "global.draft.only_visible_by_you"
  Then I follow "action_edit"
  And I should be redirected to "/admin/capco/app/project/ProjectAccessibleForMeOnlyByAdmin/edit"

Scenario: Pierre can access to a restricted project to his user group
  Given feature "projects_form" is enabled
  And I am logged in as pierre
  When I visited "collect page" with:
    | projectSlug | un-avenir-meilleur-pour-les-nains-de-jardins-custom-access |
    | stepSlug    | collecte-des-propositions-liberer-les-nains-de-jardin      |
  Then I should see "Un avenir meilleur pour les nains de jardins (custom access)"
  And I wait 1 seconds
  And I should see "restrictedaccess"
  And I open restricted access modal
  Then I should see "people-with-access-to-project"
  And I should see "Agent de la ville"
  And I should see "Utilisateurs"
  And I should see "global.close"
  When I unfold "group3" group inside restricted access modal
  Then I should see "Utilisateurs"
  And I should not see "ptondereau" in the "#group3-modal .list-group" element
  Then I search "ptondereau" in list "#group3-modal .list-group"

Scenario: Anonymous can access a project and see all authors
  Given I visited "collect page" with:
    | projectSlug | questions-responses                |
    | stepSlug    | collecte-des-questions-chez-youpie |
  Then I should see "Questions/Responses"
  And I should see 2 "#project-header .user-avatar" elements

Scenario: User can access project's trashed opinions
  Given feature "project_trash" is enabled
  And I am logged in as admin
  And I visited "project trashed page" with:
    | projectSlug | strategie-technologique-de-letat-et-services-publics   |
  Then I wait ".project__show-trash" to appear on current page

@database
Scenario: Admin deletes a proposal ...
  Given feature "project_trash" is enabled
  And I am logged in as admin
  When I visited "collect page" with:
    | projectSlug | projet-avec-beaucoup-dopinions   |
    | stepSlug | collecte-des-propositions-avec-questions-qui-va-etre-jetee   |
  And I wait 2 seconds
  Then I should see "Proposition qui va être jetée"
  When I go to the admin proposal page with proposalid "proposal105"
  And I wait "#proposal-admin-page-tabs-tab-7" to appear on current page
  And I click on button "#proposal-admin-page-tabs-tab-7"
  And I wait 2 seconds
  And I click on button "#proposal-trashed-tab"
  And I fill in the following:
  | trashedReason| Pas intéressant désolé |
  And I click on button "[id='proposal-change-state']"
  And I wait 4 seconds
  When I visited "collect page" with:
    | projectSlug | projet-avec-beaucoup-dopinions   |
    | stepSlug | collecte-des-propositions-avec-questions-qui-va-etre-jetee   |
  Then I should not see "Proposition qui va être jetée"

@database @rabbitmq
Scenario: User wants to evaluate a project proposal analysis immediately
  Given feature "unstable__analysis" is enabled
  And I am logged in as spyl
  And I click on button "#cookie-consent"
  Then I go to "/admin/capco/app/proposalform/proposalformIdf/edit"
  And I wait "#link-tab-new-analysis" to appear on current page
  When I click on button "#link-tab-new-analysis"
  And I wait "#step_now" to appear on current page
  Then I click on button "#step_now"
  And I click on button "#analysis-configuration-submit"
  When I go to "/projects/budget-participatif-idf/collect/collecte-des-projets-idf-privee/proposals/mon-projet-local-en-tant-quassociation-avec-siret"
  And I wait "#proposal_analysis_decision" to appear on current page
  Then I should not see ".proposal__last__news__title"
  When I click the ".edit-analysis-icon" element
  And I click on button "#validate-proposal-decision-button"
  And I wait ".saving" to appear on current page
  And I wait ".saved" to appear on current page
  And I reload the page
  And I wait "#proposal_analysis_decision" to appear on current page
  And I wait ".proposal__last__news__title" to appear on current page
  Then I should see "card.title.official.answer" in the ".proposal__last__news__title" element

@database @rabbitmq
Scenario: User wants to assess a project proposal analysis immediately
  Given feature "unstable__analysis" is enabled
  And I am logged in as spyl
  And I click on button "#cookie-consent"
  Then I go to "/admin/capco/app/proposalform/proposalformIdf/edit"
  And I wait "#link-tab-new-analysis" to appear on current page
  When I click on button "#link-tab-new-analysis"
  And I wait "#step_now" to appear on current page
  Then I click on button "#step_now"
  And I click on button "#analysis-configuration-submit"
  When I go to "/projects/budget-participatif-idf/collect/collecte-des-projets-idf-privee/proposals/mon-projet-local-en-tant-quorganisme-public"
  And I wait "#proposal_analysis_assessment" to appear on current page
  When I click the ".edit-analysis-icon" element
  And I fill in the following:
    | proposalAssessment-officialResponse | J'apprécie a moitié garçon |
  And I click on button "#label-radio-status-FAVOURABLE"
  And I click on button "#validate-proposal-assessment-button"
  And I wait ".saving" to appear on current page
  And I wait ".saved" to appear on current page
  And I reload the page
  And I wait "#proposal_analysis_assessment" to appear on current page
  Then I should see "global.favorable" in the "#proposal_analysis_assessment" element

@database @rabbitmq
Scenario: User wants to analyse a project proposal analysis immediately
  Given feature "unstable__analysis" is enabled
  And I am logged in as Agui
  And I click on button "#cookie-consent"
  When I go to "/projects/budget-participatif-idf/collect/collecte-des-projets-idf-privee/proposals/mon-projet-local-en-tant-quorganisme-public"
  And I wait "#proposal_analysis_analyses" to appear on current page
  When I click the ".edit-analysis-icon" element
  And I fill the element "#proposal-analysis-form-responses10" with value 42
  And I fill the element "#proposal-analysis-form-responses11" with value 12
  And I click on button "#label-radio-status-FAVOURABLE"
  And I click on button "#validate-proposal-analysis-button"
  And I wait ".saving" to appear on current page
  And I wait ".saved" to appear on current page
  And I reload the page
  And I wait "#proposal_analysis_analyses" to appear on current page
  Then I should see "global.favorable" in the "#proposal_analysis_analyses" element

@database
Scenario: Can not see the analysis panel when not logged in
  Given feature "unstable__analysis" is enabled
  And I go to "/projects/budget-participatif-rennes/collect/collecte-des-propositions/proposals/test-de-publication-avec-accuse-de-reception"
  Then I should not see "#proposal_analysis_panel"
