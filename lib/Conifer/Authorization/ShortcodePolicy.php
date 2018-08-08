<?php
/**
 * ShortcodePolicy class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Authorization;

use Timber\User;

/**
 * Abstract class providing a basis for defining shortcodes that filter their
 * content according to custom authorization logic
 */
abstract class ShortcodePolicy extends AbstractPolicy {
  /**
   * The shortcode tag
   *
   * @var string
   */
  protected $tag;

  /**
   * Sets the shortcode tag for the new shortcode policy
   *
   * @param string $tag
   */
  public function __construct(string $tag = 'protected') {
    $this->tag = $tag;
  }

  /**
   * Filter the shortcode content based on the implementation of the `decide`
   * method.
   *
   * @return PolicyInterface fluent interface
   */
  public function adopt() : PolicyInterface {
    add_shortcode($this->tag(), function(
      array $atts,
      string $content = ''
    ) : string {
      return $this->enforce($atts, $content, $this->get_user());
    });

    return $this;
  }


  /**
   * Determine whether the user has access to content based on shortcode
   * attributes, user data, and possibly the content itself.
   *
   * @param array $atts the shortcode attributes
   * @param string $content the shortcode content
   * @param \Timber\User $user the user to check against
   * @return bool whether `$user` meets the criteria described in `$atts`
   */
  abstract public function decide(
    array $atts,
    string $content,
    User $user
  ) : bool;

  /**
   * Get the shortcode tag to be declared
   *
   * @see https://codex.wordpress.org/Function_Reference/add_shortcode
   * @return string the shortcode tag to declare
   */
  protected function tag() : string {
    return $this->tag;
  }

  /**
   * Filter the shortcode content based on the current user's data
   *
   * @param string $template the template file being loaded
   * @param \Timber\User the User whose privileges we want to check
   */
  public function enforce(
    array $atts,
    string $content,
    User $user
  ) : string {
    $authorized = $this->decide($atts, $content, $user);

    return $authorized
      ? $this->filter_authorized($content)
      : $this->filter_unauthorized($content);
  }


  /**
   * Get the user to check against shortcode attributes.
   * Override this method to perform authorization against someone other
   * than the current user.
   *
   * @return \Timber\User
   */
  protected function get_user() : User {
    return new User();
  }

  /**
   * Get the filtered shortcode content to display to unauthorized users.
   * Override this method to display something other than the empty string.
   *
   * @return string the content to display
   */
  protected function filter_unauthorized(string $content) : string {
    return '';
  }

  /**
   * Get the filtered shortcode content to display to _authorized_ users.
   * Override this method to display something other thatn the original content.
   *
   * @return string the content to display
   */
  protected function filter_authorized(string $content) : string {
    return $content;
  }
}
