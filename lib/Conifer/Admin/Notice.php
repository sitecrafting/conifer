<?php

declare(strict_types=1);

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
   * The session array key where flash notice data is stored
   *
   * @var string
   */
  const FLASH_SESSION_KEY = 'conifer_admin_notices';

  /**
   * Whether flash notices are enabled. Default: false
   */
  private static bool $flash_enabled = false;

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
  public function __construct(protected string $message, string $extraClasses = '') {
    // clean up classes and convert to an array
    $classes = array_map(trim(...), array_filter(explode(' ', $extraClasses)));

    $this->classes = array_unique(array_merge(['notice'], $classes));
  }

  /**
   * Clear all flash notices in session
   */
  public static function clear_flash_notices(): void {
    $_SESSION[static::FLASH_SESSION_KEY] = [];
  }

  /**
   * Enable flash notices to be stored in the `$_SESSION` superglobal
   */
  public static function enable_flash_notices(): void {
    self::$flash_enabled = true;

    add_action('admin_init', [static::class, 'display_flash_notices']);
  }

  /**
   * Disable flash notices
   */
  public static function disable_flash_notices(): void {
    self::$flash_enabled = false;
  }

  /**
   * Whether flash notices are enabled
   *
   * @return bool
   */
  public static function flash_notices_enabled() : bool {
    return self::$flash_enabled;
  }

  /**
   * Display any flash notices stored in session during the admin_notices hook
   */
  public static function display_flash_notices(): void {
    if (!static::flash_notices_enabled()) {
      return;
    }

    foreach (static::get_flash_notices() as $notice) {
      $notice->display();
    }

    static::clear_flash_notices();
  }

  /**
   * Get the flash notices to be displayed based on session data
   *
   * @return Notice[] an array of Notice instances
   */
  public static function get_flash_notices() : array {
    if (!static::flash_notices_enabled()) {
      return [];
    }

    $sessionNotices = $_SESSION[static::FLASH_SESSION_KEY] ?? [];
    if (empty($sessionNotices) || !is_array($sessionNotices)) {
      return [];
    }

    // filter out invalid notice data
    $sessionNotices = array_filter($sessionNotices, fn($notice, $idx): bool => static::valid_session_notice($notice), ARRAY_FILTER_USE_BOTH);

    return array_map(fn(array $notice): self => new static($notice['message'], $notice['class'] ?? ''), $sessionNotices);
  }

  /**
   * Display the admin notice
   *
   * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
   */
  public function display(): void {
    add_action('admin_notices', function(): void {
      // Because this class is designed to echo HTML, the user is responsible
      // for ensuring the message doesn't contain any malicious markup.
      // Class is already escaped.
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      echo $this->html();
    });
  }

  /**
   * Display this notice as an error
   */
  public function error(): void {
    $this->add_class('notice-error');
    $this->display();
  }

  /**
   * Display this notice as a warning
   */
  public function warning(): void {
    $this->add_class('notice-warning');
    $this->display();
  }

  /**
   * Display this notice as an info message
   */
  public function info(): void {
    $this->add_class('notice-info');
    $this->display();
  }

  /**
   * Display this notice as a success message
   */
  public function success(): void {
    $this->add_class('notice-success');
    $this->display();
  }

  /**
   * Display this notice as an error message on the next page load
   */
  public function flash_error(): void {
    $this->add_class('notice-error');
    $this->flash();
  }

  /**
   * Display this notice as a warning on the next page load
   */
  public function flash_warning(): void {
    $this->add_class('notice-warning');
    $this->flash();
  }

  /**
   * Display this notice as an info message on the next page load
   */
  public function flash_info(): void {
    $this->add_class('notice-info');
    $this->flash();
  }

  /**
   * Display this notice as a success message on the next page load
   */
  public function flash_success(): void {
    $this->add_class('notice-success');
    $this->flash();
  }

  /**
   * Display this notice on the next page load
   */
  public function flash(): void {
    // set up a handler for the admin_notices action, to ensure that any
    // flash notices are added AFTER displaying notices for this request
    add_action('admin_notices', function(): void {
      $_SESSION[static::FLASH_SESSION_KEY] ??= [];

      $_SESSION[static::FLASH_SESSION_KEY][] = [
        'class'   => $this->get_class(),
        'message' => $this->message,
      ];
    });
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
     * @param string $class
     * @return bool
     */
  public function has_class(string $class) : bool {
    return in_array($class, $this->classes, true);
  }


    /**
     * Validate a session notice array
     *
     * @param $notice
     * @return bool
     */
  protected static function valid_session_notice($notice) : bool {
    return is_array($notice)
      && !empty($notice['message'])
      && is_string($notice['message'])
      && (empty($notice['class']) || is_string($notice['class']));
  }
}
