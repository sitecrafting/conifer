<?php

namespace Conifer\Unit\Support;

use Conifer\Navigation\Menu;

// Menu wrapper to test get_current_top_level_item()
// Menu will have the following structure:

class MenuDouble extends Menu
{
    public $items = [];

    public function __construct(?\WP_Term $term = null)
    {
        return parent::__construct($term);
    }
}
