<?php

/**
 * Tests for the Conifer\Post\Post class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit\Post;

use Conifer\Post\Post;
use Conifer\Unit\Base;
use Conifer\Unit\Support\Person;
use WP_Mock;

class PostTest extends Base
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_latest_returns_posts()
    {
        // Skip: requires Timber integration
        $this->markTestSkipped('Requires Timber integration with full WordPress environment');
    }

    public function test_latest_uses_default_count()
    {
        // Skip: requires Timber integration
        $this->markTestSkipped('Requires Timber integration with full WordPress environment');
    }

    public function test_get_all_returns_posts_with_post_type()
    {
        // Skip: requires Timber integration
        $this->markTestSkipped('Requires Timber integration with full WordPress environment');
    }

    public function test_exists_with_matching_post_type()
    {
        $post = $this->getMockBuilder(\WP_Post::class)
            ->getMock();
        $post->post_type = 'person';

        WP_Mock::userFunction('get_post', [
            'args' => [123],
            'return' => $post,
        ]);

        $this->assertTrue(Person::exists(123));
    }

    public function test_exists_with_non_matching_post_type()
    {
        $post = $this->getMockBuilder(\WP_Post::class)
            ->getMock();
        $post->post_type = 'post';

        WP_Mock::userFunction('get_post', [
            'args' => [123],
            'return' => $post,
        ]);

        $this->assertFalse(Person::exists(123));
    }

    public function test_exists_with_nonexistent_post()
    {
        WP_Mock::userFunction('get_post', [
            'args' => [999],
            'return' => null,
        ]);

        $this->assertFalse(Person::exists(999));
    }

    public function test_create_with_post_fields_and_metadata()
    {
        // Skip: requires Timber integration (Timber::get_post call)
        $this->markTestSkipped('Requires Timber integration with full WordPress environment');
    }

    public function test_create_ignores_blacklisted_fields()
    {
        // Skip: requires Timber integration
        $this->markTestSkipped('Requires Timber integration with full WordPress environment');
    }

    public function test_get_blog_url_returns_string()
    {
        // Skip: requires Timber integration
        $this->markTestSkipped('Requires Timber integration with full WordPress environment');
    }

    public function test_get_related_by_taxonomy_calls_terms()
    {
        // Skip: requires Timber integration
        $this->markTestSkipped('Requires Timber integration with full WordPress environment');
    }

    public function test_get_related_by_category_delegates_to_taxonomy_method()
    {
        $person = $this->getMockBuilder(Person::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_related_by_taxonomy'])
            ->getMock();

        $person->expects($this->once())
            ->method('get_related_by_taxonomy')
            ->with('category', 5)
            ->willReturn([]);

        $person->get_related_by_category(5);
    }

    public function test_get_related_by_tag_delegates_to_taxonomy_method()
    {
        $person = $this->getMockBuilder(Person::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_related_by_taxonomy'])
            ->getMock();

        $person->expects($this->once())
            ->method('get_related_by_taxonomy')
            ->with('post_tag', 5)
            ->willReturn([]);

        $person->get_related_by_tag(5);
    }

    public function test_type_returns_post_type()
    {
        // Create a partial mock that keeps real methods but disable constructor
        $person = $this->getMockBuilder(Person::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Person::POST_TYPE = 'person'
        $this->assertSame('person', $person->type());
    }

    public function test_get_related_by_taxonomy_respects_limit()
    {
        // Skip: requires Timber integration
        $this->markTestSkipped('Requires Timber integration with full WordPress environment');
    }
}
