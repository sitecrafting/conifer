<?php

/**
 * Tests the following exposed methods of the Conifer\Navigation\MenuItem class:
 * display_children
 * points_to_current_post_or_ancestor
 * has_children
 * 
 * NOTE: This test is not intended to be a comprehensive test of the MenuItem class, but rather to test the methods specifically.
 * Testing of the MenuItem class is done in the Timber tests, and should be relied upon for testing the base functionality of the MenuItem class.
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit;

use Conifer\Navigation\MenuItem;

class MenuItemTest extends Base
{
    private $mockMenuItem1;
    private $mockMenuItem2;
    private $mockMenuItem3;

    public function setUp(): void
    {
        parent::setUp();
    }

    // points_to_current_post_or_ancestor tests
    public function test_points_to_current_post_or_ancestor_returns_true_if_CLASS_CURRENT_is_in_classes()
    {
        $menuItem = $this->createMockMenuItem([MenuItem::CLASS_CURRENT]);

        $this->assertTrue($menuItem->points_to_current_post_or_ancestor());
    }

    public function test_points_to_current_post_or_ancestor_returns_true_if_CLASS_CURRENT_ANCESTOR_is_in_classes()
    {
        $menuItem = $this->createMockMenuItem([MenuItem::CLASS_CURRENT_ANCESTOR]);

        $this->assertTrue($menuItem->points_to_current_post_or_ancestor());
    }

    public function test_points_to_current_post_or_ancestor_returns_false_if_neither_CLASS_CURRENT_nor_CLASS_CURRENT_ANCESTOR_is_in_classes()
    {
        $menuItem = $this->createMockMenuItem([]);

        $this->assertFalse($menuItem->points_to_current_post_or_ancestor());
    }

    // has_children tests
    public function test_has_children_returns_true_if_CLASS_HAS_CHILDREN_is_in_classes()
    {
        $menuItem = $this->createMockMenuItem([MenuItem::CLASS_HAS_CHILDREN]);

        $this->assertTrue($menuItem->has_children());
    }

    public function test_has_children_returns_false_if_CLASS_HAS_CHILDREN_is_not_in_classes()
    {
        $menuItem = $this->createMockMenuItem([]);

        $this->assertFalse($menuItem->has_children());
    }

    // display_children tests
    public function test_display_children_returns_true_if_has_children_is_true_and_points_to_current_post_or_ancestor_is_true()
    {
        $menuItem = $this->createMockMenuItem([
            MenuItem::CLASS_HAS_CHILDREN,
            MenuItem::CLASS_CURRENT_ANCESTOR,
        ]);

        $this->assertTrue($menuItem->display_children());
    }

    public function test_display_children_returns_false_if_has_children_is_false()
    {
        $menuItem = $this->createMockMenuItem([
            MenuItem::CLASS_CURRENT_ANCESTOR,
        ]);

        $this->assertFalse($menuItem->display_children());
    }

    public function test_display_children_returns_false_if_points_to_current_post_or_ancestor_is_false()
    {
        $menuItem = $this->createMockMenuItem([
            MenuItem::CLASS_HAS_CHILDREN,
        ]);

        $this->assertFalse($menuItem->display_children());
    }

    public function test_display_children_returns_false_if_both_has_children_and_points_to_current_post_or_ancestor_are_false()
    {
        $menuItem = $this->createMockMenuItem([]);

        $this->assertFalse($menuItem->display_children());
    }

    private function createMockMenuItem(array $classes): MenuItem
    {
        $menuItem = $this->getMockBuilder(MenuItem::class)
            ->disableOriginalConstructor()
            ->onlyMethods([]) // No methods to mock, we just want to set the classes property
            ->getMock();

        // Set the classes property to the provided classes
        $menuItem->classes = $classes;

        return $menuItem;
    }
}
