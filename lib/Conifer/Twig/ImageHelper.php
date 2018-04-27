<?php
/**
 * Custom Twig functions for dealing with images
 */

namespace Conifer\Twig;

use Timber;
use TimberImage;

/**
 * Twig Wrapper around high-level image functions
 *
 * @package Conifer
 */
class ImageHelper implements HelperInterface {
  /**
   * Does not supply any additional Twig functions.
   *
   * @return  array an associative array of callback functions, keyed by name
   */
  public function get_functions() : array {
    return [];
  }

  /**
   * Get the Twig functions to register
   *
   * @return  array an associative array of callback functions, keyed by name
   */
  public function get_filters() : array {
    return [
      'src_to_retina' => [$this, 'src_to_retina'],
    ];
  }

  /**
   * Convert the image URL `$src` to its retina equivalent
   *
   * @param `$src` the original src URL
   * @return string the retina version of `$src`
   */
  public function src_to_retina(string $src) : string {
    // greedily find the last dot
    return preg_replace('~(\.[a-z]+)$~i', '@2x$1', $src);
  }
}
