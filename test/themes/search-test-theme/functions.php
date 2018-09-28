<?php

use Conifer\Post\Post;
use Conifer\Site;

$site = new Site();
$site->configure(function () {
  Post::include_meta_fields_in_search([
    [
      'fields' => [
        'hello',
        ['key' => '%bye', 'key_compare' => 'LIKE'],
      ],
    ]
  ]);
});
