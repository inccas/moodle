@editor @editor_tiny @tiny_stash @javascript @_file_upload
Feature: Create a stash item with tiny stash
    In order to create a stash item
    As a content creator using TinyMCE
    I can use the editor to create a stash item

Background:
    Given the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity   | name            | intro           | introformat | course | content    | contentformat | idnumber | assignsubmission_onlinetext_enabled |
      | page       | PageName1       | PageDesc1       | 1           | C1     | Stash test | 1             | 1        |                                     |
      | assign     | AssignmentName1 | AssignmentDesc1 | 1           | C1     |            |               | 2        | 1                                   |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | stash         | Course       | C1        | course-view-*   | site-post     |

Scenario: The teacher can create a stash item
    Given I am on the PageName1 "page activity editing" page logged in as teacher1
    And I click on the "Insert stash item" button for the "Page content" TinyMCE editor
    And I press "Add item"
    And I wait "1" seconds
    # Seem to be having trouble getting this field to fill out. Direct copy from block stash feature which works.
    And I set the field "Item name" to "coin"
    And I upload "lib/editor/tiny/plugins/stash/tests/fixtures/coin.png" file to "Image" filemanager
    And I set the field "Item name" to "Coin"
    And I press "Add item and create drop"
    And I wait "2" seconds

