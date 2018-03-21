<?php
/**
 * Conifer\Post\Page class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Post;

use Timber\Timber;

/**
 * Class to represent WordPress pages.
 *
 * @package Conifer
 */
class Page extends Post {
  const POST_TYPE = 'page';

  public static function post_type() : string {
    return static::POST_TYPE;
  }

  /**
   * Get the top-level title to display from the nav structure, fall back
   * on this Page object's title it it's outside the nav hierarchy.
   * @param \Conifer\Post\Menu $menu the menu to look at to determine the title
   * @return string the title to display
   */
  public function get_title_from_nav_or_post( Menu $menu ) : string {
    return $menu->get_current_top_level_item( $this )->title
      ?? $this->title;
  }

  /**
   * Get the Blog Landing Page.
   * @return \Conifer\Post\Page
   */
  public static function get_blog_page() : Page {
    return new static( get_option('page_for_posts') );
  }

  /**
   * Get a page by its template filename, relative to the theme root.
   * @param string $template
   * @return Page the first page found matching the template
   */
  public static function get_by_template(string $template) : Page {
    // query the Page by template
    $pages = Timber::get_posts([
      'post_type' => 'page',
      'post_status' => 'publish',
      'posts_per_page' => 1,
      'meta_query' => [
        [
          'key' => '_wp_page_template',
          'value' => $template,
        ]
      ]
    ], static::class);

    // return the first page we find, if it exists
    if (isset($pages[0])) {
      return $pages[0];
    }
  }
}


