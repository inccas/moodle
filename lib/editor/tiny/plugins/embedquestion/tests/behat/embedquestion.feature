@ou @ou_vle @editor @tiny  @editor_tiny @tiny_embedquestion @filter_embedquestion
Feature: Embed question in the Tiny editor
  In order to encourage students interacting with ativity and learning from it
  As a teacher
  I need to insert interactive questions in my content

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher  | Terry     | Teacher  | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           | idnumber |
      | Course       | C1        | Test questions | embed |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | idnumber |
      | Test questions   | truefalse | First question | test1    |
    And the "embedquestion" filter is "on"

  @javascript
  Scenario: Test using 'Embed question' button
    Given I am on the "Course 1" course page logged in as teacher
    And I turn editing mode on
    When I add a page activity to course "Course 1" section "1"
    And I set the field "Name" to "Test page 01"
    And I set the field "Description" to "Test page description"
    And I set the field "content" to "Test page content"
    When I click on the "Insert > Embedded question" menu item for the "Description" TinyMCE editor
    And I set the field "Question category" to "Test questions [embed] (1)"
    And I set the field "id_questionidnumber" to "First question [test1]"
    And I click on "Embed question" "button" in the "Embedded question" "dialogue"
    And I switch to the "Description" TinyMCE editor iframe
    Then I should see "{Q{embed/test1|"
    And I should see "}Q}Test page description"
    And I switch to the main frame
    # Check that reopening the form sets the fields to the current question.
    And I select the "Q" "text" in the "Description" TinyMCE editor
    And I click on the "Insert > Embedded question" menu item for the "Description" TinyMCE editor
    And the field "Question category" matches value "Test questions [embed] (1)"
    And the field "id_questionidnumber" matches value "First question [test1]"
