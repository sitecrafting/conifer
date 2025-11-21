<?php

/**
 * Tests for the Conifer\Admin\Notice class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

declare(strict_types=1);

namespace Conifer\Unit;

use WP_Mock;
use WP_Mock\Functions;

use Conifer\Admin\Notice;

class AdminNoticeTest extends Base {
    protected function setUp(): void {
        parent::setUp();
        Notice::clear_flash_notices();
        Notice::enable_flash_notices();
    }

    protected function tearDown(): void {
        parent::tearDown();
        Notice::disable_flash_notices();
        Notice::clear_flash_notices();
    }

    public function test_success(): void {
        $notice = new Notice('hello');
        $notice->success();

        $this->assertEquals('notice notice-success', $notice->get_class());
        $this->expect_admin_notices_action_added();
    }

    public function test_info(): void {
        $notice = new Notice('hello');
        $notice->info();

        $this->assertEquals('notice notice-info', $notice->get_class());
        $this->expect_admin_notices_action_added();
    }

    public function test_warning(): void {
        $notice = new Notice('hello');
        $notice->warning();

        $this->assertEquals('notice notice-warning', $notice->get_class());
        $this->expect_admin_notices_action_added();
    }

    public function test_error(): void {
        $notice = new Notice('hello');
        $notice->error();

        $this->assertEquals('notice notice-error', $notice->get_class());
        $this->expect_admin_notices_action_added();
    }

    public function test_html(): void {
        $notice = new Notice('message');

        // notices are errors by default
        $this->assertEquals(
        '<div class="notice notice-error"><p>message</p></div>',
        $notice->html()
        );
    }

    public function test_optional_constructor_arg(): void {
        $notice = new Notice('msg', 'example');

        $this->assertEquals(
        'notice example',
        $notice->get_class()
        );
    }

    public function test_add_class(): void {
        $notice = new Notice('msg');
        $notice->add_class('example');

        $this->assertEquals(
        'notice example',
        $notice->get_class()
        );
    }

    public function test_add_class_with_duplicate(): void {
        $notice = new Notice('msg');
        $notice->add_class('once');
        $notice->add_class('once');

        $this->assertEquals(
        'notice once',
        $notice->get_class()
        );
    }

    public function test_get_flash_notices(): void {
        $_SESSION['conifer_admin_notices'] = [
        [
        'class'   => 'notice notice-success',
        'message' => 'all your base',
        ],
        [
        'class'   => 'notice notice-error',
        'message' => 'are belong to us',
        ],
        ];

        $this->expect_admin_notices_action_added();

        $notices = Notice::get_flash_notices();
        $this->assertEquals(
        '<div class="notice notice-success"><p>all your base</p></div>',
        $notices[0]->html()
        );
        $this->assertEquals(
        '<div class="notice notice-error"><p>are belong to us</p></div>',
        $notices[1]->html()
        );
    }

    public function test_get_flash_notices_invalid(): void {
        $_SESSION['conifer_admin_notices'] = [
        false,
        'foobar',
        [ 'message' => '' ],
        [
        'message' => 'valid message, bad class',
        'class' => 123,
        ],
        ];

        $this->assertEquals([], Notice::get_flash_notices());
    }

    protected function expect_admin_notices_action_added(): void {
        WP_Mock::expectActionAdded('admin_notices', Functions::type('callable'));
    }
}
