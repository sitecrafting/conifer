<?php

/**
 * Test the Conifer\Twig\WordPressHelper class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit\Twig;

use Conifer\Twig\WordPressHelper;
use Conifer\Unit\Base;

class WordPressHelperTest extends Base
{
    private ?WordPressHelper $helper = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->helper = new WordPressHelper();
    }

    public function test_get_functions_returns_array_of_expected_callables()
    {
        $expectedFunctions = [
            'get_search_form',
            'get_blog_url',
            'img_url',
            'wp_nav_menu',
            'paginate_links',
            'get_option',
            'get_theme_setting',
            'get_sidebar_widgets',
            'get_latest_posts',
        ];

        $functions = $this->helper->get_functions();

        foreach ($expectedFunctions as $functionName) {
            $this->assertArrayHasKey($functionName, $functions);

            // Special case for get_blog_url, which is a static method on the Post class, but here just returns an array with a callable function name
            if ($functionName === 'get_blog_url') {
                $this->assertIsArray($functions[$functionName]);
                $this->assertCount(2, $functions[$functionName]);
                $this->assertEquals('\Conifer\Post\Post', $functions[$functionName][0]);
                $this->assertEquals('get_blog_url', $functions[$functionName][1]);
            } else {
                $this->assertIsCallable($functions[$functionName]);
            }
        }
    }

    public function test_get_filters_returns_an_empty_array()
    {
        $filters = $this->helper->get_filters();
        $this->assertIsArray($filters);
        $this->assertEmpty($filters);
    }
}
