<?php

/**
 * Test the Conifer\Post\HasTerms trait
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */


namespace Conifer\Unit\Post;

use Conifer\Unit\Base;
use Conifer\Unit\Support\Person;
use Timber\Term;
use WP_Mock;

class HasTermsTest extends Base
{
    public function setUp(): void
    {
        parent::setUp();
    }

    // ---------------------------------------------------------------------------
    // Trait method presence
    // ---------------------------------------------------------------------------

    public function test_does_post_object_have_terms_methods()
    {
        $this->assertTrue(method_exists(Person::class, 'get_all_grouped_by_term'));
        $this->assertTrue(method_exists(Person::class, 'register_taxonomy'));
        $this->assertTrue(method_exists(Person::class, 'count_statuses_toward_term_count'));
    }

    // ---------------------------------------------------------------------------
    // register_taxonomy()
    // ---------------------------------------------------------------------------

    public function test_register_taxonomy_calls_wp_register_taxonomy()
    {
        WP_Mock::userFunction('register_taxonomy', [
            'times' => 1,
            'args'  => ['sign', 'person', \WP_Mock\Functions::type('array')],
        ]);

        Person::register_taxonomy('sign');

        $this->assertTrue(true); // If we got here, the function was called as expected
    }

    public function test_register_taxonomy_infers_singular_label_from_name()
    {
        $capturedOptions = null;

        WP_Mock::userFunction('register_taxonomy', [
            'times'  => 1,
            'return' => function ($name, $postType, $options) use (&$capturedOptions) {
                $capturedOptions = $options;
            },
        ]);

        Person::register_taxonomy('sign');

        $this->assertEquals('Sign', $capturedOptions['labels']['singular_name']);
    }

    public function test_register_taxonomy_infers_plural_label_from_singular()
    {
        $capturedOptions = null;

        WP_Mock::userFunction('register_taxonomy', [
            'times'  => 1,
            'return' => function ($name, $postType, $options) use (&$capturedOptions) {
                $capturedOptions = $options;
            },
        ]);

        Person::register_taxonomy('sign');

        $this->assertEquals('Signs', $capturedOptions['labels']['name']);
    }

    public function test_register_taxonomy_respects_explicit_plural_label()
    {
        $capturedOptions = null;

        WP_Mock::userFunction('register_taxonomy', [
            'times'  => 1,
            'return' => function ($name, $postType, $options) use (&$capturedOptions) {
                $capturedOptions = $options;
            },
        ]);

        Person::register_taxonomy('sign', ['plural_label' => 'Omens']);

        $this->assertEquals('Omens', $capturedOptions['labels']['name']);
        $this->assertEquals('Omens', $capturedOptions['labels']['menu_name']);
    }

    public function test_register_taxonomy_respects_explicit_singular_label()
    {
        $capturedOptions = null;

        WP_Mock::userFunction('register_taxonomy', [
            'times'  => 1,
            'return' => function ($name, $postType, $options) use (&$capturedOptions) {
                $capturedOptions = $options;
            },
        ]);

        Person::register_taxonomy('sign', [
            'labels' => ['singular_name' => 'Omen'],
        ]);

        $this->assertEquals('Omen', $capturedOptions['labels']['singular_name']);
    }

    public function test_register_taxonomy_strips_plural_label_from_passed_options()
    {
        $capturedOptions = null;

        WP_Mock::userFunction('register_taxonomy', [
            'times'  => 1,
            'return' => function ($name, $postType, $options) use (&$capturedOptions) {
                $capturedOptions = $options;
            },
        ]);

        Person::register_taxonomy('sign', ['plural_label' => 'Omens']);

        $this->assertArrayNotHasKey('plural_label', $capturedOptions);
    }

    public function test_register_taxonomy_infers_labels_from_singular_and_plural()
    {
        $capturedOptions = null;

        WP_Mock::userFunction('register_taxonomy', [
            'times'  => 1,
            'return' => function ($name, $postType, $options) use (&$capturedOptions) {
                $capturedOptions = $options;
            },
        ]);

        Person::register_taxonomy('sign', ['plural_label' => 'Signs']);

        $labels = $capturedOptions['labels'];
        $this->assertEquals('All Signs', $labels['all_items']);
        $this->assertEquals('Edit Sign', $labels['edit_item']);
        $this->assertEquals('View Sign', $labels['view_item']);
        $this->assertEquals('Add New Sign', $labels['add_new_item']);
        $this->assertEquals('Search Signs', $labels['search_items']);
        $this->assertEquals('No Signs found', $labels['not_found']);
    }

    public function test_register_taxonomy_omits_post_type_when_flag_set()
    {
        $capturedPostType = 'not-set';

        WP_Mock::userFunction('register_taxonomy', [
            'times'  => 1,
            'return' => function ($name, $postType, $options) use (&$capturedPostType) {
                $capturedPostType = $postType;
            },
        ]);

        Person::register_taxonomy('sign', [], true);

        $this->assertNull($capturedPostType);
    }

    public function test_register_taxonomy_uses_post_type_by_default()
    {
        $capturedPostType = null;

        WP_Mock::userFunction('register_taxonomy', [
            'times'  => 1,
            'return' => function ($name, $postType, $options) use (&$capturedPostType) {
                $capturedPostType = $postType;
            },
        ]);

        Person::register_taxonomy('sign');

        $this->assertEquals('person', $capturedPostType);
    }

    // ---------------------------------------------------------------------------
    // get_all_grouped_by_term()
    // ---------------------------------------------------------------------------

    public function test_get_all_grouped_by_term_returns_empty_array_when_no_terms_have_posts()
    {
        $mockTerm = $this->createMockTerm(1, 'sign');
        $mockTerm->method('posts')->willReturn([]);

        $timber = \Mockery::mock('alias:Timber\Timber');
        $timber->shouldReceive('get_terms')->once()->andReturn([$mockTerm]);

        $result = Person::get_all_grouped_by_term('sign');

        $this->assertEquals([], $result);
    }

    public function test_get_all_grouped_by_term_groups_posts_under_their_term()
    {
        $mockPost1 = $this->getMockBuilder(\Timber\Post::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $mockTerm = $this->createMockTerm(1, 'sign');
        $mockTerm->method('posts')->willReturn([$mockPost1]);

        $timber = \Mockery::mock('alias:Timber\Timber');
        $timber->shouldReceive('get_terms')->once()->andReturn([$mockTerm]);

        $result = Person::get_all_grouped_by_term('sign');

        $this->assertCount(1, $result);
        $this->assertSame($mockTerm, $result[0]['term']);
        $this->assertSame([$mockPost1], $result[0]['posts']);
    }

    public function test_get_all_grouped_by_term_skips_terms_with_no_posts()
    {
        $termWithPosts    = $this->createMockTerm(1, 'sign');
        $termWithoutPosts = $this->createMockTerm(2, 'sign');

        $mockPost = $this->getMockBuilder(\Timber\Post::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $termWithPosts->method('posts')->willReturn([$mockPost]);
        $termWithoutPosts->method('posts')->willReturn([]);

        $timber = \Mockery::mock('alias:Timber\Timber');
        $timber->shouldReceive('get_terms')->once()->andReturn([$termWithPosts, $termWithoutPosts]);

        $result = Person::get_all_grouped_by_term('sign');

        $this->assertCount(1, $result);
        $this->assertSame($termWithPosts, $result[0]['term']);
    }

    public function test_get_all_grouped_by_term_accepts_explicit_term_list()
    {
        // When terms are passed explicitly, Timber::get_terms() should NOT be called
        $timber = \Mockery::mock('alias:Timber\Timber');
        $timber->shouldReceive('get_terms')->never();

        $mockPost = $this->getMockBuilder(\Timber\Post::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Pre-built Timber\Term objects are passed through without calling Timber::get_term()
        $mockTerm = $this->createMockTerm(1, 'sign');
        $mockTerm->method('posts')->willReturn([$mockPost]);

        $result = Person::get_all_grouped_by_term('sign', [$mockTerm]);

        $this->assertCount(1, $result);
        $this->assertSame($mockTerm, $result[0]['term']);
    }

    public function test_get_all_grouped_by_term_converts_term_ids_via_timber_get_term()
    {
        $mockPost = $this->getMockBuilder(\Timber\Post::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $mockTerm = $this->createMockTerm(42, 'sign');
        $mockTerm->method('posts')->willReturn([$mockPost]);

        $timber = \Mockery::mock('alias:Timber\Timber');
        $timber->shouldReceive('get_term')->once()->with(42)->andReturn($mockTerm);

        $result = Person::get_all_grouped_by_term('sign', [42]);

        $this->assertCount(1, $result);
        $this->assertSame($mockTerm, $result[0]['term']);
    }

    // ---------------------------------------------------------------------------
    // count_statuses_toward_term_count()
    // ---------------------------------------------------------------------------

    public function test_count_statuses_toward_term_count_updates_wpdb_with_post_count()
    {
        global $wpdb;

        // Set up a mock $wpdb
        $wpdb = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['update'])
            ->getMock();
        $wpdb->term_taxonomy = 'wp_term_taxonomy';

        $mockPost = $this->getMockBuilder(\Timber\Post::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $mockTerm = $this->createMockTerm(7, 'sign');
        $mockTerm->term_taxonomy_id = 99;
        $mockTerm->method('posts')->willReturn([$mockPost]);

        $wpdb->expects($this->once())
            ->method('update')
            ->with(
                'wp_term_taxonomy',
                ['count' => 1],
                ['term_taxonomy_id' => 99]
            );

        Person::count_statuses_toward_term_count($mockTerm, ['draft']);
    }

    public function test_count_statuses_toward_term_count_skips_update_when_no_posts()
    {
        global $wpdb;

        $wpdb = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['update'])
            ->getMock();
        $wpdb->term_taxonomy = 'wp_term_taxonomy';

        $mockTerm = $this->createMockTerm(7, 'sign');
        $mockTerm->term_taxonomy_id = 99;
        $mockTerm->method('posts')->willReturn(null); // non-array return skips update

        $wpdb->expects($this->never())->method('update');

        Person::count_statuses_toward_term_count($mockTerm, ['draft']);
    }

    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    private function createMockTerm(int $termId, string $taxonomy): Term
    {
        $term = $this->getMockBuilder(Term::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['posts'])
            ->getMock();

        $term->term_id  = $termId;
        $term->taxonomy = $taxonomy;

        return $term;
    }
}
