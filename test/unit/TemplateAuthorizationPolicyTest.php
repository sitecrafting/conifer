<?php

declare(strict_types=1);

/**
 * Test the TemplateAuthorizationPolicy class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */
namespace Conifer\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use WP_Mock;

use Conifer\Authorization\TemplatePolicy;

class TemplateAuthorizationPolicyTest extends Base {
  private MockObject $policy;

  protected function setUp(): void {
    parent::setUp();
    $this->policy = $this->getMockForAbstractClass(
      TemplatePolicy::class
    );
  }

  public function test_adopt(): void {
    WP_Mock::expectFilterAdded('template_include', WP_Mock\Functions::type('callable'));
    $policy = $this->policy->adopt();

    // test fluent interface
    $this->assertEquals($policy, $this->policy);
  }
}
