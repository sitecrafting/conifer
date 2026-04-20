<?php

/**
 * EmailNotifier class
 */

namespace Conifer\Notifier;

/**
 * Class for emailing WordPress admins
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo
 * @package   Conifer
 */
abstract class EmailNotifier {
  use SendsEmail;

  /**
   * Get the destination email address(es)
   *
   * @return mixed the email(s) to send to, as a comma-separated string
   * or array
   */
  abstract public function to();
}


