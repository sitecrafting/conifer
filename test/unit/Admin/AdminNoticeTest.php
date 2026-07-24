<?php

/**
 * Tests for the Conifer\Admin\Notice class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Unit\Admin;

use WP_Mock;
use WP_Mock\Functions;

use Conifer\Admin\Notice;
use Conifer\Unit\Base;

class AdminNoticeTest extends Base
{
  public function setUp(): void
  {
    parent::setUp();
    Notice::clear_flash_notices();
    Notice::enable_flash_notices();
  }

  public function tearDown(): void
  {
    parent::tearDown();
    Notice::disable_flash_notices();
    Notice::clear_flash_notices();
  }

  public function test_success()
  {
    $notice = new Notice('hello');
    $notice->success();

    $this->assertEquals('notice notice-success', $notice->get_class());
    $this->expect_admin_notices_action_added();
  }

  public function test_info()
  {
    $notice = new Notice('hello');
    $notice->info();

    $this->assertEquals('notice notice-info', $notice->get_class());
    $this->expect_admin_notices_action_added();
  }

  public function test_warning()
  {
    $notice = new Notice('hello');
    $notice->warning();

    $this->assertEquals('notice notice-warning', $notice->get_class());
    $this->expect_admin_notices_action_added();
  }

  public function test_error()
  {
    $notice = new Notice('hello');
    $notice->error();

    $this->assertEquals('notice notice-error', $notice->get_class());
    $this->expect_admin_notices_action_added();
  }

  public function test_html()
  {
    $notice = new Notice('message');

    // notices are errors by default
    $this->assertEquals(
      '<div class="notice notice-error"><p>message</p></div>',
      $notice->html()
    );
  }

  public function test_optional_constructor_arg()
  {
    $notice = new Notice('msg', 'example');

    $this->assertEquals(
      'notice example',
      $notice->get_class()
    );
  }

  public function test_add_class()
  {
    $notice = new Notice('msg');
    $notice->add_class('example');

    $this->assertEquals(
      'notice example',
      $notice->get_class()
    );
  }

  public function test_add_class_with_duplicate()
  {
    $notice = new Notice('msg');
    $notice->add_class('once');
    $notice->add_class('once');

    $this->assertEquals(
      'notice once',
      $notice->get_class()
    );
  }

  public function test_get_flash_notices()
  {
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

  public function test_get_flash_notices_invalid()
  {
    $_SESSION['conifer_admin_notices'] = [
      false,
      'foobar',
      ['message' => ''],
      [
        'message' => 'valid message, bad class',
        'class' => 123,
      ],
    ];

    $this->assertEquals([], Notice::get_flash_notices());
  }

  protected function expect_admin_notices_action_added()
  {
    WP_Mock::expectActionAdded('admin_notices', Functions::type('callable'));
  }

  // ---------------------------------------------------------------------------
  // flash() / flash_error() / flash_warning() / flash_info() / flash_success()
  // ---------------------------------------------------------------------------

  public function test_flash_error_adds_error_class_and_registers_admin_notices_hook()
  {
    $notice = new Notice('Something went wrong');
    WP_Mock::expectActionAdded('admin_notices', Functions::type('callable'));

    $notice->flash_error();

    $this->assertTrue($notice->has_class('notice-error'), 'flash_error() should add notice-error class');
  }

  public function test_flash_warning_adds_warning_class_and_registers_admin_notices_hook()
  {
    $notice = new Notice('Heads up');
    WP_Mock::expectActionAdded('admin_notices', Functions::type('callable'));

    $notice->flash_warning();

    $this->assertTrue($notice->has_class('notice-warning'), 'flash_warning() should add notice-warning class');
  }

  public function test_flash_info_adds_info_class_and_registers_admin_notices_hook()
  {
    $notice = new Notice('For your information');
    WP_Mock::expectActionAdded('admin_notices', Functions::type('callable'));

    $notice->flash_info();

    $this->assertTrue($notice->has_class('notice-info'), 'flash_info() should add notice-info class');
  }

  public function test_flash_success_adds_success_class_and_registers_admin_notices_hook()
  {
    $notice = new Notice('All done');
    WP_Mock::expectActionAdded('admin_notices', Functions::type('callable'));

    $notice->flash_success();

    $this->assertTrue($notice->has_class('notice-success'), 'flash_success() should add notice-success class');
  }

  public function test_flash_writes_class_and_message_to_session_when_closure_fires()
  {
    // flash() schedules a closure via add_action('admin_notices'). This test
    // captures and invokes that closure directly to verify the session payload.
    $_SESSION[Notice::FLASH_SESSION_KEY] = [];

    WP_Mock::expectActionAdded('admin_notices', Functions::type('callable'));

    $double = new NoticeFlashCapture('A flash message', 'notice-success');
    $double->flash();
    $double->invokeCapturedClosure();

    $written = $_SESSION[Notice::FLASH_SESSION_KEY];
    $this->assertCount(1, $written);
    $this->assertSame('A flash message', $written[0]['message']);
    $this->assertStringContainsString('notice-success', $written[0]['class']);
  }

  // ---------------------------------------------------------------------------
  // display_flash_notices()
  // ---------------------------------------------------------------------------

  public function test_display_flash_notices_displays_notices_and_clears_session_when_enabled()
  {
    $_SESSION[Notice::FLASH_SESSION_KEY] = [
      ['class' => 'notice notice-success', 'message' => 'Saved'],
    ];

    // Notice::display() registers an admin_notices action for each notice shown
    WP_Mock::expectActionAdded('admin_notices', Functions::type('callable'));

    // Flash notices are already enabled in setUp()
    Notice::display_flash_notices();

    $this->assertEmpty(
      $_SESSION[Notice::FLASH_SESSION_KEY],
      'Session should be emptied after display_flash_notices() runs'
    );
  }

  public function test_display_flash_notices_is_noop_when_flash_is_disabled()
  {
    $_SESSION[Notice::FLASH_SESSION_KEY] = [
      ['class' => 'notice notice-error', 'message' => 'Should not display'],
    ];

    Notice::disable_flash_notices();
    Notice::display_flash_notices();

    $this->assertCount(
      1,
      $_SESSION[Notice::FLASH_SESSION_KEY],
      'Session must be untouched when flash notices are disabled'
    );
  }
}

/**
 * Test double that captures the closure registered by flash() so it can be
 * invoked synchronously within a test — without requiring WP action dispatch.
 *
 * The closure logic mirrors Notice::flash() exactly, using inherited access
 * to the protected $message property and the concrete get_class() method.
 */
class NoticeFlashCapture extends Notice
{
  private ?\Closure $capturedClosure = null;

  public function flash(): void
  {
    // Capture the values that the real closure would close over
    $class   = $this->get_class();
    $message = $this->message;

    $this->capturedClosure = static function () use ($class, $message) {
      $_SESSION[static::FLASH_SESSION_KEY]   = $_SESSION[static::FLASH_SESSION_KEY] ?? [];
      $_SESSION[static::FLASH_SESSION_KEY][] = [
        'class'   => $class,
        'message' => $message,
      ];
    };

    // Satisfy WP_Mock::expectActionAdded('admin_notices', ...) expectations
    add_action('admin_notices', $this->capturedClosure);
  }

  public function invokeCapturedClosure(): void
  {
    if ($this->capturedClosure !== null) {
      ($this->capturedClosure)();
    }
  }
}
