<?php

/**
 * Test the Conifer\AjaxHandler\AbstractBase class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Scott Dunham <sdunham@sitecrafting.com>
 */

namespace Conifer\Unit\AjaxHandler;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use WP_Mock;

use Conifer\AjaxHandler\AbstractBase;
use Conifer\Unit\Base;

class AjaxHandlerTest extends Base
{

  // Creed is the best band? Uh huh, uh huh, suuuuure
  // Best or GREATEST?
  const BEST_BAND = 'Creed';

  protected ?AbstractBase $handler = null;

  public function setUp(): void
  {
    parent::setUp();

    WP_Mock::userFunction('has_action', [
      'return' => false,
    ]);

    // Mock the abstract base AJAX handler class so we can test against it
    $ajaxHanderStub = $this->getMockForAbstractClass(AbstractBase::class, [$this->get_request_array()]);

    // Set up our stub's execute method to return the name of the best band
    $ajaxHanderStub
      ->expects($this->any())
      ->method('execute')
      ->willReturn(['best_band' => self::BEST_BAND]);

    // Save for later use in test function(s)
    $this->handler = $ajaxHanderStub;
  }

  public function test_send_json_response()
  {
    // Tell PHPUnit to expect the following string to be
    // output, proclaiming what should be obvious to all
    $this->expectOutputString('{"best_band":"' . self::BEST_BAND . '"}');

    // Call the mocked abstract execute method to get the
    // raw response array of the AJAX handler function
    // (this is protected, so we need to use reflection)
    $response = $this->callProtectedMethod(
      $this->handler,
      'execute',
      [$this->get_request_array()]
    );

    // Mock the wp_send_json function to send a json_encoded
    // version of the response from above
    WP_Mock::userFunction('wp_send_json', [
      'times' => 1,
      'return' => function ($response) {
        echo json_encode($response); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
      },
    ]);

    // Call the protected send_json_response method to output the
    // JSON string response we'd expect for this mocked AJAX call
    $this->callProtectedMethod(
      $this->handler,
      'send_json_response',
      [$response]
    );
  }

  private function get_request_array()
  {
    // AJAX handler classes require an action to be included
    // with each request to be considered valid
    return ['action' => 'best_band'];
  }

  // ---------------------------------------------------------------------------
  // Constructor
  // ---------------------------------------------------------------------------

  public function test_constructor_throws_when_action_key_is_absent_from_request()
  {
    $this->expectException(\LogicException::class);

    new AjaxHandlerInspectable(['data' => 'no_action_here']);
  }

  // ---------------------------------------------------------------------------
  // handle() / handle_post() / handle_get()
  // ---------------------------------------------------------------------------

  public function test_handle_creates_handler_executes_and_stores_response()
  {
    AjaxHandlerInspectable::$lastResponse = null;

    AjaxHandlerInspectable::handle(['action' => 'inspect', 'foo' => 'bar']);

    $response = AjaxHandlerInspectable::$lastResponse;
    $this->assertIsArray($response);
    $this->assertSame('inspect', $response['action']);
    $this->assertSame(['action' => 'inspect', 'foo' => 'bar'], $response['params']);
  }

  public function test_handle_passes_cookie_superglobal_to_handler()
  {
    $savedCookie = $_COOKIE;
    $_COOKIE     = ['session_token' => 'abc123'];
    AjaxHandlerInspectable::$lastResponse = null;

    try {
      AjaxHandlerInspectable::handle(['action' => 'inspect']);
    } finally {
      $_COOKIE = $savedCookie;
    }

    $response = AjaxHandlerInspectable::$lastResponse;
    $this->assertIsArray($response);
    $this->assertSame(['session_token' => 'abc123'], $response['cookies']);
  }

  public function test_handle_falls_back_to_request_superglobal_when_no_data_passed()
  {
    $savedRequest = $_REQUEST;
    $_REQUEST     = ['action' => 'from_request', 'key' => 'val'];
    AjaxHandlerInspectable::$lastResponse = null;

    try {
      AjaxHandlerInspectable::handle();
    } finally {
      $_REQUEST = $savedRequest;
    }

    $response = AjaxHandlerInspectable::$lastResponse;
    $this->assertIsArray($response, 'handle() with no args should populate $lastResponse from $_REQUEST');
    $this->assertSame('from_request', $response['action']);
    $this->assertSame(['action' => 'from_request', 'key' => 'val'], $response['params']);
  }

  public function test_handle_post_delegates_post_superglobal_to_handle()
  {
    $savedPost = $_POST;
    $_POST     = ['action' => 'post_action', 'data' => 'posted'];
    AjaxHandlerInspectable::$lastResponse = null;

    try {
      AjaxHandlerInspectable::handle_post();
    } finally {
      $_POST = $savedPost;
    }

    $response = AjaxHandlerInspectable::$lastResponse;
    $this->assertIsArray($response);
    $this->assertSame('posted', $response['params']['data']);
  }

  public function test_handle_get_delegates_get_superglobal_to_handle()
  {
    $savedGet = $_GET;
    $_GET     = ['action' => 'get_action', 'q' => 'search_term'];
    AjaxHandlerInspectable::$lastResponse = null;

    AjaxHandlerInspectable::handle_get();

    $_GET = $savedGet;

    $response = AjaxHandlerInspectable::$lastResponse;
    $this->assertIsArray($response);
    $this->assertSame('search_term', $response['params']['q']);
  }

  // ---------------------------------------------------------------------------
  // dispatch_action() failure modes
  // ---------------------------------------------------------------------------

  public function test_dispatch_action_throws_logic_exception_when_action_has_no_mapped_method()
  {
    $this->expectException(\LogicException::class);

    $handler = new AjaxHandlerInspectable(['action' => 'unmapped_action']);
    $handler->exposeDispatch();
  }

  public function test_dispatch_action_throws_bad_method_call_when_mapped_method_does_not_exist()
  {
    $this->expectException(\BadMethodCallException::class);

    $handler = new AjaxHandlerInspectable(['action' => 'my_action']);
    $handler->exposeMap('my_action', 'this_method_does_not_exist');
    $handler->exposeDispatch();
  }

  public function test_dispatch_action_throws_bad_method_call_when_mapped_method_is_static()
  {
    // dispatch_action explicitly forbids static methods as handlers
    $this->expectException(\BadMethodCallException::class);

    $handler = new AjaxHandlerInspectable(['action' => 'my_action']);
    $handler->exposeMap('my_action', 'aStaticMethod');
    $handler->exposeDispatch();
  }

  // ---------------------------------------------------------------------------
  // dispatch_action() + map_action() success path
  // ---------------------------------------------------------------------------

  public function test_dispatch_action_calls_mapped_instance_method_and_returns_its_result()
  {
    $handler = new AjaxHandlerInspectable(['action' => 'my_action']);
    $handler->exposeMap('my_action', 'anInstanceMethod');

    $result = $handler->exposeDispatch();

    $this->assertSame(['dispatched_by' => 'anInstanceMethod'], $result);
  }

  public function test_map_action_returns_handler_instance_for_fluent_chaining()
  {
    $handler  = new AjaxHandlerInspectable(['action' => 'my_action']);
    $returned = $handler->exposeMap('my_action', 'anInstanceMethod');

    $this->assertSame($handler, $returned, 'map_action() should return $this for chaining');
  }

  // ---------------------------------------------------------------------------
  // register_action() / add_actions()
  // ---------------------------------------------------------------------------

  public function test_add_actions_registers_authenticated_hook_by_default()
  {
    AjaxHandlerInspectable::clearRegisteredActions();
    AjaxHandlerInspectable::register_action('inspect');

    WP_Mock::expectActionAdded('wp_ajax_inspect', [AjaxHandlerInspectable::class, 'handle']);

    AjaxHandlerInspectable::add_actions();

    $this->assertArrayHasKey('inspect', AjaxHandlerInspectable::get_actions());
    $this->assertTrue(AjaxHandlerInspectable::get_actions()['inspect']['include_priv']);
    $this->assertFalse(AjaxHandlerInspectable::get_actions()['inspect']['include_no_priv']);
  }

  public function test_add_actions_registers_both_hooks_when_requested()
  {
    AjaxHandlerInspectable::clearRegisteredActions();
    AjaxHandlerInspectable::register_action('inspect', true, true);

    WP_Mock::expectActionAdded('wp_ajax_inspect', [AjaxHandlerInspectable::class, 'handle']);
    WP_Mock::expectActionAdded('wp_ajax_nopriv_inspect', [AjaxHandlerInspectable::class, 'handle']);

    AjaxHandlerInspectable::add_actions();

    $this->assertArrayHasKey('inspect', AjaxHandlerInspectable::get_actions());
    $this->assertTrue(AjaxHandlerInspectable::get_actions()['inspect']['include_priv']);
    $this->assertTrue(AjaxHandlerInspectable::get_actions()['inspect']['include_no_priv']);
  }

  public function test_add_actions_can_register_unauthenticated_hook_only()
  {
    AjaxHandlerInspectable::clearRegisteredActions();
    AjaxHandlerInspectable::register_action('inspect', false, true);

    WP_Mock::expectActionAdded('wp_ajax_nopriv_inspect', [AjaxHandlerInspectable::class, 'handle']);

    AjaxHandlerInspectable::add_actions();

    $this->assertArrayHasKey('inspect', AjaxHandlerInspectable::get_actions());
    $this->assertFalse(AjaxHandlerInspectable::get_actions()['inspect']['include_priv']);
  }

  public function test_register_action_replaces_existing_privilege_options()
  {
    AjaxHandlerInspectable::clearRegisteredActions();
    AjaxHandlerInspectable::register_action('inspect');
    AjaxHandlerInspectable::register_action('inspect', false, true);

    WP_Mock::expectActionAdded('wp_ajax_nopriv_inspect', [AjaxHandlerInspectable::class, 'handle']);

    AjaxHandlerInspectable::add_actions();

    $this->assertArrayHasKey('inspect', AjaxHandlerInspectable::get_actions());
    $this->assertFalse(AjaxHandlerInspectable::get_actions()['inspect']['include_priv']);
  }

  public function test_add_actions_only_uses_actions_registered_by_its_class()
  {
    AjaxHandlerInspectable::clearRegisteredActions();
    AlternateAjaxHandlerInspectable::clearRegisteredActions();
    AjaxHandlerInspectable::register_action('inspect');
    AlternateAjaxHandlerInspectable::register_action('alternate');

    WP_Mock::expectActionAdded('wp_ajax_inspect', [AjaxHandlerInspectable::class, 'handle']);

    AjaxHandlerInspectable::add_actions();

    $this->assertArrayHasKey('alternate', AlternateAjaxHandlerInspectable::get_actions());
    $this->assertTrue(AlternateAjaxHandlerInspectable::get_actions()['alternate']['include_priv']);
    $this->assertFalse(AlternateAjaxHandlerInspectable::get_actions()['alternate']['include_no_priv']);
  }

  // ---------------------------------------------------------------------------
  // log()
  // ---------------------------------------------------------------------------

  /**
   * @dataProvider valid_log_levels
   */
  public function test_log_delegates_message_to_configured_logger(string $level)
  {
    $logger = $this->createMock(LoggerInterface::class);
    $logger
      ->expects($this->once())
      ->method($level)
      ->with(self::BEST_BAND);

    new AjaxHandlerInspectable($this->get_request_array(), $logger);

    AjaxHandlerInspectable::log(self::BEST_BAND, $level);
  }

  public function test_log_converts_stringable_message_before_delegating()
  {
    $message = new class implements \Stringable {
      public function __toString(): string
      {
        return AjaxHandlerTest::BEST_BAND;
      }
    };
    $logger = $this->createMock(LoggerInterface::class);
    $logger
      ->expects($this->once())
      ->method(LogLevel::DEBUG)
      ->with(self::BEST_BAND);

    new AjaxHandlerInspectable($this->get_request_array(), $logger);

    AjaxHandlerInspectable::log($message);
  }

  public function test_log_throws_when_logger_is_not_configured()
  {
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Logger is not set. Cannot log message.');

    AjaxHandlerInspectable::log(self::BEST_BAND);
  }

  public function test_log_throws_when_level_is_invalid()
  {
    $logger = $this->createMock(LoggerInterface::class);
    new AjaxHandlerInspectable($this->get_request_array(), $logger);

    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Invalid log level: verbose');

    AjaxHandlerInspectable::log(self::BEST_BAND, 'verbose');
  }

  public function valid_log_levels(): array
  {
    return [
      'emergency' => [LogLevel::EMERGENCY],
      'alert'     => [LogLevel::ALERT],
      'critical'  => [LogLevel::CRITICAL],
      'error'     => [LogLevel::ERROR],
      'warning'   => [LogLevel::WARNING],
      'notice'    => [LogLevel::NOTICE],
      'info'      => [LogLevel::INFO],
      'debug'     => [LogLevel::DEBUG],
    ];
  }
}

/**
 * Concrete test double for AbstractBase.
 *
 * – Overrides send_json_response() so tests do not depend on wp_send_json.
 * – Exposes protected dispatch_action() and map_action() via public proxies.
 * – Provides one valid instance method and one static method to exercise
 *   dispatch_action failure/success paths.
 */
class AjaxHandlerInspectable extends AbstractBase
{
  /** Stores the last response captured by send_json_response(). */
  public static ?array $lastResponse = null;

  protected function execute(): array
  {
    return [
      'action' => $this->action,
      'params' => $this->request,
      'cookies' => $this->cookie,
    ];
  }

  /**
   * Overridden to capture the response in a static property instead of
   * calling wp_send_json, keeping handle/handle_post/handle_get tests
   * free of wp_send_json WP_Mock expectations.
   */
  protected function send_json_response(array $response)
  {
    self::$lastResponse = $response;
  }

  public function exposeDispatch(): array
  {
    return $this->dispatch_action();
  }

  public function exposeMap(string $action, string $method): static
  {
    // map_action() returns $this; we explicitly re-return $this to let static
    // analysis resolve the return type without traversing the parent's declaration.
    $this->map_action($action, $method);
    return $this;
  }

  public static function clearRegisteredActions(): void
  {
    unset(self::$registered_actions[static::class]);
  }

  /** Used to verify the success-path of dispatch_action(). */
  protected function anInstanceMethod(): array
  {
    return ['dispatched_by' => 'anInstanceMethod'];
  }

  /** Used to verify that dispatch_action() rejects static methods. */
  protected static function aStaticMethod(): array
  {
    return ['dispatched_by' => 'aStaticMethod'];
  }
}

class AlternateAjaxHandlerInspectable extends AjaxHandlerInspectable {}
