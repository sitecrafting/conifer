<?php

/**
 * Test the TemplateAuthorizationPolicy class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace Conifer\Unit\Authorization;

use WP_Mock;

use Conifer\Authorization\TemplatePolicy;
use Conifer\Unit\Base;

class TemplateAuthorizationPolicyTest extends Base
{
  private ?TemplatePolicy $policy = null;

  public function setUp(): void
  {
    parent::setUp();
    $this->policy = $this->getMockForAbstractClass(
      TemplatePolicy::class
    );
  }

  public function test_adopt()
  {
    WP_Mock::expectFilterAdded('template_include', WP_Mock\Functions::type('callable'));
    $policy = $this->policy->adopt();

    // test fluent interface
    $this->assertEquals($policy, $this->policy);
  }
}
