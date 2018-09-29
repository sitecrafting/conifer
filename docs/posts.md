# Posts and Post Types

To leverage the power of Conifer, extend the `Conifer\Post\Post` class rather than the more generic `Timber\Post`. Since `Conifer\Post\Post` in turn extends `Timber\Post`, you get all the benefits of Timber's class, with some added functionality for querying posts of specific types, along with other goodies.

Conifer comes with a few built-in post classes that extend `Conifer\Post\Post`. These are:

* `BlogPost` for representing WP posts of type `post`

* `FrontPage` for representing the homepage of the site

* `Page` for representing WP posts of type `page`

## Getting started with Custom Post Types

There are couple things you need to do to get up and running with CPTs in Conifer:

### Define the `POST_TYPE` class constant

This tells Conifer what type of post you're defining, even for static methods (such as `create()`).

```php
use Conifer\Post\Post;

class Robot extends Post {
  const POST_TYPE = 'robot';

  // ...
}
```

Failing to define this constant may result in a `RuntimeException` being thrown later. The message should tell you exactly what you need to do.

### Register your Custom Post Type

Just like in vanilla WP, you need to register your CPT using `register_post_type`. You can of course do this directly in `functions.php`, but the convention we've found to be most helpful is to define a static `register` method on your CPT class, and call that method.

Building on the example above, our `Robot` class now looks like:

```php
use Conifer\Post\Post;

class Robot extends Post {
  const POST_TYPE = 'robot';

  public static function register() {
    register_post_type('robot', ['label' => 'Robots']);
  }
}
```

Meanwhile, over in `functions.php` land, don't forget to call your new `register` method!

```php
$site = new Conifer\Site();
$site->configure(function() {
	add_action('init', [Robot::class, 'register']);
});
```

This approach keeps the behavior of the `Robot` class/post type encapsulated and the high-level site configuration scannable.

## Checking for the existence of a post ID

You can check whether a post exists with a given ID *and an appropriate post_type* using the `Post::exists()` method. It uses [late static binding](https://secure.php.net/manual/en/language.oop5.late-static-bindings.php) on the `POST_TYPE` class constant to check the `post_type` of any posts it finds. That is, the `exists()` method will only return `true` when the `post_type` matches up with the `POST_TYPE` constant.

For example, let's say we have a `page` in the database:

```php
$pageId = wp_insert_post(['post_type' => 'page']);
\Conifer\Post\Page::exists($pageId); // -> true
\Conifer\Post\BlogPost::exists($pageId); // -> false
```

Of course, it'll also return `false` if we call it with an `ID` that does not exist at all, regardless of `post_type`:

```php
$badId = 9001;
get_post($badId); // -> null
\Conifer\Post\BlogPost::exists($badId); // -> false
\Conifer\Post\Page::exists($badId); // -> false
\Conifer\Post\Post::exists($badId); // -> false
```

### Checking for any type of post

You can call `exists()` on the abstract `Post` class to skip the `post_type` check and look for a post of *any* `post_type`:

```php
$pageId = wp_insert_post(['post_type' => 'page']);
$postId = wp_insert_post(['post_type' => 'post']);

\Conifer\Post\Post::exists($pageId); // -> true
\Conifer\Post\Post::exists($postId); // -> true

$badId = 9001;
get_post($badId); // -> null
\Conifer\Post\Post::exists($badId); // -> false
```

### Checking for custom posts

This works for custom posts, too, as long as your custom post class defines its own `POST_TYPE` contant:

```php
echo Robot::POST_TYPE; // -> outputs "robot"
$robot = Robot::create(['post_title' => 'Some robot or other']);
Robot::exists($robot->id); // -> true

$postId = wp_insert_post(['post_type' => 'post']);
Robot::exists($postId); // -> false
```

## Saving New Posts

Using our `Robot` class from above as an example, let's see how easy it is to create posts, even ones that implement a CPT, using `Post::create()`:

```php
$wall_e = Robot::create([
  'post_title' => 'WALL-E',
  'post_content' => 'Waaaaallll eeeeee! Eeeeevaaaaa!',
  'disposition' => 'friendly',
  'directive' => 'save the planet',
]);

$wall_e->get_field('disposition');
```

Note that we can mix in the custom `disposition` and `directive` fields with our arguments. Conifer filters the data intelligently, discerning which keys correspond to proper `wp_posts` columns, and which should become meta fields.

## Querying for custom post types

Conifer Post classes know how to instantiate themselves in query results. The static `get_all()` method will return an array of whichever subclass of `Post` was called, whether that's `BlogPost`, `Page`, or a CPT:

```php
$robots = Robot::get_all(['numberposts' => 3]);
// -> array of Robot instances
```

Contrast this to the `Timber::get_posts()` method, which we'd have to tell to return `Robot`s:

```php
$robots = Timber::get_posts(['numberposts' => 3], Robot::class);
```

Thanks to Conifer's use of [late static binding](https://secure.php.net/manual/en/language.oop5.late-static-bindings.php), we can omit this argument to `get_all()`.

## Getting Related Posts

Conifer provides the awesome `Post::get_related_by_taxonomy() ` method, which lets you query a specific post for posts of the same type that share its taxonomy terms. Helpers are provided for WordPress's built-in category and tag taxonomies, but you can use this method with any taxonomy, including custom ones.

Say we want to compare Robots on the basis of where they fall within the [uncanny valley](https://en.wikipedia.org/wiki/Uncanny_valley), or in other words how much they freak us out. Let's first declare an `eeriness_level` taxonomy to use as a coarse-grained metric:

```php
use Conifer\Post\Post;

class Robot extends Post {
  const POST_TYPE = 'robot';
  
  public static function register() {
    register_post_type('robot', ['label' => 'Robots']);
    register_taxonomy('eeriness_level', 'robot');
  }
}
// make sure to call `Robot::register()` in an `init` action callback!
```

You can set up several `eeriness_level` terms through the WP Admin UI, just as you would for any other taxonomy term. For example, say you set up the terms **Cute**, **Kinda Eerie**, and **Eerie af**. Furthermore, let's create a couple Robot posts called "WALL-E" and "EVE" in the database, and assign them each an eeriness level of **Cute**.

Now, you can query a `Robot` instance for others posts of type `robot` with the same `eeriness_level`:

```php
// single-robot.php
$robot = new Robot();
$similarlyEerieRobots = $robot->get_related_by_taxonomy('eeriness_level');
```

EVE will show up in WALL-E's related list, and vice versa, because they're both **Cute**.

### Built-in taxonomies

WordPress's built-in `category` and `post_tag` taxonomies get their own helpers in Conifer, which you can use on blog posts. (You can of course [extend those taxonomies](https://spicewp.com/add-categories-tags-pages-wordpress/) to organize pages or any other post type, but that's beyond the scope of Conifer's docs.)

```php
// single.php
$post = new BlogPost();
$relatedPosts = $post->get_related_posts_by_category();
$relatedPostsByTag = $post->get_related_posts_by_tag();
```

## Customizing Post Admin Columns

Conifer lets you easily add custom admin columns to your WP Admin post listing screen:

```php
use Conifer\Post\Post;

class Robot extends Post {
  const POST_TYPE = 'robot';

  public static function register() {
    register_post_type('robot', ['label' => 'Robots']);
    static::add_admin_column('disposition', 'Disposition');
  }
}
```

This adds a new **Disposition** column to the listing screen for Robot posts that displays each Robot's `disposition` from its meta data.

### Specifying the column value

If you want to display something more involved than a simple `meta_value`, you can specify a callback parameter to the `add_admin_column` method. Let's say you have some meta column called `beeps` but you don't care about the actual values - you just want to know how many `beeps` each Robot has:

```php
    static::add_admin_column('beep_count', 'Beep Count', function($id) {
      // count the beeps for this Robot.
      $robot = new static($id);
      $beeps = $robot->meta('beeps') ?: [];
      return count($beeps) ? 'None';
    });
```

This code tells Conifer to grab the `beeps` for each Robot, count them, and display the count (or "None" if the count is zero, or if no `beeps` value exists for any given Robot).

## Customizing Post Admin Filters

As with columns, Conifer lets you easily add custom *filters* to your WP Admin post listing screen. Say we want to extend the WP Admin to be able to filter Robots by the custom taxonomy `eeriness_level`:

```php
use Conifer\Post\Post;

class Robot extends Post {
  const POST_TYPE = 'robot';

  public static function register() {
    register_post_type('robot', ['label' => 'Robots']);
    register_taxonomy('eeriness_level', 'robot', [
      'labels' => ['name' => 'Eeriness Levels', 'singular_name' => 'Eeriness Level'],
    ]);

    static::add_admin_filter('eeriness_level');
  }
}
```

Conifer recognizes that `eeriness_level` is a taxonomy and queries its terms automatically, displaying them as options (along with an "Any Eeriness Level" option at the top, of course) in a new admin filter dropdown.

### Defining advanced filters

If you want to filter by some aspect of your posts that isn't a taxonomy, you can specify your own options and a callback for modifying the `WP_Query` instance in some way. Say you have a `beeps` meta field that has zero or more rows in the database:

```php
// ensure post ID=4 has zero beeps
delete_post_meta(4, 'beeps');
// add some beeps for post ID=3
add_post_meta(3, 'beeps', 'BEEP');
add_post_meta(3, 'beeps', 'BEEP!');
add_post_meta(3, 'beeps', 'BEEEEP!');
```

You can define an admin filter that queries by the presence of absence of `beeps` in the meta table, just by specifying how you want to modify the query when you look for it:

```php
    $options = [
      ''    => 'Beeps or not',
      'yes' => 'With beeps',
      'no'  => 'Without beeps',
    ];

    static::add_admin_filter('has_beeps', $options, function(
      WP_Query $query,
      string $beepsFilterValue
    ) {
      if ($beepsFilterValue === '') {
        // don't filter
        return;
      }

      $comparison = $beepsFilterValue === 'yes' ? 'EXISTS' : 'NOT EXISTS';

      $query->query_vars['meta_query'] = [
        [
          'key' => 'beeps',
          'compare' => $comparison,
        ],
      ];
    });
```

This displays a dropdown with the options **Beeps or not**, **With beeps**, and **Without beeps**. The callback passed as the third parameter runs when WordPress fires the `pre_get_posts` filter, and takes the current `WP_Query` object and the user-specified filter value as arguments. You can, of course, put any code you want in here, modifying `$query` in whatever way you choose.

## Custom columns/filters for existing post types

The static `register` method is just a Conifer convention for a place to put stuff about your post types. You don't *need* to define it any more than you need to define custom post types in the first place!

The custom admin column/filter functionality is defined in the `Conifer\Post\HasCustomAdminColumns` and `Conifer\Post\HasCustomAdminFilters` traits, respectively. **Both traits are included in the `Conifer\Post\Post` base class**. This means you can define custom columns and filters for existing post types, for example using Conifer's `Page` class:

```php
use Conifer\Post\Page;

register_taxonomy('specialty', 'page', [
  'labels' => ['name' => 'Specialties', 'singular_name' => 'Specialty'],
]);

Page::add_admin_column('specialties', 'Specialties', function($id) {
  $page = new Page($id);
  return implode(', ', $page->get_terms('specialty'));
});
Page::add_admin_filter('specialty');
```

Note that the `get_terms()` instance method returns `Timber\Term` objects, which implement their own `__toString()` method. That is, we don't even have to loop through the result of `get_terms()` to get each term's name because Timber does that for us. Thanks, Timber!

## Grouping by Term

The `Conifer\Post\HasTerms` trait, included in `Conifer\Post\Post`, defines the `get_all_grouped_by_term()` helper, for getting `Timber\Term` instances and their respective posts. Say we have our `Robot` class from previous examples, plus the `eeriness_level` taxonomy declared for the `robot` custom post type, and we want to list the different eeriness levels and their corresponding robots:

```php
// page-robots-by-eeriness-level.php

// set up Timber context with the current page
$data = $site->get_context_with_post(new Page());

$data['robots_by_eeriness_level'] = Robot::get_all_grouped_by_term('eeriness_level');

Timber::render('robots-by-eeriness-level.twig', $data);
```

This will query all non-empty `eeriness_level` terms and grab all the posts of type `robot` inside them, so that we can do something like this in our Twig template:

```twig
{# robots-by-eeriness-level.twig #}

<div class="eeriness-levels">
  {% for level in robots_by_eeriness_level %}
    <div>
      <h3>{{ level.term }}</h3>
      <ul>
        {% for robot in level.posts %}
          <li>
            <a href="{{ robot.link }}">{{ robot.title }}</a>
          </li>
        {% endfor %}
      </ul>
    </div>
  {% endfor %}
</div>
```

The `term` object inside each `level` in the returned array is an instance of `Timber\Term`.

### Overriding the list of terms

Say we only want to know about the Robots at the extremes: those at the `cute` and `eerie-af` levels. By default, Conifer will query all non-empty terms in the taxonomy. But we can specify a second argument as a list of terms:

```php
$data['robots_by_eeriness_level'] = Robot::get_all_grouped_by_term(
  'eeriness_level',
  ['cute', 'eerie-af']
);
```

This will filter the Terms queried to just those with the specified slugs. Alternatively, each item in the array can be a `term_id`, an instance of `Timber\Term` (or any subclass thereof), or a native `WP_Term` object.

We can even mix and match types:

```php
$terms = [
  'cute',
  new Timber\Term('eerie-af'),
  get_term(123), // WP_Term object
  456 // hard-code term_id
];

$data['robots_by_eeriness_level'] = Robot::get_all_grouped_by_term(
  'eeriness_level',
  $terms
);
```

### Filtering the posts in each group

Say you're curating the premiere website for Robot enthusiasts, and you need to review user submissions. You have so many submissions, you need to conquer and divide: you're the expert on cute robots, and your intrepid co-founder covers eerie ones. You can group robots just fine, but how do you filter down to just those that pending review?

You can specify a *third* argument, an array that specifies additional query parameters for the post query:

```php
$data['robots_by_eeriness_level'] = Robot::get_all_grouped_by_term(
  'eeriness_level',
  ['cute', 'eerie-af'],
  ['post_status' => 'pending']
);
```

Each time posts for a given term are queried, Conifer will merge the `pending` status constraint into the query. This third argument can be any valid [arguments to `WP_Query`](https://codex.wordpress.org/Class_Reference/WP_Query#Parameters) , with the exception of `post_type`, which is locked down.

## Advanced Search Features

Conifer exposes a very powerful API for customizing how post searches works, allowing you to expose meta fields in search with fine-grained control over post types, statuses, and more. See [Search](/search.md) for more details.