<?php

/**
 * Conifer utility functions
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author  	Coby Tamayo <ctamayo@sitecrafting.com>
 */
namespace Conifer\Util;

/**
 * Log stuff to wp-content/debug.log, with a prefix so you can pipe
 * to a grep to filter out irrelevant log entries.
 * Assumes you have WP debugging configured like so in wp-config.php:
 * ```
 * define('WP_DEBUG',         true);
 * define('WP_DEBUG_LOG',     true);
 * define('WP_DEBUG_DISPLAY', false);
 * ```
 * @param  mixed $message the message or other value to log. If not a string, gets var_export'ed
 */
function debug($message) {
  if(!is_string($message)) {
    $message = var_export($message,true);
  }

  // break up message by line and prefix each one
  error_log( implode("\n", array_map(function($line) {
    return "debug $line";
  }, explode("\n", $message))));
}

/**
 * Variadic (sprintf() style) version of debug(). Calls sprintf() internally.
 *
 * @param mixed ...$args variadic argument list, like sprintf.
 * @example
 * ```
 * sdebug('%s, %s!', 'Hello', 'World');
 * // -> logs "Hello, World!"
 * ```
 */
function sdebug() {
  // TODO use the PHP 7 spread operator! :D
  debug(call_user_func_array('sprintf', func_get_args()));
}

/**
 * Die on $message
 *
 * @param mixed $message the variable to print out
 * @param bool $pre whether to print $message inside a <pre> element.
 * Defaults to true.
 */
function debug_die($message, bool $pre = true) {
  if (!is_string($message)) {
    $message = var_export($message, true);
  }

  if ($pre) {
    echo '<pre>';
  }

  die($message);
}
