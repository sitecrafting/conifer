<?php
/**
 * Test the Conifer\Notifier\SendsEmail trait
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Unit;

use WP_Mock;

use Conifer\Notifier\SendsEmail;

class SendsEmailTest extends Base {
  const TO_ADDRESS = 'you@example.com';

  const HTML_HEADERS = ['Content-Type: text/html; charset=UTF-8'];

  protected $notifier;

  public function setUp(): void {
    parent::setUp();

    $this->notifier = $this->getMockForTrait(SendsEmail::class);

    // mock the abstract to() method
    $this->notifier
      ->expects($this->any())
      ->method('to')
      ->will($this->returnValue(self::TO_ADDRESS));
  }

  public function test_html_message() {
    $expectedHeaders   = self::HTML_HEADERS;
    $expectedHeaders[] = 'x-can-haz-cheezburger: yas';

    // assert that it adds in the correct header
    WP_Mock::userFunction('wp_mail', [
      'times'  => 1,
      'args'   => [self::TO_ADDRESS, 'hi', 'lorem ipsum', $expectedHeaders],
      'return' => true,
    ]);

    // pass custom headers
    $this->assertTrue($this->notifier->send_html_message(
      self::TO_ADDRESS,
      'hi',
      'lorem ipsum',
      ['x-can-haz-cheezburger: yas']
    ));
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
