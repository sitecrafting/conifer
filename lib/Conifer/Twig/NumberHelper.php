<?php

declare(strict_types=1);

/**
 * Custom Twig filters for formatting numbers
 */
namespace Conifer\Twig;

/**
 * Twig Wrapper around generic filters for numbers
 *
 * @package Conifer
 */
class NumberHelper implements HelperInterface {
  /**
   * Get the Twig functions to register
   *
   * @return \Closure[] an associative array of callback functions, keyed by name
   */
  public function get_filters() : array {
    return [
      'us_phone' => $this->us_phone(...),
    ];
  }

  /**
   * Does not supply any additional Twig functions.
   *
   * @return array{}
   */
  public function get_functions() : array {
    return [];
  }

  /**
   * Filter any 10-digit number into a formatted US phone number
   *
   * @param  string $phone a string of digits. Must be a 10-character string of
   * digits (with an optional leading "1") or it won't filter anything.
   * @return string the formatted phone number
   */
  public function us_phone( $phone ) {
    // Capture pieces of the number
    $matches = [];
    preg_match( '/^1?(\d\d\d)(\d\d\d)(\d\d\d\d)$/', $phone, $matches );

    // If we have the correct number of digits, format it out...
    if ( count($matches) === 4 ) {
      $phone = sprintf('(%s) %s-%s', $matches[1], $matches[2], $matches[3]);
    }

    // Return the phone number, formatted or not
    return $phone;
  }
}
