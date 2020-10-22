<?php

/**
 * Test the Conifer\Shortcode\Button class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace ConiferTest;

use Conifer\Shortcode\Button;

class ButtonTest extends Base {
  protected $button;

  public function setUp() {
    parent::setUp();

    $this->button = new Button();
  }

  public function test_render_with_link() {
    $this->assertEquals(
      '<a href="/link" class="btn">A Link</a>',
      $this->button->render([], '<a href="/link">A Link</a>')
    );
  }

  public function test_render_with_wrapped_link() {
    $this->assertEquals(
      '<span><a href="/link" class="btn">A Link</a></span>',
      $this->button->render([], '<span><a href="/link">A Link</a></span>')
    );
  }

  public function test_render_with_custom_class() {
    $this->assertEquals(
      '<a href="/link" class="my-button-class">A Link</a>',
      $this->button->render(
        ['class' => 'my-button-class'],
        '<a href="/link">A Link</a>'
      ));
  }
}
