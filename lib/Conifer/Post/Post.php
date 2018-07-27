<?php
/**
 * High-level WP Post behavior
 */

namespace Conifer\Post;

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

  const POST_TYPE = '';

  const RELATED_POST_COUNT = 3;

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
  public static function get_all( $query = false ) {
    $class = static::class;

    // Avoid instantiating this (abstract) class, causing a Fatal Error.
    // TODO figure out a more elegant way to do this??
    // Might have to rework this at the Timber level
    // @see https://github.com/timber/timber/pull/1218
    if ($class === self::class) {
      $class = TimberPost::class;
    }

    return Timber::get_posts( $query, $class );
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
    return new static($id);
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
  ) : array {
    // Get any previously queried related posts
    $relatedPosts     = $this->related_by[$taxonomy] ?? [];
    $relatedPostCount = $this->related_post_counts[$taxonomy] ?? null;

    if (count($relatedPosts) < $postCount && !isset($relatedPostCount)) {
      // There may be more related posts than previously queried; look for them
      $termIds = array_map(function(Term $term) {
        return $term->id;
      }, $this->terms($taxonomy));

      $this->related_by[$taxonomy] = static::get_all([
        'post_type'     => $this->get_post_type(),
        'post__not_in'  => [$this->ID],
        'numberposts'   => $postCount,
        'tax_query'     => [
          [
            'taxonomy'   => $taxonomy,
            'terms'      => $termIds,
          ],
        ],
      ]);

      $newCount = count($this->related_by[$taxonomy]);
      if ($newCount < $relatedPostCount) {
        // Our query fewer than $postCount posts, so we know this is the
        // exact number of related posts for this taxonomy. Save this count
        // for future calls.
        $this->related_post_counts[$taxonomy] = $newCount;
      }
    }

    return $this->related_by[$taxonomy];
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
  ) : array {
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
  ) : array {
    return $this->get_related_by_taxonomy('post_tag', $postCount);
  }
}

