<?php

/**
 * SendsEmail trait for mailer classes to consume
 */

namespace Conifer\Notifier;

/**
 * Trait for sending email messages via WordPress
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo
 * @package   Conifer
 */
trait SendsEmail {
  /**
   * Send an HTML email
   *
   * @param array|string $to array or comma-separated list of email addresses
   * to send to
   * @param string $subject the email subject
   * @param string $message the body of the email
   * @param array $headers any additional headers to set
   * @return bool whether the messages were sent successfully
   */
  public function send_html_message(
    $to,
    string $subject,
    string $message,
    array $headers = []
  ) : bool {
    return wp_mail($to, utf8_encode($subject), $message, array_merge([
      'Content-Type: text/html; charset=UTF=8'
    ], $headers));
  }

  /**
   * Send a plaintext email
   *
   * @param array|string $to array or comma-separated list of email addresses
   * to send to
   * @param string $subject the email subject
   * @param string $message the body of the email
   * @param array $headers any additional headers to set
   * @return bool whether the messages were sent successfully
   */
  public function send_plaintext_message(
    $to,
    string $subject,
    string $message,
    array $headers = []
  ) : bool {
    return wp_mail($to, utf8_encode($subject), $message, $headers);
  }

  /**
   * Alias of notify_html
   */
  public function notify(...$args) {
    return $this->notify_html(...$args);
  }

  /**
   * Send an HTML notification email
   *
   * @param string $subject the email subject
   * @param string $message the body of the email
   * @param array $headers any additional headers to set
   * @return bool whether the messages were sent successfully
   */
  public function notify_html(
    string $subject,
    string $message,
    array $headers = []
  ) : bool {
    return $this->send_html_message(
      $this->get_valid_to_address(),
      $subject,
      $message,
      $headers
    );
  }

  /**
   * Send a plaintext notification email
   *
   * @param string $subject the email subject
   * @param string $message the body of the email
   * @param array $headers any additional headers to set
   * @return bool whether the messages were sent successfully
   */
  public function notify_plaintext(
    string $subject,
    string $message,
    array $headers = []
  ) : bool {
    return $this->send_plaintext_message(
      $this->get_valid_to_address(),
      $subject,
      $message,
      $headers
    );
  }

  /**
   * Call the user-defined to() method, and throw an exception if returned
   * value is invalid
   */
  protected function get_valid_to_address() {
    $to = $this->to();

    // Warn the (dev) user extending this class that they're doing it wrong
    if (!is_string($to) && !is_array($to)) {
      throw new \LogicException(
        static::class.'::get_admin_email() must return a string or array'
      );
    }

    return $to;
  }
}


