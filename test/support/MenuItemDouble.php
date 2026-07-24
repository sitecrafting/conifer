<?php

namespace Conifer\Unit\Support;

use Conifer\Navigation\MenuItem;

// Menu wrapper to test get_current_top_level_item() without having to register a menu in WordPress.

class MenuItemDouble extends MenuItem
{
    public function __construct(?\WP_Post $wp_object, $menu = null)
    {
        parent::__construct($wp_object, $menu);

    }
}
