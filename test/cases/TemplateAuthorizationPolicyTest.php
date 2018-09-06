<?php
/**
 * Test the TemplateAuthorizationPolicy class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace ConiferTest;

use WP_Mock;

use Conifer\Authorization\TemplatePolicy;

class TemplateAuthorizationPolicyTest extends Base {
  private $policy;

  public function setUp() {
    parent::setUp();
    $this->policy = $this->getMockForAbstractClass(
      TemplatePolicy::class
    );
  }

  public function test_adopt() {
    WP_Mock::expectFilterAdded('template_include', WP_Mock\Functions::type('callable'));
    $policy = $this->policy->adopt();

    // test fluent interface
    $this->assertEquals($policy, $this->policy);
  }
}
