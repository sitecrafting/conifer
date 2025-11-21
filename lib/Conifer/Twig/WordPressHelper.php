<?php

/**
 * General-purpose WordPress functions in Twig
 */

declare(strict_types=1);

namespace Conifer\Twig;

use Closure;
use Conifer\Post\BlogPost;
use Conifer\Post\Post;

use Timber\Timber;

/**
 * Twig Wrapper around generic or global functions, such as WordPress
 * template tags.
 *
 * @package Conifer
 */
class WordPressHelper implements HelperInterface {
    /**
     * Get the Twig functions to register
     *
     * @return array<string, Closure> an associative array of callback functions, keyed by name
     */
    public function get_functions(): array {
        return [
        'get_search_form' => fn() => get_search_form( false ),
        'get_blog_url' => Post::get_blog_url(...),
        'img_url' => fn($file ): string => get_stylesheet_directory_uri() . '/img/' . $file,
        'wp_nav_menu' => function ( $args ): string|false {
            ob_start();
            wp_nav_menu( $args );
            return ob_get_clean();
        },
        'paginate_links' => fn($args = [] ) => paginate_links($args),
        /**
         * Twig function for getting a global WP option
         */
        'get_option' => fn($name ) => get_option($name),
        /**
         * Like get_option, but applies ACF filters, e.g. if need to return an object. Only works with ACF-configured option fields.
         */
        'get_theme_setting' => function ($name ) {

            if (function_exists('get_field')) {
                return get_field($name, 'option');
            } else {
                return '';
            }
        },
        'get_sidebar_widgets' => fn($name ) => Timber::get_widgets($name),
        'get_latest_posts' => BlogPost::latest(...),
        ];
    }

    /**
     * Does not supply any additional Twig filters.
     *
     * @return array{}
     */
    public function get_filters(): array {
        return [];
    }
}
