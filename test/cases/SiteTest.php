<?php
/**
 * Test the Conifer\Site class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace ConiferTest;

use WP_Mock;

use Conifer\Site;
use Conifer\Twig\HelperInterface;

class SiteTest extends Base {
  const THEME_DIRECTORY = 'wp-content/themes/foo';

  public function setUp() {
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
    ]);

    vfsStreamWrapper::register();
    vfsStreamWrapper::setRoot(new vfsStreamDirectory('exampleDir'));
  }

  public function tearDown() {
    WP_Mock::tearDown();
  }

  public function test_find_file() {
    $this->markTestSkipped();
  }

  public function test_get_assets_version() {
    $this->markTestSkipped();
  }

  public function test_get_theme_file() {
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

  public function test_add_twig_helper() {
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

  public function test_get_twig_with_helper() {
    $site = new Site();

    // mock Twig API
    $twig = $this->getMockBuilder('Twig_Environment')
      ->disableOriginalConstructor()
      ->setMethods(['addFilter', 'addFunction'])
      ->getMock();
    $twig->expects($this->once())
      ->method('addFilter');
    $twig->expects($this->once())
      ->method('addFunction');

    // mock HelperInterface
    $helper = $this->getMockBuilder(HelperInterface::class)
      ->setMethods(['get_functions', 'get_filters'])
      ->getMock();
    $helper->expects($this->once())
      ->method('get_filters')
      ->will($this->returnValue(['foo' => function() {}]));
    $helper->expects($this->once())
      ->method('get_functions')
      ->will($this->returnValue(['bar' => function() {}]));

    $this->assertEquals(
      $twig,
      $site->get_twig_with_helper($twig, $helper)
    );
  }
}
