<?php
/**
 * Conifer\Admin\Notice class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Admin;

/**
 * Provides a high-level API for dislaying all sorts of WP Admin notices
 */
class Notice {
  /**
   * Classes to put on the notice <div>
   *
   * @var string
   */
  protected $classes;

  /**
   * Constructor
   *
   * @param string $message the message to display
   * @param string $extraClasses any extra HTML class to display.
   * Multiple classes can be specified with a space-separated string, e.g.
   * `"one two three"`
   */
  public function __construct(string $message, string $extraClasses = '') {
    $this->message = $message;

    // clean up classes and convert to an array
    $classes = array_map('trim', array_filter(explode(' ', $extraClasses)));

    $this->classes = array_merge(['notice'], $classes);
  }

  /**
   * Display the admin notice
   *
   * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
   */
  public function display() {
    add_action('admin_notices', function() {
      // Because this class is designed to echo HTML, the user is responsible
      // for ensuring the message doesn't contain any malicious markup.
      // Class is already escaped.
      // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
      echo $this->html();
    });
  }

  /**
   * Display this notice as an error
   */
  public function error() {
    $this->add_class('notice-error');
    $this->display();
  }

  /**
   * Display this notice as a warning
   */
  public function warning() {
    $this->add_class('notice-warning');
    $this->display();
  }

  /**
   * Display this notice as an info message
   */
  public function info() {
    $this->add_class('notice-info');
    $this->display();
  }

  /**
   * Display this notice as a success message
   */
  public function success() {
    $this->add_class('notice-success');
    $this->display();
  }

  /**
   * Get the message `<div>` markup
   *
   * @return string the HTML to be rendered
   */
  public function html() : string {
    // default to error style
    if (!$this->has_style_class()) {
      $this->add_class('notice-error');
    }

    return sprintf(
      '<div class="%s"><p>%s</p></div>',
      esc_attr($this->get_class()),
      $this->message
    );
  }

  /**
   * Add an HTML class to be rendered on this notice
   *
   * @param string $class the class to be added
   * @return Notice
   */
  public function add_class(string $class) : self {
    if ($this->has_class($class)) {
      // noop
      return $this;
    }

    $this->classes[] = trim($class);
    return $this;
  }

  /**
   * Get the HTML class or classes to be rendered in the notice markup
   *
   * @return string e.g. `"notice notice-error"`
   */
  public function get_class() : string {
    return trim(implode(' ', $this->classes));
  }

  /**
   * Whether this notice has a special style class that WordPress targets
   * in its built-in admin styles.
   *
   * @return bool
   */
  public function has_style_class() : bool {
    $styleClasses = [
      'notice-error',
      'notice-warning',
      'notice-info',
      'notice-success',
    ];

    foreach ($styleClasses as $class) {
      if ($this->has_class($class)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Whether this Notice has the given $class
   *
   * @param bool
   */
  public function has_class(string $class) : bool {
    return in_array($class, $this->classes, true);
  }
}
