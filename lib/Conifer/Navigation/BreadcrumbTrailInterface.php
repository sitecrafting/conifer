<?php

/**
 * BreadcrumbTrail interface
 *
 * @copyright 2019 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Navigation;

use Timber\Core;

/**
 * Generic interface for a Breadcrumb Trail component.
 */
interface BreadcrumbTrailInterface {
  /**
   * Get the breadcrumbs (pages, terms, etc.) in this breadcrumb trail
   *
   * @return an Iterable (e.g. array) of breadcrumb objects or arrays
   * (typically Conifer Post or Term objects).
   */
  public function breadcrumbs(Core $core) : Iterable;
}
