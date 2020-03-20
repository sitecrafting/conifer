<?php
/**
 * Powerful utility trait for adding custom columns in the WP Admin
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Post;

/**
 * This Trait adds a powerful abstraction to post type classes:
 * the ability to declaratively add custom admin columns.
 */
trait HasCustomAdminColumns {
  /**
   * Add a custom column to the admin for the given post type, with content provided
   * through a callback.
   *
   * @param string   $key      the $columns array key to add
   * @param string   $label    label for the column header
   * @param callable $getValue (Optional) a callback to get the value to
   * display in the custom column for. If not given, the column will
   * display the value of the `meta` field whose `meta_key` is equal to `$key`.
   * a given post. Takes a post ID as its sole parameter.
   */
  public static function add_admin_column($key, $label, callable $getValue = null) {
    $postType = static::_post_type();

    if ($postType === 'page' || $postType === 'post') {
      // e.g. manage_pages_columns
      $addHook = "manage_{$postType}s_columns";

      // e.g. manage_pages_custom_column
      $displayHook = "manage_{$postType}s_custom_column";

    } else {
      // e.g. manage_my_post_type_posts_columns
      $addHook = "manage_{$postType}_posts_columns";

      // e.g. manage_my_post_type_posts_custom_column
      $displayHook = "manage_{$postType}_posts_custom_column";
    }

    // Add the column to the admin
    add_filter($addHook, function(array $columns) use ($key, $label) {
      $columns[$key] = $label;
      return $columns;
    });

    // If no callback is given, infer a sensible default from the key.
    $getValue = $getValue ?? static::value_getter($key);

    // register a callback to display the value for this column
    add_action($displayHook, function($column, $id) use ($key, $getValue) {
      if ( $column === $key ) {
        // NOTE: THE USER IS RESPONSIBLE FOR ESCAPING USER INPUT AS NECESSARY
        // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
        echo $getValue( (int) $id);
      }
    }, 10, 2);
  }

  /**
   * Get a function to run based on the meta $key.
   *
   * @param string $key the column key whose value we need to get when rendering
   * a custom column
   * @return callable
   */
  private static function value_getter($key) : callable {
    $keyToGetterMapping = [
      '_wp_page_template' => [static::class, 'page_template_name'],
    ];

    return $keyToGetterMapping[$key] ?? static::post_meta_getter($key);
  }

  /**
   * Basic get_post_meta-like fallback
   *
   * @param string $key the column key
   */
  private static function post_meta_getter($key) {
    return function(int $id) use ($key) {
      $post = new static($id);
      return $post->meta($key);
    };
  }

  /**
   * Get the page template given a post ID
   *
   * @param int $id the post ID
   * @return string the page template name, as declared in the template header comment, or "Default Template"
   */
  private static function page_template_name(int $id) : string {
    // get mapping of Template File => Template Name
    static $templates = null;
    $templates        = $templates ?: array_flip(get_page_templates());

    // get the template file for this page
    $templateFile = get_post_meta($id, '_wp_page_template', true) ?: '';

    // return the template name for this page
    return $templates[$templateFile] ?? 'Default Template';
  }
}
