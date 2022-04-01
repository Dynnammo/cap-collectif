@core @rgpd
Feature: RGPD

Scenario: An anonymous wants to simply accept cookies by clicking on accept button and I check that the cookie is well loaded
  Given I visited "home page" with cookies not accepted
  And I should see 0 ".classname-set-by-advertising-cookie" elements
  And I should see "cookies-text"
  When I click on button "#cookie-consent"
  And I wait ".classname-set-by-advertising-cookie" to appear on current page
  Then I should not see "cookies-text"

Scenario: An anonymous cant simply accept cookies by clicking anywhere
  Given I visited "home page" with cookies not accepted
  And I should see "cookies-text"
  When I click the "#main" element
  Then I should see "cookies-text"

Scenario: An anonymous cant simply accept cookies by scrolling
  Given I go to a proposal with lot of comments
  And I should see "cookies-text"
  Then I scroll to the bottom
  And I scroll to the bottom
  Then I should see "cookies-text"

Scenario: An anonymous wants to refuse all cookies
  Given I visited "home page" with cookies not accepted
  And I should see "cookies-text"
  Then I click on button "#cookie-decline-button"
  And I should see a cookie named "hasFullConsent"

Scenario: An anonymous wants to toggle cookies performance
  Given I visited "projects page" with cookies not accepted
  And I scroll to the bottom
  And I wait "#cookies-management" to appear on current page
  When I click on button "#cookies-management"
  And I should see "global.disabled" appear on current page in "body"
  Then I check element "cookies-enable-analytic"
  And I should see "list.label_enabled"
  And I click on button "#cookies-save"
  And I wait "cookies.content.page" to disappear on current page in "#cookies-manager"
  Then I go to a proposal with lot of comments
  And I visited "projects page" with cookies not accepted
  When I click on button "#cookies-management"
  And I should see "list.label_enabled"
  And I should not see "cookies-text"

Scenario: An anonymous wants to toggle cookies advertising
  Given I visited "projects page" with cookies not accepted
  And I scroll to the bottom
  When I click on button "#cookies-management"
  And I should see "global.disabled"
  Then I check element "cookies-enable-ads"
  And I should see "list.label_enabled"
  And I click on button "#cookies-save"
  And I should not see "cookies-text"
  Then I go to a proposal with lot of comments
  And I visited "projects page" with cookies not accepted
  When I click on button "#cookies-management"
  And I should see "global.disabled"
  And I should see "list.label_enabled"
  And I should not see "cookies-text"

Scenario: An anonymous accept cookies then should have one created
  Given I visited "home page" with cookies not accepted
  And I should not see a cookie named "hasFullConsent"
  And I should see "cookies-text"
  And I click on button "#cookie-consent"
  Then I should not see "cookies-text"
  Then I should see a cookie named "hasFullConsent"

Scenario: An anonymous accept cookies then change is mind and the cookies should be deleted
  Given I am on the homepage
  And I should not see "cookies-text"
  And I should see a cookie named "hasFullConsent"
  And I create a cookie named "_pk_id.2733"
  And I visited "projects page"
  When I click on button "#cookies-management"
  Then I check element "cookies-enable-analytic"
  And I click on button "#cookies-save"
  Then I should not see a cookie named "_pk_id.2733"

@read-only
Scenario: An anonymous visit privacy page
  Given I visited "privacy page" with cookies not accepted
  And I should not see "error.500"

@read-only
Scenario: An anonymous visit legal page
  Given I visited "legal page" with cookies not accepted
  And I should not see "error.500"

@international
Scenario: An anonymous goes to a page not in his default language and should see banner only the first time, not
  after selecting a locale
  Given feature "multilangue" is enabled
  Given I go to "/de/"
  And I close the old browser banner
  And I should not see a cookie named "locale"
  And I select "fr-FR" in the language header
  And the locale should be "fr-FR"
  And I should see a cookie named "locale"

@international
Scenario: An anonymous goes to a page not in his default language and should see banner only the first time,
  not after dismissing it
  Given feature "multilangue" is enabled
  Given I go to "/de/"
  And I close the old browser banner
  And I should not see a cookie named "locale"
  And I wait "#changeLanguageProposalContainer" to appear on current page
  And I click the "#language-header-close" element
  And I reload the page
  Then I should see a cookie named "locale"
  And I should not see "#changeLanguageProposalContainer"

@international
Scenario: An anonymous goes to a page not in his default language and should see banner. Then, after his choice, all  pages not in his locale should show the banner
  Given feature "multilangue" is enabled
  Given I go to "/de/"
  And I should not see "error.404.title"
  And I should not see a cookie named "locale"
  And I wait "#changeLanguageProposalContainer" to appear on current page
  And I close the old browser banner
  And I click the "#language-header-close" element
  And I reload the page
  Then I should see a cookie named "locale"
  Then the locale should be "de-DE"
  And I should not see "#changeLanguageProposalContainer"
  Then I go to "/en/"
  And I wait "#changeLanguageProposalContainer" to appear on current page
  Then I should see a cookie named "locale"

@international
Scenario: An anonymous wants to change locale through footer
  Given feature "multilangue" is enabled
  Given I visited "home page"
  And I wait "#footer-links" to appear on current page
  And I should not see a cookie named "locale"
  And I scroll to the bottom
  And I select "de-DE" in the language footer
  And I should be redirected to "/de/" within 10 seconds
  Then the locale should be "de-DE"
  And I should see a cookie named "locale"

@international
Scenario: An anonymous wants to change locale through footer in route with params
  Given feature "multilangue" is enabled
  Given I visited homepage
  Then I go to "/project/budget-participatif-rennes/collect/collecte-des-propositions"
  And I wait "#footer-links" to appear on current page
  And I should not see a cookie named "locale"
  And I scroll to the bottom
  And I select "de-DE" in the language footer
  And I should be redirected to "/de/project/budget-participatif-rennes/collect/collecte-des-propositions" within 10 seconds
  Then the locale should be "de-DE"
  And I should see a cookie named "locale"

@international
Scenario: A user wants to change locale through profile page
  Given features "multilangue", "profiles" are enabled
  And I go to "/"
  And I set cookie consent
  And I am logged in as user
  Then I go to "/profile/edit-profile#account"
  And I should not see a cookie named "locale"
  And I wait "#display__language" to appear on current page
  And I select "deutsch" from react "#display__language"
  And I click on button "#edit-account-profile-button"
  And I should be redirected to "/de/profile/edit-profile#account"
  And I wait "#footer-links" to appear on current page
  Then the locale should be "de-DE"
  And I should see a cookie named "locale"
