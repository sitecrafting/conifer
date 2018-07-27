<?php
/**
 * PolicyInterface
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Authorization;

/**
 * Interface for a high-level authorization API
 */
interface PolicyInterface {
  /**
   * Put this policy in place, typically via an action or filter
   */
  public function adopt() : self;

  /**
   * Create and adopt a new instance of this interface
   */
  public static function register() : self;
}
