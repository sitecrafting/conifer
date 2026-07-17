<?php

/**
 * Test the Conifer\Shortcode\Button class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace Conifer\Unit;

use Conifer\Shortcode\Button;
use DOMDocument;
use DOMElement;

class ButtonTest extends Base
{
  protected null|Button $button;

  public function setUp(): void
  {
    parent::setUp();

    $this->button = new Button();
  }

  public function test_render_with_link()
  {
    $this->assertEquals(
      '<a href="/link" class="btn">A Link</a>',
      $this->button->render([], '<a href="/link">A Link</a>')
    );
  }

  public function test_render_with_wrapped_link()
  {
    $this->assertEquals(
      '<span><a href="/link" class="btn">A Link</a></span>',
      $this->button->render([], '<span><a href="/link">A Link</a></span>')
    );
  }

  public function test_render_with_custom_class()
  {
    $this->assertEquals(
      '<a href="/link" class="my-button-class">A Link</a>',
      $this->button->render(
        ['class' => 'my-button-class'],
        '<a href="/link">A Link</a>'
      )
    );
  }

  public function test_render_returns_empty_string_when_html_is_empty()
  {
    $button = new Button();

    $result = $button->render(['class' => 'cta'], '');

    $this->assertSame('', $result);
  }

  public function test_render_applies_default_class_to_first_anchor()
  {
    $button = new Button();
    $html = '<a href="/contact">Contact</a>';

    $result = $button->render([], $html);
    $link = $this->getFirstAnchor($result);

    $this->assertSame(Button::DEFAULT_BUTTON_CLASS, $link->getAttribute('class'));
    $this->assertSame('/contact', $link->getAttribute('href'));
  }

  public function test_render_applies_custom_class_to_first_anchor()
  {
    $button = new Button();
    $html = '<a href="/about">About</a>';

    $result = $button->render(['class' => 'btn btn-primary'], $html);
    $link = $this->getFirstAnchor($result);

    $this->assertSame('btn btn-primary', $link->getAttribute('class'));
  }

  public function test_render_replaces_existing_class_instead_of_appending()
  {
    $button = new Button();
    $html = '<a href="/donate" class="old-class">Donate</a>';

    $result = $button->render(['class' => 'new-class'], $html);
    $link = $this->getFirstAnchor($result);

    $this->assertSame('new-class', $link->getAttribute('class'));
  }

  public function test_render_updates_only_the_first_anchor_when_multiple_exist()
  {
    $button = new Button();
    $html = '<a href="/first">First</a><a href="/second" class="keep-me">Second</a>';

    $result = $button->render(['class' => 'primary'], $html);

    $dom = new DOMDocument();
    $dom->loadHTML($result, LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $links = $dom->getElementsByTagName('a');

    $this->assertSame('primary', $links->item(0)->getAttribute('class'));
    $this->assertSame('keep-me', $links->item(1)->getAttribute('class'));
  }

  public function test_render_returns_trimmed_markup_when_no_anchor_exists()
  {
    $button = new Button();
    $html = '  <p>No anchor tag here</p>  ';

    $result = $button->render(['class' => 'ignored'], $html);

    // No <a> means no DOM rewrite; method returns the original HTML trimmed.
    $this->assertSame('<p>No anchor tag here</p>', $result);
  }

  private function getFirstAnchor(string $html): DOMElement
  {
    $dom = new DOMDocument();
    $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $link = $dom->getElementsByTagName('a')->item(0);

    $this->assertInstanceOf(DOMElement::class, $link);

    return $link;
  }
}
