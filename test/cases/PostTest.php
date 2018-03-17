<?php

/**
 * Test the Conifer\Post class
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace ConiferTest;

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
        ]
      ],
      'return'  => 123,
    ]);

    WP_Mock::userFunction('is_wp_error', [
      'times'   => 1,
      'args'    => 123,
      'return'  => false
    ]);

    // Timber will look for a Post with ID=123
    // when we call Page's constructor
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
    $this->markTestSkipped();
  }
}
