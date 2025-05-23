@editor @editor_tiny @tiny_stash @javascript
Feature: Check visibility of the stash icon for teachers and students
    In order to work with stash
    As a content creator using TinyMCE
    I need to be able to see the stash icon while students should not

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

Scenario: Teacher can see the stash icon while editing
    Given I am on the PageName1 "page activity editing" page logged in as teacher1
    Then "Insert stash item" "button" should exist

Scenario: Student can not see the stash icon while editing
    Given I am on the AssignmentName1 "assign activity" page logged in as student1
    When I press "Add submission"
    Then "Insert stash item" "button" should not exist
