<?php
/**
 * Custom MenuItem class
 */

namespace Conifer;

use Timber\MenuItem as TimberMenuItem;

/**
 * Custom MenuItem class for adding special nav behavior to
 * TimberMenuItem instances
 *
 * @package Conifer
 */
class MenuItem extends TimberMenuItem {
    const CLASS_HAS_CHILDREN            = 'menu-item-has-children';
    const CLASS_CURRENT                     = 'current-menu-item';
    const CLASS_CURRENT_ANCESTOR    = 'current-menu-ancestor';

    /**
     * Whether to display this item's children. Typically for use in
     * side navigation structures with lots of hierarchy.
     *
     * @return boolean true if this Item has nav children AND
     * represents the current page or an ancestor of the current page
     */
    public function display_children() {
        // If this item has children,
        // and it points to the current top-level post in the nav structure,
        // display its children
        return $this->has_children() && $this->points_to_current_post_or_ancestor();
    }

    /**
     * Whether this item points to the current post, or an ancestor of the current post
     *
     * @return boolean
     */
    public function points_to_current_post_or_ancestor() {
        return  in_array( static::CLASS_CURRENT, $this->classes )
                ||  in_array( static::CLASS_CURRENT_ANCESTOR, $this->classes );
    }

    /**
     * Whether this MenuItem has child MenuItems or not.
     *
     * @return boolean true if this MenuItem has children.
     */
    public function has_children() {
        return in_array( static::CLASS_HAS_CHILDREN, $this->classes );
    }
}



