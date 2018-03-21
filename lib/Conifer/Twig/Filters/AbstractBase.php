<?php
/**
 * Abstract base class for declarative, OO Twig functions
 */

namespace Conifer\Twig\Filters;

use Conifer\Site;

/**
 * Easily define custom filters to add to Twig by extending this class.
 * Then just call YourCustomFilterClass::add_twig_filters( $site );
 *
 * @package Conifer
 */
abstract class AbstractBase {
  protected $site;

  /**
   * Constructor
   *
   * @param \Conifer\Site $site the Site object
   */
  public function __construct( Site $site ) {
    $this->site = $site;
  }

  /**
   * Register the Twig filters this class defines in get_filters()
   * on the central Site object
   *
   * @param type \Conifer\Site $site the Site object to register filters on
   */
  public static function add_twig_filters( Site $site ) {
    $wrapper = new static( $site );
    foreach ( $wrapper->get_filters() as $name => $closure ) {
      $site->add_twig_filter( $name, $closure );
    }
  }

  /**
   * Must return an array of filters
   *
   * @return array an associative array of callables, keyed by the filter name
   */
  abstract public function get_filters();
}
