<?php
/**
 * Test the Conifer\Alert\DismissableAlert class
 *
 * @copyright 2020 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Unit;

use \DateTime;

use Conifer\Alert\DismissableAlert;

class DismissableAlertTest extends Base {
  public function test_cookie_text() {
    $text   = 'IMPORTANT ALERT!!!';
    $cookie = 'wp-user_dismissed_alert_' . md5($text);
    $alert  = new DismissableAlert($text, [
      'cookies' => [$cookie => 1],
      'cookie_expires' => 'Sat, 21 Mar 2020 12:11:34 -0700',
    ]);

    $this->assertEquals(
      'wp-user_dismissed_alert_7f408226eeff9c2f4661611635792d9c=1; expires=Sat, 21 Mar 2020 12:11:34 -0700; path=/',
      $alert->cookie_text()
    );
  }

  public function test_cookie_text_default_expires() {
    $text   = 'IMPORTANT ALERT!!!';
    $cookie = 'wp-user_dismissed_alert_' . md5($text);
    $alert  = new DismissableAlert($text, [
      'cookies' => [$cookie => 1],
      // let the expiry default to one year from now
    ]);
    preg_match('~=1; expires=(.+); path=/$~', $alert->cookie_text(), $matches);
    $dt = date_create_from_format('U', strtotime($matches[1]));

    // assert that expires=(...) gets a valid formatted datetime
    $this->assertNotEmpty($matches[1]);
    $this->assertEquals($matches[1], $dt->format('r'));
  }

  public function test_cooke_text_path_opt() {
    $text   = 'THIS IS AN IMPORTANT ALERT!!!';
    $cookie = 'wp-user_dismissed_alert_' . md5($text);
    $alert  = new DismissableAlert($text, [
      'cookies' => [$cookie => 1],
      'cookie_expires' => 'Sat, 21 Mar 2020 12:11:34 -0700',
      'cookie_path' => '/custom',
    ]);

    $this->assertEquals(
      'wp-user_dismissed_alert_c3b076f60a0a7bf31661e52ad147f761=1; expires=Sat, 21 Mar 2020 12:11:34 -0700; path=/custom',
      $alert->cookie_text()
    );
  }

  public function test_dismissed() {
    $text   = 'IMPORTANT ALERT!!!';
    $cookie = 'wp-user_dismissed_alert_' . md5($text);
    $alert  = new DismissableAlert($text, [
      'cookies' => [$cookie => 1],
    ]);

    $this->assertTrue($alert->dismissed());
  }
}
