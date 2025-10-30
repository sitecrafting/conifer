<?php

declare(strict_types=1);

/**
 * Conifer\Post\Page class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */
namespace Conifer\Post;

use Timber\Post as TimberPost;
use Timber\Timber;

use Conifer\Navigation\Menu;

/**
 * Class to represent WordPress pages.
 *
 * @package Conifer
 */
class Page extends Post {
  const POST_TYPE = 'page';

  /**
   * Get the top-level title to display from the nav structure, fall back
   * on this Page object's title it it's outside the nav hierarchy.
   *
   * @param \Conifer\Navigation\Menu $menu the menu to look at to determine the title
   * @return string the title to display
   */
  public function get_title_from_nav_or_post( Menu $menu ) : string {
    return $menu->get_current_top_level_item()->title ?? $this->title();
  }

  /**
   * Get the Blog Landing Page.
   *
   * @return \Conifer\Post\Page
   */
  public static function get_blog_page() : TimberPost {
    return Timber::get_post( get_option('page_for_posts') );
  }

  /**
   * Get a page by its template filename, relative to the theme root.
   *
   * @param string $template
   * @param array extra query params to be merged in with the posts query
   * to be performed.
   * @return TimberPost the first page found matching the template, or null if no such page exists
   */
  public static function get_by_template(string $template, array $query = []) {
    return Timber::get_post(array_merge($query, [
      'post_type'  => 'page',
      'meta_query' => [
        [
          'key'    => '_wp_page_template',
          'value'  => $template,
        ],
      ],
    ]));
  }
}
