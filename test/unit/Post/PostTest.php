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

        $property = new \ReflectionProperty(Post::class, 'blog_url');
        $property->setValue(null);
    }

    public function test_latest_returns_posts()
    {
        $expectedPosts = [
            $this->getMockBuilder(\Timber\Post::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_posts')
            ->once()
            ->with([
                'posts_per_page' => 2,
            ])
            ->andReturn($expectedPosts);

        $this->assertSame($expectedPosts, Person::latest(2));
    }

    public function test_latest_uses_default_count()
    {
        $expectedPosts = [];

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_posts')
            ->once()
            ->with([
                'posts_per_page' => Post::LATEST_POST_COUNT,
            ])
            ->andReturn($expectedPosts);

        $this->assertSame($expectedPosts, Person::latest());
    }

    public function test_get_all_returns_posts_with_post_type()
    {
        $expectedPosts = [
            $this->getMockBuilder(Person::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_posts')
            ->once()
            ->with([
                'post_status' => 'publish',
                'post_type' => 'person',
            ], Person::class)
            ->andReturn($expectedPosts);

        set_error_handler(static function () {
            return true;
        });

        $result = Person::get_all(['post_status' => 'publish']);

        restore_error_handler();

        $this->assertSame($expectedPosts, $result);
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
        $expectedPost = $this->getMockBuilder(Person::class)
            ->disableOriginalConstructor()
            ->getMock();

        WP_Mock::userFunction('wp_insert_post', [
            'times' => 1,
            'args' => [[
                'post_title' => 'Ada Lovelace',
                'post_status' => 'publish',
                'post_type' => 'person',
                'meta_input' => [
                    'nickname' => 'Enchantress of Numbers',
                ],
            ]],
            'return' => 55,
        ]);

        WP_Mock::userFunction('is_wp_error', [
            'times' => 1,
            'args' => [55],
            'return' => false,
        ]);

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_post')
            ->once()
            ->with(55)
            ->andReturn($expectedPost);

        $post = Person::create([
            'post_title' => 'Ada Lovelace',
            'post_status' => 'publish',
            'nickname' => 'Enchantress of Numbers',
        ]);

        $this->assertSame($expectedPost, $post);
    }

    public function test_create_ignores_blacklisted_fields()
    {
        $expectedPost = $this->getMockBuilder(Person::class)
            ->disableOriginalConstructor()
            ->getMock();

        WP_Mock::userFunction('wp_insert_post', [
            'times' => 1,
            'args' => [[
                'post_title' => 'Grace Hopper',
                'post_type' => 'person',
                'meta_input' => [
                    'role' => 'Rear Admiral',
                ],
            ]],
            'return' => 77,
        ]);

        WP_Mock::userFunction('is_wp_error', [
            'times' => 1,
            'args' => [77],
            'return' => false,
        ]);

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_post')
            ->once()
            ->with(77)
            ->andReturn($expectedPost);

        $post = Person::create([
            'ID' => 999,
            'post_type' => 'page',
            'post_title' => 'Grace Hopper',
            'role' => 'Rear Admiral',
        ]);

        $this->assertSame($expectedPost, $post);
    }

    public function test_get_blog_url_returns_string()
    {
        $page = $this->getMockBuilder(\Timber\Post::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['link'])
            ->getMock();
        $page->expects($this->once())
            ->method('link')
            ->willReturn('https://example.com/blog/');

        WP_Mock::userFunction('get_option', [
            'times' => 1,
            'args' => ['page_for_posts'],
            'return' => 42,
        ]);

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_post')
            ->once()
            ->with(42)
            ->andReturn($page);

        $this->assertSame('https://example.com/blog/', Person::get_blog_url());
    }

    public function test_get_related_by_taxonomy_calls_terms()
    {
        $person = $this->getMockBuilder(Person::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['terms'])
            ->getMock();
        $person->ID = 44;

        $termOne = $this->getMockBuilder(\Timber\Term::class)
            ->disableOriginalConstructor()
            ->getMock();
        $termOne->id = 11;

        $termTwo = $this->getMockBuilder(\Timber\Term::class)
            ->disableOriginalConstructor()
            ->getMock();
        $termTwo->id = 22;

        $person->expects($this->once())
            ->method('terms')
            ->with('category')
            ->willReturn([$termOne, $termTwo]);

        $expectedPosts = [
            $this->getMockBuilder(Person::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $postsCollection = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['to_array'])
            ->getMock();
        $postsCollection->expects($this->once())
            ->method('to_array')
            ->willReturn($expectedPosts);

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_posts')
            ->once()
            ->with([
                'post_type' => 'person',
                'post__not_in' => [44],
                'posts_per_page' => 3,
                'tax_query' => [
                    [
                        'taxonomy' => 'category',
                        'terms' => [11, 22],
                    ],
                ],
            ])
            ->andReturn($postsCollection);

        $this->assertSame($expectedPosts, $person->get_related_by_taxonomy('category'));
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

        $this->assertSame(Person::POST_TYPE, $person->type());
    }

    public function test_get_related_by_taxonomy_respects_limit()
    {
        $person = $this->getMockBuilder(Person::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $cachedPosts = [
            (object) ['ID' => 1],
            (object) ['ID' => 2],
            (object) ['ID' => 3],
        ];

        $this->setProtectedProperty($person, 'related_by', [
            'category' => $cachedPosts,
        ]);

        $this->assertSame(
            array_slice($cachedPosts, 0, 2),
            $person->get_related_by_taxonomy('category', 2)
        );
    }
}
