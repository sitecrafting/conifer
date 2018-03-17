<?php

/**
 * Conifer test suite bootstrap file; included before every unit test run
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

require_once __DIR__.'/../vendor/autoload.php';

define('TEST_LIB_DIR', __DIR__.'/cases/');

spl_autoload_register(function(string $className) {
	$components = explode('\\', $className);

	if (array_shift($components) === 'ConiferTest') {
    $file = TEST_LIB_DIR . implode('/', $components) . '.php';

    if (file_exists($file)) {
      require $file;
    }
	}
});
