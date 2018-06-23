<?php
/**
 * Powerful utility trait for adding custom filters in the WP Admin
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Post;

use WP_Query;
use Timber\Timber;

/**
 * Declaratively add custom admin filter options.
 */
trait HasCustomAdminFilters {
  /**
   * Add a custom filter to the admin for the given post type, with custom
   * query behavior provided through a callback.
   *
   * @param string $name the form input name for the filter
   * @param array $options the options to display in the filter dropdown
   * @param callable $queryModifier a callback to mutate the WP_Query object
   * at query time.
   *
   * Callback params:
   *
   * * `WP_Query` `$query` the query being executed
   * * `string` `$value` the filter value selected by the admin user
   *
   * The `$queryModifier` param is optional for cases such as querying by
   * taxonomy term, in which case WP adds the term to the query automatically.
   */
  public static function add_admin_filter(
    string $name,
    array $options,
    callable $queryModifier = null
  ) {
    // safelist $name as a query_var
    add_filter('query_vars', function(array $vars) use($name) {
      return array_merge($vars, [$name]);
    });

    add_action('restrict_manage_posts', function() use ($name, $options) {

      // we only want to render the filter menu if we're on the
      // edit screen for the given post type
      if ( static::allow_custom_filtering() ) {

        static::render_custom_filter_select([
          'name'           => $name,
          'options'        => $options,
          'filtered_value' => get_query_var($name),
        ]);
      }
    });

    // in some cases, such as querying by custom taxonomies that already
    // appear in post queries automatically, we don't need to hook into
    // `pre_get_posts` at all, and as such don't need a custom $queryModifier
    if (is_callable($queryModifier)) {
      add_action('pre_get_posts', function(WP_Query $query) use (
        $name,
        $queryModifier
      ) {
        if (static::querying_by_custom_filter($name, $query, $queryModifier)) {
          // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
          $queryModifier($query, get_query_var($name));
        }
      });
    }
  }

  /**
   * Render the <select> element for an arbitrary custom admin filter.
   * Override this to customize the dropdown further.
   *
   * @param array $data the view data
   */
  protected static function render_custom_filter_select( array $data ) {
    Timber::render( 'admin/custom-filter-select.twig', $data );
  }

  /**
   * Whether to show the custom filter on the edit screen for the given post type.
   */
  protected static function allow_custom_filtering() : bool {
    return ($GLOBALS['post_type'] ?? null) === static::_post_type()
      && ($GLOBALS['pagenow'] ?? null) === 'edit.php';
  }

  /**
   * Whether the user is currently trying to query by the custom filter,
   * according to GET params and the current post type; determines whether
   * the current WP_Query needs to be modified.
   *
   * @param string $name the filter name, i.e. the <select> element's
   * `name` attribute
   * @param WP_Query $query the current WP_Query object
   */
  protected static function querying_by_custom_filter(
    string $name,
    WP_Query $query
  ) : bool {
    return static::allow_custom_filtering()
      && ($query->query_vars['post_type'] ?? null) === static::_post_type()
      // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
      && !empty(get_query_var($name));
  }
}
