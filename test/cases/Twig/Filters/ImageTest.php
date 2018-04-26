<?php

/**
 * Test the Conifer\Twig\Filters\Image helper class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace ConiferTest;

use Conifer\Twig\Filters\Image;

class ImageTest extends Base {
  public function test_src_to_retina() {
    $helper = new Image();
    $this->assertEquals(
      'foo.bar@2x.baz',
      $helper->src_to_retina('foo.bar.baz')
    );
  }
}
