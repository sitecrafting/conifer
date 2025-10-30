<?php

declare(strict_types=1);

/**
 * YoastIntegration class
 */
namespace Conifer\Integrations;

/**
 * General-purpose wrapper for Yoast WordPress SEO plugin
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */
class YoastIntegration {
  /**
   * Demote Yoast SEO meta box to the bottom of the post edit screen
   */
  public static function demote_metabox(): void {
    if (is_admin()) {
      add_filter('wpseo_metabox_prio', fn(): string => 'low');
    }
  }
}
