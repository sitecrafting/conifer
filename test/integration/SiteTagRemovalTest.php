<?php

/**
 * Test tag removal helpers on the Conifer\Site class.
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @package Conifer
 */

namespace Conifer\Integration;

class SiteTagRemovalTest extends Base
{
    private const POST_TYPE_ONE = 'conifer_tag_test_one';
    private const POST_TYPE_TWO = 'conifer_tag_test_two';
    private const EXCLUDED_POST_TYPE = 'tribe_events';

    /**
     * Post types associated with post_tag before each test.
     *
     * @var string[]
     */
    private array $postTypesWithTags = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->postTypesWithTags = array_filter(
            get_post_types([], 'names'),
            function (string $postType): bool {
                return is_object_in_taxonomy($postType, 'post_tag');
            }
        );

        register_post_type(self::POST_TYPE_ONE, [
            'public'     => true,
            'taxonomies' => ['post_tag'],
        ]);
        register_post_type(self::POST_TYPE_TWO, [
            'public'     => true,
            'taxonomies' => ['post_tag'],
        ]);
        register_post_type(self::EXCLUDED_POST_TYPE, [
            'public'     => true,
            'taxonomies' => ['post_tag'],
        ]);
    }

    public function tearDown(): void
    {
        foreach ([self::POST_TYPE_ONE, self::POST_TYPE_TWO, self::EXCLUDED_POST_TYPE] as $postType) {
            unregister_post_type($postType);
        }

        foreach ($this->postTypesWithTags as $postType) {
            register_taxonomy_for_object_type('post_tag', $postType);
        }

        parent::tearDown();
    }

    public function test_disable_tags_for_post_types_removes_tags_only_from_specified_post_types()
    {
        $this->site->disable_tags_for_post_types([self::POST_TYPE_ONE]);

        $this->assertFalse(is_object_in_taxonomy(self::POST_TYPE_ONE, 'post_tag'));
        $this->assertTrue(is_object_in_taxonomy(self::POST_TYPE_TWO, 'post_tag'));
    }

    public function test_disable_tags_excludes_tribe_events_by_default()
    {
        $this->site->disable_tags();

        $this->assertFalse(is_object_in_taxonomy(self::POST_TYPE_ONE, 'post_tag'));
        $this->assertTrue(is_object_in_taxonomy(self::EXCLUDED_POST_TYPE, 'post_tag'));
    }

    public function test_disable_tags_honors_custom_excluded_post_types()
    {
        $this->site->disable_tags([self::POST_TYPE_ONE]);

        $this->assertTrue(is_object_in_taxonomy(self::POST_TYPE_ONE, 'post_tag'));
        $this->assertFalse(is_object_in_taxonomy(self::POST_TYPE_TWO, 'post_tag'));
    }
}
