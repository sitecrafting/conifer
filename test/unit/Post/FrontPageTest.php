<?php

/**
 * Tests for the Conifer\Post\FrontPage class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */


namespace Conifer\Unit\Post;

use Conifer\Post\FrontPage;
use Conifer\Unit\Base;

class FrontPageTest extends Base
{
    protected null|FrontPage $frontPage = null;

    public function setUp(): void
    {
        parent::setUp();
    }

    // Test that the get() method returns a FrontPage instance
    public function test_get_method_returns_front_page_instance()
    {
        $frontPage = FrontPage::get();

        $this->assertInstanceOf(FrontPage::class, $frontPage);
    }
}
