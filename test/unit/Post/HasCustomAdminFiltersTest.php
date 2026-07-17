<?php

/**
 * Test the Conifer\Post\HasCustomAdminFilters trait
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */


namespace Conifer\Unit\Post;

use Conifer\Unit\Base;
use Conifer\Unit\Support\HasCustomAdminFiltersPostDouble;
use Conifer\Unit\Support\Person;
use WP_Query;
use WP_Term;
use WP_Mock;

class HasCustomAdminFiltersTest extends Base
{
    private mixed $originalPostType = null;

    private mixed $originalPagenow = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->originalPostType = $GLOBALS['post_type'] ?? null;
        $this->originalPagenow  = $GLOBALS['pagenow'] ?? null;

        HasCustomAdminFiltersPostDouble::resetRenderedFilters();
    }

    public function tearDown(): void
    {
        $GLOBALS['post_type'] = $this->originalPostType;
        $GLOBALS['pagenow']   = $this->originalPagenow;

        parent::tearDown();
    }

    // ---------------------------------------------------------------------------
    // Trait method presence
    // ---------------------------------------------------------------------------

    public function test_does_post_object_have_custom_admin_filter_methods()
    {
        $this->assertTrue(method_exists(Person::class, 'add_admin_filter'));
        $this->assertTrue(method_exists(Person::class, 'add_taxonomy_admin_filter'));
    }

    // ---------------------------------------------------------------------------
    // add_admin_filter()
    // ---------------------------------------------------------------------------

    public function test_add_admin_filter_registers_query_var_filter()
    {
        HasCustomAdminFiltersPostDouble::add_admin_filter('sign', ['' => 'Any Sign']);

        $queryVarsFilter = $this->getAddedHookCallback('filter', 'query_vars');
        $vars            = $queryVarsFilter(['post_type']);

        $this->assertContains('sign', $vars);
    }

    public function test_add_admin_filter_registers_restrict_manage_posts_action()
    {
        HasCustomAdminFiltersPostDouble::add_admin_filter('sign', ['' => 'Any Sign']);

        $this->assertTrue($this->hasAddedHook('action', 'restrict_manage_posts'));

        $callback = $this->getAddedHookCallback('action', 'restrict_manage_posts');
        $this->assertIsCallable($callback);
    }

    public function test_add_admin_filter_renders_select_when_filtering_is_allowed()
    {
        $GLOBALS['post_type'] = 'person';
        $GLOBALS['pagenow']   = 'edit.php';

        WP_Mock::userFunction('get_query_var', [
            'return' => 'aries',
        ]);

        HasCustomAdminFiltersPostDouble::add_admin_filter('sign', [
            '' => 'Any Sign',
            'aries' => 'Aries',
        ]);

        $renderFilter = $this->getAddedHookCallback('action', 'restrict_manage_posts');
        $renderFilter();

        $rendered = HasCustomAdminFiltersPostDouble::getRenderedFilters();
        $this->assertCount(1, $rendered);
        $this->assertEquals('sign', $rendered[0]['name']);
        $this->assertEquals('aries', $rendered[0]['filtered_value']);
        $this->assertEquals('Aries', $rendered[0]['options']['aries']);
    }

    public function test_add_admin_filter_does_not_render_select_when_filtering_is_not_allowed()
    {
        $GLOBALS['post_type'] = 'post';
        $GLOBALS['pagenow']   = 'edit.php';

        WP_Mock::userFunction('get_query_var', [
            'return' => 'aries',
        ]);

        HasCustomAdminFiltersPostDouble::add_admin_filter('sign', [
            '' => 'Any Sign',
            'aries' => 'Aries',
        ]);

        $renderFilter = $this->getAddedHookCallback('action', 'restrict_manage_posts');
        $renderFilter();

        $this->assertCount(0, HasCustomAdminFiltersPostDouble::getRenderedFilters());
    }

    public function test_add_admin_filter_defaults_to_taxonomy_terms_when_options_are_empty()
    {
        $GLOBALS['post_type'] = 'person';
        $GLOBALS['pagenow']   = 'edit.php';

        WP_Mock::userFunction('taxonomy_exists', [
            'args' => ['sign'],
            'return' => true,
        ]);

        WP_Mock::userFunction('get_taxonomy', [
            'args' => ['sign'],
            'return' => (object) ['name' => 'sign'],
        ]);

        WP_Mock::userFunction('get_taxonomy_labels', [
            'return' => (object) ['singular_name' => 'Sign'],
        ]);

        WP_Mock::userFunction('get_terms', [
            'args' => [['taxonomy' => 'sign']],
            'return' => [
                $this->createMockWpTerm('aries', 'Aries'),
                $this->createMockWpTerm('taurus', 'Taurus'),
            ],
        ]);

        WP_Mock::userFunction('get_query_var', [
            'return' => '',
        ]);

        HasCustomAdminFiltersPostDouble::add_admin_filter('sign');

        $renderFilter = $this->getAddedHookCallback('action', 'restrict_manage_posts');
        $renderFilter();

        $rendered = HasCustomAdminFiltersPostDouble::getRenderedFilters();
        $this->assertCount(1, $rendered);
        $this->assertEquals('Any Sign', $rendered[0]['options']['']);
        $this->assertEquals('Aries', $rendered[0]['options']['aries']);
        $this->assertEquals('Taurus', $rendered[0]['options']['taurus']);
    }

    public function test_add_admin_filter_registers_pre_get_posts_when_query_modifier_is_callable()
    {
        HasCustomAdminFiltersPostDouble::add_admin_filter('sign', [], function () {});

        $this->assertTrue($this->hasAddedHook('action', 'pre_get_posts'));
    }

    public function test_add_admin_filter_does_not_register_pre_get_posts_without_query_modifier()
    {
        HasCustomAdminFiltersPostDouble::add_admin_filter('sign');

        $this->assertFalse($this->hasAddedHook('action', 'pre_get_posts'));
    }

    public function test_pre_get_posts_callback_calls_query_modifier_with_selected_value()
    {
        $GLOBALS['post_type'] = 'person';
        $GLOBALS['pagenow']   = 'edit.php';

        WP_Mock::userFunction('get_query_var', [
            'return' => 'aries',
        ]);

        $capturedQuery = null;
        $capturedValue = null;

        HasCustomAdminFiltersPostDouble::add_admin_filter('sign', [], function (WP_Query $query, string $value) use (&$capturedQuery, &$capturedValue) {
            $capturedQuery = $query;
            $capturedValue = $value;
        });

        /** @var WP_Query $query */
        $query = $this->getMockBuilder(WP_Query::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $query->query_vars = ['post_type' => 'person'];

        $modifyQuery = $this->getAddedHookCallback('action', 'pre_get_posts');
        $modifyQuery($query);

        $this->assertSame($query, $capturedQuery);
        $this->assertEquals('aries', $capturedValue);
    }

    public function test_pre_get_posts_callback_skips_query_modifier_when_not_querying_by_filter()
    {
        $GLOBALS['post_type'] = 'person';
        $GLOBALS['pagenow']   = 'edit.php';

        WP_Mock::userFunction('get_query_var', [
            'return' => '',
        ]);

        $calls = 0;

        HasCustomAdminFiltersPostDouble::add_admin_filter('sign', [], function () use (&$calls) {
            $calls++;
        });

        /** @var WP_Query $query */
        $query = $this->getMockBuilder(WP_Query::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $query->query_vars = ['post_type' => 'person'];

        $modifyQuery = $this->getAddedHookCallback('action', 'pre_get_posts');
        $modifyQuery($query);

        $this->assertEquals(0, $calls);
    }

    // ---------------------------------------------------------------------------
    // add_taxonomy_admin_filter()
    // ---------------------------------------------------------------------------

    public function test_add_taxonomy_admin_filter_builds_taxonomy_options_and_renders_select()
    {
        $GLOBALS['post_type'] = 'person';
        $GLOBALS['pagenow']   = 'edit.php';

        WP_Mock::userFunction('get_taxonomy', [
            'args' => ['sign'],
            'return' => (object) ['name' => 'sign'],
        ]);

        WP_Mock::userFunction('get_taxonomy_labels', [
            'return' => (object) ['singular_name' => 'Sign'],
        ]);

        WP_Mock::userFunction('get_terms', [
            'args' => ['sign'],
            'return' => [
                $this->createMockWpTerm('aries', 'Aries'),
                $this->createMockWpTerm('taurus', 'Taurus'),
            ],
        ]);

        WP_Mock::userFunction('get_query_var', [
            'args' => ['sign'],
            'return' => 'taurus',
        ]);

        HasCustomAdminFiltersPostDouble::add_taxonomy_admin_filter('sign');

        $this->assertTrue($this->hasAddedHook('filter', 'query_vars'));
        $this->assertTrue($this->hasAddedHook('action', 'restrict_manage_posts'));

        $renderFilter = $this->getAddedHookCallback('action', 'restrict_manage_posts');
        $renderFilter();

        $rendered = HasCustomAdminFiltersPostDouble::getRenderedFilters();
        $this->assertCount(1, $rendered);
        $this->assertEquals('Any Sign', $rendered[0]['options']['']);
        $this->assertEquals('Aries', $rendered[0]['options']['aries']);
        $this->assertEquals('Taurus', $rendered[0]['options']['taurus']);
        $this->assertEquals('taurus', $rendered[0]['filtered_value']);
    }

    // ---------------------------------------------------------------------------
    // Protected trait methods (through test double)
    // ---------------------------------------------------------------------------

    public function test_allow_custom_filtering_returns_true_on_matching_edit_screen()
    {
        $GLOBALS['post_type'] = 'person';
        $GLOBALS['pagenow']   = 'edit.php';

        $this->assertTrue(HasCustomAdminFiltersPostDouble::allow_custom_filtering_public());
    }

    public function test_allow_custom_filtering_returns_false_outside_matching_edit_screen()
    {
        $GLOBALS['post_type'] = 'page';
        $GLOBALS['pagenow']   = 'edit.php';

        $this->assertFalse(HasCustomAdminFiltersPostDouble::allow_custom_filtering_public());
    }

    public function test_querying_by_custom_filter_returns_true_when_filter_is_active()
    {
        $GLOBALS['post_type'] = 'person';
        $GLOBALS['pagenow']   = 'edit.php';

        WP_Mock::userFunction('get_query_var', [
            'args' => ['sign'],
            'return' => 'aries',
        ]);

        /** @var WP_Query $query */
        $query = $this->getMockBuilder(WP_Query::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $query->query_vars = ['post_type' => 'person'];

        $this->assertTrue(
            HasCustomAdminFiltersPostDouble::querying_by_custom_filter_public('sign', $query)
        );
    }

    public function test_querying_by_custom_filter_returns_false_when_filter_value_is_empty()
    {
        $GLOBALS['post_type'] = 'person';
        $GLOBALS['pagenow']   = 'edit.php';

        WP_Mock::userFunction('get_query_var', [
            'args' => ['sign'],
            'return' => '',
        ]);

        /** @var WP_Query $query */
        $query = $this->getMockBuilder(WP_Query::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $query->query_vars = ['post_type' => 'person'];

        $this->assertFalse(
            HasCustomAdminFiltersPostDouble::querying_by_custom_filter_public('sign', $query)
        );
    }

    public function test_get_taxonomy_label_returns_singular_name()
    {
        WP_Mock::userFunction('get_taxonomy', [
            'args' => ['sign'],
            'return' => (object) ['name' => 'sign'],
        ]);

        WP_Mock::userFunction('get_taxonomy_labels', [
            'return' => (object) ['singular_name' => 'Sign'],
        ]);

        $this->assertEquals('Sign', HasCustomAdminFiltersPostDouble::get_taxonomy_label_public('sign'));
    }

    public function test_get_taxonomy_label_returns_empty_string_when_singular_name_is_missing()
    {
        WP_Mock::userFunction('get_taxonomy', [
            'args' => ['sign'],
            'return' => (object) ['name' => 'sign'],
        ]);

        WP_Mock::userFunction('get_taxonomy_labels', [
            'return' => (object) [],
        ]);

        $this->assertEquals('', HasCustomAdminFiltersPostDouble::get_taxonomy_label_public('sign'));
    }

    private function createMockWpTerm(string $slug, string $name): WP_Term
    {
        /** @var WP_Term $term */
        $term = $this->getMockBuilder(WP_Term::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $term->slug = $slug;
        $term->name = $name;

        return $term;
    }

    private function getAddedHookCallback(string $type, string $hookName): callable
    {
        $callbacks = $this->getAddedHooks();

        $callbackKey = "{$type}::{$hookName}";
        $this->assertArrayHasKey($callbackKey, $callbacks);

        $hookedCallbackReflection = new \ReflectionClass($callbacks[$callbackKey]);
        $callbackProperty = $hookedCallbackReflection->getProperty('callback');
        $callback = $callbackProperty->getValue($callbacks[$callbackKey]);

        $this->assertIsCallable($callback);

        return $callback;
    }

    private function hasAddedHook(string $type, string $hookName): bool
    {
        $callbacks = $this->getAddedHooks();
        $callbackKey = "{$type}::{$hookName}";

        return array_key_exists($callbackKey, $callbacks);
    }

    private function getAddedHooks(): array
    {
        $wpMock = new \ReflectionClass(WP_Mock::class);
        $eventManagerProperty = $wpMock->getProperty('event_manager');
        $eventManager = $eventManagerProperty->getValue();

        $eventManagerReflection = new \ReflectionClass($eventManager);
        $callbacksProperty = $eventManagerReflection->getProperty('callbacks');

        return $callbacksProperty->getValue($eventManager) ?? [];
    }
}
