<?php

declare(strict_types=1);

/**
 * Test the Conifer\Shortcode\Button class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */
namespace Conifer\Unit;

use Conifer\Shortcode\Button;

class ButtonTest extends Base {
  protected $button;

  protected function setUp(): void {
    parent::setUp();

    $this->button = new Button();
  }

  public function test_render_with_link(): void {
    $this->assertEquals(
      '<a href="/link" class="btn">A Link</a>',
      $this->button->render([], '<a href="/link">A Link</a>')
    );
  }

  public function test_render_with_wrapped_link(): void {
    $this->assertEquals(
      '<span><a href="/link" class="btn">A Link</a></span>',
      $this->button->render([], '<span><a href="/link">A Link</a></span>')
    );
  }

  public function test_render_with_custom_class(): void {
    $this->assertEquals(
      '<a href="/link" class="my-button-class">A Link</a>',
      $this->button->render(
        ['class' => 'my-button-class'],
        '<a href="/link">A Link</a>'
      ));
  }
}
