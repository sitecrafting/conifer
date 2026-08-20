<?php

/**
 * Tests for the Conifer\Notifier\SimpleNotifier class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit\Notifier;

use Conifer\Notifier\SimpleNotifier;
use Conifer\Unit\Base;
use PHPUnit\Framework\MockObject\MockObject;

class SimpleNotifierTest extends Base
{
    protected null|SimpleNotifier|MockObject $notifier = null;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_simple_notifier_can_be_instantiated_with_valid_email_string()
    {
        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs(['hello@example.com'])
            ->getMock();

        $this->assertInstanceOf(SimpleNotifier::class, $this->notifier);
    }

    public function test_simple_notifier_can_be_instantiated_with_comma_separated_email_string()
    {
        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs(['hello@example.com, world@example.com'])
            ->getMock();

        $this->assertInstanceOf(SimpleNotifier::class, $this->notifier);
    }

    public function test_simple_notifier_can_be_instantiated_with_array()
    {
        $to = ['hello@example.com', 'world@example.com'];
        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs([$to])
            ->getMock();

        $this->assertInstanceOf(SimpleNotifier::class, $this->notifier);
    }

    public function test_simple_notifier_to_method_returns_email_address()
    {
        $to = 'hello@example.com';

        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs([$to])
            ->onlyMethods([])
            ->getMock();

        $this->assertEquals($to, $this->notifier->to());
    }

    public function test_simple_notifier_to_method_returns_comma_separated_email_string_unmodified()
    {
        // the raw string is preserved as-is (not converted to an array), even
        // though it is split up internally in order to validate each address
        $to = 'hello@example.com, world@example.com';

        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs([$to])
            ->onlyMethods([])
            ->getMock();

        $this->assertEquals($to, $this->notifier->to());
    }

    public function test_simple_notifier_to_method_returns_email_address_array()
    {
        $to = ['hello@example.com', 'world@example.com'];

        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs([$to])
            ->onlyMethods([])
            ->getMock();

        $this->assertEquals($to, $this->notifier->to());
    }

    public function test_simple_notifier_should_be_instance_of_email_notifier()
    {
        $this->notifier = $this->getMockBuilder(SimpleNotifier::class)
            ->setConstructorArgs(['hello@example.com'])
            ->onlyMethods([])
            ->getMock();

        $this->assertInstanceOf(\Conifer\Notifier\EmailNotifier::class, $this->notifier);
    }

    public function test_constructor_throws_exception_for_invalid_single_email_string()
    {
        $this->expectException(\InvalidArgumentException::class);

        new SimpleNotifier('Hello');
    }

    public function test_constructor_throws_exception_for_empty_string()
    {
        $this->expectException(\InvalidArgumentException::class);

        new SimpleNotifier('');
    }

    public function test_constructor_throws_exception_for_empty_array()
    {
        $this->expectException(\InvalidArgumentException::class);

        new SimpleNotifier([]);
    }

    public function test_constructor_throws_exception_when_comma_separated_string_contains_invalid_email()
    {
        $this->expectException(\InvalidArgumentException::class);

        new SimpleNotifier('hello@example.com, not-an-email');
    }

    public function test_constructor_throws_exception_for_array_containing_invalid_email()
    {
        $this->expectException(\InvalidArgumentException::class);

        new SimpleNotifier(['hello@example.com', 'not-an-email']);
    }

    public function test_constructor_throws_exception_for_array_of_all_invalid_emails()
    {
        $this->expectException(\InvalidArgumentException::class);

        new SimpleNotifier(['Hello', 'World']);
    }

    public function test_constructor_throws_type_error_for_non_string_non_array_argument()
    {
        // an int/bool would be coerced to string under weak typing, so use a
        // value that cannot be coerced to either string or array
        $this->expectException(\TypeError::class);

        // Intelliphense might complain about this, but we want to test the type error handling
        new SimpleNotifier(new \stdClass());
    }
}
