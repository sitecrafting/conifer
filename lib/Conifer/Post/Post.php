<?php
/**
 * High-level WP Post behavior
 */

namespace Conifer\Post;

use Timber\Helper;
use Timber\Post as TimberPost;
use Timber\Term;
use Timber\Timber;

use Conifer\Post\Image;

/**
 * High-level behavior for WP Posts, on top of TimberPost class
 */
abstract class Post extends TimberPost {
  use HasTerms;
  use HasCustomAdminColumns;
  use HasCustomAdminFilters;
  use SupportsAdvancedSearch;

  const POST_TYPE = '';

  const RELATED_POST_COUNT = 3;

  const LATEST_POST_COUNT = 3;

  /**
   * When instantiating TimberImages, create instances of this class
   *
   * @var string
   * @codingStandardsIgnoreStart
   */
  public $ImageClass = '\Conifer\Post\Image';
  /* @codingStandardsIgnoreEnd non-standard var case, needed by Timber */

  /**
   * The default blog landing page URL
   *
   * @var string
   */
  protected static $blog_url;

  /**
   * The collection of related posts, via arbitrary taxonomies
   *
   * @var array
   */
  protected $related_by = [];

  /**
   * Related post counts, via arbitrary taxonomies
   *
   * @var array
   */
  protected $related_post_counts = [];

  /**
   * Register this post type given the high-level label options.
   *
   * @example
   * ```php
   * Post::register_type('person', [
   *   'plural_label' => 'People',
   *   'labels' => [
   *     'add_new_item' => 'Onboard New Person'
   *   ],
   * ]);
   *
   * // equivalent to:
   * register_post_type('person', [
   *   'label' => 'Person', // inferred from post_type,
   *   'labels' => [
   *     'singular_name' => 'Person', // inferred from post_type
   *     'add_new_item' => 'Onboard New Person', // overridden directly w/ labels.add_new_item
   *     'view_items' => 'View People', // inferred from plural_label
   *     // ... other singular/plural labels are inferred in the same way
   *   ],
   * ]);
   * ```
   * @param array $options any valid array of options to `register_post_type()`,
   * plus an optional "plural_label" index. It produces a more comprehensive
   * array of labels before passing it to `register_post_type()`.
   */
  public static function register_type() {
    $options = static::type_options();

    $options['labels'] = $options['labels'] ?? [];

    // For singular label, fallback on post type
    $singular = $options['labels']['singular_name']
      // convert underscore_inflection to Words Separated By Spaces
      // TODO separate this into a utility method
      ?? implode(' ', array_map(function(string $word) {
        return ucfirst($word);
      }, explode('_', static::_post_type())));

    // Unless there's an explicity plural_label, follow the same default logic
    // as register_post_type()
    $plural = $options['plural_label']
      ?? $options['label']
      ?? $options['labels']['name']
      ?? $singular . 's'; // pluralize singular naively

    // this isn't meaningful to WP, just remove it
    unset($options['plural_label']);

    $options['labels']['name'] = $options['labels']['name'] ?? $plural;

    $options['labels']['singular_name'] = $singular;

    $options['labels']['add_new_item'] = $options['labels']['add_new_item']
      ?? "Add New $singular";

    $options['labels']['edit_item'] = $options['labels']['edit_item']
      ?? "Edit $singular";

    $options['labels']['new_item'] = $options['labels']['new_item']
      ?? "New $singular";

    $options['labels']['view_item'] = $options['labels']['view_item']
      ?? "View $singular";

    $options['labels']['view_items'] = $options['labels']['view_items']
      ?? "View $plural";

    $options['labels']['search_items'] = $options['labels']['search_items']
      ?? "Search $plural";

    $options['labels']['not_found'] = $options['labels']['not_found']
      ?? "No $plural found";

    $options['labels']['not_found_in_trash'] = $options['labels']['not_found_in_trash']
      ?? "No $plural found in trash";

    $options['labels']['all_items'] = $options['labels']['all_items']
      ?? "All $plural";

    $options['labels']['archives'] = $options['labels']['archives']
      ?? "$singular Archives";

    $options['labels']['attributes'] = $options['labels']['attributes']
      ?? "$singular Attributes";

    $options['labels']['insert_into_item'] = $options['labels']['insert_into_item']
      ?? "Insert into $singular";

    $options['labels']['uploaded_to_this_item'] = $options['labels']['uploaded_to_this_item']
      ?? "Uploaded to this $singular";

    register_post_type(static::_post_type(), $options);
  }

  /**
   * Default implementation of custom post type labels,
   * for use in register_post_type().
   *
   * @return array
   */
  public static function type_options() : array {
    return [];
  }

  /**
   * Child classes must declare their own post types
   *
   * @throws \RuntimeException if the POST_TYPE class constant is empty
   * @return string
   * @codingStandardsIgnoreStart PSR2.Methods.MethodDeclaration.Underscore
   */
  protected static function _post_type() : string {
    // @codingStandardsIgnoreEnd
    if (empty(static::POST_TYPE)) {
      throw new \RuntimeException(
        'For some static methods to work correctly, you must define the '
        . static::class . '::POST_TYPE constant'
      );
    }

    return static::POST_TYPE;
  }

  /**
   * Get the latest posts
   *
   * @return
   */
  public static function latest(int $count = self::LATEST_POST_COUNT) : iterable {
    return Timber::get_posts([
      'numberposts' => $count,
    ]);
  }



  /*
   * Instance methods
   */


  /**
   * Place tighter restrictions on post types than Timber,
   * forcing all concrete subclasses to implement this method.
   */
  public function type() : string {
    return static::_post_type();
  }

  /**
   * Get all the posts matching the given query
   * (defaults to the current/global WP query constraints)
   *
   * @param  array|string $query any valid Timber query
   * @return array         an array of all matching post objects
   */
  public static function get_all(array $query = []) : iterable {
    // phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
    // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
    trigger_error( '[ Conifer ] Post::get_all() is deprecated in Conifer 1.0.0. Use Timber::get_posts() with Class Maps instead. https://timber.github.io/docs/v2/guides/class-maps' );

    $class = static::class;

    // Avoid instantiating this (abstract) class, causing a Fatal Error.
    // TODO figure out a more elegant way to do this??
    // Might have to rework this at the Timber level
    // @see https://github.com/timber/timber/pull/1218
    if ($class === self::class) {
      $class = TimberPost::class;
    } else {
      // we're NOT just defaulting to blog post, so get the post type to query
      $query['post_type'] = static::_post_type();
    }

    return Timber::get_posts($query, $class) ?: [];
  }

  /**
   * Get the URL of the blog landing page
   * (what WP calls the "post archive" page)
   *
   * @return string the URL
   */
  public static function get_blog_url() {
    if ( ! static::$blog_url ) {
      // haven't fetched the URL yet...go get it
      $page = Page::get_blog_page();

      // cache it
      static::$blog_url = $page->link();
    }

    return static::$blog_url;
  }

  /**
   * Check whether a post by the given ID exists
   *
   * @param  int $id the post ID to check for
   * @return boolean     true if the post exists, false otherwise
   */
  public static function exists( $id ) {
    $post = get_post($id);

    // support calling Post::exists() directly (not on subclasses)
    if (static::class === self::class) {
      return !empty($post);
    }

    return $post && $post->post_type === static::_post_type();
  }

  /**
   * Create a new post from an array of data
   *
   * @param array $data key/value pairs to populate the post and post meta
   * tables. The following keys are special, and their corresponding values
   * will end up in the wp_posts table:
   *
   *  * post_author
   *  * post_date
   *  * post_date_gmt
   *  * post_content
   *  * post_content_filtered
   *  * post_title
   *  * post_excerpt
   *  * post_status
   *  * comment_status
   *  * ping_status
   *  * post_password
   *  * post_name
   *  * to_ping
   *  * pinged
   *  * post_modified
   *  * post_modified_gmt
   *  * post_parent
   *  * menu_order
   *  * post_mime_type
   *  * guid
   *  * post_category
   *  * tags_input
   *  * tax_input
   *
   * The keys "ID" and "post_type" are blacklisted and will be ignored.
   * The value for "ID" is generated on post creation by WordPress/MySQL;
   * The value for "post_type" will always come from $this->get_post_type().
   *
   * All others key/value pairs are considered metadata and end up in wp_postmeta.
   * @return \Project\Post
   */
  public static function create(array $data) : Post {
    // blacklist ID and post_type; we get these automagically
    unset($data['ID']);
    unset($data['post_type']);

    $postFields = [
      'post_author',
      'post_date',
      'post_date_gmt',
      'post_content',
      'post_content_filtered',
      'post_title',
      'post_excerpt',
      'post_status',
      'comment_status',
      'ping_status',
      'post_password',
      'post_name',
      'to_ping',
      'pinged',
      'post_modified',
      'post_modified_gmt',
      'post_parent',
      'menu_order',
      'post_mime_type',
      'guid',
      'post_category',
      'tags_input',
      'tax_input',
    ];

    // compute the data to go in the wp_posts table
    $postData = array_intersect_key(
      $data,
      array_flip($postFields)
    );

    // $data becomes post meta data
    foreach ($postData as $key => $value) {
      unset($data[$key]);
    }

    // merge the metadata and post type in with any post "proper" data
    $id = wp_insert_post(array_merge($postData, [
      'post_type' => static::_post_type(),
      'meta_input' => $data,
    ]));

    if (is_wp_error($id)) {
      return $id;
    }

    // return a new instance of the called class
    return Timber::get_post($id);
  }

  /**
   * Get related Posts of the same post type, who share terms in $taxonomy with
   * this Post.
   *
   * @param string $taxonomy the taxonomy to associate with, e.g. "category"
   * @param int $postCount Optional. The number of posts to get. Defaults to 3.
   * @return Post[] an array of Post objects
   */
  public function get_related_by_taxonomy(
    string $taxonomy,
    int $postCount = self::RELATED_POST_COUNT
  ) : iterable {
    // Get any previously queried related posts
    $relatedPosts     = $this->related_by[$taxonomy] ?? [];
    $relatedPostCount = $this->related_post_counts[$taxonomy] ?? null;

    if (count($relatedPosts) < $postCount && !isset($relatedPostCount)) {
      // There may be more related posts than previously queried; look for them
      $termIds = array_map(function(Term $term) {
        return $term->id;
      }, $this->terms($taxonomy));

      $this->related_by[$taxonomy] = Timber::get_posts([
        'post_type'        => static::_post_type(),
        'post__not_in'     => [$this->ID],
        'posts_per_page'   => $postCount,
        'tax_query'        => [
          [
            'taxonomy'     => $taxonomy,
            'terms'        => $termIds,
          ],
        ],
      ])->to_array();

      $newCount = count($this->related_by[$taxonomy]);
      if ($newCount < $relatedPostCount) {
        // Our query fewer than $postCount posts, so we know this is the
        // exact number of related posts for this taxonomy. Save this count
        // for future calls.
        $this->related_post_counts[$taxonomy] = $newCount;
      }
    }

    return array_slice($this->related_by[$taxonomy], 0, $postCount);
  }

  /**
   * Get related Posts of the same post type, who share categories with
   * this Post.
   *
   * @param int $postCount Optional. The number of posts to get. Defaults to 3.
   * @return Post[] an array of Post objects
   */
  public function get_related_by_category(
    int $postCount = self::RELATED_POST_COUNT
  ) : iterable {
    return $this->get_related_by_taxonomy('category', $postCount);
  }

  /**
   * Get related Posts of the same post type, who share tags with
   * this Post.
   *
   * @param int $postCount Optional. The number of posts to get. Defaults to 3.
   * @return Post[] an array of Post objects
   */
  public function get_related_by_tag(
    int $postCount = self::RELATED_POST_COUNT
  ) : iterable {
    return $this->get_related_by_taxonomy('post_tag', $postCount);
  }
}

