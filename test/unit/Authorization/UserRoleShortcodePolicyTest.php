<?php

/**
 * Test the UserRoleShortcodePolicy class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace Conifer\Unit\Authorization;

use Conifer\Authorization\UserRoleShortcodePolicy;
use Conifer\Unit\Base;
use Timber\User;

class UserRoleShortcodeAuthorizationPolicyTest extends Base
{
  private ?UserRoleShortcodePolicy $policy = null;

  public function setUp(): void
  {
    parent::setUp();
    $this->policy = new UserRoleShortcodePolicy();
  }

  private function createUserWithCapabilities(array $capabilities): User
  {
    $user = $this->getMockBuilder(User::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['meta'])
      ->getMock();

    $user->method('meta')
      ->with('wp_capabilities')
      ->willReturn($capabilities);

    return $user;
  }

  public function test_decide_authorized()
  {
    $user = $this->createUserWithCapabilities(['editor' => true]);
    $this->assertTrue($this->policy->decide(
      ['role' => 'editor'],
      'some content',
      $user
    ));
  }

  public function test_decide_unauthorized()
  {
    $user = $this->createUserWithCapabilities(['subscriber' => true]);
    $this->assertFalse($this->policy->decide(
      ['role' => 'editor'],
      'some content',
      $user
    ));
  }

  public function test_decide_with_default_atts()
  {
    $user = $this->createUserWithCapabilities(['administrator' => true]);
    $this->assertTrue($this->policy->decide(
      [], // require "administrator" role by default
      'some content',
      $user
    ));
  }

  public function test_decide_with_multiple_roles()
  {
    $user = $this->createUserWithCapabilities(['editor' => true]);
    $this->assertTrue($this->policy->decide(
      ['role' => ' editor, administrator'],
      'some content',
      $user
    ));
  }
}
