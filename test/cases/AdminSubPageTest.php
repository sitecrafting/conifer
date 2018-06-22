<?php

/**
 * AdminSubPageTest class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace ConiferTest;

use WP_Mock;
use WP_Mock\Functions;

use Conifer\Admin\Page;
use Conifer\Admin\SubPage;

class AdminSubPageTest extends Base {
  public function test_do_add_sub_page() {
    WP_Mock::userFunction('sanitize_key', [
      'times'   => 1,
      'args'    => ['Hello'],
      'return'  => 'hello',
    ]);
    WP_Mock::userFunction('sanitize_key', [
      'times'   => 1,
      'args'    => ['Hello Again'],
      'return'  => 'helloagain',
    ]);
    WP_Mock::userFunction('add_submenu_page', [
      'times'   => 1,
      'args'    => [
        'hello',
        'Hello Again',
        'Hello Again',
        'manage_options',
        'helloagain',
        Functions::type('callable'),
      ],
    ]);

    $page = $this->getMockForAbstractClass(Page::class, ['Hello']);

    $subPage = $this->getMockForAbstractClass(SubPage::class, [
      $page,
      'Hello Again',
    ]);
    $subPage->do_add();
  }
}
