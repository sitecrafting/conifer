<?php

/**
 * Tests for the Conifer\Post\BlogPost class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit;

use Conifer\Post\BlogPost;
use PHPUnit\Framework\MockObject\MockObject;

class BlogPostTest extends Base
{
    protected null|MockObject|BlogPost $blogPost = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->blogPost = $this->getMockBuilder(BlogPost::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['categories'])
            ->getMock();

        $this->blogPost->post_type = BlogPost::POST_TYPE;
        $this->blogPost->ID = 99;
    }

    public function test_get_all_published_months_queries_wpdb_and_returns_rows()
    {
        global $wpdb;

        $expectedRows = [
            [
                'y' => '2026',
                'm' => '07',
                'formatted_month' => '2026-07',
                'pretty_month' => 'July 2026',
            ],
        ];

        $wpdb = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['get_results'])
            ->getMock();
        $wpdb->posts = 'wp_posts';

        $wpdb->expects($this->once())
            ->method('get_results')
            ->with(
                $this->callback(function (string $sql): bool {
                    return str_contains($sql, 'FROM wp_posts')
                        && str_contains($sql, "post_type = 'post'")
                        && str_contains($sql, "post_status = 'publish'")
                        && str_contains($sql, 'ORDER BY post_date DESC');
                }),
                ARRAY_A
            )
            ->willReturn($expectedRows);

        $this->assertSame($expectedRows, BlogPost::get_all_published_months());
    }

    public function test_get_all_published_years_queries_wpdb_and_returns_columns()
    {
        global $wpdb;

        $expectedYears = ['2026', '2025'];

        $wpdb = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['get_col'])
            ->getMock();
        $wpdb->posts = 'wp_posts';

        $wpdb->expects($this->once())
            ->method('get_col')
            ->with(
                $this->callback(function (string $sql): bool {
                    return str_contains($sql, 'SELECT DISTINCT YEAR(post_date)')
                        && str_contains($sql, 'FROM wp_posts')
                        && str_contains($sql, "post_type = 'post'")
                        && str_contains($sql, "post_status = 'publish'")
                        && str_contains($sql, 'ORDER BY post_date DESC');
                }),
                ARRAY_A
            )
            ->willReturn($expectedYears);

        $this->assertSame($expectedYears, BlogPost::get_all_published_years());
    }

    public function test_get_related_uses_default_count_when_not_provided()
    {
        $this->blogPost->method('categories')->willReturn([
            (object) ['id' => 11],
            (object) ['id' => 22],
        ]);

        $expectedQuery = [
            'post_type' => 'post',
            'posts_per_page' => BlogPost::NUM_RELATED_POSTS,
            'post__not_in' => [99],
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'terms' => [11, 22],
                ],
            ],
        ];

        $expectedPosts = [
            $this->getMockBuilder(\Timber\Post::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_posts')
            ->once()
            ->with($expectedQuery)
            ->andReturn($expectedPosts);

        $this->assertSame($expectedPosts, $this->blogPost->get_related());
    }

    public function test_get_related_fetches_once_and_reuses_cached_results()
    {
        $this->blogPost->method('categories')->willReturn([
            (object) ['id' => 7],
        ]);

        $expectedPosts = [
            $this->getMockBuilder(\Timber\Post::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_posts')
            ->once()
            ->with([
                'post_type' => 'post',
                'posts_per_page' => 4,
                'post__not_in' => [99],
                'tax_query' => [
                    [
                        'taxonomy' => 'category',
                        'terms' => [7],
                    ],
                ],
            ])
            ->andReturn($expectedPosts);

        $firstResult = $this->blogPost->get_related(4);
        $secondResult = $this->blogPost->get_related(999);

        // Second call should use the cached related_posts value from the first call.
        $this->assertSame($expectedPosts, $firstResult);
        $this->assertSame($firstResult, $secondResult);
    }

    public function test_get_related_handles_empty_categories_array()
    {
        $this->blogPost->method('categories')->willReturn([]);

        $expectedPosts = [];

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_posts')
            ->once()
            ->with([
                'post_type' => 'post',
                'posts_per_page' => 2,
                'post__not_in' => [99],
                'tax_query' => [
                    [
                        'taxonomy' => 'category',
                        'terms' => [],
                    ],
                ],
            ])
            ->andReturn($expectedPosts);

        $this->assertSame($expectedPosts, $this->blogPost->get_related(2));
    }
}
