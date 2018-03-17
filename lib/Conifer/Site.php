<?php
/**
 * Central Site class
 */

namespace Conifer;

use Timber\Timber;
use Timber\Site as TimberSite;

use Twig_Environment;
use Twig_Extension_StringLoader;
use Twig_Extension_Debug;
use Twig_SimpleFunction;
use Twig_SimpleFilter;

use Conifer\Twig\Filters;
use Conifer\Twig\Functions;

use Conifer\Post\Image;
use Conifer\Post\Post;
use Conifer\Shortcode\Gallery;
use Conifer\Shortcode\Button;

/**
 * Wrapper for any and all theme-specific behavior.
 *
 * @package Conifer
 */
class Site extends TimberSite {
  protected $relative_script_dir;

  protected $relative_style_dir;

	/**
	 * Assets version timestamp, used for cache-busting
	 * @var string
	 */
	protected $assets_version;

	/**
	 * @var array An associative array of Twig functions.
	 * Keys are function names and values are closures.
	 */
	protected $twig_functions = [];

	/**
	 * @var array An associative array of Twig filters.
	 * Keys are function names and values are closures.
	 */
	protected $twig_filters = [];

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

    $this->relative_script_dir = '/js/';
    $this->relative_style_dir = '/';
	}

	/**
	 * Configure any WordPress hooks and register site-wide components, such as nav menus
	 * @return Conifer\Site the Site object it was called on
	 */
	public function configure() : Site {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );

		// configure Twig/Timber
		add_filter( 'timber_context', [$this, 'add_to_context'] );
		add_filter( 'get_twig', [$this, 'add_to_twig'] );

		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts_and_styles'] );

		add_action( 'admin_init', ['\Conifer\Admin', 'init'] );
		add_action( 'init', ['\Conifer\Admin', 'add_theme_settings_page'] );

		add_filter( 'posts_search', ['\Conifer\AcfSearch', 'advanced_custom_search'], 10, 2 );

		// Add default Twig filters/functions
		Filters\Number::add_twig_filters( $this );
		Filters\TextHelper::add_twig_filters( $this );
		Filters\TermHelper::add_twig_filters( $this );
		Filters\Image::add_twig_filters( $this );

		Functions\WordPress::add_twig_functions( $this );
		Functions\Image::add_twig_functions( $this );

		// used for Gallery ACF layout option in flexible content
		Image::add_size( 'gallery', 900, 600, true );

		//USED FOR Image-Row ACF layout option in flexible content
		Image::add_size( 'image-row-small', 300, 235, true );
		Image::add_size( 'image-row-medium', 450, 350, true );
		Image::add_size( 'image-row-large', 900, 450, true );

		// Make certain custom sizes available in the RTE
		// use this to unset or add image size options for RTE insert
    /*add_filter( 'image_size_names_choose', function($sizes) {

			//USE THIS TO UNSET DEFAULT VARIABLE SIZES AND SET YOUR OWN CUSOM SIZES
			//unset( $sizes['large'] );
			//unset( $sizes['medium'] );
			//unset( $sizes['small'] );

      return array_merge( $sizes, [
				'image-row-small' => __( 'Small 300x235' ),
        'image-row-medium' => __( 'Medium 450x350' ),
        'image-row-large' => __( 'Large 900x450' )
      ]);
    });*/

		//remove_shortcode( 'gallery' );
		//Gallery::register( 'gallery' );
		add_filter( 'use_default_gallery_style', '__return_false' );

		// register common nav menus
		register_nav_menus([
			'primary' => 'Main Navigation', // main page/nav structure
			'global' => 'Global Navigation', // for stuff like social icons
			'footer' => 'Footer Navigation', // footer links
		]);

		//blog sidebar
		register_sidebar([
			'name' => 'Blog Filter Bar',
			'id' => 'blog-filters',
			'before_widget' => '<div id="%1$s" class="filter %2$s">',
			'after_widget'  => "</div>\n",
			'before_title'  => '<h3 class="filtertitle">',
			'after_title'   => "</h3>\n"
		]);

		// Banish the Yoast SEO meta box to the bottom of the post edit screen, where it belongs
		add_filter( 'wpseo_metabox_prio', function() { return 'low';});


		return $this;
	}

	/**
	 * Enqueue custom JS/CSS
	 */
	public function enqueue_scripts_and_styles() {
		/*
		 * Enqueue our own project-specific JavaScript, including dependencies.
		 * If you need to add a script to be enqueued and it's ok to do so site-wide, please consider doing so via Grunt
		 * instead of here to reduce page load times.
		 */
		wp_enqueue_script(
			'project-common',
			$this->get_script_uri('project-common.min.js'),
			$dependencies = ['jquery'],
			$version = $this->get_assets_version(),
			$inFooter = true
		);

		//modernizr
		wp_enqueue_script(
			'project-modernizr',
			$this->get_script_uri('modernizr/modernizr.custom.53630.js'),
			$dependencies = [],
			$version = $this->get_assets_version(),
			$inFooter = false
		);


		// NOTE: If you do need to enqueue additional scripts here, please enqueue them in the footer
		// unless there's a very good reason not to.

		wp_enqueue_style(
			'project-css',
			$this->get_stylesheet_uri('style.css'),
			$dependencies = [],
			$version = $this->get_assets_version()
		);
		wp_enqueue_style(
			'project-print-css',
			$this->get_stylesheet_uri('print.css'),
			$dependencies = [],
			$version = $this->get_assets_version(),
			'print'
		);
	}

	/**
	 * Get the current Timber context, with the "post" index set to $post
	 * @param Conifer\Post $post the current Post object
	 * @return array the Timber context
	 */
	public function get_context_with_post( Post $post ) {
		$context = Timber::get_context();
		$context['post'] = $post;
		return $context;
	}

	/**
	 * Get the current Timber context, with the "posts" index set to $posts
	 * @param array $posts an array of Conifer\Post objects
	 * @return array the Timber context
	 */
	public function get_context_with_posts( array $posts ) {
		$context = Timber::get_context();
		$context['posts'] = $posts;
		return $context;
	}

	/**
	 * Add arbitrary data to the site-wide context array
	 * @param array $context the default context
	 * @return array the updated context
	 */
	public function add_to_context( array $context ) : array {
		$context['site'] = $this;
		$context['primary_menu'] = new Menu( 'primary' );
		$context['body_classes'] = get_body_class();
		$context['search_query'] = get_search_query();
		return $context;
	}

	/**
	 * Register a custom Twig filter to be added at render time via the
	 * "get_twig" WordPress filter
	 * @param string $name the name of the filter
	 * @param callable $filter a callable that implements the custom filter
	 * @return Conifer\Site the Site object it was called on
	 */
	public function add_twig_filter( string $name, callable $filter ) : Site {
		$this->twig_filters[$name] = $filter;
		return $this;
	}

	/**
	 * Register a custom Twig function to be added at render time via
	 * the "get_twig" WordPress filter
	 * @param string $name the name of the function
	 * @param callable $function a callable that implements the custom function
	 * @return Conifer\Site the Site object it was called on
	 */
	public function add_twig_function( string $name, callable $function ) : Site {
		$this->twig_functions[$name] = $function;
		return $this;
	}

	/**
	 * Add your own extenstions/filters/functions to the internal Twig environment.
	 * @param Twig_Environment $twig Timber's internal Twig_Environment instance
	 * @return Twig_Environment the extended Twig instance
	 */
	public function add_to_twig( Twig_Environment $twig ) : Twig_Environment {
		$twig->addExtension( new Twig_Extension_StringLoader() );

		// Make debugging available through Twig
		// Note: in order for Twig's dump() function to work,
		// the WP_DEBUG constant must be set to true in wp-config.php
		$twig->addExtension( new Twig_Extension_Debug() );

		foreach( $this->twig_functions as $name => $callable ) {
			$function = new Twig_SimpleFunction( $name, $callable );
			$twig->addFunction( $function );
		}

		foreach( $this->twig_filters as $name => $callable ) {
			$filter = new Twig_SimpleFilter( $name, $callable );
			$twig->addFilter( $filter );
		}

		return $twig;
	}

	/**
	 * Get the full URI for a script file
	 * @param string $file the base file name (relative to js/)
	 * @return the script's full URI
	 */
	public function get_script_uri( string $file ) : string {
    // TODO don't hard-code relative dir
    return get_stylesheet_directory_uri()
      . $this->get_relative_script_dir()
      . $file;
	}

	/**
	 * Get the full URI for a stylesheet
	 * @param string $file the base file name (relative to the theme dir URI)
	 * @return the stylesheet's full URI
	 */
	public function get_stylesheet_uri( string $file ) : string {
    return get_stylesheet_directory_uri()
      . $this->get_relative_style_dir()
      . $file;
	}

  public function get_relative_script_dir(string $subdir = '') : string {
    return $this->relative_script_dir . $subdir;
  }

  public function get_relative_style_dir(string $subdir = '') : string {
    return $this->relative_style_dir . $subdir;
  }

  public function set_relative_script_dir(string $dir) : string {
    return $this->relative_script_dir = $dir;
  }

  public function set_relative_style_dir(string $dir) : string {
    return $this->relative_style_dir = $dir;
  }

	/**
	 * Get the build-tool-generated hash for global assets
	 * @return the hash for
	 */
	public function get_assets_version() : string {
		if(!$this->assets_version && is_readable($this->get_theme_file('assets.version')) ) {
      $contents = file_get_contents($this->get_theme_file('assets.version'));
      $this->assets_version = trim($contents);
		}

		// Cache the version in this object
		return $this->assets_version;
	}

  public function get_assets_version_filepath() : string {
    return $this->get_theme_file('assets.version');
  }

  public function get_theme_file(string $file) : string {
    if ($file[0] !== '/') {
      $file = "/$file";
    }
    return get_stylesheet_directory().$file;
  }
}
