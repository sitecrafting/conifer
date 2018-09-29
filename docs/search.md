# Customizing Search

You can customize the criteria WordPress uses to match search results to include meta fields:

```php
register_post_type('flower');
register_post_status('in_season');

Post::configure_advanced_search([
  [
    'post_type' => ['flower'],
    'meta_fields' => [
      'color',
      ['key' => '%thorn%', 'key_compare' => 'LIKE'],
    ],
    'post_status' => ['in_season'],
  ],
  [
    'post_type' => ['page'],
    'meta_fields' => [
      'details',
    ],
  ],
]);
```

This tells WordPress search queries to compare not just `post_content`, `post_title`, and `post_excerpt`, but also:

* for the `flower` post type, match on:
  * `meta_value` for the `color` field; and
  * for any fields whose keys match the pattern `%thorn%` (e.g. `thorniness` or `has_thorns`)
  * for posts in `in_season` status **only**
* for the `page` type:
  * match on `meta_value` for the `details` field
  * for published posts only (this is the default)

More work is planned for the near future to make this even more powerful. Stay tuned!

> #### Warning::Calling `configure_advanced_search()` does not change how post types are registered
>
> You still need to make sure you register custom post types as "public." Conifer does not override this for you.