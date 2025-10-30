<?php

declare(strict_types=1);

/**
 * Custom Menu class extending TimberMenu.
 */
namespace Conifer\Navigation;

use Timber\Menu as TimberMenu;

/**
 * Custom Menu class to add special nav behavior on top of
 * TimberMenu instances.
 *
 * @package Conifer
 */
class Menu extends TimberMenu {

  /**
   * Get the top-level nav item that points, or whose ancestor points,
   * to the current post
   *
   * @return ?Conifer\MenuItem the current top-level MenuItem
   */
  public function get_current_top_level_item() {
    foreach ( $this->get_items() as $item ) {
      if ( $item->points_to_current_post_or_ancestor() ) {
        return $item;
      }
    }

    return null;
  }
}
