<?php
/**
 * General-purpose WordPress functions in Twig
 */

namespace Conifer\Twig;

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
   * @return  array an associative array of callback functions, keyed by name
   */
  public function get_functions() : array {
    return [
      'get_search_form' => function() {
        return get_search_form( false );
      },
      'get_blog_url' => [ '\Conifer\Post\Post', 'get_blog_url' ],
      'img_url' => function( $file ) {
        return get_stylesheet_directory_uri() . '/img/' . $file;
      },
      'wp_nav_menu' => function( $args ) {
        ob_start();
        wp_nav_menu( $args );
        return ob_get_clean();
      },
      'get_terms' => function( $taxonomy, $opts = ['hide_empty' => true] ) {
        return Timber::get_terms($taxonomy, $opts);
      },
      'paginate_links' => function( $args = [] ) {
        return paginate_links($args);
      },
      /**
       * Twig function for getting a global WP option
       */
      'get_option' => function($name) {
        return get_option($name);
      },
      /**
       * Like get_option, but applies ACF filters, e.g. if need to return an object. Only works with ACF-configured option fields.
       */
      'get_theme_setting' => function($name) {
        return get_field($name, 'option');
      },
      'get_sidebar_widgets' => function($name) {
        return Timber::get_widgets($name);
      },
      'get_latest_posts' => function(int $count = Post::LATEST_POST_COUNT) : Iterable {
        return BlogPost::latest($count);
      },
    ];
  }

  /**
   * Does not supply any additional Twig filters.
   *
   * @return  array
   */
  public function get_filters() : array {
    return [];
  }
}
