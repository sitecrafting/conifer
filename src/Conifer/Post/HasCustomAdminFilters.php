<?php
/**
 * Powerful utility trait for adding custom filters in the WP Admin
 */

namespace Conifer\Post;

use Timber\Timber;

/**
 * Declaratively add custom admin filter options.
 *
 * @package Conifer
 */
trait HasCustomAdminFilters {
	/**
	 * Add a custom column to the admin for the given post type, with content provided
	 * through a callback.
	 * @param string   $key      the $columns array key to add
	 * @param string   $label    label for the column header
	 * @param string   $postType the post type, for inferring which filters/actions to hook into
	 * @param callable $getValue a callback to get the value to display in the custom column for
	 * a given post. Takes a post ID as its sole parameter.
	 */
	public static function add_admin_filter( $name, $options, $postType, callable $queryModifier ) {
		add_action('restrict_manage_posts', function() use($name, $options, $postType) {

			// only want to render the filter menu if we're on the edit screen for the given post type
			if ( static::allow_custom_filtering($postType) ) {

				// default to blank string, which should mean "any"
				$value = isset( $_GET[$name] )
					? $_GET[$name]
					: '';

				static::render_custom_filter_select([
					'name' => $name,
					'options' => $options,
					'filtered_value' => $value,
				]);
			}
		});

		add_action('pre_get_posts', function(\WP_Query $query) use($name, $postType, $queryModifier) {
			if( static::querying_by_custom_filter($name, $postType, $query) ) {
				$queryModifier($query, $_GET[$name]);
			}
		});
	}

	/**
	 * Render the <select> element for an arbitrary custom admin filter.
	 * Override this to customize the dropdown further.
	 * @param array $data the view data
	 */
	protected static function render_custom_filter_select( array $data ) {
		Timber::render( 'admin/custom-filter-select.twig', $data );
	}

	/**
	 * Whether to show the custom filter on the edit screen for the given post type.
	 * @param string $postType the post type to which the custom filter applies.
	 */
	protected static function allow_custom_filtering( $postType ) {
		return $GLOBALS['post_type'] === $postType && $GLOBALS['pagenow'] === 'edit.php';
	}

	/**
	 * Whether the user is currently trying to query by the custom filter, according to GET params
	 * and the current post type; determines whether the current WP_Query needs to be modified.
	 * @param string $name the filter name, i.e. the <input> element's "name" attribute
	 * @param string $postType the post type to which the custom filter applies
	 * @param WP_Query $query the current WP_Query object
	 */
	protected static function querying_by_custom_filter( $name, $postType, \WP_Query $query ) {
		return static::allow_custom_filtering( $postType )
			and $query->query_vars['post_type'] === $postType
			and !empty( $_GET[$name] );
	}
}
