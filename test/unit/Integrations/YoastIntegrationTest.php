<?php

/**
 * Test the Conifer\Integration\YoastIntegration class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit\Integrations;

use Conifer\Unit\Base;
use WP_Mock;

class YoastIntegrationTest extends Base
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_demote_metabox_adds_wpseo_metabox_prio_filter()
    {
        $GLOBALS['is_admin'] = true;

        WP_Mock::expectFilterAdded(
            'wpseo_metabox_prio',
            function () {
                return 'low';
            }
        );

        \Conifer\Integrations\YoastIntegration::demote_metabox();

        // Assert added to quash PHPUnit warning about no assertions in test
        $this->assertTrue(true, 'Expected wpseo_metabox_prio filter to be added');
    }

    public function test_demote_metabox_does_not_add_filter_when_not_admin()
    {
        $GLOBALS['is_admin'] = false;

        WP_Mock::expectFilterNotAdded('wpseo_metabox_prio', function () {
            return 'low';
        });

        \Conifer\Integrations\YoastIntegration::demote_metabox();

        // Assert added to quash PHPUnit warning about no assertions in test
        $this->assertTrue(true, 'Expected wpseo_metabox_prio filter to not be added');
    }
}
