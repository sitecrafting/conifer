<?php

use Conifer\Post\Post;
use Conifer\Site;

$site = new Site();
$site->configure(function () {
  Post::configure_advanced_search([
    [
      'meta_fields' => [
        'hello',
        ['key' => '%bye', 'key_compare' => 'LIKE'],
      ],
    ]
  ]);
});
