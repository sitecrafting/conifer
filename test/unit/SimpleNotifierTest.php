<?php

/**
 * Tests for the Conifer\Notifier\SimpleNotifier class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit;

use Conifer\Notifier\SimpleNotifier;
use PHPUnit\Framework\MockObject\MockObject;

class SimpleNotifierTest extends Base
{
    protected null|SimpleNotifier|MockObject $notifier = null;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_simple_notifier_can_be_instantiated_with_string()
    {
        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs(['Hello']) // Technically correct, but should be an email address
            ->getMock();

        $this->assertInstanceOf(SimpleNotifier::class, $this->notifier);
    }

    public function test_simple_notifier_can_be_instantiated_with_array()
    {
        $to = ['Hello', 'World'];
        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs([$to]) // Technically correct, but should be an email address
            ->getMock();

        $this->assertInstanceOf(SimpleNotifier::class, $this->notifier);
    }

    public function test_simple_notifier_to_method_returns_email_address()
    {
        $to = 'Hello';

        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs([$to]) // Technically correct, but should be an email address
            ->onlyMethods([])
            ->getMock();

        $this->assertEquals($to, $this->notifier->to());
    }

    public function test_simple_notifier_should_be_instance_of_email_notifier()
    {
        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs(['Hello']) // Technically correct, but should be an email address
            ->onlyMethods([])
            ->getMock();

        $this->assertInstanceOf(\Conifer\Notifier\EmailNotifier::class, $this->notifier);
    }
}
