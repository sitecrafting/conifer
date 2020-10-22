<?php
/**
 * Test the ShortcodePolicy class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace ConiferTest;

use WP_Mock;

use Timber\User;

use Conifer\Authorization\ShortcodePolicy;

class ShortcodeAuthorizationPolicyTest extends Base {
  private $policy;

  public function setUp() {
    parent::setUp();
    $this->policy = $this->getMockBuilder(ShortcodePolicy::class)->setMethods(['tag'])->getMockForAbstractClass();
  }

  public function test_adopt() {
    $this->policy->expects($this->once())
      ->method('tag')
      ->will($this->returnValue('foobar'));
    WP_Mock::userFunction('add_shortcode', [
      'times' => 1,
      'args' => [
        'foobar',
        WP_Mock\Functions::type('callable'),
      ],
    ]);

    $policy = $this->policy->adopt();

    // test fluent interface
    $this->assertEquals($policy, $this->policy);
  }

  public function test_enforce_when_unauthorized() {
    $this->markTestSkipped();
    $user = $this->mockCurrentUser(123);

    $this->policy->expects($this->once())
      ->method('decide')
      ->will($this->returnValue(false));

    $this->assertEquals('', $this->policy->enforce(
      [],
      'This is restricted content',
      $user
    ));
  }

  public function test_enforce_when_authorized() {
    $this->markTestSkipped();
    $user = $this->mockCurrentUser(123);

    $this->policy->expects($this->once())
      ->method('decide')
      ->will($this->returnValue(true));

    // a restricted place with golden trees
    $restricted = 'CAN YOU TAKE ME HIGHER';
    $this->assertEquals($restricted, $this->policy->enforce(
      [],
      $restricted,
      $user
    ));
  }
}
