<?php
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
  public static function demote_metabox() {
    if (is_admin()) {
      add_filter('wpseo_metabox_prio', function() {
        return 'low';
      });
    }
  }
}


