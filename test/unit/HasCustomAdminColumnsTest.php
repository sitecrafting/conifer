<?php

/**
 * Test the Conifer\Post\HasCustomAdminColumns trait
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit;

use Conifer\Post\Post;
use Conifer\Unit\Support\HasCustomAdminColumnsPostDouble;
use Conifer\Unit\Support\Person;
use WP_Mock;

class HasCustomAdminColumnsTest extends Base
{
    public function setUp(): void
    {
        parent::setUp();
    }

    // ---------------------------------------------------------------------------
    // Trait method presence
    // ---------------------------------------------------------------------------

    public function test_does_post_object_have_custom_admin_column_methods()
    {
        $this->assertTrue(method_exists(Person::class, 'add_admin_column'));
    }

    // ---------------------------------------------------------------------------
    // add_admin_column()
    // ---------------------------------------------------------------------------

    public function test_add_admin_column_registers_hooks_for_custom_post_type()
    {
        HasCustomAdminColumnsPostDouble::add_admin_column('nickname', 'Nickname', function (): string {
            return 'Captain';
        });

        $this->assertTrue($this->hasAddedHook('filter', 'manage_person_posts_columns'));
        $this->assertTrue($this->hasAddedHook('action', 'manage_person_posts_custom_column'));
    }

    public function test_add_admin_column_registers_hooks_for_page_post_type()
    {
        AdminPageColumnsPostDouble::add_admin_column('subtitle', 'Subtitle', function (): string {
            return 'Hero';
        });

        $this->assertTrue($this->hasAddedHook('filter', 'manage_pages_columns'));
        $this->assertTrue($this->hasAddedHook('action', 'manage_pages_custom_column'));
    }

    public function test_add_admin_column_filter_adds_column_to_columns_array()
    {
        HasCustomAdminColumnsPostDouble::add_admin_column('nickname', 'Nickname', function (): string {
            return 'Captain';
        });

        $columnsFilter = $this->getAddedHookCallback('filter', 'manage_person_posts_columns');
        $columns       = $columnsFilter(['title' => 'Title']);

        $this->assertEquals('Title', $columns['title']);
        $this->assertEquals('Nickname', $columns['nickname']);
    }

    public function test_add_admin_column_action_outputs_value_for_matching_column()
    {
        HasCustomAdminColumnsPostDouble::add_admin_column('nickname', 'Nickname', function (int $id): string {
            return "Captain #{$id}";
        });

        $displayColumn = $this->getAddedHookCallback('action', 'manage_person_posts_custom_column');

        $this->expectOutputString('Captain #42');
        $displayColumn('nickname', 42);
    }

    public function test_add_admin_column_action_outputs_nothing_for_non_matching_column()
    {
        HasCustomAdminColumnsPostDouble::add_admin_column('nickname', 'Nickname', function (int $id): string {
            return "Captain #{$id}";
        });

        $displayColumn = $this->getAddedHookCallback('action', 'manage_person_posts_custom_column');

        $this->expectOutputString('');
        $displayColumn('not_nickname', 42);
    }

    public function test_add_admin_column_uses_default_post_meta_getter_when_callback_is_omitted()
    {
        AdminColumnsMetaPostDouble::add_admin_column('nickname', 'Nickname');

        $displayColumn = $this->getAddedHookCallback('action', 'manage_person_posts_custom_column');

        $this->expectOutputString('meta:nickname');
        $displayColumn('nickname', 123);
    }

    public function test_add_admin_column_uses_page_template_name_getter_for_wp_page_template()
    {
        WP_Mock::userFunction('get_page_templates', [
            'return' => ['Landing Page' => 'landing.php'],
        ]);

        WP_Mock::userFunction('get_post_meta', [
            'args' => [7, '_wp_page_template', true],
            'return' => 'landing.php',
        ]);

        AdminColumnsMetaPostDouble::add_admin_column('_wp_page_template', 'Template');

        $displayColumn = $this->getAddedHookCallback('action', 'manage_person_posts_custom_column');

        $this->expectOutputString('Landing Page');
        $displayColumn('_wp_page_template', 7);
    }

    // ---------------------------------------------------------------------------
    // Private trait helpers (through test double wrappers)
    // ---------------------------------------------------------------------------

    public function test_page_template_name_defaults_when_template_file_is_unknown()
    {
        WP_Mock::userFunction('get_page_templates', [
            'return' => ['Landing Page' => 'landing.php'],
        ]);

        WP_Mock::userFunction('get_post_meta', [
            'args' => [99, '_wp_page_template', true],
            'return' => 'missing.php',
        ]);

        $this->assertEquals('Default Template', HasCustomAdminColumnsPostDouble::page_template_name_public(99));
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

class AdminPageColumnsPostDouble extends Post
{
    public const POST_TYPE = 'page';

    public static function type_options(): array
    {
        return [];
    }
}

class AdminColumnsMetaPostDouble extends HasCustomAdminColumnsPostDouble
{
    public function __construct(int $id = 0) {}

    public function meta($field_name = '', $args = [])
    {
        return "meta:{$field_name}";
    }
}
