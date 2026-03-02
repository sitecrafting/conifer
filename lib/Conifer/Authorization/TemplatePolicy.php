<?php
/**
 * TemplatePolicy class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Authorization;

use Timber\Timber;
use Timber\User;

/**
 * Abstract class providing a basis for defining custom, template-level
 * authorization logic
 */
abstract class TemplatePolicy extends AbstractPolicy {
  /**
   * Adopt this policy
   *
   * @return PolicyInterface fluent interface
   */
  public function adopt() : PolicyInterface {
    add_filter('template_include', function(string $template) {
      $this->enforce($template, Timber::get_user());
    });

    return $this;
  }

  /**
   * Enforce this template-level policy
   *
   * @param string $template the template file being loaded
   * @param \Timber\User the User whose privileges we want to check
   */
  abstract public function enforce(string $template, User $user);
}
