<?php

/**
 * Test the Conifer\Shortcode\AbstractBase class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit\Shortcode;

use Conifer\Shortcode\AbstractBase;
use Conifer\Unit\Base;
use WP_Mock;

class ShortcodeAbstractBaseTest extends Base
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_register_calls_add_shortcode_with_tag_and_closure()
    {
        $tag = 'test_shortcode';

        // Expect add_shortcode to be called with the correct tag and a closure
        WP_Mock::userFunction('add_shortcode', [
            'times' => 1,
            'args' => [$tag, \WP_Mock\Functions::type('callable')],
        ]);

        // Call the register method
        $response = AbstractBase::register($tag);

        // Assert something to quash warning about no assertions in test
        $this->assertNull($response);
    }
}
