<?php

declare(strict_types=1);

/**
 * Custom Twig functions for dealing with images
 */
namespace Conifer\Twig;

use Closure;
use Timber\ImageHelper as TimberImageHelper;

/**
 * Twig Wrapper around high-level image functions
 *
 * @package Conifer
 */
class ImageHelper implements HelperInterface {
  /**
   * Does not supply any additional Twig functions.
   *
   * @return Closure[] an associative array of callback functions, keyed by name
   */
  public function get_functions() : array {
    return [
      'generate_retina_srcset' => $this->generate_retina_srcset(...),
    ];
  }

  /**
   * Get the Twig functions to register
   *
   * @return array<string, Closure> an associative array of callback functions, keyed by name
   */
  public function get_filters() : array {
    return [
      'src_to_retina' => $this->src_to_retina(...),
      'src_to_retina_at_multiplier' => $this->src_to_retina_at_multiplier(...),
    ];
  }

    /**
     * Convert the image URL `$src` to its retina equivalent
     *
     * @param string $src the original src URL
     * @return string the retina version of `$src`
     */
  public function src_to_retina(string $src) : string {
    // greedily find the last dot
    return preg_replace('~(\.[a-z]+)$~i', '@2x$1', $src);
  }

  /**
   * Convert the image URL `$src` to its retina equivalent at given multiplier.
   * Makes sure the file exists.
   *
   * @param ?string $src the original src URL
   * @param int $multiplier the multiplier for the retina image
   * @return string the retina src
   */
  public function src_to_retina_at_multiplier(?string $src, int $multiplier = 2) : string {

    if (!$src) {
      return '';
    }

    // get the path of the original file
    $file = TimberImageHelper::get_server_location($src);

    // bail if the file doesnt exist. No point continuing on.
    if (!file_exists($file)) {
      return '';
    }

    // return base $src if multiplier less than 2
    if ($multiplier < 2) {
      return $src;
    }

    $image = preg_replace('~(\.[a-z]+)$~i', sprintf('@%dx$1', $multiplier), $src);

    $file = TimberImageHelper::get_server_location($image);

    // return image src if file exists
    if (file_exists($file)) {
      return $image;
    }

    return '';

  }

  /**
   * Convert the image URL `$src` srcset string up to given size multiplier.
   * Will return srcset with files that exist.
   *
   * @param ?string $src the original src URL
   *  @param int $max_multiplier the max multiplier for the set
   * @return string the retina srcset
   */
  public function generate_retina_srcset(?string $src, int $max_multiplier = 2) : string {

    if (!$src) {
      return '';
    }

    $set = [];

    // bail if $max_multiplier < 2. We don't need srcset.
    if ($max_multiplier < 2) {
      return '';
    }

    // get the path of the original file
    $file = TimberImageHelper::get_server_location($src);

    // bail if the file doesnt exist. No point continuing on.
    if (!file_exists($file)) {
      return '';
    }

    // add to the set
    $set[] = $src;

    // add additional retna image sizes if they exist
    $count = 2;
    do {
      $image = preg_replace('~(\.[a-z]+)$~i', '@' . $count . 'x$1', $src);
      $file  = TimberImageHelper::get_server_location($image);

      if (file_exists($file)) {
          $set[] = $image . sprintf(' %dx', $count);
      }

      $count++;
    } while ($count < $max_multiplier);

    // return srcset or empty string if retina images don't exist
    return count($set)>1 ? 'srcset="' . implode(', ', $set) . '"': '';

  }
}
