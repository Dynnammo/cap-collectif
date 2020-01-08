@core @rgpd
Feature: RGPD

Scenario: An anonymous wants to simply accept cookies by clicking on accept button and I check that the cookie is well loaded
  Given I visited "home page" with cookies not accepted
  And I should see 0 ".classname-set-by-advertising-cookie" elements
  And I should see "texte-cookie-audience-communication"
  When I click on button "#cookie-consent"
  And I wait ".classname-set-by-advertising-cookie" to appear on current page
  Then I should not see "texte-cookie-audience-communication"

Scenario: An anonymous cant simply accept cookies by clicking anywhere
  Given I visited "home page" with cookies not accepted
  And I should see "texte-cookie-audience-communication"
  When I click the "#main" element
  Then I should see "texte-cookie-audience-communication"

Scenario: An anonymous cant simply accept cookies by scrolling
  Given I go to a proposal with lot of comments
  And I should see "texte-cookie-audience-communication"
  Then I scroll to the bottom
  And I scroll to the bottom
  Then I should see "texte-cookie-audience-communication"

Scenario: An anonymous wants to show cookie page
  Given I visited "home page" with cookies not accepted
  And I should see "texte-cookie-audience-communication"
  Then I click on button "#cookie-more-button"
  And I should see "cookies.content.page"
  When I click the "#cookies-cancel" element
  And I should not see "cookies.content.page"
  Then I should see "texte-cookie-audience-communication"

Scenario: An anonymous wants to toggle cookies performance
  Given I visited "projects page" with cookies not accepted
  And I scroll to the bottom
  When I click on button "#cookies-management"
  And I wait 1 seconds
  And I should see "global.disabled"
  Then I toggle performance cookies
  And I should see "list.label_enabled"
  And I click on button "#cookies-save"
  And I should not see "cookies.content.page"
  And I should not see "texte-cookie-audience-communication"
  Then I go to a proposal with lot of comments
  And I visited "projects page" with cookies not accepted
  When I click on button "#cookies-management"
  And I should see "list.label_enabled"
  And I should not see "texte-cookie-audience-communication"

Scenario: An anonymous wants to toggle cookies advertising
  Given I visited "projects page" with cookies not accepted
  And I scroll to the bottom
  When I click on button "#cookies-management"
  And I should see "global.disabled"
  Then I toggle advertising cookies
  And I should see "list.label_enabled"
  And I click on button "#cookies-save"
  And I should not see "texte-cookie-audience-communication"
  Then I go to a proposal with lot of comments
  And I visited "projects page" with cookies not accepted
  When I click on button "#cookies-management"
  And I should see "global.disabled"
  And I should see "list.label_enabled"
  And I should not see "texte-cookie-audience-communication"

Scenario: An anonymous accept cookies then should have one created
  Given I visited "home page" with cookies not accepted
  And I should not see a cookie named "hasFullConsent"
  And I should see "texte-cookie-audience-communication"
  And I click on button "#cookie-consent"
  Then I should not see "texte-cookie-audience-communication"
  Then I should see a cookie named "hasFullConsent"

Scenario: An anonymous accept cookies then change is mind and the cookies should be deleted
  Given I am on the homepage
  And I should not see "texte-cookie-audience-communication"
  And I should see a cookie named "hasFullConsent"
  And I create a cookie named "_pk_id.2733"
  And I visited "projects page"
  When I click on button "#cookies-management"
  And I toggle performance cookies
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
