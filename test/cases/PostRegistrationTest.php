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

}
