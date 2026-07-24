<?php

/**
 * Test the Conifer\Site class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Unit;

use WP_Mock;

use Conifer\Site;
use Conifer\Twig\HelperInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class SiteTest extends Base
{
  const THEME_DIRECTORY = 'wp-content/themes/foo';

  protected ?vfsStreamDirectory $file_system = null;

  public function setUp(): void
  {
    parent::setUp();

    // do a terrible amount of boilerplate to workaround Timber's decision
    // to put a ton of stuff in the constructor
    // NOTE: due to a deprecation in setMethods, the following is broken. Equivalent functionality has been implemented in the form of creating a new anonymous class and adding the methods to it
    // $theme = $this->getMockBuilder('\WP_Theme')
    //   ->setMethods([
    //     'get',
    //     'get_stylesheet',
    //     'get_template_directory_uri',
    //     'parent',
    //     '__toString',
    //   ])
    //   ->getMock();
    $theme = new class {
      public function get($key = null)
      {
        return '';
      }
      public function get_stylesheet()
      {
        return '';
      }
      public function get_template_directory_uri()
      {
        return '';
      }
      public function parent()
      {
        return null;
      }
      public function __toString()
      {
        return '';
      }
    };

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

    WP_Mock::userFunction('get_locale', [
      'return' => 'en_US',
    ]);

    // Set up a new virtual file system to test some of the site functions
    $structure         = [
      'theme-dir' => [
        'test.php'    => 'some text content',
        'assets.version' => '1',
        'custom-assets.version' => 'CUSTOM',
      ],
      'an_empty_folder' => [],
    ];
    $this->file_system = vfsStream::setup('root', null, $structure);
  }

  public function tearDown(): void
  {
    WP_Mock::tearDown();
  }

  public function test_find_file()
  {

    $site = new Site();

    $fileURL = $site->find_file('test.php', [
      vfsStream::url('root/theme-dir/'),
      vfsStream::url('root/an_empty_folder/'),
    ]);

    $this->assertEquals('vfs://root/theme-dir/test.php', $fileURL);
  }

  public function test_find_file_without_trailing_slash()
  {

    $site = new Site();

    $fileURL = $site->find_file('test.php', [
      vfsStream::url('root/theme-dir'),
    ]);

    $this->assertEquals('vfs://root/theme-dir/test.php', $fileURL);
  }

  public function test_find_file_fail()
  {

    $site = new Site();

    $fileURL = $site->find_file('test2.php', [
      vfsStream::url('root/theme-dir/'),
      vfsStream::url('root/an_empty_folder/'),
    ]);

    $this->assertEquals('', $fileURL);
  }

  public function test_get_assets_version()
  {

    $path = vfsStream::url('root/theme-dir/assets.version');
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_theme_file'])
      ->getMock();
    $site->expects($this->exactly(2))
      ->method('get_theme_file')
      ->with('assets.version')
      ->willReturn($path);

    // read the file value from our file in the virtual file system directory
    $this->assertEquals(
      '1',
      $site->get_assets_version()
    );
  }

  public function test_get_assets_version_with_arg()
  {

    $path = vfsStream::url('root/theme-dir/custom-assets.version');
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_theme_file'])
      ->getMock();
    $site->expects($this->exactly(2))
      ->method('get_theme_file')
      ->with('custom-assets.version')
      ->willReturn($path);

    // read the file value from our file in the virtual file system directory
    $this->assertEquals(
      'CUSTOM',
      $site->get_assets_version('custom-assets.version')
    );
  }

  public function test_subsequent_get_assets_version_with()
  {

    $paths = [
      'custom-assets.version' => vfsStream::url('root/theme-dir/custom-assets.version'),
      'assets.version' => vfsStream::url('root/theme-dir/assets.version'),
    ];
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_theme_file'])
      ->getMock();
    $site->expects($this->exactly(4))
      ->method('get_theme_file')
      ->willReturnCallback(function (string $file) use ($paths) {
        return $paths[$file];
      });

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

  public function test_get_assets_version_with_no_file()
  {

    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_theme_file'])
      ->getMock();

    $this->file_system = vfsStream::setup('root', null, [
      'theme-dir' => [],
    ]);

    $site->expects($this->once())
      ->method('get_theme_file')
      ->with('assets.version')
      ->willReturn(vfsStream::url('root/theme-dir/assets.version'));

    // read the file value from our file in the virtual file system directory
    $this->assertEquals('', $site->get_assets_version());
  }

  public function test_get_theme_file()
  {
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

  public function test_add_twig_helper()
  {
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

  public function test_get_twig_with_helper()
  {
    $site = new Site();

    // mock Twig API
    $twig = $this->getMockBuilder('Twig\Environment')
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
      ->will($this->returnValue(['foo' => function () {}]));
    $helper->expects($this->once())
      ->method('get_functions')
      ->will($this->returnValue(['bar' => function () {}]));

    $this->assertEquals(
      $twig,
      $site->get_twig_with_helper($twig, $helper)
    );
  }

  // ---------------------------------------------------------------------------
  // Helpers
  // ---------------------------------------------------------------------------

  /**
   * Return a Site partial mock with the constructor disabled.
   * Only the listed methods are stubbed; all other methods call the real
   * implementation, which is required so the hook-registration logic executes.
   *
   * @param string[] $methodsToStub Method names to stub on the mock.
   */
  private function makeSiteMock(array $methodsToStub = []): Site
  {
    return $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods($methodsToStub)
      ->getMock();
  }

  // ---------------------------------------------------------------------------
  // configure()
  // ---------------------------------------------------------------------------

  public function test_configure_calls_defaults_invokes_user_callback_and_returns_self()
  {
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['configure_defaults'])
      ->getMock();

    // configure_defaults must be called exactly once when the flag is true (default)
    $site->expects($this->once())->method('configure_defaults');

    $callbackInvoked = false;
    $capturedSelf    = null;

    $result = $site->configure(function () use (&$callbackInvoked, &$capturedSelf) {
      $callbackInvoked = true;
      // The callback is bound via Closure::call($this), so $this is the Site instance
      $capturedSelf = $this;
    });

    $this->assertTrue($callbackInvoked, 'User-defined callback was not invoked');
    $this->assertSame($site, $capturedSelf, 'Callback $this should be bound to the Site instance');
    $this->assertSame($site, $result, 'configure() must return $this for fluent chaining');
  }

  public function test_configure_skips_defaults_when_flag_is_false()
  {
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['configure_defaults'])
      ->getMock();

    $site->expects($this->never())->method('configure_defaults');

    $result = $site->configure(null, false);

    $this->assertSame($site, $result, 'configure() must return $this even when skipping defaults');
  }

  public function test_configure_with_null_callback_still_calls_defaults()
  {
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['configure_defaults'])
      ->getMock();

    $site->expects($this->once())->method('configure_defaults');

    $result = $site->configure(null);

    $this->assertSame($site, $result);
  }

  // ---------------------------------------------------------------------------
  // configure_default_classmaps()
  // ---------------------------------------------------------------------------

  public function test_configure_default_classmaps_registers_timber_classmap_filter()
  {
    $site = $this->makeSiteMock();

    WP_Mock::expectFilterAdded('timber/post/classmap', WP_Mock\Functions::type('callable'));

    $this->assertNull($site->configure_default_classmaps());
  }

  // ---------------------------------------------------------------------------
  // register_script()
  // ---------------------------------------------------------------------------

  public function test_register_script_resolves_version_from_custom_file_key()
  {
    // When $version is ['file' => '...'], the named file's contents are used as the version
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_assets_version', 'get_script_uri'])
      ->getMock();

    $site->expects($this->once())
      ->method('get_assets_version')
      ->with('my.version')
      ->willReturn('v1.2');
    $site->method('get_script_uri')
      ->with('main.js')
      ->willReturn('https://cdn.example.com/main.js');

    WP_Mock::userFunction('wp_register_script', [
      'times' => 1,
      'args'  => ['my-script', 'https://cdn.example.com/main.js', [], 'v1.2', true],
    ]);

    $site->register_script('my-script', 'main.js', [], ['file' => 'my.version']);
  }

  public function test_register_script_auto_resolves_version_when_true()
  {
    // When $version === true, get_assets_version() is called with the default file arg
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_assets_version', 'get_script_uri'])
      ->getMock();

    $site->expects($this->once())
      ->method('get_assets_version')
      ->willReturn('auto-ver');
    $site->method('get_script_uri')->willReturn('https://cdn.example.com/auto.js');

    WP_Mock::userFunction('wp_register_script', [
      'times' => 1,
      'args'  => ['auto-script', 'https://cdn.example.com/auto.js', [], 'auto-ver', true],
    ]);

    $site->register_script('auto-script', 'auto.js', [], true);
  }

  public function test_register_script_passes_explicit_version_string_without_resolution()
  {
    // An explicit string version bypasses the assets-version file lookup entirely
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_assets_version', 'get_script_uri'])
      ->getMock();

    $site->expects($this->never())->method('get_assets_version');
    $site->method('get_script_uri')->willReturn('https://cdn.example.com/pinned.js');

    WP_Mock::userFunction('wp_register_script', [
      'times' => 1,
      'args'  => ['pinned-script', 'https://cdn.example.com/pinned.js', [], '3.0.0', false],
    ]);

    $site->register_script('pinned-script', 'pinned.js', [], '3.0.0', false);
  }

  // ---------------------------------------------------------------------------
  // enqueue_script()
  // ---------------------------------------------------------------------------

  public function test_enqueue_script_resolves_version_from_custom_file_key()
  {
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_assets_version', 'get_script_uri'])
      ->getMock();

    $site->expects($this->once())
      ->method('get_assets_version')
      ->with('app.version')
      ->willReturn('enq-v1');
    $site->method('get_script_uri')->willReturn('https://cdn.example.com/enq.js');

    WP_Mock::userFunction('wp_enqueue_script', [
      'times' => 1,
      'args'  => ['enq-script', 'https://cdn.example.com/enq.js', ['dep'], 'enq-v1', true],
    ]);

    $site->enqueue_script('enq-script', 'enq.js', ['dep'], ['file' => 'app.version']);
  }

  public function test_enqueue_script_passes_explicit_version_string_without_resolution()
  {
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_assets_version', 'get_script_uri'])
      ->getMock();

    $site->expects($this->never())->method('get_assets_version');
    $site->method('get_script_uri')->willReturn('https://cdn.example.com/pinned.js');

    WP_Mock::userFunction('wp_enqueue_script', [
      'times' => 1,
      'args'  => ['pinned-enq', 'https://cdn.example.com/pinned.js', [], '2.0', false],
    ]);

    $site->enqueue_script('pinned-enq', 'pinned.js', [], '2.0', false);
  }

  // ---------------------------------------------------------------------------
  // register_style()
  // ---------------------------------------------------------------------------

  public function test_register_style_resolves_version_from_custom_file_key()
  {
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_assets_version', 'get_stylesheet_uri'])
      ->getMock();

    $site->expects($this->once())
      ->method('get_assets_version')
      ->with('css.version')
      ->willReturn('css-v2');
    $site->method('get_stylesheet_uri')->willReturn('https://cdn.example.com/style.css');

    WP_Mock::userFunction('wp_register_style', [
      'times' => 1,
      'args'  => ['theme-style', 'https://cdn.example.com/style.css', [], 'css-v2', 'all'],
    ]);

    $site->register_style('theme-style', 'style.css', [], ['file' => 'css.version']);
  }

  public function test_register_style_passes_explicit_version_string_without_resolution()
  {
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_assets_version', 'get_stylesheet_uri'])
      ->getMock();

    $site->expects($this->never())->method('get_assets_version');
    $site->method('get_stylesheet_uri')->willReturn('https://cdn.example.com/pinned.css');

    WP_Mock::userFunction('wp_register_style', [
      'times' => 1,
      'args'  => ['pinned-style', 'https://cdn.example.com/pinned.css', [], '1.5', 'screen'],
    ]);

    $site->register_style('pinned-style', 'pinned.css', [], '1.5', 'screen');
  }

  // ---------------------------------------------------------------------------
  // enqueue_style()
  // ---------------------------------------------------------------------------

  public function test_enqueue_style_auto_resolves_version_when_true()
  {
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_assets_version', 'get_stylesheet_uri'])
      ->getMock();

    $site->expects($this->once())
      ->method('get_assets_version')
      ->willReturn('auto-css');
    $site->method('get_stylesheet_uri')->willReturn('https://cdn.example.com/auto.css');

    WP_Mock::userFunction('wp_enqueue_style', [
      'times' => 1,
      'args'  => ['auto-style', 'https://cdn.example.com/auto.css', [], 'auto-css', 'all'],
    ]);

    $site->enqueue_style('auto-style', 'auto.css', [], true);
  }

  public function test_enqueue_style_passes_explicit_version_string_without_resolution()
  {
    $site = $this->getMockBuilder(Site::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get_assets_version', 'get_stylesheet_uri'])
      ->getMock();

    $site->expects($this->never())->method('get_assets_version');
    $site->method('get_stylesheet_uri')->willReturn('https://cdn.example.com/pinned-e.css');

    WP_Mock::userFunction('wp_enqueue_style', [
      'times' => 1,
      'args'  => ['pinned-enq-style', 'https://cdn.example.com/pinned-e.css', [], '4.0', 'print'],
    ]);

    $site->enqueue_style('pinned-enq-style', 'pinned.css', [], '4.0', 'print');
  }

  // ---------------------------------------------------------------------------
  // add_to_context()
  // ---------------------------------------------------------------------------

  public function test_add_to_context_populates_all_required_context_keys()
  {
    $site = $this->makeSiteMock();

    $mockMenu = new \stdClass();

    // Timber::get_menu is a static call; intercept it via a Mockery alias mock
    $timber = \Mockery::mock('alias:Timber\\Timber');
    $timber->shouldReceive('get_menu')
      ->once()
      ->with('primary')
      ->andReturn($mockMenu);

    WP_Mock::userFunction('get_body_class', [
      'times'  => 1,
      'return' => ['page-template-default', 'logged-in'],
    ]);
    WP_Mock::userFunction('get_search_query', [
      'times'  => 1,
      'return' => 'conifer test',
    ]);

    $result = $site->add_to_context([]);

    $this->assertSame($site, $result['site'], '"site" key should be the Site instance');
    $this->assertSame($mockMenu, $result['primary_menu']);
    $this->assertSame(['page-template-default', 'logged-in'], $result['body_classes']);
    $this->assertSame('conifer test', $result['search_query']);
  }

  public function test_add_to_context_merges_caller_data_with_defaults()
  {
    $site = $this->makeSiteMock();

    $timber = \Mockery::mock('alias:Timber\\Timber');
    $timber->shouldReceive('get_menu')->andReturn(null);

    WP_Mock::userFunction('get_body_class', ['return' => []]);
    WP_Mock::userFunction('get_search_query', ['return' => '']);

    $result = $site->add_to_context(['extra_key' => 'extra_value']);

    $this->assertSame('extra_value', $result['extra_key'], 'Caller-provided data should be present in result');
    // All default keys must still be populated
    $this->assertArrayHasKey('site', $result);
    $this->assertArrayHasKey('primary_menu', $result);
    $this->assertArrayHasKey('body_classes', $result);
    $this->assertArrayHasKey('search_query', $result);
  }

  // ---------------------------------------------------------------------------
  // configure_default_admin_dashboard_widgets() / remove_conifer_widget()
  // ---------------------------------------------------------------------------

  public function test_configure_default_admin_dashboard_widgets_registers_setup_action()
  {
    $site = $this->makeSiteMock();

    WP_Mock::expectActionAdded('wp_dashboard_setup', WP_Mock\Functions::type('callable'));

    $this->assertNull($site->configure_default_admin_dashboard_widgets());
  }

  public function test_remove_conifer_widget_registers_setup_action()
  {
    $site = $this->makeSiteMock();

    WP_Mock::expectActionAdded('wp_dashboard_setup', WP_Mock\Functions::type('callable'));

    $this->assertNull($site->remove_conifer_widget());
  }

  // ---------------------------------------------------------------------------
  // enable_admin_hotkeys() / disable_admin_hotkeys()
  // ---------------------------------------------------------------------------

  public function test_enable_admin_hotkeys_registers_enqueue_scripts_action()
  {
    $site = $this->makeSiteMock();

    WP_Mock::expectActionAdded('admin_enqueue_scripts', WP_Mock\Functions::type('callable'));

    $this->assertNull($site->enable_admin_hotkeys());
  }

  public function test_disable_admin_hotkeys_registers_enqueue_scripts_action()
  {
    $site = $this->makeSiteMock();

    WP_Mock::expectActionAdded('admin_enqueue_scripts', WP_Mock\Functions::type('callable'));

    $this->assertNull($site->disable_admin_hotkeys());
  }

  // ---------------------------------------------------------------------------
  // disable_comments()
  // ---------------------------------------------------------------------------

  public function test_disable_comments_registers_all_required_actions_and_filters()
  {
    $site = $this->makeSiteMock();

    // Actions
    WP_Mock::expectActionAdded('admin_init', WP_Mock\Functions::type('callable'));
    WP_Mock::expectActionAdded('admin_menu', WP_Mock\Functions::type('callable'));
    WP_Mock::expectActionAdded('wp_before_admin_bar_render', WP_Mock\Functions::type('callable'));

    // Closure-based filter
    WP_Mock::expectFilterAdded('manage_page_columns', WP_Mock\Functions::type('callable'));

    // String-callback filters with non-default priorities
    WP_Mock::expectFilterAdded('comments_array', '__return_empty_array');
    WP_Mock::expectFilterAdded('comments_open', '__return_false', 20);
    WP_Mock::expectFilterAdded('pings_open', '__return_false', 20);

    $this->assertNull($site->disable_comments());
  }
}
