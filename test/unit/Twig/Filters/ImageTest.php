<?php

/**
 * Test the Conifer\Twig\Filters\ImageHelper class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace Conifer\Unit;

use Conifer\Twig\ImageHelper;

class ImageHelperTest extends Base {
  public function test_src_to_retina() {
    $helper = new ImageHelper();
    $this->assertEquals(
      'foo.bar@2x.baz',
      $helper->src_to_retina('foo.bar.baz')
    );
  }
}
