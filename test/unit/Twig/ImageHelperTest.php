<?php

/**
 * Test the Conifer\Twig\ImageHelper class
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit\Twig;

use Conifer\Twig\ImageHelper;
use Conifer\Unit\Base;

class ImageHelperTest extends Base
{
    private ?ImageHelper $helper = null;

    private string $src = '';
    private string $src2x = '';
    private string $src3x = '';

    public function setUp(): void
    {
        parent::setUp();

        $this->helper = new ImageHelper();

        $fixtureDir = ABSPATH . '/img';
        $this->src = $fixtureDir . '/test-image-helper.jpg';
        $this->src2x = $fixtureDir . '/test-image-helper@2x.jpg';
        $this->src3x = $fixtureDir . '/test-image-helper@3x.jpg';

        // Fixtures are tiny placeholders; only existence checks matter for helper behavior.
        file_put_contents($this->src, 'base');
        file_put_contents($this->src2x, '2x');
        file_put_contents($this->src3x, '3x');
    }

    public function tearDown(): void
    {
        foreach ([$this->src, $this->src2x, $this->src3x] as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }

    public function test_get_filters_returns_expected_filter_callables()
    {
        $filters = $this->helper->get_filters();

        $this->assertArrayHasKey('src_to_retina', $filters);
        $this->assertArrayHasKey('src_to_retina_at_multiplier', $filters);
        $this->assertIsCallable($filters['src_to_retina']);
        $this->assertIsCallable($filters['src_to_retina_at_multiplier']);
    }

    public function test_get_functions_returns_expected_function_callables()
    {
        $functions = $this->helper->get_functions();

        $this->assertArrayHasKey('generate_retina_srcset', $functions);
        $this->assertIsCallable($functions['generate_retina_srcset']);
    }

    public function test_src_to_retina_inserts_2x_before_extension()
    {
        $this->assertSame('foo.bar@2x.baz', $this->helper->src_to_retina('foo.bar.baz'));
    }

    public function test_src_to_retina_at_multiplier_returns_empty_string_for_null_src()
    {
        $this->assertSame('', $this->helper->src_to_retina_at_multiplier(null));
    }

    public function test_src_to_retina_at_multiplier_returns_empty_string_when_original_file_missing()
    {
        $this->assertSame(
            '',
            $this->helper->src_to_retina_at_multiplier(ABSPATH . '/img/does-not-exist.jpg', 2)
        );
    }

    public function test_src_to_retina_at_multiplier_returns_original_src_for_multiplier_below_two()
    {
        $this->assertSame($this->src, $this->helper->src_to_retina_at_multiplier($this->src, 1));
    }

    public function test_src_to_retina_at_multiplier_returns_retina_src_when_target_file_exists()
    {
        $this->assertSame($this->src2x, $this->helper->src_to_retina_at_multiplier($this->src, 2));
    }

    public function test_src_to_retina_at_multiplier_returns_empty_string_when_retina_target_missing()
    {
        $this->assertSame($this->src3x, $this->helper->src_to_retina_at_multiplier($this->src, 3));

        unlink($this->src3x);

        $this->assertSame('', $this->helper->src_to_retina_at_multiplier($this->src, 3));
    }

    public function test_generate_retina_srcset_returns_empty_for_null_src()
    {
        $this->assertSame('', $this->helper->generate_retina_srcset(null, 4));
    }

    public function test_generate_retina_srcset_returns_empty_when_max_multiplier_is_less_than_two()
    {
        $this->assertSame('', $this->helper->generate_retina_srcset($this->src, 1));
    }

    public function test_generate_retina_srcset_returns_empty_when_original_file_is_missing()
    {
        $this->assertSame(
            '',
            $this->helper->generate_retina_srcset(ABSPATH . '/img/nope.jpg', 4)
        );
    }

    public function test_generate_retina_srcset_includes_existing_retina_files_only()
    {
        // Implementation currently uses $count < $max_multiplier, so 4 includes 2x and 3x.
        $this->assertSame(
            'srcset="' . $this->src . ', ' . $this->src2x . ' 2x, ' . $this->src3x . ' 3x"',
            $this->helper->generate_retina_srcset($this->src, 4)
        );

        unlink($this->src3x);

        $this->assertSame(
            'srcset="' . $this->src . ', ' . $this->src2x . ' 2x"',
            $this->helper->generate_retina_srcset($this->src, 4)
        );

        unlink($this->src2x);

        $this->assertSame('', $this->helper->generate_retina_srcset($this->src, 4));
    }
}
