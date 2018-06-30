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

```\Conifer\Post\BlogPost::exists($badId); // -&gt; false
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

## Querying for subclasses

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

