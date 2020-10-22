<?php

/**
 * Tests for the Conifer\Admin\Page class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Unit;

use WP_Mock;
use WP_Mock\Functions;

use Conifer\Admin\Page;
use Conifer\Admin\SubPage;

class AdminPageTest extends Base {
  private $page;

  public function setUp() {
    parent::setUp();

    WP_Mock::userFunction('sanitize_key', [
      'times'  => 1,
      'args'   => 'Hello',
      'return' => 'hello',
    ]);

    $this->page = $this->getMockForAbstractClass(Page::class, ['Hello']);
  }

  public function test_add() {
    WP_Mock::expectActionAdded('admin_menu', Functions::type('callable'));

    // fluid interface
    $this->assertEquals($this->page, $this->page->add());
  }

  public function test_add_sub_page() {
    WP_Mock::userFunction('sanitize_key', [
      'times'  => 1,
      'args'   => 'Hello Again',
      'return' => 'helloagain',
    ]);

    WP_Mock::expectActionAdded('admin_menu', Functions::type('callable'));

    // register a mock of the abstract SubPage class,
    // to get something we can instantiate in add_sub_page()
    $this->getMockForAbstractClass(SubPage::class, [], 'SubPageMock', false);

    // fluid interface
    $this->assertEquals($this->page, $this->page->add_sub_page(
      'SubPageMock',
      'Hello Again'
    ));
  }

  public function test_do_add() {
    WP_Mock::userFunction('add_menu_page', [
      'times'  => 1,
      'args'   => [
        'Hello',
        'Hello',
        'manage_options',
        'hello',
        Functions::type('callable'),
        null,
      ],
    ]);

    // fluid interface
    $this->assertEquals($this->page, $this->page->do_add());
  }

  // TODO test ::render() in an integration test
}
