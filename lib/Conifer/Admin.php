<?php
/**
 * Customize the WP Admin
 */

namespace Conifer;

/**
 * Utility class for configuring WP Admin behavior.
 *
 * @package Conifer
 */
class Admin {
  /**
   * Register a theme settings page in the WP Admin.
   */
  public static function add_theme_settings_page() {
    if ( is_admin() && function_exists('acf_add_options_page') ) {
      acf_add_options_page([
        'page_title' => 'Theme Settings',
        'menu_slug' => 'theme-settings',
      ]);
    }
  }

  /**
   * Check the session for admin notices that might have been thrown before a redirect.
   */
  public static function check_for_notices() {
    if ( isset($_SESSION['admin_notices']) ) {
      $notices = $_SESSION['admin_notices'];
    } else {
      return;
    }

    foreach ( $notices as $notice ) {
      static::notify( $notice['message'], $notice['class'] );
    }

    $_SESSION['admin_notices'] = null;
  }

  /**
   * Set a session variable to display $message in an admin notification after the next redirect.
   *
   * @param  string $message the message to display
   * @param  string $class   the class to put on the message element
   */
  public static function notify_after_redirect( $message, $class = 'error' ) {
    $_SESSION['admin_notices']   = $_SESSION['admin_notices'] ?: [];
    $_SESSION['admin_notices'][] = [
      'message' => $message,
      'class' => $class,
    ];
  }

  /**
   * Display an admin notification
   *
   * @param  string $message the message to display
   * @param  string $class   the class to put on the message element
   */
  public static function notify( $message, $class = 'error' ) {
    add_action( 'admin_notices', function() use ($message, $class) {
      // NOTE: THE USER IS RESPONSIBLE FOR ESCAPING USER INPUT AS NECESSARY
      // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
      echo "<div class=\"{$class}\"><p>{$message}</p></div>";
    });
  }
}

