<?php

/**
 * Test the Conifer\Post\SupportsAdvancedSearch trait
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit;

use Conifer\Unit\Support\Person;
use WP_Mock;
use WP_Query;

class SupportsAdvancedSearchTest extends Base
{
    private mixed $originalWpdb = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->originalWpdb = $GLOBALS['wpdb'] ?? null;
    }

    public function tearDown(): void
    {
        $GLOBALS['wpdb'] = $this->originalWpdb;

        parent::tearDown();
    }

    // ---------------------------------------------------------------------------
    // Trait method presence
    // ---------------------------------------------------------------------------

    public function test_does_post_object_have_advanced_search_methods()
    {
        $this->assertTrue(method_exists(Person::class, 'configure_advanced_search'));
    }

    // ---------------------------------------------------------------------------
    // configure_advanced_search()
    // ---------------------------------------------------------------------------

    public function test_configure_advanced_search_registers_posts_clauses_filter()
    {
        Person::configure_advanced_search($this->basicSearchConfig());

        $this->assertTrue($this->hasAddedHook('filter', 'posts_clauses'));
        $this->assertIsCallable($this->getAddedHookCallback('filter', 'posts_clauses'));
    }

    public function test_posts_clauses_callback_returns_original_clauses_when_not_search_query()
    {
        $this->mockWpdb();

        Person::configure_advanced_search($this->basicSearchConfig());

        $clauses = [
            'fields' => 'wp_posts.*',
            'join' => '',
            'where' => 'original-where',
        ];

        $query = $this->createSearchQueryDouble(false, ['alpha'], 'person');

        $filter = $this->getAddedHookCallback('filter', 'posts_clauses');
        $result = $filter($clauses, $query);

        $this->assertSame($clauses, $result);
    }

    public function test_posts_clauses_callback_returns_original_clauses_when_search_terms_are_missing()
    {
        $this->mockWpdb();

        Person::configure_advanced_search($this->basicSearchConfig());

        $clauses = [
            'fields' => 'wp_posts.*',
            'join' => '',
            'where' => 'original-where',
        ];

        $query = $this->createSearchQueryDouble(true, [], 'person');

        $filter = $this->getAddedHookCallback('filter', 'posts_clauses');
        $result = $filter($clauses, $query);

        $this->assertSame($clauses, $result);
    }

    public function test_posts_clauses_callback_returns_original_clauses_when_post_type_has_no_matching_config()
    {
        $this->mockWpdb();

        Person::configure_advanced_search($this->basicSearchConfig());

        $clauses = [
            'fields' => 'wp_posts.*',
            'join' => '',
            'where' => 'original-where',
        ];

        $query = $this->createSearchQueryDouble(true, ['alpha'], 'page');

        $filter = $this->getAddedHookCallback('filter', 'posts_clauses');
        $result = $filter($clauses, $query);

        $this->assertSame($clauses, $result);
    }

    public function test_posts_clauses_callback_adds_distinct_join_and_where_for_matching_search()
    {
        $this->mockWpdb();

        Person::configure_advanced_search([
            [
                'post_type' => ['person'],
                'meta_fields' => ['nickname', ['key' => 'bio', 'key_compare' => 'LIKE']],
                'post_status' => ['publish', 'draft'],
            ],
        ]);

        $clauses = [
            'fields' => 'wp_posts.*',
            'join' => ' JOIN existing_table ON (1=1) ',
            'where' => 'legacy-where',
        ];

        $query = $this->createSearchQueryDouble(true, ['alex'], 'person');

        $filter = $this->getAddedHookCallback('filter', 'posts_clauses');
        $result = $filter($clauses, $query);

        // Non-obvious: the trait replaces the WHERE clause entirely with its grouped search clauses.
        $this->assertStringStartsWith(' DISTINCT ', $result['fields']);
        $this->assertStringContainsString('LEFT JOIN wp_postmeta meta_search', $result['join']);
        $this->assertStringContainsString("wp_posts.post_type IN ('person')", $result['where']);
        $this->assertStringContainsString("wp_posts.post_status IN ('publish', 'draft')", $result['where']);
        $this->assertStringContainsString("meta_search.meta_key = 'nickname'", $result['where']);
        $this->assertStringContainsString("meta_search.meta_key LIKE 'bio'", $result['where']);
        $this->assertStringContainsString("meta_value LIKE '%alex%'", $result['where']);
    }

    public function test_posts_clauses_callback_supports_any_post_type_wildcard()
    {
        $this->mockWpdb();

        WP_Mock::userFunction('get_post_types', [
            'args' => [['public' => true], 'names'],
            'return' => ['person', 'page'],
        ]);

        // The config must list 'any' as a post_type so the trait's intersection check
        // matches queries that use the 'any' wildcard. The wildcard is then expanded to
        // all public post types when building the WHERE clause, but only if the config
        // match step succeeds first.
        Person::configure_advanced_search([
            [
                'post_type' => ['any'],
                'meta_fields' => ['nickname'],
                'post_status' => ['publish'],
            ],
        ]);

        $clauses = [
            'fields' => 'wp_posts.*',
            'join' => '',
            'where' => '',
        ];

        $query = $this->createSearchQueryDouble(true, ['alpha'], 'any');

        $filter = $this->getAddedHookCallback('filter', 'posts_clauses');
        $result = $filter($clauses, $query);

        $this->assertStringContainsString("wp_posts.post_type IN ('person', 'page')", $result['where']);
    }

    public function test_posts_clauses_callback_omits_post_status_clause_for_any_status()
    {
        $this->mockWpdb();

        Person::configure_advanced_search([
            [
                'post_type' => ['person'],
                'meta_fields' => ['nickname'],
                'post_status' => 'any',
            ],
        ]);

        $clauses = [
            'fields' => 'wp_posts.*',
            'join' => '',
            'where' => '',
        ];

        $query = $this->createSearchQueryDouble(true, ['alpha'], 'person');

        $filter = $this->getAddedHookCallback('filter', 'posts_clauses');
        $result = $filter($clauses, $query);

        $this->assertStringNotContainsString('wp_posts.post_status IN', $result['where']);
    }

    public function test_posts_clauses_callback_normalizes_non_like_key_compare_to_equals()
    {
        $this->mockWpdb();

        Person::configure_advanced_search([
            [
                'post_type' => ['person'],
                'meta_fields' => [['key' => 'nickname', 'key_compare' => '!=']],
            ],
        ]);

        $clauses = [
            'fields' => 'wp_posts.*',
            'join' => '',
            'where' => '',
        ];

        $query = $this->createSearchQueryDouble(true, ['alpha'], 'person');

        $filter = $this->getAddedHookCallback('filter', 'posts_clauses');
        $result = $filter($clauses, $query);

        $this->assertStringContainsString("meta_search.meta_key = 'nickname'", $result['where']);
        $this->assertStringNotContainsString("meta_search.meta_key != 'nickname'", $result['where']);
    }

    private function createSearchQueryDouble(bool $isSearch, array $terms, string|array $postType): WP_Query
    {
        /** @var WP_Query $query */
        $query = $this->getMockBuilder(WP_Query::class)
            ->disableOriginalConstructor()
            ->addMethods(['is_search'])
            ->getMock();

        $query->query_vars = [
            'search_terms' => $terms,
            'post_type' => $postType,
        ];

        $query->method('is_search')->willReturn($isSearch);

        return $query;
    }

    private function mockWpdb(): void
    {
        $wpdb = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['prepare'])
            ->getMock();

        $wpdb->posts = 'wp_posts';
        $wpdb->postmeta = 'wp_postmeta';

        $wpdb->method('prepare')
            ->willReturnCallback(function (string $sql, string $value): string {
                return str_replace('%s', "'{$value}'", $sql);
            });

        $GLOBALS['wpdb'] = $wpdb;
    }

    private function basicSearchConfig(): array
    {
        return [[
            'post_type' => ['person'],
            'meta_fields' => ['nickname'],
            'post_status' => ['publish'],
        ]];
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
