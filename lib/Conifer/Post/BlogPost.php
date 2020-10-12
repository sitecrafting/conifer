<?php
/**
 * BlogPost class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Post;

use DateTime;
use Timber\Timber;

/**
 * Class for encapsulating WP posts of type "post"
 */
class BlogPost extends Post {
  const POST_TYPE = 'post';

  const NUM_RELATED_POSTS = 10;

  /**
   * Get all months for which a published blog post exists
   *
   * @return array an array of formatted month strings
   */
  public static function get_all_published_months() : array {
    global $wpdb;

    $sql = <<<_SQL_
SELECT DISTINCT DATE_FORMAT(post_date, '%Y') AS y,
DATE_FORMAT(post_date, '%m') AS m,
DATE_FORMAT(post_date, '%Y-%m') AS formatted_month,
DATE_FORMAT(post_date, '%M %Y') AS pretty_month
FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'
ORDER BY post_date DESC
_SQL_;

    // phpcs:ignore WordPress.WP.PreparedSQL.NotPrepared
    return $wpdb->get_results( $sql, ARRAY_A );
  }

  /**
   * Get all years for which a published blog post exists
   *
   * @return array an array of formatted year strings
   */
  public static function get_all_published_years() : array {
    global $wpdb;

    $sql = <<<_SQL_
SELECT DISTINCT YEAR(post_date)
FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'
ORDER BY post_date DESC
_SQL_;

    // phpcs:ignore WordPress.WP.PreparedSQL.NotPrepared
    return $wpdb->get_col( $sql, ARRAY_A );
  }

  /**
   * Get related posts of the same type
   *
   * @param  int $numPosts the number of posts to fetch.
   * @return array         an array of Conifer\Post\Post objects
   */
  public function get_related( $numPosts = self::NUM_RELATED_POSTS ) {
    if (!isset($this->related_posts)) {
      // Get term_ids to query by
      $categoryIds = array_map(function($cat) {
        return $cat->id;
      }, $this->categories());

      $this->related_posts = Timber::get_posts([
        // posts of this same type only
        'post_type' => $this->post_type,
        // limit number of posts
        'numberposts' => $numPosts,
        // exclude this post
        'post__not_in' => [$this->ID],
        // query by shared categories
        'tax_query' => [
          [
            'taxonomy' => 'category',
            'terms' => $categoryIds,
          ],
        ],
      ]);
    }

    return $this->related_posts;
  }
}
