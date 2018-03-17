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
}

