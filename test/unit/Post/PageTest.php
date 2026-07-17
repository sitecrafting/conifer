<?php

/**
 * Tests for the Conifer\Post\Page class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */


namespace Conifer\Unit\Post;

use Conifer\Navigation\Menu;
use Conifer\Post\Page;
use Conifer\Unit\Base;
use PHPUnit\Framework\MockObject\MockObject;
use WP_Mock;

class PageTest extends Base
{
    protected null|MockObject|Page $page = null;
    protected null|MockObject|Menu $menu = null;

    private string $mockChildSlug           = 'child';

    public function setUp(): void
    {
        parent::setUp();

        $this->page = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['title'])
            ->getMock();

        $this->menu = $this->getMockBuilder(Menu::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_current_top_level_item'])
            ->getMock();
    }

    public function test_get_title_from_nav_or_post_returns_nav_title_when_menu_has_title()
    {
        $item = (object) ['title' => $this->mockChildSlug];

        $this->menu->method('get_current_top_level_item')->willReturn($item);

        $title = $this->page->get_title_from_nav_or_post($this->menu);

        $this->assertEquals($this->mockChildSlug, $title);
    }

    public function test_get_title_from_nav_or_post_returns_post_title_when_menu_has_no_title()
    {
        $this->menu->method('get_current_top_level_item')->willReturn(null);
        $this->page->method('title')->willReturn('Post Title');

        $title = $this->page->get_title_from_nav_or_post($this->menu);

        $this->assertEquals('Post Title', $title);
    }

    public function test_get_title_from_nav_or_post_returns_post_title_when_menu_item_title_is_null()
    {
        $item = (object) ['title' => null];

        $this->menu->method('get_current_top_level_item')->willReturn($item);
        $this->page->method('title')->willReturn('Fallback Title');

        $title = $this->page->get_title_from_nav_or_post($this->menu);

        $this->assertSame('Fallback Title', $title);
    }

    public function test_get_blog_page_returns_page_for_posts_from_timber()
    {
        WP_Mock::userFunction('get_option', [
            'times' => 1,
            'args' => ['page_for_posts'],
            'return' => 42,
        ]);

        $expectedPost = $this->createMock(Page::class);

        // Use a class alias mock to assert static Timber::get_post() behavior.
        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_post')
            ->once()
            ->with(42)
            ->andReturn($expectedPost);

        $this->assertSame($expectedPost, Page::get_blog_page());
    }

    public function test_get_by_template_uses_default_query_when_no_extra_args_provided()
    {
        $template = 'default-template.php';
        $expectedPost = $this->createMock(Page::class);

        $expectedQuery = [
            'post_type' => 'page',
            'meta_query' => [
                [
                    'key' => '_wp_page_template',
                    'value' => $template,
                ],
            ],
        ];

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_post')
            ->once()
            ->with($expectedQuery)
            ->andReturn($expectedPost);

        $this->assertSame($expectedPost, Page::get_by_template($template));
    }

    public function test_get_by_template_overrides_conflicting_post_type_and_meta_query_from_input()
    {
        $template = 'forced-template.php';
        $query = [
            'post_type' => 'post',
            'meta_query' => [
                [
                    'key' => 'other_key',
                    'value' => 'other_value',
                ],
            ],
            'posts_per_page' => 5,
        ];

        $expectedPost = $this->createMock(Page::class);

        $expectedQuery = [
            'post_type' => 'page',
            'meta_query' => [
                [
                    'key' => '_wp_page_template',
                    'value' => $template,
                ],
            ],
            'posts_per_page' => 5,
        ];

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_post')
            ->once()
            ->with($expectedQuery)
            ->andReturn($expectedPost);

        $this->assertSame($expectedPost, Page::get_by_template($template, $query));
    }

    public function test_get_by_template_merges_template_query_with_extra_query_args()
    {
        $template = 'my-template.php';
        $query = [
            'post_status' => 'draft',
            'posts_per_page' => 1,
        ];

        $expectedQuery = [
            'post_status' => 'draft',
            'posts_per_page' => 1,
            'post_type' => 'page',
            'meta_query' => [
                [
                    'key' => '_wp_page_template',
                    'value' => $template,
                ],
            ],
        ];

        $expectedPost = $this->createMock(Page::class);
        $expectedPost->template = $template;

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_post')
            ->once()
            ->with($expectedQuery)
            ->andReturn($expectedPost);

        $this->assertSame($expectedPost, Page::get_by_template($template, $query));
    }

    public function test_get_by_template_returns_null_when_timber_returns_null()
    {
        $template = 'missing-template.php';

        $timber = \Mockery::mock('alias:Timber\\Timber');
        $timber->shouldReceive('get_post')
            ->once()
            ->andReturn(null);

        $this->assertNull(Page::get_by_template($template));
    }
}
