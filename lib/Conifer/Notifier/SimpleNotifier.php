<?php

/**
 * SimpleNotifier class
 *
 * Useful for use-cases where you have destination email addresses ready,
 * and just want to compose and send a message:
 *
 * ```php
 * // get the email contact info
 * $email = $_POST['signup_email'];
 * $name = $_POST['signup_name'];
 *
 * // compose the message
 * $message = "Hi $name, thanks for signing up!";
 *
 * // send it
 * $notifier = new Conifer\Notifier\SimpleNotifier($email);
 * $notifier->notify('you signed up!', $message);
 * ```
 */

namespace Conifer\Notifier;

use InvalidArgumentException;

/**
 * Class for emailing arbitrary email addresses
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo
 * @package   Conifer
 */
class SimpleNotifier extends EmailNotifier
{
  const DEFAULT_EXCEPTION_MESSAGE = 'The $to argument must be a valid email address, a comma-separated list of valid email addresses, or an array of valid email addresses';
  /**
   * The email address(es) to send to
   *
   * @var string|array
   */
  protected $to;

  /**
   * Constructor. Pass the to email here.
   *
   * @param string|array $to the email addresses to send to.
   * Can be a comma-separated string or an array
   * 
   * @throws InvalidArgumentException if $to is not a valid single email address, a comma-separated list of valid email addresses, or an array of valid email addresses.
   */
  public function __construct(string|array $to)
  {
    // Save a copy to validate, since we may need to convert a string to an array
    $toCopy = $to;

    if (empty($to)) {
      throw new \InvalidArgumentException(self::DEFAULT_EXCEPTION_MESSAGE);
    }

    if (gettype($to) === 'string') {
      // Check if this is a comma-separated list of emails, or just a single email address
      $toCopy = array_map('trim', explode(',', $to));

      // If our array is not empty, loop through and validate all of the emails
      if (!empty($toCopy)) {
        foreach ($toCopy as $email) {
          if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(self::DEFAULT_EXCEPTION_MESSAGE);
          }
        }
        // We are likely working with a single email address, so we will just validate that one
      } else {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
          throw new \InvalidArgumentException(self::DEFAULT_EXCEPTION_MESSAGE);
        }
      }
    } else if (gettype($to) !== 'array') {
      throw new \InvalidArgumentException(self::DEFAULT_EXCEPTION_MESSAGE);
    }

    // If we have reached this point, we have an array of some sort, so we need to validate all of the emails in the array.
    // We will still check the type for sanity
    if (gettype($toCopy) === 'array') {
      foreach ($toCopy as $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          throw new \InvalidArgumentException(self::DEFAULT_EXCEPTION_MESSAGE);
        }
      }
    }

    $this->to = $to;
  }

  /**
   * Get the admin email address configured in General Settings
   */
  public function to()
  {
    return $this->to;
  }
}
