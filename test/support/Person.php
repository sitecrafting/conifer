<?php

/**
 * Support class for testing CPT registration
 */

namespace Conifer\Unit\Support;

use Conifer\Post\Post;

/**
 * Custom Person post type class
 */
class Person extends Post {
  const POST_TYPE = 'person';

  public static function type_options() : array {
    return [
      'plural_label' => 'People',
      'labels'       => [
        'singular_name'    => 'Person',
        'add_new_item'     => 'Onboard New Person',
        'insert_into_item' => 'Insert into description', // avoid "Insert into person"
      ],
    ];
  }
}
