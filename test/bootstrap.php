<?php
/**
 * Conifer test suite bootstrap file; included before every unit test run
 *
 * @todo remove dependency on WP_Mock
 * @copyright 2020 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

require_once __DIR__ . '/../vendor/autoload.php';


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
function apply_filters_deprecated(string $filter, $filterArgs) {
  deprecated_hook_notice('filter', $filter);

  return $filterArgs[0];
}

function do_action_deprecated(string $action): void {
  deprecated_hook_notice('action', $action);
}

function deprecated_hook_notice($type, string $hook): void {
  // Do some terrible horcrux-style dark magic shit
  // @codingStandardsIgnoreStart
  $wpMock = new ReflectionClass(WP_Mock::class);
  $mgrProp = $wpMock->getProperty('event_manager');
  $mgr = $mgrProp->getValue();
  $mgrReflection = new ReflectionClass($mgr);
  $callbacksProp = $mgrReflection->getProperty('callbacks');
  $callbacks = $callbacksProp->getValue($mgr);

  // were any filters added?
  if ($callbacks && isset($callbacks[sprintf('%s::%s', $type, $hook)])) {
    trigger_error($hook . ' is deprecated');
  }
}
