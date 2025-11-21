<?php

/**
 * Test the Conifer\Post class
 *
 * @copyright 2020 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

declare(strict_types=1);

namespace Conifer\Integration;

use WP_Term;

use Timber\Timber;

use Conifer\Post\Page;
use Conifer\Post\Post;
use Conifer\Post\BlogPost;

class PostTest extends Base {
    public $factory;

    public function test_create(): void {
        $page = Page::create([
        'post_title'   => 'Hello',
        'post_name'    => 'hello',
        'custom_field' => 'CUSTOM',
        'array_field'  => [ 'hey', 'there' ],
        'ID'           => 'this should have no effect',
        'post_type'    => 'this should too',
        ]);

        $this->assertInstanceOf(Page::class, $page);
        $this->assertNotEmpty($page->id);
        $this->assertEquals('Hello', $page->title());
        $this->assertEquals('hello', $page->slug);
        $this->assertEquals('CUSTOM', $page->meta('custom_field'));
        $this->assertEquals([ 'hey', 'there' ], $page->meta('array_field'));

        $this->assertEquals('page', $page->post_type);
    }

    public function test_exists_on_existent_post(): void {
        $post = BlogPost::create([
        'post_title' => 'Cogito ergo sum',
        ]);

        $this->assertTrue(Post::exists($post->ID));
        $this->assertTrue(BlogPost::exists($post->ID));
        $this->assertFalse(Page::exists($post->ID));
    }

    public function test_exists_on_existent_page(): void {
        $page = Page::create([
        'post_title' => 'Cogito ergo sum',
        ]);

        $this->assertTrue(Post::exists($page->ID));
        $this->assertTrue(Page::exists($page->ID));
        $this->assertFalse(BlogPost::exists($page->ID));
    }

    public function test_exists_on_nonexistent_post(): void {
        $this->assertFalse(Post::exists(99999));
    }

    public function test_get_blog_page(): void {
        $page = Page::create([
        'post_title' => 'News',
        'post_name'  => 'news',
        ]);

        update_option('page_for_posts', $page->id);

        $this->assertEquals($page->id, Page::get_blog_page()->id);
    }

    public function test_get_blog_url(): void {
        $page = Page::create([
        'post_title' => 'News',
        'post_name'  => 'news',
        ]);

        update_option('page_for_posts', $page->id);

        // TODO figure out how to get permalinks working in the test env
        $this->assertEquals(
        sprintf('http://example.org/?page_id=%d', $page->id),
        Page::get_blog_url()
        );
    }

    public function test_get_related_by_taxonomy(): void {
        $awesome = $this->factory->term->create([
        'name'     => 'Awesome',
        'taxonomy' => 'category',
        ]);

        $post = BlogPost::create([
        'post_title' => 'My Post',
        ]);

        wp_set_object_terms($post->id, [ $awesome ], 'category');

        $ids = $this->factory->post->create_many(3, [
        'post_type'  => 'post',
        ]);
        foreach ($ids as $id) {
            wp_set_object_terms($id, [ $awesome ], 'category');
        }

        // Create uncategorized posts
        $this->factory->post->create_many(2, [
        'post_type'  => 'post',
        ]);

        $this->assertCount(3, $post->get_related_by_taxonomy('category'));
        $this->assertCount(3, $post->get_related_by_taxonomy('category', 5));
        $this->assertCount(2, $post->get_related_by_taxonomy('category', 2));
    }

    public function test_get_by_template(): void {
        $id = $this->factory->post->create([
        'post_title'          => 'My Custom Page',
        'post_status'         => 'publish',
        'post_type'           => 'page',
        'meta_input'          => [
        '_wp_page_template' => 'my-template.php',
        ],
        ]);

        $this->assertEquals($id, Page::get_by_template('my-template.php')->id);
    }

    public function test_get_by_template_with_query_params(): void {
        $id = $this->factory->post->create([
        'post_title'          => 'My Custom Page',
        'post_status'         => 'draft',
        'post_type'           => 'page',
        'meta_input'          => [
        '_wp_page_template' => 'my-template.php',
        ],
        ]);

        $this->assertEquals($id, Page::get_by_template('my-template.php', [
        'post_status' => 'draft',
        ])->id);
    }
}
