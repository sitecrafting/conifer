# The Site Class

As the Conifer Basics page illustrates, the `Site` class is a concept [inherited from Timber](https://timber.github.io/docs/reference/timber-site/) that gives you a single place to put all your site-wide configuration code.

## Basic Usage

The most basic usage involves:

* creating a `Site` instance
* calling `configure()` on that instance

## Defaults

Calling `configure()` with no arguments will set you up with some sensible defaults:

```php
// functions.php
use Conifer\Site;

// create a new Site instance...
$site = new Site();

// ...and configure it
$site->configure();
```

These defaults include:

* Injecting helpful Twig variables into the Timber view context automatically
* Setting up the Twig "view cascade," a configurable, prioritized list of directories to look in when rendering Twig views
* Adding the `StringLoader` and `Debug` Twig extensions
* Registering default Twig helper functions and filters
* Configuring default integrations with other plugins, such as Yoast SEO (work in progress)

## Simple configuration

Rather than just relying on the defaults, you're likely to need to add some actions or filters. For this, you can pass an anonymous function to `configure()`. We'll refer to this anonymous function as the **configure callback**, or **config callback** for short.

Here's a simple, but realistic, example of what a config callback might look like:

```php
// functions.php
use Conifer\Site;
use MyProject\FancyTwigHelper;

// create a new Site instance
$site = new Site();

// configure it
$site->configure(function() {
  add_action('wp_enqueue_scripts', function() {
    /*
     * Enqueue your theme scripts and styles here.
     * A couple things to note:
     * - inside this closure, `$this` refers to the site instance
     * - file paths are relative to the theme directory
     */
    $this->enqueue_script('site-common-js', 'dist/common.js');
    $this->enqueue_style('site-commons-css', 'dist/common.css');
  });
  
  /*
   * Register custom Twig functions/filters defined in your theme code
   */
  $this->add_twig_helper(new FancyTwigHelper());
    
  // more custom configuration here...
});
```

This approach of passing a function to `conifigure()` has a couple advantages:

- You don't have to define your own `Site` subclass.
- Wrapping your config code in a closure avoids polluting the global namespace, but also allows it to remain in the familiar functions.php rather than in a random library class, where newcomers to your codebase may not know to look.

### The config callback is totally optional

The config callback-style architecture is pure syntactic sugar: there's no reason you *have* to use it. If you prefer to encapsulate all site-wide behavior directly in a custom `Site` subclass, nothing is stopping you. If you want to preserve the configuration defaults, just make sure you call `parent::configure()` from your subclass:

```php
// /your/theme/lib/MyProject/Site.php
namespace MyProject;

use Timber\Site as TimberSite;

class Site extends TimberSite {
  public function configure() {
    // configure defaults
    parent::configure();
    
    // your custom config code here...
  }
}

// functions.php
use MyProject\Site;

$site = new Site();
$site->configure();
```

## Disabling defaults

If you want to *only* run your custom config callback without funning Conifer's default config code, you can pass `false` as the second argument to `configure()`, which tells Conifer to disable its defaults:

```php
$site->configure(function() { /* ... */ }, false);
```

