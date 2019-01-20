<?php
/**
 * Conifer test suite bootstrap file; included before every unit test run
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

require_once __DIR__ . '/../vendor/autoload.php';

define('TEST_LIB_DIR', __DIR__ . '/cases/');
define('TEST_SUPPORT_DIR', __DIR__ . '/support/');

spl_autoload_register(function(string $className) {
  $components = explode('\\', $className);

  $topNamespace = array_shift($components);

  if ($topNamespace === 'ConiferTest') {
    $file = TEST_LIB_DIR . implode('/', $components) . '.php';

    if (file_exists($file)) {
      require $file;
    }
  }

  if ($topNamespace === 'ConiferTestSupport') {
    $file = TEST_SUPPORT_DIR . implode('/', $components) . '.php';

    if (file_exists($file)) {
      require $file;
    }
  }
});


/*
 * Define some WP constants that are referenced directly in Conifer
 */
define('ABSPATH', realpath(__DIR__ . '/../'));
define('WP_PLUGIN_DIR', ABSPATH . '/wp-content/plugins');
define('WP_CONTENT_URL', 'http://appserver/wp-content');
define('WPMU_PLUGIN_DIR', ABSPATH . '/wp-content/plugins');

/**
 * Define our own version of apply_filters_deprecated, rather than mocking,
 * so that we can raise warnings from our tests.
 */
function apply_filters_deprecated($filter, $filterArgs) {
  deprecated_hook_notice('filter', $filter);

  return $filterArgs[0];
}

function do_action_deprecated($action) {
  deprecated_hook_notice('action', $action);
}

function deprecated_hook_notice($type, $hook) {
  // Do some terrible horcrux-style dark magic shit
  // @codingStandardsIgnoreStart
  $wpMock = new ReflectionClass(WP_Mock::class);
  $mgrProp = $wpMock->getProperty('event_manager');
  $mgrProp->setAccessible(true);
  $mgr = $mgrProp->getValue();
  $mgrReflection = new ReflectionClass($mgr);
  $callbacksProp = $mgrReflection->getProperty('callbacks');
  $callbacksProp->setAccessible(true);
  $callbacks = $callbacksProp->getValue($mgr);

  // were any filters added?
  if ($callbacks && isset($callbacks["$type::$hook"])) {
    trigger_error("{$hook} is deprecated");
  }
}
