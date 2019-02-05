<?php

/**
 * Test syntax sugar for registering custom post types
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace ConiferTest;

use Conifer\Post\Post;
use ConiferTestSupport\Person;
use WP_Mock;
use WP_Mock\Functions;
use WP_Term;

class PostRegistrationTest extends Base {
  public function test_register_type() {
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

  public function test_register_taxonomy() {
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
      'labels'       => ['all_items' => 'All Astrological Signs'],
    ]));
  }

  public function test_register_taxonomy_omitting_post_type() {
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
