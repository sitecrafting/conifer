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
   * array passed to `Timber::get_posts()`. Defaults to an empty array.
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
   * Register a taxonomy for this post type
   *
   * @example
   * ```php
   * Post::register_taxonomy('sign', [
   *   'plural_label' => 'Signs',
   *   'labels' => [
   *     'add_new_item' => 'Divine New Sign'
   *   ]
   * ]);
   *
   * // equivalent to:
   * register_taxonomy('sign', 'person', [
   *   'labels' => [
   *     'name'          => 'Signs',
   *     'singular_name' => 'Sign', // inferred from taxonomy name
   *     'add_new_item'  => 'Divine New Sign', // overridden directly w/ labels.add_new_item
   *     'menu_naem'     => 'View Signs' // inferred from plural_label
   *     // ... other singular/plural labels are inferred in the same way
   *   ]
   * ]);
   * ```
   * @param string $name the name of the taxonomy. Must be all lower-case, with
   * no spaces.
   * @param array $options any valid array of options to `register_taxonomy()`,
   * plus an optional "plural_label" index. It produces a more comprehensive
   * array of labels before passing it to `register_taxonomy()`.
   * @param bool $omitPostType whether to omit the post type in the declaration.
   * If true, passes `null` as the `$object_type` argument to
   * `register_taxonomy()`, which is useful for declaring taxonomies across
   * post types. Defaults to `false`.
   * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
   */
  public static function register_taxonomy(
    string $name,
    array $options = [],
    bool $omitPostType = false
  ) {
    $options['labels'] = $options['labels'] ?? [];

    // For singular label, fallback on taxonomy name
    $singular = $options['labels']['singular_name']
      // convert underscore_inflection to Words Separated By Spaces
      // TODO separate this into a utility method
      ?? implode(' ', array_map(function(string $word) {
        return ucfirst($word);
      }, explode('_', $name)));

    // Unless there's an explicity plural_label, follow the same default logic
    // as register_post_type()
    $plural = $options['plural_label']
      ?? $options['label']
      ?? $options['labels']['name']
      ?? $singular . 's'; // pluralize singular naively

    // this isn't meaningful to WP, just remove it
    unset($options['plural_label']);

    $options['labels']['name'] = $options['labels']['name'] ?? $plural;

    // omit $object_type option in taxonomy declaration?
    $postType = $omitPostType ? null : self::_post_type();

    $options['labels']['singular_name'] = $singular;

    $options['labels']['menu_name'] = $options['labels']['menu_name']
      ?? $plural;

    $options['labels']['all_items'] = $options['labels']['all_items']
      ?? "All {$plural}";

    $options['labels']['edit_item'] = $options['labels']['edit_item']
      ?? "Edit {$singular}";

    $options['labels']['view_item'] = $options['labels']['view_item']
      ?? "View {$singular}";

    $options['labels']['update_item'] = $options['labels']['update_item']
      ?? "Update {$singular}";

    $options['labels']['add_new_item'] = $options['labels']['add_new_item']
      ?? "Add New {$singular}";

    $options['labels']['new_item_name'] = $options['labels']['new_item_name']
      ?? "New {$singular} Name";

    $options['labels']['parent_item'] = $options['labels']['parent_item']
      ?? "Parent {$singular}";

    $options['labels']['parent_item_colon'] = $options['labels']['parent_item_colon']
      ?? "Parent {$singular}:";

    $options['labels']['search_items'] = $options['labels']['search_items']
      ?? "Search {$plural}";

    $options['labels']['popular_items'] = $options['labels']['r_items']
      ?? "Popular {$plural}";

    $options['labels']['separate_items_with_commas'] = $options['labels']['separate_items_with_commas']
      ?? "Separate {$plural} with commas";

    $options['labels']['add_or_remove_items'] = $options['labels']['add_or_remove_items']
      ?? "Add or remove {$plural}";

    $options['labels']['choose_from_most_used'] = $options['labels']['choose_from_most_used']
      ?? "Choose from the most used {$plural}";

    $options['labels']['not_found'] = $options['labels']['not_found']
      ?? "No {$plural} found";

    $options['labels']['back_to_items'] = $options['labels']['back_to_items']
      ?? "← Back to {$plural}";

    // Honor custom statuses in term counts
    if (is_array($options['statuses_toward_count'] ?? null)) {
      $statuses = $options['statuses_toward_count'];

      // Include "publish" status in query?
      $includePublished = $statuses['publish'] ?? null;
      if ($includePublished !== false) {
        // user explicitly remove publish from counted statuses
        $statuses = array_unique(array_merge(['publish'], $statuses));
      }
      unset($statuses['publish']);

      $options['update_count_callback'] = function($terms) use ($statuses) {
        foreach ($terms as $term) {
          static::count_statuses_toward_term_count(new Term($term), $statuses);
        }
      };
    }

    register_taxonomy($name, $postType, $options);
  }

  /**
   * Keep term counts up to date, taking into account posts in $status
   *
   * @param Timber\Term the Term instance whose count we want to update
   * @param string $taxonomy the taxonomy whose terms we want to affect
   */
  public static function count_statuses_toward_term_count(Term $term, array $statuses) {
    global $wpdb;

    // Get all posts in $statuses, plus all published posts
    $inStatus = $term->posts([
      'post_status' => $statuses,
      'post_type'   => static::POST_TYPE,
      'numberposts' => -1,
    ]);

    if (is_array($inStatus)) {
      // increment count by the number of term posts in $statuses
      $wpdb->update(
        $wpdb->term_taxonomy,
        ['count' => count($inStatus)],
        ['term_taxonomy_id' => $term->term_taxonomy_id]
      );
    }
  }
}

