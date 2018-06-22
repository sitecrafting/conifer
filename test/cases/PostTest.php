<?php
/**
 * Test the Conifer\Post class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace ConiferTest;

use WP_Term;

use WP_Mock;

use Conifer\Post\Page;
use Conifer\Post\Post;

class PostTest extends Base {
  public function test_create() {
    WP_Mock::userFunction('wp_insert_post', [
      'times'   => 1,
      'args'    => [
        [
          'post_title'  => 'Hello',
          'post_name'   => 'hello',
          'meta_input'  => [
            'custom_field'  => 'CUSTOM',
            'array_field'   => ['hey', 'there'],
          ],
          'post_type'   => 'page',
        ],
      ],
      'return'  => 123,
    ]);

    WP_Mock::userFunction('is_wp_error', [
      'times'   => 1,
      'args'    => 123,
      'return'  => false,
    ]);

    // Timber will look for a Post with ID=123
    // when we call Page's constructor.
    $this->mockPost(['ID' => 123]);

    $result = Page::create([
      'post_title' => 'Hello',
      'post_name'  => 'hello',
      'custom_field' => 'CUSTOM',
      'array_field' => ['hey', 'there'],
      'ID' => 'this should get blacklisted',
      'post_type' => 'this should too',
    ]);

    $this->assertEquals(123, $result->ID);
  }

  public function test_exists_on_existent_post() {
    WP_Mock::userFunction('get_post_status', [
      'times'   => 1,
      'args'    => 3,
      'return'  => 'draft',
    ]);

    $this->assertTrue(Post::exists(3));
  }

  public function test_exists_on_nonexistent_post() {
    WP_Mock::userFunction('get_post_status', [
      'times'   => 1,
      'args'    => 3,
      'return'  => false,
    ]);

    $this->assertFalse(Post::exists(3));
  }

  public function test_get_all() {
    $this->markTestSkipped();
  }

  public function test_get_blog_url() {
    WP_Mock::userFunction('get_option', [
      'times'   => 1,
      'args'    => 'page_for_posts',
      'return'  => 456,
    ]);

    WP_Mock::userFunction('get_permalink', [
      'times'   => 1,
      'args'    => 456,
      'return'  => 'https://www.sitecrafting.com',
    ]);

    $this->assertEquals('https://www.sitecrafting.com', Page::get_blog_url());
  }

  public function test_get_related_by_taxonomy() {
    // Not sure how we can mock Timber's hard-coded `new \WP_Query()`
    // in QueryIterator::__construct(), so skipping for now...
    return $this->markTestSkipped();

    $this->mockPost(['ID' => 123]);
    $post = new Page(123);

    $related = ['mock', 'related', 'post', 'data'];
    WP_Mock::userFunction('get_posts', [
      'times'   => 1,
      'return'  => $related,
    ]);
    $term = $this->mockTerm([
      'term_id' => 456,
      'taxonomy' => 'category',
    ]);
    WP_Mock::userFunction('wp_get_post_terms', [
      'times'   => 1,
      'args'    => [
        123,
        'category',
      ],
      'return'  => [$term],
    ]);

    $this->assertEquals($related, $post->get_related_by_taxonomy(
      'category',
      13
    ));
  }
}
