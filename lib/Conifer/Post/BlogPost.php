<?php

namespace Conifer\Post;

use DateTime;

class BlogPost extends Post {
  const POST_TYPE = 'post';

  public static function post_type() : string {
    return static::POST_TYPE;
  }

  public static function build_query_params(array $request) {
    // This is a blog-specific route, so we only care about blog posts here
    $query = [
      'post_type' => static::POST_TYPE,
      'tax_query' => [],
    ];

    // let AJAX request specify the specific offset for lazy-loading pagination
    if (isset($request['offset'])) {
      $query['offset'] = intval($request['offset']);
    }

    // filter by category slug
    if (isset($request['category'])) {
      $query['tax_query'][] = [
        'taxonomy' => 'category',
        'field' => 'slug',
        'terms' => [$request['category']],
      ];
    }

    // filter by month/year (YYYY-MM) - all other formats are ignored
    if (isset($request['month']) && $date = DateTime::createFromFormat('Y-m', $request['month'])) {
      $query['year']     = $date->format('Y');
      $query['monthnum'] = $date->format('m');
    } elseif (isset($request['year']) && $date = DateTime::createFromFormat('Y', $request['year'])) {
      $query['year'] = $date->format('Y');
    }

    return $query;
  }

  public static function get_all_published_months() {
    global $wpdb;

    $sql = <<<_SQL_
SELECT DISTINCT DATE_FORMAT(post_date, '%Y') AS y,
DATE_FORMAT(post_date, '%m') AS m,
DATE_FORMAT(post_date, '%Y-%m') AS formatted_month,
DATE_FORMAT(post_date, '%M %Y') AS pretty_month
FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'
ORDER BY post_date DESC
_SQL_;

    return $wpdb->get_results( $sql, ARRAY_A );
  }

  public static function get_all_published_years() {
    global $wpdb;

    $sql = <<<_SQL_
SELECT DISTINCT YEAR(post_date)
FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'
ORDER BY post_date DESC
_SQL_;

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
      }, $this->get_categories());

      $this->related_posts = static::get_all([
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
