<?php

/**
 * SimpleNotifier class
 *
 * Useful for use-cases where you have destination email addresses ready,
 * and just want to compose and send a message
 *
 * Example usage:
 *
 *   // get the email contact info
 *   $email = $_POST['signup_email'];
 *   $name = $_POST['signup_name'];
 *
 *   // compose the message
 *   $message = "Hi $name, thanks for signing up!";
 *
 *   // send it
 *   $notifier = new Conifer\Notifier\SimpleNotifier($email);
 *   $notifier->notify('you signed up!', $message);
 */

namespace Conifer\Notifier;

/**
 * Class for emailing arbitrary email addresses
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo
 * @package   Conifer
 */
class SimpleNotifier extends EmailNotifier {
  /**
   * The email address(es) to send to
   * @var string|array
   */
  protected $to;

  /**
   * Constructor. Pass the to email here.
   * @param string|array $o the email addresses to send to.
   * Can be a comma-separated string or an array
   */
  public function __construct($to) {
    $this->to = $to;
  }

  /**
   * Get the admin email address configured in General Settings
   */
  public function to() {
    return $this->to;
  }
}


