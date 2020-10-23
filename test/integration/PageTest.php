<?php

/**
 * Integration tests for the Conifer\Post\Page class.
 *
 * @copyright 2020 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Integration;

use Conifer\Post\Page;

class PageTest extends Base {
  public function test_get_blog_page() {
    $id = $this->factory->post->create([
      'post_title' => 'Blog',
      'post_type'  => 'page',
    ]);

    update_option('page_for_posts', $id);

    $page = Page::get_blog_page();
    $this->assertEquals($id, $page->id);
    $this->assertEquals('Blog', $page->title());
  }
}
