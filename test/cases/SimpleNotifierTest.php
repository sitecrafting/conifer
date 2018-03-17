<?php

/**
 * Test the Conifer\Post class
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace ConiferTest;

use WP_Mock;

use Conifer\Notifier\SimpleNotifier;

class SimpleNotifierTest extends Base {
  const TO_ADDRESS = 'you@example.com';

  const HTML_HEADERS = ['Content-Type: text/html; charset=UTF=8'];

  protected $notifier;

  public function setUp() {
    parent::setUp();

    $this->notifier = new SimpleNotifier(self::TO_ADDRESS);
  }

  public function test_notify_html() {
    WP_Mock::userFunction('wp_mail', [
      'times' => 1,
      'args'  => [self::TO_ADDRESS, 'hi', 'lorem ipsum', self::HTML_HEADERS],
      'return' => true,
    ]);

    $this->assertTrue($this->notifier->notify('hi', 'lorem ipsum'));
  }

  public function test_notify_plaintext() {
    WP_Mock::userFunction('wp_mail', [
      'times' => 1,
      'args'  => [self::TO_ADDRESS, 'hi', 'lorem ipsum', []],
      'return' => true,
    ]);

    $this->assertTrue($this->notifier->notify_plaintext('hi', 'lorem ipsum'));
  }
}
