<?php
/**
 * Interface for declarative, OO Twig functions and filters
 */

namespace Conifer\Twig;

/**
 * Easily define custom functions to add to Twig by extending this class.
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */
interface HelperInterface {
  /**
   * Get the Twig functions implemented by this helper, keyed by the function
   * name to call from Twig views
   *
   * @return array
   */
  public function get_functions() : array;

  /**
   * Get the Twig filters implemented by this helper, keyed by the filter
   * name to call from Twig views
   *
   * @return array
   */
  public function get_filters() : array;
}
