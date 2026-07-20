<?php

/**
 * Tests the following exposed methods of the Conifer\Notifier\AdminNotifier class:
 * to
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit\Notifier;

use Conifer\Notifier\AdminNotifier;
use Conifer\Unit\Base;
use PHPUnit\Framework\MockObject\MockObject;

class AdminNotifierTest extends Base
{
    protected null|AdminNotifier|MockObject $notifier = null;

    private string $adminEmail = 'amerk@sitecrafting.com';
    private string $adminEmailCommanSeparated = 'amerk@sitecrafting.com, another@example.com';
    private array $adminEmailArray = ['amerk@sitecrafting.com', 'another@example.com'];

    public function setUp(): void
    {
        parent::setUp();

        $this->notifier = $this->getMockBuilder(AdminNotifier::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
    }

    public function test_to_returns_single_admin_email()
    {
        \WP_Mock::userFunction('get_option', [
            'args' => ['admin_email'],
            'return' => $this->adminEmail,
        ]);

        $this->assertEquals($this->adminEmail, $this->notifier->to());
    }

    public function test_to_returns_comma_separated_admin_emails()

        \WP_Mock::userFunction('get_option', [
            'args' => ['admin_email'],
            'return' => $this->adminEmailCommanSeparated,
        ]);

        $this->assertEquals($this->adminEmailCommanSeparated, $this->notifier->to());
    }

    public function test_to_returns_array_of_admin_emails()
    {
        \WP_Mock::userFunction('get_option', [
            'args' => ['admin_email'],
            'return' => $this->adminEmailArray,
        ]);

        $this->assertEquals($this->adminEmailArray, $this->notifier->to());
    }
    public function test_admin_notifier_should_extend_email_notifier()
    {
        $this->assertInstanceOf(\Conifer\Notifier\EmailNotifier::class, $this->notifier);
    }
}
