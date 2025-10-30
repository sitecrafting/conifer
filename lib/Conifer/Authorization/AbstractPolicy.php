<?php

declare(strict_types=1);

/**
 * AbstractPolicy class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */
namespace Conifer\Authorization;

/**
 * Abstract class providing a basis for defining custom, template-level
 * authorization logic
 */
abstract class AbstractPolicy implements PolicyInterface {
  /**
   * Create and adopt a new instance
   */
  public static function register() : PolicyInterface {
    return (new static())->adopt();
  }
}
