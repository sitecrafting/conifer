<?php

/**
 * Declarative-style WP shortcodes
 */

declare(strict_types=1);

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
     * @param string $tag The tag to be used to write the actual shortcode
     */
    public static function register(string $tag ): void {
        add_shortcode( $tag, function ($args = [], string $html = '' ): string {
            $shortcode = new static();
            // coerce args to an array
            return $shortcode->render(!empty($args) ? $args : [], $html);
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
    ): string;
}
