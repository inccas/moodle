@theme @theme_boost_union_dhoch3 @theme_boost_union_dhoch3_feelsettings @theme_boost_union_dhoch3_feelsettings_pagelayouts
Feature: Configuring the theme_boost_union_dhoch3 plugin for the "Page layouts" tab on the "Feel" page
  In order to use the features
  As admin
  I need to be able to configure the theme Boost Union Dhoch3 plugin

  @javascript
  Scenario Outline: Setting: Show navigation on policy overview page
    Given the following config values are set as admin:
      | config                   | value     | plugin            |
      | policyoverviewnavigation | <setting> | theme_boost_union_dhoch3 |
    And I visit '/admin/tool/policy/viewall.php'
    Then ".navbar" "css_element" <shouldornot> exist
    And "#page-footer" "css_element" <shouldornot> exist

    Examples:
      | setting | shouldornot |
      | yes     | should      |
      | no      | should not  |
