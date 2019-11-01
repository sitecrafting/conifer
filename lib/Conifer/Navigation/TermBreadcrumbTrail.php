<?php

/**
 * MetroParks\Breadcrumb\TermBreadcrumbTrail class
 *
 * @copyright 2019 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Navigation;

use Conifer\Post\Page;
use Conifer\Post\FrontPage;

use Timber\Core;
use Traversable;

/**
 * Logic for term-hierarchy-based breadcrumbs
 */
class TermBreadcrumbTrail implements BreadcrumbTrailInterface {
  public function breadcrumbs(Core $term) : Iterable {
    $ancestry = [];
    foreach ($this->ancestry($term) as $crumb) {
      $ancestry[] = $crumb;
    }
    return array_reverse($ancestry);
  }

  /**
   * @internal
   */
  protected function ancestry(Core $term) : Traversable {
    // include this term as the last item in the breadcrumbs
    yield $term;

    while ($term->parent()) {
      yield $term->parent();
    }

    // Home is always at the beginning
    yield FrontPage::get();
  }
}
