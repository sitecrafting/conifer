<?php

/**
 * SendsEmail trait for mailer classes to consume
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
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
   * MUST return the destination email address(es)
   *
   * @return string|array
   */
  abstract public function to();

  /**
   * Send a UTF-8-encoded HTML email
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
    return wp_mail($to, mb_convert_encoding($subject, 'UTF-8'), $message, array_merge([
      'Content-Type: text/html; charset=UTF-8',
    ], $headers));
  }

  /**
   * Send a UTF-8-encoded plaintext email
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
    return wp_mail($to, mb_convert_encoding($subject, 'UTF-8'), $message, $headers);
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
   *
   * @throws \LogicException if to() returns the wrong type
   */
  protected function get_valid_to_address() {
    $to = $this->to();

    // Warn the (dev) user extending this class that they're doing it wrong
    if (!is_string($to) && !is_array($to)) {
      throw new \LogicException(
        static::class . '::to() must return a string or array'
      );
    }

    return $to;
  }
}


