<?php
/**
 * PSR-4 autoloading
 */

namespace Groot;

/**
 * Provides an abstraction around basic plugin requirements, along with a way
 * to warn admin users about any requirements that aren't met.
 *
 * @package Groot
 */
class PluginManager {
	protected $required_classes;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->required_classes = [];
	}

	/**
	 * Specify that certain classes are required, or else
	 * display an admin warning.
	 */
	public function require_classes(array $classes) {
		$this->required_classes = array_merge($this->required_classes, $classes);
	}

	/**
	 * Warn admin user if Timber or Conifer is not installed/activated
	 */
	public function requirements_met(bool $displayAdminWarnings = true) : bool {
		foreach ($this->required_classes as $class) {
			if (!class_exists($class) ) {

				if ($displayAdminWarnings) {
					$missingPlugin = $this->get_implementing_plugin($class);
					$this->generate_admin_warning($missingPlugin);
				}

        return false;
			}
		}

		return true;
	}

	protected function get_implementing_plugin(string $class) : array {
		switch ($class) {
			case 'Timber\Timber':
				$plugin = ['slug' => 'timber', 'name' => 'Timber'];
				break;

			case 'Conifer\Site':
				$plugin = ['slug' => 'conifer', 'name' => 'Conifer'];
				break;

			default:
				$plugin = ['name' => "The plugin that implements the $class class"];
				break;
		}

		return $plugin;
	}

	protected function generate_admin_warning(array $plugin) {
		add_action( 'admin_notices', function() use($plugin) {
			// TODO I18n
			$message = "{$plugin['name']} is not activated. Make sure to activate it";
			
			$anchor = $plugin['slug']
				? '#'.$plugin['slug'] // Link to the specific plugin section
				: ''; 								// Just link to the plugins page

			$link = esc_url(admin_url('plugins.php'.$anchor));

			echo sprintf(
				'<div class="error"><p>%s <a href="%s">%s</a></p></div>',
				$message,
				$link,
				__('here')
			);
		});
	}
}

?>
