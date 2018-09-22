# Conifer Basics

Getting started with Conifer is easy. Let's dig into the code!

Before diving in, make sure you've [installed](/installation.md) the plugin first, and that your system [meets the requirements](/requirements.md).

After installing and activating Conifer, the first thing you want to do is set up your `Site` instance. In contrast to Timber, Conifer encourages instantiating a `Site` instance and *then* configuring it, rather than throwing site-wide configuration in the constructor. At its most basic, you can call `configure()` with no arguments and it will set you up with some sensible defaults:

```php
// functions.php
use Conifer\Site;

// create a new Site instance...
$site = new Site();

// ...and configure it
$site->configure();
```

You can now use the global `$site` variable in your templates.

## Configuring site-wide behavior

Before we explore further, let's say we want to define some custom Twig filters and functions:

```php
// functions.php
use Conifer\Site;
use MyProject\FancyTwigHelper;

// create a new Site instance...
$site = new Site();

// ...and configure it
$site->configure(function() {
  /*
   * Register custom Twig functions/filters defined in your theme code.
   * Note that inside this closure, `$this` refers to the site instance!
   */
  $this->add_twig_helper(new FancyTwigHelper());
});
```

In your WP templates, for example `front-page.php`, stick the home page into Timber/Twig's rendering context with a single call:

```php
// front-page.php
use Conifer\Post\FrontPage;
use Timber\Timber;

// get the Timber context, setting the `post` Twig variable to the FrontPage instance
$data = $site->get_context_with_post(FrontPage::get());

// render the home page view
Timber::render('front-page.twig', $data);
```

The Twig view is standard Timber fare, but note that even with this simplistic approach, you're already set up to use your custom filters and functions defined in your `FancyTwigHelper`:

```twig
{# front-page.twig #}
{% extends 'layouts/main.twig' %}

<h1>{{ post.title | my_fancy_twig_filter }}</h1>

<div class="home-page-content {{ my_fancy_post_class_function(post) }}">
	{{ post.content }}
</div>
```

Of course, this is barely scratching the surface of what Conifer can do.

## Diving in

Here's a brief overview of Conifer's biggest features.

### The Site Class

[The `Site` class](/site.md) is a concept inherited [from Timber](https://timber.github.io/docs/reference/timber-site/). As in the example above, the Site class `configure()` callback is where your site-wide config code goes in a conventional Conifer-style architecture. This class provides a number of helper methods for:

* setting up sensible site-wide defaults
* enqueueing scripts and styles
* easily adding custom Twig functions and filters
* setting up file "cascades," which tell Timber, Twig, and WordPress where to look for files you tell them to load

### Posts and Post Types

Conifer boasts a simple [Post API](/posts.md) that lets you query and manage posts in powerful ways:

* Easily register custom post types
* Create posts with a single `create()` call
* Query related posts by any taxonomy
* Group post query results by taxonomy term
* Configure custom admin filters and columns

### Forms

Conifer comes with an insanely powerful [Form API](/forms.md) that lets you create and process forms with:

- an extensible, ergonomic OO interface
- field-specific error states and messaging
- custom field validations and filters
- tightly integrated Twig filters for rendering form state

### Admin Helpers

Conifer's [Admin Helpers](/admin.md) make working with the WP Admin much simpler:

* Display notices to admin users without writing markup
* Build custom settings pages and sub-pages easily

### Authorization

Tell WordPress to [hide or modify certain content](/authorization.md) based on the current user:

* Register a shortcode to let admins control pieces of content to hide based on user role. With a single line of code.
* Easily define custom shortcodes with arbitrary user authorization logic
* Redirect away from certain templates if the user lacks specific/arbitrary privileges

### Notifiers

Conifer's dead-simple [Notifiers](/notifiers.md) let you send emails to admins and other users at the drop of a hat:

* Email the main site admin with a simple method call
* Easily send formatted emails to arbitrary addresses
* Decouple email contents from the destination while reusing code

