Feature: Examples

  @initLdapDbBeforeScenario
  @initLdapDbAfterScenario
  Scenario Outline: Examples for cruftman/foo
    Given I executed doc example <example_file>
    Then I should see stdout from <stdout_file>
    And I should see stderr from <stderr_file>
    And I should see exit code <exit_code>

    Examples:
      | example_file                              | stdout_file                                  | stderr_file                                  | exit_code |

  Scenario Outline: Examples for cruftman/bar
    Given I executed doc example <example_file>
    Then I should see stdout from <stdout_file>
    And I should see stderr from <stderr_file>
    And I should see exit code <exit_code>

    Examples:
      | example_file                              | stdout_file                                  | stderr_file                                  | exit_code |
