<?php
/**
 * PSR-4 autoloading
 */

namespace Groot;

/**
 * Basic Project-specific Autoloader with configurable search paths.
 *
 * @package Groot
 */
class Autoloader {
	/**
	 * Directory paths to check when autoloading classes.
	 * @var array
	 */
	protected $paths;

	/**
	 * Constructor
	 * @param array $paths Directories to register. When searching for classes to autoload,
	 * the autoloader will check these directories in the order they are specified here.
	 */
	public function __construct(array $paths = []) {
		// Look first in Conifer libs directory for unloaded classes, then theme libs
		$defaultPaths = [
			WP_PLUGIN_DIR.'/conifer/lib/',
			get_stylesheet_directory().'/lib/',
		];

		$this->paths = $paths ?: $defaultPaths;
	}

	/**
	 * Register this Autoloader's paths with spl_autoload_register
	 */
	public function register() {
		spl_autoload_register( function($namespacedClassName) {
			// Infer relative file path to
			// "Foo\Bar" to "Foo/Bar.php"
			$relativeFile = str_replace('\\', '/', $namespacedClassName) . '.php';

			foreach( $this->paths as $path ) {
				$file = $path . $relativeFile;

				if( file_exists($file) ) {
					require $file;
				}
			}
		});
	}
}

?>