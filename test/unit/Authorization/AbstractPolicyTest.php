<?php

/**
 * Test the Conifer\Authorization\AbstractPolicy class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit\Authorization;

use Conifer\Authorization\AbstractPolicy;
use Conifer\Authorization\PolicyInterface;
use Conifer\Unit\Base;

class AbstractPolicyTest extends Base
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_register_returns_instance_of_policy_interface()
    {
        $policy = TestPolicy::register();

        $this->assertInstanceOf(PolicyInterface::class, $policy);
    }
}

class TestPolicy extends AbstractPolicy
{
    public function can_view(): bool
    {
        return true;
    }

    public function adopt(): PolicyInterface
    {
        // For testing purposes, just return $this
        return $this;
    }
}
