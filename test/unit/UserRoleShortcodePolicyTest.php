<?php
/**
 * Test the UserRoleShortcodePolicy class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace ConiferTest;

use WP_Mock;

use Conifer\Authorization\UserRoleShortcodePolicy;

class UserRoleShortcodeAuthorizationPolicyTest extends Base {
  private $policy;

  public function setUp() {
    parent::setUp();
    $this->policy = new UserRoleShortcodePolicy();
  }

  public function test_decide_authorized() {
    $this->markTestSkipped();
    $this->assertTrue($this->policy->decide(
      ['role' => 'editor'],
      'some content',
      $this->mockCurrentUser(123, [], ['wp_capabilities' => ['editor' => true]])
    ));
  }

  public function test_decide_unauthorized() {
    $this->markTestSkipped();
    $this->assertFalse($this->policy->decide(
      ['role' => 'editor'],
      'some content',
      $this->mockCurrentUser(123, [], ['wp_capabilities' => ['subscriber' => true]])
    ));
  }

  public function test_decide_with_default_atts() {
    $this->markTestSkipped();
    $this->assertTrue($this->policy->decide(
      [], // require "administrator" role by default
      'some content',
      $this->mockCurrentUser(123, [], ['wp_capabilities' => ['administrator' => true]])
    ));
  }

  public function test_decide_with_multiple_roles() {
    $this->markTestSkipped();
    $user = $this->mockCurrentUser(123, [], [
      'wp_capabilities' => ['editor' => true],
    ]);

    $this->assertTrue($this->policy->decide(
      ['role' => ' editor, administrator'],
      'some content',
      $user
    ));
  }
}
