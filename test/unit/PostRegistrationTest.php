<?php

/**
 * Test syntax sugar for registering custom post types
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

declare(strict_types=1);

namespace Conifer\Unit;

use Conifer\Post\Post;
use Conifer\Unit\Support\Person;
use WP_Mock;
use WP_Mock\Functions;
use WP_Term;

class PostRegistrationTest extends Base {
    public function test_register_type(): void {
        /* https://codex.wordpress.org/Function_Reference/register_post_type */
        WP_Mock::userFunction('register_post_type', [
        'times' => 1,
        'args'  => [
        'person',
        [
            'labels' => [
            'name'                  => 'People',
            'singular_name'         => 'Person',
            'add_new_item'          => 'Onboard New Person',
            'edit_item'             => 'Edit Person',
            'new_item'              => 'New Person',
            'view_item'             => 'View Person',
            'view_items'            => 'View People',
            'search_items'          => 'Search People',
            'not_found'             => 'No People found',
            'not_found_in_trash'    => 'No People found in trash',
            'all_items'             => 'All People',
            'archives'              => 'Person Archives',
            'attributes'            => 'Person Attributes',
            'insert_into_item'      => 'Insert into description',
            'uploaded_to_this_item' => 'Uploaded to this Person',
            ],
        ],
        ],
        ]);

        $this->assertNull(Person::register_type());
    }

    public function test_register_taxonomy(): void {
        /* https://codex.wordpress.org/Function_Reference/register_taxonomy */
        WP_Mock::userFunction('register_taxonomy', [
        'times' => 1,
        'args'  => [
        'sign',
        'person',
        [
            'labels' => [
            'name'                       => 'Signs',
            'singular_name'              => 'Sign',
            'menu_name'                  => 'Signs',
            'all_items'                  => 'All Astrological Signs',
            'edit_item'                  => 'Edit Sign',
            'view_item'                  => 'View Sign',
            'update_item'                => 'Update Sign',
            'add_new_item'               => 'Add New Sign',
            'new_item_name'              => 'New Sign Name',
            'parent_item'                => 'Parent Sign',
            'parent_item_colon'          => 'Parent Sign:',
            'search_items'               => 'Search Signs',
            'popular_items'              => 'Popular Signs',
            'separate_items_with_commas' => 'Separate Signs with commas',
            'add_or_remove_items'        => 'Add or remove Signs',
            'choose_from_most_used'      => 'Choose from the most used Signs',
            'not_found'                  => 'No Signs found',
            'back_to_items'              => '← Back to Signs',
            ],
        ],
        ],
        ]);

        $this->assertNull(Person::register_taxonomy('sign', [
        'plural_label' => 'Signs',
        'labels'       => [ 'all_items' => 'All Astrological Signs' ],
        ]));
    }

    public function test_register_taxonomy_without_explicit_options(): void {
        /* https://codex.wordpress.org/Function_Reference/register_taxonomy */
        WP_Mock::userFunction('register_taxonomy', [
        'times' => 1,
        'args'  => [
        'sign',
        'person',
        [
            'labels' => [
            'name'                       => 'Signs',
            'singular_name'              => 'Sign',
            'menu_name'                  => 'Signs',
            'all_items'                  => 'All Astrological Signs',
            'edit_item'                  => 'Edit Sign',
            'view_item'                  => 'View Sign',
            'update_item'                => 'Update Sign',
            'add_new_item'               => 'Add New Sign',
            'new_item_name'              => 'New Sign Name',
            'parent_item'                => 'Parent Sign',
            'parent_item_colon'          => 'Parent Sign:',
            'search_items'               => 'Search Signs',
            'popular_items'              => 'Popular Signs',
            'separate_items_with_commas' => 'Separate Signs with commas',
            'add_or_remove_items'        => 'Add or remove Signs',
            'choose_from_most_used'      => 'Choose from the most used Signs',
            'not_found'                  => 'No Signs found',
            'back_to_items'              => '← Back to Signs',
            ],
        ],
        ],
        ]);

        $this->assertNull(Person::register_taxonomy('sign', [
        'plural_label' => 'Signs',
        'labels'       => [ 'all_items' => 'All Astrological Signs' ],
        ]));
    }

    public function test_register_taxonomy_with_underscores(): void {
        /* https://codex.wordpress.org/Function_Reference/register_taxonomy */
        WP_Mock::userFunction('register_taxonomy', [
        'times' => 1,
        'args'  => [
        'personal_attribute',
        'person',
        [
            'labels' => [
            'name'                       => 'Personal Attributes',
            'singular_name'              => 'Personal Attribute',
            'menu_name'                  => 'Personal Attributes',
            'all_items'                  => 'All Personal Attributes',
            'edit_item'                  => 'Edit Personal Attribute',
            'view_item'                  => 'View Personal Attribute',
            'update_item'                => 'Update Personal Attribute',
            'add_new_item'               => 'Add New Personal Attribute',
            'new_item_name'              => 'New Personal Attribute Name',
            'parent_item'                => 'Parent Personal Attribute',
            'parent_item_colon'          => 'Parent Personal Attribute:',
            'search_items'               => 'Search Personal Attributes',
            'popular_items'              => 'Popular Personal Attributes',
            'separate_items_with_commas' => 'Separate Personal Attributes with commas',
            'add_or_remove_items'        => 'Add or remove Personal Attributes',
            'choose_from_most_used'      => 'Choose from the most used Personal Attributes',
            'not_found'                  => 'No Personal Attributes found',
            'back_to_items'              => '← Back to Personal Attributes',
            ],
        ],
        ],
        ]);

        $this->assertNull(Person::register_taxonomy('personal_attribute'));
    }

    public function test_register_taxonomy_omitting_post_type(): void {
        WP_Mock::userFunction('register_taxonomy', [
        'times' => 1,
        'args'  => [
        'sign',
        null,
        Functions::type('array'),
        ],
        ]);

        $this->assertNull(Person::register_taxonomy('sign', [], true));
    }
}
