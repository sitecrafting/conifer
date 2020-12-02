<?php
/**
 * Base class for Conifer test cases
 *
 * @copyright 2020 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Integration;

use PHPUnit\Framework\TestCase;

use Conifer\Site;

/**
 * Base test class for the plugin. Declared abstract so that PHPUnit doesn't
 * complain about a lack of tests defined here.
 */
abstract class Base extends TestCase {
  /** @var Site */
  protected $site;

  public function setUp() {
    $this->site = new Site();
    $this->site->configure_defaults();
  }
}
