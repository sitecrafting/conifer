<?php
/**
 * Retrieve tags and categories from posts
 */

namespace Conifer\Post;

use Timber\Term;
use Timber\Timber;

/**
 * Trait that can be used from any post type class to get
 * tags and categories.
 *
 * @package Conifer
 */
trait HasTerms {
  /**
   * Get all published posts of this type, grouped by terms of $taxonomy
   *
   * @param string $taxonomy the name of the taxonomy to group by,
   * e.g. `"category"`
   * @param array $terms The list of specific terms to filter by. Each item
   * in the array can be any of the following:
   * * a term ID (int or numeric string)
   * * a term slug (string)
   * * a WP_Term object
   * * a Timber\Term object
   * Defaults to all terms within $taxonomy.
   * @param array $postQueryArgs additional query filters to merge into the
   * array passed to `get_all()`. Defaults to an empty array.
   * @return array an array like:
   * ```php
   * [
   *   [ 'term' => { Category 1 WP_Term object }, 'posts' => [...],
   *   [ 'term' => { Category 2 WP_Term object }, 'posts' => [...],
   * ]
   * ```
   */
  public static function get_all_grouped_by_term(
    string $taxonomy,
    array $terms = [],
    array $postQueryArgs = []
  ) : array {
    // ensure we have a list of taxonomy terms
    $terms = $terms ?: Timber::get_terms([
      'taxonomy'   => $taxonomy,
      'hide_empty' => true,
    ]);

    // convert each term ID/slug/obj to a Timber\Term
    $timberTerms = array_map(function($termIdent) {
      // Pass through already-instantiated Timber\Term objects.
      // This allows for a polymorphic list of terms! ✨
      return is_a($termIdent, Term::class)
        ? $termIdent
        : new Term($termIdent);
    }, $terms);

    // reduce each term in $taxonomy to an array containing:
    //  * the term
    //  * the term's corresponding posts
    return array_reduce($timberTerms, function(
      array $grouped,
      Term $term
    ) use ($taxonomy, $postQueryArgs) : array {
      // Because the count may be different from the denormalized term count,
      // since this may be a special query, we need to check if this term is
      // actually populated/empty.
      $posts = $term->posts(
        $postQueryArgs,
        static::_post_type(),
        static::class
      );
      if ($posts) {
        // Group this term with its respective posts.
        $grouped[] = [
          'term'  => $term,
          'posts' => $posts,
        ];
      }

      // return the grouped posts so far
      return $grouped;
    }, []);
  }

  /**
   * Build up the wp_posts query based on term, taxonomy, and any additional
   * query args specified by the user
   *
   * @param int $termId the term_id to limit posts by
   * @param string $taxonomy the name of the taxonomy to query
   * @param array $extraQueryArgs extra post query args, if any
   * @return array the query params to pass to `get_all()`
   */
  private static function build_term_posts_query(
    int $termId,
    string $taxonomy,
    array $extraQueryArgs
  ) : array {
    // compose a query for all posts for $category
    $query = array_merge($extraQueryArgs, [
      'post_type' => static::_post_type(),
      'tax_query' => [
        [
          'taxonomy' => $taxonomy,
          'terms'    => $termId,
        ],
      ],
    ]);

    // honor additional tax_queries
    $query['tax_query'] = array_merge(
      $query['tax_query'],
      $extraQueryArgs['tax_query'] ?? []
    );

    return $query;
  }
}

