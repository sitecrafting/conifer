<?php

declare(strict_types=1);

/**
 * Test the Conifer\Site class
 *
 * @copyright 2020 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */
namespace Conifer\Unit;

use Conifer\Twig\TextHelper;

class TextHelperTest extends Base {
  public $helper;

  const THEME_DIRECTORY = 'wp-content/themes/foo';

  protected function setUp(): void {
    parent::setUp();

    $this->helper = new TextHelper();
  }

  public function test_oxford_comma(): void {
    $this->assertEquals(
      'one',
      $this->helper->oxford_comma(['one'])
    );
    $this->assertEquals(
      'one and two',
      $this->helper->oxford_comma(['one', 'two'])
    );
    $this->assertEquals(
      'one, two, and three',
      $this->helper->oxford_comma(['one', 'two', 'three'])
    );
    $this->assertEquals(
      'one, two, three, and four',
      $this->helper->oxford_comma(['one', 'two', 'three', 'four'])
    );
  }

  public function test_pluralize(): void {
    $this->assertEquals('person', $this->helper->pluralize('person', 1));
    $this->assertEquals('people', $this->helper->pluralize('person', 2));
    $this->assertEquals('zebras', $this->helper->pluralize('zebra', 2));
  }

  public function test_capitalize_each(): void {
    $this->assertEquals(
      'Three Blind Mice',
      $this->helper->capitalize_each('three blind mice')
    );
    $this->assertEquals(
      'One, Two, or Three',
      $this->helper->capitalize_each('one, two, or three')
    );
    $this->assertEquals(
      'The Old Man and the Sea',
      $this->helper->capitalize_each('the old man and the sea')
    );
    $this->assertEquals(
      'Made by SiteCrafting',
      $this->helper->capitalize_each('Made By SiteCrafting')
    );
  }
}
