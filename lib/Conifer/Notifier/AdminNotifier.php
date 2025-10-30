<?php

declare(strict_types=1);

/**
 * AdminNotifier class
 */
namespace Conifer\Notifier;

/**
 * Class for emailing the default WordPress admin contact
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo
 * @package   Conifer
 */
class AdminNotifier extends EmailNotifier {
  /**
   * Get the admin email address configured in General Settings
   */
  public function to() {
    return get_option('admin_email');
  }
}
