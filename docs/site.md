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

## Directory Cascades

A **Directory Cascade** is an ordered list of directories where Conifer looks for Twig views (`*.twig` files), JS files, or stylesheets. Each type of asset (views, JS, CSS) has its own set of directories - its own *cascade* - where Conifer looks for that type of file. Each cascade has its own getter and setter on the `Site` class:

```php
$site->configure(function() {
  // set the view, JS, and CSS cascades, respectively
  $this->set_view_directory_cascade([
    get_template_directory() . '/views',
    '/custom/fallback/path/to/views',
  ]);
  $this->set_script_directory_cascade([
    get_template_directory() . '/js',
    '/custom/fallback/path/to/js',
  ]);
  $this->set_style_directory_cascade([
    get_template_directory() . '/css',
    '/custom/fallback/path/to/css',
  ]);

  // getters
  $this->get_view_directory_cascade();
  $this->get_script_directory_cascade();
  $this->get_style_directory_cascade();
});
```

This comes in handy for setting up an override system, for example if you are writing a plugin that comes with Twig views but you also want to allow themes to override those views at a granular level.

Note that Conifer hooks into the `timber/loader/paths` filter by default, telling Timber to look in the active theme's `views` directory, then in Conifer's, when loading Twig views. If you override Conifer's default site settings but you still want to preserve this behavior, make sure to set up the view cascade directly with:

```php
$site->configure(function() {
	$this->configure_twig_view_cascade();
});
```

### Finding generic file paths

Under the hood, the script and style cascades call the generic `file_file()` method, which simply traverses a list of directories looking for a relative file path within each. It returns the full path of the first existing file it finds:

```php
// directory tree:
// .
// ├── one
// │   ├── a
// │   └── b
// └── two
//     ├── a
//     │   └── example.txt
//     └── b

$site->find_file('a/example.txt', ['one', 'two']);
// -> 'two/a/example.txt'
```

## Managing front-end assets

The `Site` class has a handful of nice wrappers abstractions over the `wp_enqueue_*` functions:

```php
$site->configure(function() {
  $this->enqueue_script(
    $handle  = 'custom',
    $src     = 'custom.js',
    $deps    = [],
    $version = true,
    $footer  = true
  );
  $this->enqueue_style(
    $handle  = 'extra',
    $src     = 'extra.css',
    $deps    = [],
    $version = true,
    $footer  = true
  );
  $this->enqueue_style(
    $handle  = 'custom-style',
    $src     = 'custom-style.css',
    $deps    = [],
    $version = ['file' => 'custom-style.version'],
    $footer  = true
	);
});
```

This looks in the [script cascade and style cascade](#directory-cascades) for `custom.js` and `extra.css`, respectively. The method arguments are almost identical to those of`wp_enqueue_script()` and `wp_enqueue_style()`, with a few exceptions:

* The `$src` argument is evaluated as a path relative to each step in the script/style cascade: that is, Conifer looks first in `js/` or `css/` in the theme by default.
* The `$version` argument is `true` by default, which tells Conifer to look for a special file called `assets.version` in the theme directory. If the file is found, its contents are passed to `wp_enqueue_*` as the version argument. This can be used as a means of fine-grained [cache-busting](https://css-tricks.com/strategies-for-cache-busting-css/) for your theme assets. If you track bundled assets as part of your theme code in source control and you use a build system such as Webpack, Gulp, or Grunt, just write a content hash or datetime to your theme's `assets.version` file. Alternatively, $vesion can be a key/value array. Where the key = "file" and value="filename". This filename will contain a version number for your custom asset. The path is relative to the theme folder.

## Timber Context helper

A common use case of Timber is to get the default data to pass to the Twig view, AKA the "context," and then immediately set the `post` or `posts` variable. The `Site::context()` method allows passing arbitrary data to merge into the current context data, to help out with this use-case and many others:

```php
// my-custom-page.php
use Conifer\Post\Page;
$data = $site->context(['post' => new Page()]);
```

Note that this method is not effectful, i.e. it doesn't actually write to the Timber context. So calling it more than once and expecting data from prior calls won't work:

```php
$data = $site->context(['stuff' => 'my stuff']);
$data = $site->context();
$stuff = $data['stuff']; // Notice: Undefined index: stuff
```
