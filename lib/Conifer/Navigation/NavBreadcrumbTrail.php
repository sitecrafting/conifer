<?php

/**
 * MetroParks\Breadcrumb\NavBreadcrumbTrail class
 *
 * @copyright 2019 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Navigation;

use Conifer\Post\FrontPage;

use Traversable;

/**
 * Logic for navigation-menu-based breadcrumbs
 */
class NavBreadcrumbTrail implements BreadcrumbTrailInterface {
  /**
   * The nav menu to be parsed
   *
   * @var \Timber\Menu
   */
  protected $menu;

  /**
   * Constructor
   *
   * @param \Timber\Menu $menu the menu whose hierarchy we want to translate
   * into breadcrumbs.
   */
  public function __construct(Menu $menu) {
    $this->menu = $menu;
  }

  public function breadcrumbs(Core $core) : Iterable {
    $ancestry = [];
    foreach ($this->ancestry($core) as $crumb) {
      $ancestry[] = $crumb;
    }
    return array_reverse($ancestry);
  }

  protected function ancestry(Core $core) : Traversable {
    $item = $this->menu->item_for($core);

    // include current page as the *last* in the breadcrumb
    yield $core;

    // TODO MenuItem::parent() method
    while ($item && $this->menu->item_parent($item)) {
      $item = $this->menu->item_parent($item);
      yield $item;
    }

    // assume that the home page is at the top of our nav hierarchy
    yield FrontPage::get();
  }
}
