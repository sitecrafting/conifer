<?php
/**
 * Abstract base class for declarative, OO Twig functions
 */

namespace Conifer\Twig\Functions;

use Conifer\Site;

/**
 * Easily define custom functions to add to Twig by extending this class.
 * Then just call YourCustomFunctionClass::add_twig_functions( $site );
 *
 * @package Conifer
 */
abstract class AbstractBase {
	protected $site;

	public function __construct( Site $site ) {
		$this->site = $site;
	}

	/**
	 * Register the Twig functions this class defines in get_functions()
	 * on the central Site object
	 * @param type \Conifer\Site $site the Site object to register functions on
	 */
	public static function add_twig_functions( Site $site ) {
		$wrapper = new static( $site );
		foreach( $wrapper->get_functions() as $name => $closure ) {
			$site->add_twig_function( $name, $closure );
		}
	}

	/**
	 * Must return an array of functions
	 * @return array an associative array of callables, keyed by the function name
	 */
	abstract public function get_functions();
}
