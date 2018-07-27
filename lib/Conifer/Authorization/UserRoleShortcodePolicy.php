<?php
/**
 * UserRoleShortcodePolicy class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Authorization;

use Timber\User;

/**
 * A ShortcodePolicy that filters content based on the current user's role
 */
class UserRoleShortcodePolicy extends ShortcodePolicy {
  /**
   * Check whether the user's role matches up with the required role
   * declared in the shortcode
   *
   * @inheritdoc
   */
  public function decide(
    array $atts,
    string $content,
    User $user
  ) : bool {
    // Parse the role[s] attribute to determine which roles are authorized
    $roleAttr        = $atts['role'] ?? $atts['roles'] ?? 'administrator';
    $authorizedRoles = array_map('trim', explode(',', $roleAttr));

    // Get the user's roles for comparison
    // WP returns user roles in an idiosyncratic way: role names are keys and
    // `true` values means the user has that role. We just want to flatten
    // this to a simple array of role/capability strings
    $userRoles = array_keys(array_filter($user->meta('wp_capabilities')));

    // Make sure the user has at least one authorized role
    return !empty(array_intersect($authorizedRoles, $userRoles));
  }

  /**
   * Get the shortcode tag to be declared
   *
   * @inheritdoc
   */
  protected function tag() : string {
    return 'check_role';
  }
}
