<?php
/**
 * Test the Conifer\Post class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Unit;

use WP_Term;

use WP_Mock;

use Conifer\Post\Page;
use Conifer\Post\Post;

class PostTest extends Base {
  public function test_exists_on_existent_post() {
    $this->mockPost(['ID' => 3]);

    $this->assertTrue(Post::exists(3));
  }

  public function test_exists_on_nonexistent_post() {
    WP_Mock::userFunction('get_post', [
      'times'   => 1,
      'args'    => 3,
      'return'  => false,
    ]);

    $this->assertFalse(Post::exists(3));
  }

  public function test_get_blog_url() {
    $this->markTestSkipped();
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
    // To get around hard-coded `new WP_Query()` calls, we need to turn this
    // into an integration test.
    // See: https://github.com/sitecrafting/conifer/issues/119
    return $this->markTestSkipped();

    /* @codingStandardsIgnoreStart

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

    @codingStandardsIgnoreEnd */
  }
}
