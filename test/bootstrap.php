<?php

/**
 * Conifer test suite bootstrap file; included before every unit test run
 *
 * @todo remove dependency on \WP_Mock
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
define('WPMU_PLUGIN_DIR', ABSPATH . '/wp-content/mu-plugins');

if (!isset($GLOBALS['conifer_deprecated_hook_registry'])) {
  $GLOBALS['conifer_deprecated_hook_registry'] = [
    'filter' => [],
    'action' => [],
  ];
}

if (!function_exists('add_filter')) {
  function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1)
  {
    $GLOBALS['conifer_deprecated_hook_registry']['filter'][$tag] = true;
    \WP_Mock::onFilterAdded($tag)->react($function_to_add, (int) $priority, (int) $accepted_args);

    return true;
  }
}

if (!function_exists('add_action')) {
  function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1)
  {
    $GLOBALS['conifer_deprecated_hook_registry']['action'][$tag] = true;
    \WP_Mock::onActionAdded($tag)->react($function_to_add, (int) $priority, (int) $accepted_args);

    return true;
  }
}

/**
 * Define our own version of apply_filters_deprecated, rather than mocking,
 * so that we can raise warnings from our tests.
 */
function apply_filters_deprecated(mixed $filter, mixed $filterArgs)
{
  deprecated_hook_notice('filter', $filter);

  return $filterArgs[0];
}

function do_action_deprecated(mixed $action)
{
  deprecated_hook_notice('action', $action);
}

function deprecated_hook_notice(mixed $type, mixed $hook)
{
  $listeners = $GLOBALS['conifer_deprecated_hook_registry'][$type][$hook] ?? false;

  if ($listeners) {
    trigger_error("{$hook} is deprecated");
  }
}

if (!function_exists('is_admin')) {
  function is_admin(): bool
  {
    return $GLOBALS['is_admin'] ?? true;
  }
}
