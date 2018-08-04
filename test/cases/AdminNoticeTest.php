<?php

/**
 * Tests for the Conifer\Admin\Notice class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace ConiferTest;

use WP_Mock;
use WP_Mock\Functions;

use Conifer\Admin\Notice;

class AdminNoticeTest extends Base {
  public function test_display() {
    $notice = new Notice('hello');
    $notice->display();

    $this->expect_admin_notices_action_added();
  }

  public function test_success() {
    $notice = new Notice('hello');
    $notice->success();

    $this->assertEquals('notice notice-success', $notice->get_class());
    $this->expect_admin_notices_action_added();
  }

  public function test_info() {
    $notice = new Notice('hello');
    $notice->info();

    $this->assertEquals('notice notice-info', $notice->get_class());
    $this->expect_admin_notices_action_added();
  }

  public function test_warning() {
    $notice = new Notice('hello');
    $notice->warning();

    $this->assertEquals('notice notice-warning', $notice->get_class());
    $this->expect_admin_notices_action_added();
  }

  public function test_error() {
    $notice = new Notice('hello');
    $notice->error();

    $this->assertEquals('notice notice-error', $notice->get_class());
    $this->expect_admin_notices_action_added();
  }

  public function test_html() {
    $notice = new Notice('message');

    // notices are errors by default
    $this->assertEquals(
      '<div class="notice notice-error"><p>message</p></div>',
      $notice->html()
    );
  }

  public function test_optional_constructor_arg() {
    $notice = new Notice('msg', 'example');

    $this->assertEquals(
      'notice example',
      $notice->get_class()
    );
  }

  public function test_add_class() {
    $notice = new Notice('msg');
    $notice->add_class('example');

    $this->assertEquals(
      'notice example',
      $notice->get_class()
    );
  }

  public function test_add_class_with_duplicate() {
    $notice = new Notice('msg');
    $notice->add_class('once');
    $notice->add_class('once');

    $this->assertEquals(
      'notice once',
      $notice->get_class()
    );
  }

  public function test_flash() {
    $this->markTestSkipped();
  }


  protected function expect_admin_notices_action_added() {
    WP_Mock::expectActionAdded('admin_notices', Functions::type('callable'));
  }
}
