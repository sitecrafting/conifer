<?php
/*
 * Conifer test suite bootstrap file; included before every unit test run
 *
 * @copyright 2020 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

require_once __DIR__ . '/../vendor/autoload.php';

if (is_dir(__DIR__ . '/wp-tests-lib')) {
  require_once __DIR__ . '/wp-tests-lib/includes/functions.php';
  require_once __DIR__ . '/wp-tests-lib/includes/bootstrap.php';
}
