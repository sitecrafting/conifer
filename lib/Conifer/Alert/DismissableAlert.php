<?php

declare(strict_types=1);

/**
 * DismissableAlert class
 *
 * @copyright 2019 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */
namespace Conifer\Alert;

/**
 * Class encapsulating a dissmissable alert, with cookie-based
 * persistence for tracking dismissals across requests
 */
class DismissableAlert {
  /**
   * Constructor. Takes the Alert message as a string.
   * Takes an optional array of options.
   *
   * @param string $message Alert message text
   * @param array $options an array with any of the following keys:
   *
   * - 'cookies': (array) The $_COOKIE superglobal (to allow for filtering)
   * - 'cookie_prefix': (string) The prefix for the cookie that indicates
   *   whether this Alert has been dismissed. This is appended to a hash of
   *   the message text to guaranteed uniqueness. Defaults to:
   *   "wp-user_dismissed_alert_"
   *   Note that on certain hosting
   *   platforms, notably Pantheon, cookies may be filtered out by the edge
   *   cache based on this prefix. Pantheon *does not* filter out cookies
   *   starting with `wp-` or `wordpress_`, so it's a good idea to start your
   *   prefix with one of these. See below for more details.
   * - 'cookie_expires': UTC datetime when cookie should expire. Defaults to
   *   UTC-formatted string of one year from now.
   * - 'cookie_path': the path to set for the cookie. Defaults to "/"
   *
   * @see https://pantheon.io/docs/cookies
   */
  public function __construct(
      protected string $message,
      protected array $options = []
  )
  {
  }

  /**
   * The full text of the cookie. This is handy for setting a cookie in
   * JavaScript via `document.cookie = "..."`
   *
   * @return string
   */
  public function cookie_text() : string {
    return sprintf(
      '%s=1; expires=%s; path=%s',
      $this->cookie_name(),
      $this->cookie_expires(),
      $this->cookie_path()
    );
  }

  /**
   * Returns an identifier unique to the Alert message
   *
   * @return string
   */
  public function cookie_name() : string {
    return $this->cookie_prefix() . md5($this->message);
  }

  /**
   * Get the cookie expiration date/time. This is only used in setting the
   * cookies on dismissal.
   *
   * @return string
   */
  public function cookie_expires() : string {
    if (!empty($this->options['cookie_expires'])) {
      return $this->options['cookie_expires'];
    }

    $now = new \DateTime('now', new \DateTimeZone('UTC'));
    $now->modify('+1 year');
    return $now->format('r');
  }

  /**
   * Get the path to set for the cookie. Defaults to "/"
   *
   * @return string
   */
  public function cookie_path() : string {
    return $this->options['cookie_path'] ?? '/';
  }

  /**
   * Get the Alert message
   *
   * @return string
   */
  public function message() : string {
    return $this->message;
  }

  /**
   * Whether this Alert has been dismissed (based on the user's cookies)
   *
   * @return bool
   */
  public function dismissed() : bool {
    return !empty($this->cookies()[$this->cookie_name()]);
  }


  /**
   * Get the cookies from options or the $_COOKIE superglobal
   *
   * @return array
   */
  protected function cookies() : array {
    return (array) ($this->options['cookies'] ?? $_COOKIE);
  }

  /**
   * String that the cookie should start with
   *
   * @return string
   */
  protected function cookie_prefix() : string {
    return $this->options['cookie_prefix'] ?? 'wp-user_dismissed_alert_';
  }
}
