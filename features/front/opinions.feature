@opinions
Feature: Opinions

@javascript @database
Scenario: Can create an opinion of contribuable type in opened project
  Given I am logged in as user
  And I visited "consultation page" with:
    | projectSlug | croissance-innovation-disruption |
    | stepSlug    | collecte-des-avis                |
  And I wait 2 seconds
  When I click the "#btn-add--les-causes" element
  And I fill in the following:
    | opinion_title | Titre                           |
    | opinion_body  | Description de ma proposition   |
  And I press "confirm-opinion-create"
  Then I should be redirected to "/projects/croissance-innovation-disruption/consultation/collecte-des-avis/opinions/les-causes/titre"
  # Then I should see "Merci ! Votre proposition a bien été enregistrée."

@javascript @security
Scenario: Can not create an opinion of non-contribuable type
  Given I am logged in as user
  And I visited "consultation page" with:
    | projectSlug | croissance-innovation-disruption |
    | stepSlug    | collecte-des-avis                |
  And I wait 2 seconds
  Then I should see 0 "#opinions--le-probleme-constate" element

@javascript @security
Scenario: Can not create an opinion in closed project
  Given I am logged in as user
  And I visited "consultation page" with:
    | projectSlug | strategie-technologique-de-l-etat-et-services-publics |
    | stepSlug    | collecte-des-avis-pour-une-meilleur-strategie         |
  Then I should see "step.consultation.alert.ended.title" in the "#main" element
  Then I should see "step.consultation.alert.ended.text" in the "#main" element
  And I wait 3 seconds
  And the create opinion button should be disabled

@javascript @security
Scenario: Can not create an opinion when not logged in
  Given I visited "consultation page" with:
    | projectSlug | croissance-innovation-disruption |
    | stepSlug    | collecte-des-avis                |
  And I wait 2 seconds
  When I click the "#btn-add--les-causes" element
  Then I should see a "#login-popover" element

@javascript @database
Scenario: Logged in user can report an opinion
  Given feature "reporting" is enabled
  And I am logged in as admin
  And I go to an opinion
  When I click the "#report-opinion-opinion2-button" element
  And I fill the reporting form
  And I submit the reporting form
  Then I should see "alert.success.report.opinion" in the "#global-alert-box" element

@javascript @database
Scenario: Author of an opinion loose their votes when updating it
  Given I am logged in as user
  And I visited "opinion page" with:
    | projectSlug | croissance-innovation-disruption |
    | stepSlug         | collecte-des-avis                |
    | opinionTypeSlug  | enjeux                           |
    | opinionSlug      | opinion-3                        |
  And I wait 1 seconds
  And I should see 'global.votes {"num":1}'
  When I press "global.edit"
  And I wait 1 seconds
  And I fill in the following:
    | opinion_body | Je modifie ma proposition !   |
  And I check "opinion_check"
  And I press "confirm-opinion-update"
  Then I should be redirected to "/projects/croissance-innovation-disruption/consultation/collecte-des-avis/opinions/les-enjeux/opinion-3"
  And I wait 1 seconds
  And I should see 'global.votes {"num":0}'

@javascript @security
Scenario: Non author of an opinion wants to update it
  Given I am logged in as admin
  And I visited "opinion page" with:
    | projectSlug | croissance-innovation-disruption |
    | stepSlug         | collecte-des-avis                |
    | opinionTypeSlug  | enjeux                           |
    | opinionSlug      | opinion-3                        |
  And I wait 1 seconds
  Then I should not see "global.edit" in the ".opinion__description .opinion__buttons" element

@javascript
Scenario: Anonymous wants to see opinion appendix
  Given I go to an opinion with versions
  Then I should see "Motifs 1"
  And I press "Exposé des motifs"
  And I wait 1 seconds
  Then I should not see "Motifs 1"
  And I press "Étude d'impact"
  And I wait 1 seconds
  Then I should see "Impacts 1"

@javascript
Scenario: Anonymous user wants to see all votes of an opinion
  Given I go to an opinion with loads of votes
  When I click the show all opinion votes button
  Then I should see all opinion votes

@javascript
Scenario: Anonymous user wants to share an opinion
  Given feature "share_buttons" is enabled
  And I go to an opinion with versions
  When I click the share opinion button
  Then I should see the opinion share dropdown
  And I click the opinion share link button
  Then I should see the share link modal

@javascript
Scenario: Anonymous wants to see votes evolution
  Given feature "votes_evolution" is enabled
  And I go to an opinion with versions
  When I go on the votes evolution tab
  Then I should see 1 ".opinion__history_chart" element
