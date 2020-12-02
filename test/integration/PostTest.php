<?php
/**
 * Test the Conifer\Post class
 *
 * @copyright 2020 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Integration;

use WP_Term;

use Conifer\Post\Page;
use Conifer\Post\Post;
use Conifer\Post\BlogPost;

class PostTest extends Base {
  public function test_create() {
    $page = Page::create([
      'post_title'   => 'Hello',
      'post_name'    => 'hello',
      'custom_field' => 'CUSTOM',
      'array_field'  => ['hey', 'there'],
      'ID'           => 'this should have no effect',
      'post_type'    => 'this should too',
    ]);

    $this->assertInstanceOf(Page::class, $page);
    $this->assertNotEmpty($page->id);
    $this->assertEquals('Hello', $page->title());
    $this->assertEquals('hello', $page->slug);
    $this->assertEquals('CUSTOM', $page->meta('custom_field'));
    $this->assertEquals(['hey', 'there'], $page->meta('array_field'));

    $this->assertEquals('page', $page->post_type);
  }

  public function test_exists_on_existent_post() {
    $post = BlogPost::create([
      'post_title' => 'Cogito ergo sum',
    ]);

    $this->assertTrue(Post::exists($post->ID));
    $this->assertTrue(BlogPost::exists($post->ID));
    $this->assertFalse(Page::exists($post->ID));
  }

  public function test_exists_on_existent_page() {
    $page = Page::create([
      'post_title' => 'Cogito ergo sum',
    ]);

    $this->assertTrue(Post::exists($page->ID));
    $this->assertTrue(Page::exists($page->ID));
    $this->assertFalse(BlogPost::exists($page->ID));
  }
}
