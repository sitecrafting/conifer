<?php

/**
 * Test the Conifer\AjaxHandler\AbstractBase class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Scott Dunham <sdunham@sitecrafting.com>
 */

declare(strict_types=1);

namespace Conifer\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use WP_Mock;

use Conifer\AjaxHandler\AbstractBase;

class AjaxHandlerTest extends Base {

    // Best or GREATEST?
    const BEST_BAND = 'Creed';

    protected MockObject $handler;

    protected function setUp(): void {
        parent::setUp();

        // Mock the abstract base AJAX handler class so we can test against it
        $ajaxHanderStub = $this->getMockForAbstractClass(AbstractBase::class, [ $this->get_request_array() ]);

        // Set up our stub's execute method to return the name of the best band
        $ajaxHanderStub
        ->expects($this->any())
        ->method('execute')
        ->willReturn([ 'best_band' => self::BEST_BAND ]);

        // Save for later use in test function(s)
        $this->handler = $ajaxHanderStub;
    }

    public function test_send_json_response(): void {
        // Tell PHPUnit to expect the following string to be
        // output, proclaiming what should be obvious to all
        $this->expectOutputString('{"best_band":"' . self::BEST_BAND . '"}');

        // Call the mocked abstract execute method to get the
        // raw response array of the AJAX handler function
        // (this is protected, so we need to use reflection)
        $response = $this->callProtectedMethod(
        $this->handler,
        'execute',
        [ $this->get_request_array() ]
        );

        // Mock the wp_send_json function to send a json_encoded
        // version of the response from above
        WP_Mock::userFunction('wp_send_json', [
        'times' => 1,
        'return' => function ($response ): void {
            echo json_encode($response); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
        },
        ]);

        // Call the protected send_json_response method to output the
        // JSON string response we'd expect for this mocked AJAX call
        $this->callProtectedMethod(
        $this->handler,
        'send_json_response',
        [ $response ]
        );
    }

    /**
     * Returns the request array.
     *
     * @return array<string, string>
     */
    private function get_request_array(): array {
        // AJAX handler classes require an action to be included
        // with each request to be considered valid
        return [ 'action' => 'best_band' ];
    }
}
