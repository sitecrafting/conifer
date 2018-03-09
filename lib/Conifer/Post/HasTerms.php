<?php
/**
 * Retrieve tags and categories from posts
 */

namespace Conifer\Post;

/**
 * Trait that can be used from any post type class to get
 * tags and categories.
 *
 * @package Conifer
 */
trait HasTerms {
	/**
	 * Get the tags for this Post
	 * @return array an array of TimberTerm objects
	 */
	public function get_tags() {
		return $this->get_terms('tag');
	}

	/**
	 * Get the categories for this Post
	 * @return array an array of TimberTerm objects
	 */
	public function get_categories() {
		return $this->get_terms('category');
	}

  /**
   * Get all published posts of this type, grouped by terms of $taxonomy
   * @param string $taxonomy the name of the taxonomy to group by,
   * e.g. "category"
   * @return an array like:
   * [
   *   [ 'term' => { Category 1 WP_Term object }, 'posts' => [...],
   *   [ 'term' => { Category 2 WP_Term object }, 'posts' => [...],
   * ]
   */
  public static function get_all_grouped_by_term(string $taxonomy) : array {
    return array_reduce(static::get_faculty_categories(), function(
      array $grouped,
      WP_Term $term
    ) use($taxonomy) : array {
      // compose a query for all posts for $category
      $query = [
        'post_type' => static::POST_TYPE,
        'tax_query' => [
          [
            'taxonomy' => $taxonomy,
            'terms'    => $term->term_id,
          ],
        ],
      ];

      // group this category with its respective posts
      $grouped[] = [
        'term' => $term,
        'posts'  => static::get_all($query),
      ];

      // return the grouped posts so far
      return $grouped;
    }, []);
  }
}

