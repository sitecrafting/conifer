<?php
/**
 * Base class for Conifer Integration test cases
 *
 * @copyright 2020 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Integration;

use WP_UnitTestCase;

/**
 * Base test class for the plugin. Declared abstract so that PHPUnit doesn't
 * complain about a lack of tests defined here.
 */
abstract class Base extends WP_UnitTestCase {
}
