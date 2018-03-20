<?php
/**
 * High-level WP Post behavior
 */

namespace Conifer\Post;

use Timber\Post as TimberPost;
use Timber\Term;

use Conifer\Post\Image;

/**
 * High-level behavior for WP Posts, on top of TimberPost class
 */
abstract class Post extends TimberPost {
  use HasTerms;
	
  const RELATED_POST_COUNT = 3;

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

  protected $related_by = [];
  protected $related_post_counts = [];

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
   * Get all published posts of this type, grouped by terms of $taxonomy
   * @param string $taxonomy the name of the taxonomy to group by,
   * e.g. "category"
   * @param array $terms The list of specific terms to filter by.
   * Defaults to all terms within $taxonomy.
   * @return an array like:
   * [
   *   [ 'term' => { Category 1 WP_Term object }, 'posts' => [...],
   *   [ 'term' => { Category 2 WP_Term object }, 'posts' => [...],
   * ]
   */
  public static function get_all_grouped_by_term(
    string $taxonomy,
    array $terms = []
  ) : array {
    // ensure we have a list of taxonomy terms
    $terms = $terms ?: get_terms(['taxonomy' => $taxonomy]);

    // reduce each term in $taxonomy to an array containing:
    //  * the term
    //  * the term's corresponding posts
    return array_reduce($terms, function(
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

        // group this term with its respective posts
        $grouped[] = [
          'term' => $term,
          'posts'  => static::get_all($query),
        ];

        // return the grouped posts so far
        return $grouped;
      }, []);
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

  /**
   * Get related Posts of the same post type, who share terms in $taxonomy with
   * this Post.
   * @param string $taxonomy the taxonomy to associate with, e.g. "category"
   * @param int $postCount Optional. The number of posts to get. Defaults to 3.
   * @return Post[] an array of Post objects
   */
  public function get_related_by_taxonomy(
    string $taxonomy,
    int $postCount = self::RELATED_POST_COUNT
  ) : array {
    // Get any previously queried related posts
    $relatedPosts = $this->related_by[$taxonomy] ?? [];
    $relatedPostCount = $this->related_post_counts[$taxonomy] ?? null;

    if (count($relatedPosts) < $postCount && !isset($relatedPostCount)) {
      // There may be more related posts than previously queried; look for them
      $termIds = array_map(function(Term $term) {
        return $term->id;
      }, $this->terms($taxonomy));

      $this->related_by[$taxonomy] = static::get_all([
        'post_type'     => static::POST_TYPE,
        'post__not_in'  => [$this->ID],
        'numberposts'   => $postCount,
        'tax_query'     => [
          [
            'taxonomy'   => $taxonomy,
            'terms'      => $termIds,
          ]
        ]
      ]);

      if (
        ($newCount = count($this->related_by[$taxonomy])) < $relatedPostCount
      ) {
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
   * @param int $postCount Optional. The number of posts to get. Defaults to 3.
   * @return Post[] an array of Post objects
   */
  public function get_related_by_tag(
    int $postCount = self::RELATED_POST_COUNT
  ) : array {
    return $this->get_related_by_taxonomy('post_tag', $postCount);
  }
}

