<?php

/**
 * Support class for testing CPT registration
 */

declare(strict_types=1);

namespace Conifer\Unit\Support;

use Conifer\Post\Post;

/**
 * Custom Person post type class
 */
class Person extends Post {
    const POST_TYPE = 'person';

    /**
     * Return type options.
     *
     * @return array<string, array<string, string>|string>
     */
    public static function type_options(): array {
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
