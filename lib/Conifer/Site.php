<?php
/**
 * Central Site class
 */

namespace Conifer;

use Timber\Timber;
use Timber\Site as TimberSite;
use Timber\URLHelper;

use Twig_Environment;
use Twig_Extension_StringLoader;
use Twig_Extension_Debug;
use Twig_SimpleFunction;
use Twig_SimpleFilter;

use Conifer\Navigation\Menu;
use Conifer\Post\Post;
use Conifer\Shortcode\Gallery;
use Conifer\Shortcode\Button;
use Conifer\Twig;

/**
 * Wrapper for any and all theme-specific behavior.
 *
 * @package Conifer
 */
class Site extends TimberSite {
  /**
   * An array of directories where Conifer will look for JavaScript files
   *
   * @var array
   */
  protected $script_directory_cascade;

  /**
   * An array of directories where Conifer will look for stylesheets
   *
   * @var array
   */
  protected $style_directory_cascade;

  /**
   * Assets version timestamp, used for cache-busting
   *
   * @var string
   */
  protected $assets_version;

  /**
   * Keys are function names and values are closures.
   *
   * @var array An associative array of Twig functions.
   */
  protected $twig_functions = [];

  /**
   * Keys are function names and values are closures.
   *
   * @var array An associative array of Twig filters.
   */
  protected $twig_filters = [];

  /**
   * Constructor
   */
  public function __construct() {
    parent::__construct();

    $this->script_directory_cascade = [get_stylesheet_directory() . '/js/'];
    $this->style_directory_cascade  = [get_stylesheet_directory() . '/'];

    // check theme for view files, then plugin
    $this->view_directory_cascade = [
      get_stylesheet_directory() . '/views/',
      realpath(__DIR__ . '/../../views/'),
    ];
  }

  /**
   * Configure any WordPress hooks and register site-wide components, such as
   * nav menus
   *
   * @param callable $userDefinedConfig a callback for configuring this Site
   * from theme code.
   * @param boole $configureDefaults whether to run Conifer's default
   * configuration code. Defaults to `true`.
   * @return Conifer\Site the Site object it was called on
   */
  public function configure(
    callable $userDefinedConfig = null,
    bool $configureDefaults = true
  ) : Site {
    // unless the user has explicitly disabled the defaults, configure them
    if ($configureDefaults) {
      $this->configure_defaults();
    }

    if (is_callable($userDefinedConfig)) {
      // Set up user-defined configuration
      $userDefinedConfig->call($this);
    }

    return $this;
  }

  /**
   * Configure useful defaults for Twig functions/filters,
   * custom image sizes, shortcodes, etc.
   */
  public function configure_defaults() {
    add_filter('timber_context', [$this, 'add_to_context']);

    $this->configure_twig_view_cascade();
    $this->configure_default_twig_extensions();
    $this->add_default_twig_helpers();

    Integrations\YoastIntegration::demote_metabox();
  }


  /**
   * Enqueue a script within the script cascade path. Calls wp_enqueue_script
   * transparently, except that it defaults to enqueueing in the footer instead
   * of the header.
   *
   * @param string $scriptHandle the script handle to register and enqueue
   * @param string $fileName the file to search for in the script cascade path
   * @param array $dependencies an array of registered dependency handles
   * @param string|bool|null $version the version of the script to append to
   * the URL rendered in the <script> tag. Accepts any valid value of the $ver
   * argument to `wp_enqueue_script`, plus the literal value `true`, which
   * tells Conifer to look for an assets version file to use for cache-busting.
   * Defaults to `true`.
   * @param bool $inFooter whether to enqueue this script in the footer. Unlike
   * the same argument to the core `wp_enqueue_script` functions, this defaults
   * to `true`.
   */
  public function enqueue_script(
    string $scriptName,
    string $fileName,
    array $dependencies = [],
    $version = true,
    bool $inFooter = true
  ) {
    if ($version === true) {
      // use automatic any automatic cache-busting in the theme build process
      $version = $this->get_assets_version();
    }

    wp_enqueue_script(
      $scriptName,
      $this->get_script_uri($fileName),
      $dependencies,
      $version,
      $inFooter
    );
  }

  /**
   * Enqueue a stylesheet within the style cascade path. Calls
   * `wp_enqueue_style` transparently.
   *
   * @param string $stylesheetHandle the style handle to register and enqueue
   * @param string $fileName the file to search for in the style cascade path
   * @param array $dependencies an array of registered dependency handles
   * @param string|bool|null $version the version of the style to append to
   * the URL rendered in the <link> tag. Accepts any valid value of the $ver
   * argument to `wp_enqueue_style`, plus the literal value `true`, which
   * tells Conifer to look for an assets version file to use for cache-busting.
   * Defaults to `true`.
   * @param bool $media the media for which this stylesheet has been defined;
   * passed transparently to `wp_enqueue_style`. Defaults to "all" (as does
   * `wp_enqueue_style` itself).
   */
  public function enqueue_style(
    string $stylesheetName,
    string $fileName,
    array $dependencies = [],
    $version = true,
    string $media = 'all'
  ) {
    if ($version === true) {
      // use automatic any automatic cache-busting in the theme build process
      $version = $this->get_assets_version();
    }

    wp_enqueue_style(
      $stylesheetName,
      $this->get_stylesheet_uri($fileName),
      $dependencies,
      $version,
      $media
    );
  }

  /**
   * Get the current Timber context, with the "post" index set to $post
   *
   * @param Conifer\Post $post the current Post object
   * @return array the Timber context
   */
  public function get_context_with_post( Post $post ) {
    $context         = Timber::get_context();
    $context['post'] = $post;
    return $context;
  }

  /**
   * Get the current Timber context, with the "posts" index set to $posts
   *
   * @param array $posts an array of Conifer\Post objects
   * @return array the Timber context
   */
  public function get_context_with_posts( array $posts ) {
    $context          = Timber::get_context();
    $context['posts'] = $posts;
    return $context;
  }

  /**
   * Add arbitrary data to the site-wide context array
   *
   * @param array $context the default context
   * @return array the updated context
   */
  public function add_to_context( array $context ) : array {
    $context['site']         = $this;
    $context['primary_menu'] = new Menu( 'primary' );
    $context['body_classes'] = get_body_class();
    $context['search_query'] = get_search_query();
    return $context;
  }



  /*
   * Twig Helper Methods
   */

  /**
   * Add a Twig helper that implements Twig filters and/or functions to the
   * Twig environment that Timber uses to render views.
   *
   * @param Twig\HelperInterface $helper any instance of a
   * Twig\HelperInterface that implements the functions/filters to add
   */
  public function add_twig_helper(Twig\HelperInterface $helper) {
    add_filter('get_twig', function(Twig_Environment $twig) use ($helper) {
      return $this->get_twig_with_helper($twig, $helper);
    });
  }

  /**
   * Add any filters/functions implemented by `$helper` to the Twig instance
   * `$twig`.
   *
   * @param Twig_Environment $twig the Twig environment to add to
   * @param Twig\HelperInterface $helper the helper instance that implements the
   * filters/functions to add
   * @return Twig_Environment
   */
  public function get_twig_with_helper(
    Twig_Environment $twig,
    Twig\HelperInterface $helper
  ) : Twig_Environment {
    // add Twig filters
    foreach ( $helper->get_filters() as $name => $callable ) {
      $filter = new Twig_SimpleFilter( $name, $callable );
      $twig->addFilter( $filter );
    }

    // add Twig functions
    foreach ( $helper->get_functions() as $name => $callable ) {
      $function = new Twig_SimpleFunction( $name, $callable );
      $twig->addFunction( $function );
    }

    return $twig;
  }

  /**
   * Tell Twig to ask Conifer which directories to look in for view files.
   *
   * @see set_view_directory_cascade
   */
  public function configure_twig_view_cascade() {
    add_filter('get_twig', function(Twig_Environment $twig) {
      $twig->getLoader()->setPaths($this->get_view_directory_cascade());
      return $twig;
    });
  }

  /**
   * Load Twig's String Loader and Debug extensions
   */
  public function configure_default_twig_extensions() {
    add_filter('get_twig', function(Twig_Environment $twig) {
      $twig->addExtension( new Twig_Extension_StringLoader() );

      // Make debugging available through Twig
      // Note: in order for Twig's dump() function to work,
      // the WP_DEBUG constant must be set to true in wp-config.php
      $twig->addExtension( new Twig_Extension_Debug() );

      return $twig;
    });
  }

  /**
   * Tell Conifer to add its default Twig functions when loading
   * the Twig environment, before rendering a view
   */
  public function add_default_twig_helpers() {
    $this->add_twig_helper(new Twig\WordPressHelper());
    $this->add_twig_helper(new Twig\ImageHelper());
    $this->add_twig_helper(new Twig\NumberHelper());
    $this->add_twig_helper(new Twig\TextHelper());
    $this->add_twig_helper(new Twig\TermHelper());
    $this->add_twig_helper(new Twig\ImageHelper());

    add_filter('get_twig', function(Twig_Environment $twig) {
      // add Twig filters
      foreach ( $this->twig_filters as $name => $callable ) {
        $filter = new Twig_SimpleFilter( $name, $callable );
        $twig->addFilter( $filter );
      }

      // add Twig functions
      foreach ( $this->twig_functions as $name => $callable ) {
        $function = new Twig_SimpleFunction( $name, $callable );
        $twig->addFunction( $function );
      }

      return $twig;
    });
  }




  /**
   * Get the array of directories where Twig should look for view files.
   *
   * @return array
   */
  public function get_view_directory_cascade() : array {
    return $this->view_directory_cascade;
  }

  /**
   * Get the array of directories where Conifer will look for JavaScript files
   * when `Site::enqueue_script()` is called.
   *
   * @return array
   */
  public function get_script_directory_cascade() : array {
    return $this->script_directory_cascade;
  }

  /**
   * Get the array of directories where Conifer will look for CSS files
   * when `Site::enqueue_style()` is called.
   *
   * @return array
   */
  public function get_style_directory_cascade() : array {
    return $this->style_directory_cascade;
  }

  /**
   * Set the array of directories where Twig should look for view files
   * when `render` or `compile` is called.
   *
   * *NOTE: This will have no effect without also running
   * `configure_twig_view_cascade`, or equivalent.*
   *
   * @param array the list of directories to check.
   */
  public function set_view_directory_cascade(array $cascade) {
    $this->view_directory_cascade = $cascade;
  }

  /**
   * Set the array of directories where Conifer will look for CSS files
   * when `Site::enqueue_style()` is called.
   *
   * @param array the list of directories to check. Conifer checks directories
   * in the order declared.
   */
  public function set_script_directory_cascade(array $cascade) {
    $this->script_directory_cascade = $cascade;
  }

  /**
   * Set the array of directories where Conifer will look for CSS files
   * when `Site::enqueue_style()` is called.
   *
   * @param array the list of directories to check. Conifer checks directories
   * in the order declared.
   */
  public function set_style_directory_cascade(array $cascade) {
    $this->style_directory_cascade = $cascade;
  }

  /**
   * Get the full URI for a script file. Returns the URI for the first file
   * it finds in the script directory cascade.
   *
   * @param string $file the base file name
   * @return the script's full URI. If $file is not found in any
   * directory, returns the empty string.
   */
  public function get_script_uri( string $file ) : string {
    $path = $this->find_file($file, $this->script_directory_cascade);
    if ($path) {
      return URLHelper::file_system_to_url($path);
    }

    return '';
  }

  /**
   * Get the full URI for a stylesheet. Returns the URI for the first file
   * it finds in the style directory cascade.
   *
   * @param string $file the base file name
   * @return the stylesheet's full URI. If $file is not found in any
   * directory, returns the empty string.
   */
  public function get_stylesheet_uri( string $file ) : string {
    $path = $this->find_file($file, $this->style_directory_cascade);
    if ($path) {
      return URLHelper::file_system_to_url($path);
    }

    return '';
  }

  /**
   * Search an arbitrary list of directories for $file and return the first
   * existent file path found
   *
   * @param string $file the filename to search for in $dirs
   * @param array $dirs an array of directories to search for $file
   * @return the path of the first file found. If $file is not found in any
   * directory, returns the empty string.
   */
  public function find_file(string $file, array $dirs) : string {
    foreach ($dirs as $dir) {
      if (file_exists($dir . $file)) {
        return $dir . $file;
      }
    }

    return '';
  }

  /**
   * Get the build-tool-generated hash for global assets
   *
   * @return the hash for
   */
  public function get_assets_version() : string {
    if (!$this->assets_version && is_readable($this->get_theme_file('assets.version')) ) {
      // phpcs:ignore WordPress.WP.AlternativeFunctions
      $contents             = file_get_contents($this->get_theme_file('assets.version'));
      $this->assets_version = trim($contents);
    }

    // Cache the version in this object
    return $this->assets_version;
  }

  /**
   * Get the filepath to the assets version file
   *
   * @return string the absolute path to the assets version file
   */
  public function get_assets_version_filepath() : string {
    return $this->get_theme_file('assets.version');
  }

  /**
   * Get an arbitrary file, relative to the theme directory
   *
   * @return string the absolute path to the file
   */
  public function get_theme_file(string $file) : string {
    // ensure leading slash
    if ($file[0] !== '/') {
      $file = "/$file";
    }
    return get_stylesheet_directory() . $file;
  }
}
