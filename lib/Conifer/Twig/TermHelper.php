<?php
/**
 * Twig filters for WP terms
 */

namespace Conifer\Twig;

use Timber\Term;
use Timber\CoreInterface as TimberCoreInterface;

/**
 * Twig Wrapper around filters for WP/Timber terms and taxonomies
 *
 * @package Conifer
 */
class TermHelper implements HelperInterface {
  /**
   * Get the Twig functions to register
   *
   * @return  array an associative array of callback functions, keyed by name
   */
  public function get_filters() : array {
    return [
      'term_item_class' => [$this, 'term_item_class'],
    ];
  }

  /**
   * Does not supply any additional Twig functions.
   *
   * @return  array
   */
  public function get_functions() : array {
    return [];
  }

  /**
   * Filters the given term into a class for an <li>; considers $term "current" if
   * $currentPostOrArchive is a TimberTerm instance (meaning we're on an archive page
   * for that term), and it represents the same term as $term.
   *
   * @param  TimberTerm $term the term for the <li> currently being rendered
   * @param  TimberCoreInterface $currentPostOrArchive the post or term representing
   * the current archive page (e.g. a category listing)
   * @return string the formatted phone number
   */
  public function term_item_class( Term $term, TimberCoreInterface $currentPostOrArchive ) {
    // If $postOrArchive is a TimberTerm instance, we're on an archive page for that term.
    // In that case, compare it to $term to get the class(es) for the <li> being rendered
    $currentTermItem = ($currentPostOrArchive instanceof Term)
      && $term->ID === $currentPostOrArchive->ID;

    return $currentTermItem
      ? 'current-menu-item'
      : '';
  }
}
