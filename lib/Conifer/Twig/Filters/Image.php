<?php
/**
 * Custom Twig filters for dealing with images
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace Conifer\Twig\Filters;

/**
 * Twig Wrapper around high-level image filters
 *
 * @package Conifer
 */
class Image extends AbstractBase {
  /**
   * Get the Twig functions to register
   *
   * @return  array an associative array of callback functions, keyed by name
   */
  public function get_filters() {
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
