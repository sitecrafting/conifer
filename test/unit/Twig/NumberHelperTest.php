<?php

/**
 * Test the Conifer\Twig\NumberHelper class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit\Twig;

use Conifer\Twig\NumberHelper;
use Conifer\Unit\Base;

class NumberHelperTest extends Base
{
    private ?NumberHelper $helper = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->helper = new NumberHelper();
    }

    public function test_get_filters_returns_array_of_expected_filters()
    {
        $expectedFilters = [
            'us_phone',
        ];

        $filters = $this->helper->get_filters();

        foreach ($expectedFilters as $filterName) {
            $this->assertArrayHasKey($filterName, $filters);
            $this->assertIsCallable($filters[$filterName]);
        }
    }

    public function test_get_functions_returns_an_empty_array()
    {
        $functions = $this->helper->get_functions();
        $this->assertIsArray($functions);
        $this->assertEmpty($functions);
    }

    // us_phone tests
    public function test_us_phone_returns_unformatted_string_when_not_10_digits()
    {
        $this->assertEquals(
            '123456789',
            $this->helper->us_phone('123456789')
        );
        $this->assertEquals(
            '22345678901', // 11 digits, but doesn't start with 1, leading with 1 would cause it to be formatted
            $this->helper->us_phone('22345678901')
        );
    }

    public function test_us_phone_formats_10_digit_number()
    {
        $this->assertEquals(
            '(123) 456-7890',
            $this->helper->us_phone('1234567890')
        );
    }

    public function test_us_phone_formats_11_digit_number_with_leading_1()
    {
        $this->assertEquals(
            '(123) 456-7890',
            $this->helper->us_phone('11234567890')
        );
    }
}
