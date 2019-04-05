<?php
/**
 * Declarative-style WP shortcodes
 */

namespace Conifer\Shortcode;

/**
 * Easily add shortcodes by calling register() on a class that implements this
 * abstract class
 *
 * @package Conifer
 */
abstract class AbstractBase {
  /**
   * Register a shortcode with the given "tag".
   * Tells WP to call render() to render the shortcode content.
   *
   * @param  string $tag The tag to be used to write the actual shortcode
   */
  public static function register( $tag ) {
    add_shortcode( $tag, function($args = [], string $html = '') {
      $shortcode = new static();
      // coerce args to an array
      return $shortcode->render($args ?: [], $html);
    });
  }

  /**
   * Output the result of this shortcode
   *
   * @param array $atts the standard WP shortcode attributes
   */
  abstract public function render(
    array $atts = [],
    string $content = ''
  ) : string;
}


