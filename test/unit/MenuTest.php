<?php

/**
 * Tests the following exposed methods of the Conifer\Navigation\Menu class:
 * - get_current_top_level_item()
 * 
 * NOTE: This test is not intended to be a comprehensive test of the Menu class, but rather to test the get_current_top_level_item() method specifically.
 * This is the only method in the Menu class that has been extended from TimberMenu, and is the only method that has been added to the Menu class.
 * Because of that, these tests do not create or mock a full menu structure, but rather just enough to test the get_current_top_level_item() method.
 * Functionality of the Menu class is tested in the Timber tests, and should be relied upon for testing the base functionality of the Menu class.
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit;

use Conifer\Navigation\Menu;
use Conifer\Navigation\MenuItem;

class MenuTest extends Base
{
    protected ?Menu $mockMenu = null;

    private string $mockHomeSlug            = 'home';
    private string $mockChildSlug           = 'child';
    private string $mockGrandchildSlug      = 'grandchild';
    private string $mockOtherGrandchildSlug = 'other-grandchild';

    public function setUp(): void
    {
        parent::setUp();

        $this->mockMenu = $this->createMockMenu();
    }

    // Test empty menu, should return null
    public function test_get_current_top_level_item_returns_null_when_no_items()
    {
        $this->mockMenu->items = [];

        $this->assertNull($this->mockMenu->get_current_top_level_item());
    }


    // Test menu with items, but none match, should return null
    public function test_get_current_top_level_item_returns_null_when_no_item_matches()
    {
        $this->mockMenu->items = [
            $this->createMockMenuItem([$this->mockHomeSlug]),
            $this->createMockMenuItem([$this->mockChildSlug]),
            $this->createMockMenuItem([$this->mockGrandchildSlug]),
        ];

        // We should get back null because none of the items match
        $this->assertNull($this->mockMenu->get_current_top_level_item());
    }

    public function test_get_current_top_level_item_returns_matching_item()
    {
        $nonMatching = $this->createMockMenuItem([$this->mockHomeSlug]);
        $matching = $this->createMockMenuItem([
            $this->mockChildSlug,
            MenuItem::CLASS_CURRENT_ANCESTOR,
        ]);

        $this->mockMenu->items = [
            $nonMatching,
            $matching,
            $this->createMockMenuItem([
                $this->mockGrandchildSlug,
                MenuItem::CLASS_CURRENT,
            ]),
        ];

        $this->assertSame($matching, $this->mockMenu->get_current_top_level_item());
        $this->assertNotSame($nonMatching, $this->mockMenu->get_current_top_level_item());
    }

    public function test_get_current_top_level_item_returns_first_match_when_multiple_items_match()
    {
        $matching1 = $this->createMockMenuItem([
            $this->mockChildSlug,
            MenuItem::CLASS_CURRENT_ANCESTOR,
        ]);
        $matching2 = $this->createMockMenuItem([
            $this->mockGrandchildSlug,
            MenuItem::CLASS_CURRENT,
        ]);

        $this->mockMenu->items = [
            $matching1,
            $matching2,
        ];

        // We should get back the first matching item, not the second
        $this->assertSame($matching1, $this->mockMenu->get_current_top_level_item());
        $this->assertNotSame($matching2, $this->mockMenu->get_current_top_level_item());
    }

    public function test_get_current_top_level_item_returns_later_match_after_non_matching_items()
    {
        $nonMatching = $this->createMockMenuItem([$this->mockHomeSlug]);
        $matching = $this->createMockMenuItem([
            $this->mockChildSlug,
            MenuItem::CLASS_CURRENT_ANCESTOR,
        ]);

        $this->mockMenu->items = [
            $nonMatching,
            $matching,
        ];

        // We should get back the matching item, even though it comes after a non-matching item
        $this->assertSame($matching, $this->mockMenu->get_current_top_level_item());
        $this->assertNotSame($nonMatching, $this->mockMenu->get_current_top_level_item());
    }

    public function test_get_current_top_level_item_with_single_matching_item()
    {
        $matching = $this->createMockMenuItem([
            $this->mockChildSlug,
            MenuItem::CLASS_CURRENT_ANCESTOR,
        ]);

        $this->mockMenu->items = [
            $matching,
        ];

        // We should get back the matching item, even though it comes after a non-matching item
        $this->assertSame($matching, $this->mockMenu->get_current_top_level_item());
        $this->assertNotNull($this->mockMenu->get_current_top_level_item());
    }

    public function test_get_current_top_level_item_with_single_non_matching_item_returns_null()
    {
        $nonMatching = $this->createMockMenuItem([$this->mockHomeSlug]);

        $this->mockMenu->items = [
            $nonMatching,
        ];

        // We should get back null because the single item does not match
        $this->assertNull($this->mockMenu->get_current_top_level_item());
    }

    // Menu structure inspired by the Timber tests menu structure: https://github.com/timber/timber/blob/282285c3fb19be10ad8f71090ebd4e43c86f958b/tests/MenuTest.php#L28
    protected function createMockMenu()
    {
        $suffix = \uniqid();

        // Setup slugs
        $this->mockHomeSlug            = 'home-' . $suffix;
        $this->mockChildSlug           = 'child-' . $suffix;
        $this->mockGrandchildSlug      = 'grandchild-' . $suffix;
        $this->mockOtherGrandchildSlug = 'other-grandchild-' . $suffix;

        // First, build our mock menu. This will be added to later, but can safely be created first
        $mockMenu = self::getMockBuilder(Menu::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_items'])
            ->getMock();
        $mockMenu->method('get_items')
            ->willReturnCallback(fn() => $mockMenu->items ?? []);
        $mockMenu->items = [
            $this->createMockMenuItem([$this->mockHomeSlug]),
            $this->createMockMenuItem([
                $this->mockChildSlug,
                MenuItem::CLASS_CURRENT_ANCESTOR,
                MenuItem::CLASS_HAS_CHILDREN,
            ]),
            $this->createMockMenuItem([
                $this->mockGrandchildSlug,
                MenuItem::CLASS_CURRENT,
            ]),
            $this->createMockMenuItem([$this->mockOtherGrandchildSlug]),
        ];

        return $mockMenu;
    }

    // Creates a mock MenuItem with the provided classes (CLASS_CURRENT_ANCESTOR, CLASS_CURRENT, etc.)
    // This emulates the behavior of navigating a site using a menu structure, where the current page and its ancestors are marked with specific classes.
    protected function createMockMenuItem(array $classes)
    {
        $menuItem = $this->getMockBuilder(MenuItem::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $menuItem->classes = $classes;

        return $menuItem;
    }
}
