<?php

/**
 * Test the Conifer\Site class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

declare(strict_types=1);

namespace Conifer\Unit;

use org\bovigo\vfs\vfsStreamDirectory;
use WP_Mock;

use Conifer\Site;
use Conifer\Twig\HelperInterface;
use org\bovigo\vfs\vfsStream;

class SiteTest extends Base {
    public vfsStreamDirectory $file_system;

    const THEME_DIRECTORY = 'wp-content/themes/foo';

    protected function setUp(): void {
        parent::setUp();

        // do a terrible amount of boilerplate to workaround Timber's decision
        // to put a ton of stuff in the constructor
        $theme = $this->getMockBuilder('\WP_Theme')
        ->setMethods([
        'get',
        'get_stylesheet',
        'get_template_directory_uri',
        'parent',
        '__toString',
        ])
        ->getMock();
        $theme->expects($this->any())
        ->method('__toString')
        ->will($this->returnValue(''));

        WP_Mock::userFunction('is_multisite', [
        'return' => false,
        ]);
        WP_Mock::userFunction('home_url', [
        'return' => 'http://appserver',
        ]);
        WP_Mock::userFunction('site_url', [
        'return' => 'http://appserver',
        ]);
        WP_Mock::userFunction('get_bloginfo', [
        'return' => [],
        ]);
        WP_Mock::userFunction('wp_get_theme', [
        'return' => $theme,
        ]);
        WP_Mock::userFunction('get_stylesheet_directory', [
        'return' => self::THEME_DIRECTORY,
        'times' => 4,
        ]);

        WP_Mock::userFunction('get_locale', [
        'return' => 'en_US',
        ]);

        // Set up a new virtual file system to test some of the site functions
        $structure         = [
        'theme-dir' => [
            'test.php'    => 'some text content',
            'assets.version' =>'1',
            'custom-assets.version' => 'CUSTOM',
        ],
        'an_empty_folder' => [],
        ];
        $this->file_system = vfsStream::setup('root', null, $structure);
    }

    protected function tearDown(): void {
        WP_Mock::tearDown();
    }

    public function test_find_file(): void {

        $site = new Site();

        $fileURL = $site->find_file('test.php', [
        vfsStream::url('root/theme-dir/'),
        vfsStream::url('root/an_empty_folder/'),
        ]);

        $this->assertEquals('vfs://root/theme-dir/test.php', $fileURL);
    }

    public function test_find_file_without_trailing_slash(): void {

        $site = new Site();

        $fileURL = $site->find_file('test.php', [
        vfsStream::url('root/theme-dir'),
        ]);

        $this->assertEquals('vfs://root/theme-dir/test.php', $fileURL);
    }

    public function test_find_file_fail(): void {

        $site = new Site();

        $fileURL = $site->find_file('test2.php', [
        vfsStream::url('root/theme-dir/'),
        vfsStream::url('root/an_empty_folder/'),
        ]);

        $this->assertEquals('', $fileURL);
    }

    public function test_get_assets_version(): void {

        $site = new Site();

        // We will set the stylesheet directory to our virtual file system directory
        WP_Mock::userFunction('get_stylesheet_directory', [
        'return' => vfsStream::url('root/theme-dir'),
        'times' => 2,
        ]);

        // read the file value from our file in the virtual file system directory
        $this->assertEquals(
        '1',
        $site->get_assets_version()
        );
    }

    public function test_get_assets_version_with_arg(): void {

        $site = new Site();

        // We will set the stylesheet directory to our virtual file system directory
        WP_Mock::userFunction('get_stylesheet_directory', [
        'return' => vfsStream::url('root/theme-dir'),
        'times' => 2,
        ]);

        // read the file value from our file in the virtual file system directory
        $this->assertEquals(
        'CUSTOM',
        $site->get_assets_version('custom-assets.version')
        );
    }

    public function test_subsequent_get_assets_version_with(): void {

        $site = new Site();

        // We will set the stylesheet directory to our virtual file system directory
        WP_Mock::userFunction('get_stylesheet_directory', [
        'return' => vfsStream::url('root/theme-dir'),
        'times' => 4,
        ]);

        // read the file value from our file in the virtual file system directory
        $this->assertEquals(
        'CUSTOM',
        $site->get_assets_version('custom-assets.version')
        );
        $this->assertEquals(
        '1',
        $site->get_assets_version('assets.version')
        );
    }

    public function test_get_assets_version_with_no_file(): void {

        $site = new Site();

        $this->file_system = vfsStream::setup('root', null, [
        'theme-dir' => [],
        ]);

        // We will set the stylesheet directory to our virtual file system directory
        WP_Mock::userFunction('get_stylesheet_directory', [
        'return' => vfsStream::url('root/theme-dir'),
        'times' => 1,
        ]);

        // read the file value from our file in the virtual file system directory
        $this->assertEquals('', $site->get_assets_version());
    }

    public function test_get_theme_file(): void {
        $site = new Site();

        WP_Mock::userFunction('get_stylesheet_directory', [
        'return' => 'wp-content/themes/foo',
        ]);

        // method should add a leading slash to the filename if necessary
        $this->assertEquals(
        self::THEME_DIRECTORY . '/bar.txt',
        $site->get_theme_file('bar.txt')
        );
        // a leading slash should be preserved
        $this->assertEquals(
        self::THEME_DIRECTORY . '/bar.txt',
        $site->get_theme_file('/bar.txt')
        );
    }

    public function test_add_twig_helper(): void {
        $site = new Site();

        // mock HelperInterface
        $helper = $this->getMockBuilder(HelperInterface::class)
        ->getMock();

        // mock Twig API
        WP_Mock::expectFilterAdded('get_twig', WP_Mock\Functions::type('callable'));

        // add the helper
        // NOTE: the real assertion is above, we just assert null here so PHPUnit
        // doesn't yell at us for not asserting anything
        $this->assertNull($site->add_twig_helper($helper));
    }

    public function test_get_twig_with_helper(): void {
        $site = new Site();

        // mock Twig API
        $twig = $this->getMockBuilder(\Twig\Environment::class)
        ->disableOriginalConstructor()
        ->setMethods([ 'addFilter', 'addFunction' ])
        ->getMock();
        $twig->expects($this->once())
        ->method('addFilter');
        $twig->expects($this->once())
        ->method('addFunction');

        // mock HelperInterface
        $helper = $this->getMockBuilder(HelperInterface::class)
        ->setMethods([ 'get_functions', 'get_filters' ])
        ->getMock();
        $helper->expects($this->once())
        ->method('get_filters')
        ->will($this->returnValue([ 'foo' => function (): void {} ]));
        $helper->expects($this->once())
        ->method('get_functions')
        ->will($this->returnValue([ 'bar' => function (): void {} ]));

        $this->assertEquals(
        $twig,
        $site->get_twig_with_helper($twig, $helper)
        );
    }
}
