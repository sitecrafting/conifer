<?php

declare(strict_types=1);

/**
 * Base class for Conifer test cases
 *
 * @copyright 2020 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */
namespace Conifer\Integration;

use WP_UnitTestCase;

use Conifer\Site;

/**
 * Base test class for the plugin. Declared abstract so that PHPUnit doesn't
 * complain about a lack of tests defined here.
 */
abstract class Base extends WP_UnitTestCase {
  /**
   * The Site instance representing the WP install we are testing
   *
   * @var Site
   */
  protected $site;

  public function setUp(): void {
    $this->site = new Site();
    $this->site->configure_defaults();
  }
}
