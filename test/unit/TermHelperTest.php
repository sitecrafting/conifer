<?php

/**
 * Test the Conifer\Twig\TermHelper class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit;

use Conifer\Site;
use Conifer\Twig\NumberHelper;
use Conifer\Twig\TermHelper;
use Timber\Term;

class TermHelperTest extends Base
{
    private ?TermHelper $helper = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->helper = new TermHelper();
    }

    public function test_get_filters_returns_expected_filters()
    {
        $filters = $this->helper->get_filters();
        $this->assertArrayHasKey('term_item_class', $filters);
        $this->assertIsCallable($filters['term_item_class']);
    }

    public function test_get_functions_returns_empty_array()
    {
        $functions = $this->helper->get_functions();
        $this->assertIsArray($functions);
        $this->assertEmpty($functions);
    }

    public function test_term_item_class_returns_current_menu_item_class_for_current_term()
    {
        $term = $this->createMock(Term::class);
        $term->ID = 1;
        $currentTerm = $this->createMock(Term::class);
        $currentTerm->ID = 1;

        $class = $this->helper->term_item_class($term, $currentTerm);
        $this->assertEquals('current-menu-item', $class);
    }

    public function test_term_item_class_returns_no_class_for_current_term_with_different_ids()
    {
        $term = $this->createMock(Term::class);
        $term->ID = 1;
        $currentTerm = $this->createMock(Term::class);
        $currentTerm->ID = 2;

        $class = $this->helper->term_item_class($term, $currentTerm);
        $this->assertEquals('', $class);
    }

    public function test_term_item_class_returns_no_class_for_non_term_current_post_or_archive()
    {
        $term = $this->createMock(Term::class);
        $term->ID = 1;
        $currentPost = $this->createMock(Site::class); // Not a Term instance, basically anything else would work

        $class = $this->helper->term_item_class($term, $currentPost);
        $this->assertEquals('', $class);
    }
}
