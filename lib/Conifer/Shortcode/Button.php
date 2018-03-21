<?php
/**
 * Custom buttons inside RTEs!
 */

namespace Conifer\Shortcode;

use DOMDocument;
use DOMElement;

/**
 * Implements a custom button shortcode that allows the client to turn any <a> tag in an RTE into a styled button.
 * Only works for content-style shortcodes with start/end tags, e.g. [button]<a href="...">Click Here</a>[/button]
 *
 * @copyright 2015 SiteCrafting, Inc.
 * @author Coby Tamayo
 * @package  Groot
 */
class Button extends AbstractBase {
  const BUTTON_CLASS = 'btn';

  /**
   * Get the HTML for rendering the button link
   *
   * @param  array  $atts key/value attribute pairs specified by the shortcode. Button ignores these.
   * @param  string $html the raw markup between the start/end shortcode tags.
   * @return string the modified <a> tag HTML
   */
  public function render( $atts = [], $html = '' ) {
    if ( $html ) {
      $dom = new DOMDocument();
      $dom->loadHTML($html);

      if ($link = $dom->getElementsByTagName('a')->item(0)) {
        $link->setAttribute('class', static::BUTTON_CLASS);
        $html = $dom->saveHTML();
      }
    }

    return $html;
  }
}
