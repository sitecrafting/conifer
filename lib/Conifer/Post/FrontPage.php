<?php

/**
 * Home page class
 */

declare(strict_types=1);

namespace Conifer\Post;

use Timber\Timber;

/**
 * Class to represent the home page.
 *
 * @package Conifer
 */
class FrontPage extends Page {
    /**
     * Get the FrontPage instance.
     *
     * @return \Timber\Post a FrontPage object
     */
    public static function get() {
        return Timber::get_post(get_option('page_on_front'));
    }
}
