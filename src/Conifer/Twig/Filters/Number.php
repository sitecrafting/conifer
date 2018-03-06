<?php
/**
 * Custom Twig filters for formatting numbers
 */

namespace Conifer\Twig\Filters;

/**
 * Twig Wrapper around generic filters for numbers
 *
 * @package Conifer
 */
class Number extends AbstractBase {
	/**
	 * Get the Twig functions to register
	 * @return  array an associative array of callback functions, keyed by name
	 */
	public function get_filters() {
		return [
			'us_phone' => [$this, 'us_phone'],
		];
	}

	/**
	 * Filter any 10-digit number into a formatted US phone number
	 * @param  string $phone a string of digits. Must be a 10-character string of
	 * digits (with an optional leading "1") or it won't filter anything.
	 * @return string the formatted phone number
	 */
	public function us_phone( $phone ) {
		// Capture pieces of the number
		$matches = [];
		preg_match( '/^1?(\d\d\d)(\d\d\d)(\d\d\d\d)$/', $phone, $matches );

		// If we have the correct number of digits, format it out...
		if( count($matches) == 4 ) {
			$phone = "({$matches[1]}) {$matches[2]}-{$matches[3]}";
		}

		// Return the phone number, formatted or not
		return $phone;
	}
}