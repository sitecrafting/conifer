<?php
/**
 * High-level WP Post behavior
 */

namespace Conifer\Post;

use Timber\Post as TimberPost;

use Conifer\Post\Image;

/**
 * High-level behavior for WP Posts, on top of TimberPost class
 */
abstract class Post extends TimberPost {
	use HasTerms;

	/**
	 * When instantiating TimberImages, create instances of this class
	 * @var string
	 */
	public $ImageClass = '\Conifer\Post\Image';

	/**
	 * The default blog landing page URL
	 * @var string
	 */
	protected static $BLOG_URL;

  /**
   * Child classes must declare their own post types
   */
  abstract public static function post_type() : string;

	/**
	 * Get all the posts matching the given query (defaults to the current/global WP query constraints)
	 * @param  array|string $query any valid Timber query
	 * @return array         an array of all matching post objects
	 */
	public static function get_all( $query = false ) {
		return \Timber::get_posts( $query, __CLASS__ );
	}

	/**
	 * Get the URL of the blog landing page
	 * (what WP calls the "post archive" page)
	 * @return string the URL
	 */
	public static function get_blog_url() {
		if( ! static::$BLOG_URL ) {
			// haven't fetched the URL yet...go get it
			$page = Page::get_blog_page();

			// cache it
			static::$BLOG_URL = $page->link();
		}

		return static::$BLOG_URL;
	}

	/**
	 * Check whether a post by the given ID exists
	 * @param  int $id the post ID to check for
	 * @return boolean     true if the post exists, false otherwise
	 */
	public static function exists( $id ) {
		return is_string( get_post_status( $id ) );
	}

  /**
   * Create a new post from an array of data
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
   * The value for "post_type" will always come from $this->post_type().
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
      'post_type' => static::post_type(),
      'meta_input' => $data,
    ]));

    if (is_wp_error($id)) {
      return $id;
    }

    // return a new instance of the called class
    return new static($id);
  }
}

