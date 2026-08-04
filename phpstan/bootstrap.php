<?php

/**
 * PHPStan bootstrap file for symbol discovery
 * https://phpstan.org/user-guide/discovering-symbols
 */

if (!defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', '/app/wp/wp-content/plugins');
}

if (!defined('WPMU_PLUGIN_DIR')) {
    define('WPMU_PLUGIN_DIR', '/app/wp/wp-content/mu-plugins');
}
