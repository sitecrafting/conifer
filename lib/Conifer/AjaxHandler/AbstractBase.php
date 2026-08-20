<?php

/**
 * Class to encapsulate handling AJAX calls. Handling a WP AJAX action
 * requires only implementing a child class with an execute() method,
 * and adding a hook to call the handle() method, i.e.:
 *
 *   ```
 *   add_action('wp_ajax_some_action', [MyAjaxHandler::class, 'handle']);
 *   // or, use handle_post or handle_get, e.g.:
 *   add_action('wp_ajax_some_action', [MyAjaxHandler::class, 'handle_post']);
 *   ```
 *
 * NOTE: To avoid duplicating logic, it's recommended that you map the action
 * to handle(), which instantiates a handler object for you and correctly
 * encodes the JSON response, rather than directly mapping to execute().
 *
 * Your execute() method should return the response as an array.
 *
 * #### Simple usage, for ad-hoc/standalone handlers:
 *
 *   use Conifer\AjaxHandler\AbstractBase;
 *
 *   class MySimpleAjaxHandler extends AbstractBase {
 *     protected function execute() {
 *       $response = ['foo' => 'bar'];
 *       // do some stuff with $response
 *       return $response;
 *     }
 *   }
 *
 *   // in Site::configure()...
 *   add_action('wp_ajax_some_action', [MySimpleAjaxHandler::class, 'handle']);
 *
 * #### Advanced usage, for grouping shared behavior in a single handler class:
 *
 *   use Conifer\AjaxHandler\AbstractBase;
 *
 *   class MyAjaxHandler extends AbstractBase {
 *     // must implement this
 *     protected function execute() {
 *       // map actions to instance methods dynamically
 *       $this->map_action('foo_action', 'foo');
 *       $this->map_action('bar_action', 'bar');
 *
 *       return $this->dispatch_action();
 *     }
 *
 *     private function foo() {
 *       // return a response with handled_by and action idxs
 *       return shared_logic(['handled_by' => 'foo']);
 *     }
 *
 *     private function bar() {
 *       // return a response with handled_by and action idxs
 *       return shared_logic(['handled_by' => 'bar']);
 *     }
 *
 *     private function shared_logic($response) {
 *       // say we want to always return the action from our endpoint...
 *       $response['action'] = $this->action;
 *       return $response;
 *     }
 *   }
 *
 *   // in Site::configure()...
 *   add_action('wp_ajax_foo_action', [MyAjaxHandler::class, 'handle']);
 *   add_action('wp_ajax_bar_action', [MyAjaxHandler::class, 'handle']);
 *
 * @copyright 2017 SiteCrafting, Inc.
 * @author    Coby Tamayo
 * @package   Conifer
 */

namespace Conifer\AjaxHandler;

use BadMethodCallException;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use Stringable;

// TODO: Need to add nonce verification to this class and update any related documentation to reflect this requirement
abstract class AbstractBase
{
  /**
   * The request array for this AJAX request (either POST or GET)
   *
   * @var array
   */
  protected $request;
  /**
   * The $_COOKIE array for this AJAX request
   *
   * @var array
   */
  protected $cookie;
  /**
   * The name of the AJAX action being requested
   *
   * @var string
   */
  protected $action;
  /**
   * Associative array which maps an action to the method name used to handle that action
   *
   * @var array
   */
  protected $action_methods;

  /**
   * Logger for logging information about Ajax Requests
   *
   * @var LoggerInterface|null
   */
  protected static ?LoggerInterface $logger = null;

  /**
   * Registered AJAX actions, keyed by handler class and action name.
   *
   * @var array<string, array<string, array{include_priv: bool, include_no_priv: bool}>>
   */
  protected static array $registered_actions = [];

  /**
   * Abstract method used to define the functionality when handling an AJAX request.
   * Should return an array to be encoded in the response.
   *
   * @return array The response after handling the request
   */
  abstract protected function execute(): array;


  /*
   * Static handler methods
   */

  /**
   * Handle an HTTP request.
   */
  public static function handle(?array $requestData = null)
  {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $handler = new static($requestData ?? $_REQUEST);
    $handler->set_cookie($_COOKIE);
    $handler->send_json_response($handler->execute());
  }

  /**
   * Handle an HTTP POST request.
   */
  public static function handle_post()
  {
    static::handle($_POST); // phpcs:ignore WordPress.Security.NonceVerification.Missing
  }

  /**
   * Handle an HTTP GET request.
   */
  public static function handle_get()
  {
    static::handle($_GET); // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
  }

  /**
   * Log a provided string message to the configured log file.
   *
   * This is not a 100% accurate implementation of the LoggerInterface log() function, but it
   * should be good enough to suit our needs
   *
   * @param string|Stringable $message The message to log
   * @param string $level The log level to use. Defaults to LogLevel::DEBUG
   * @return void
   *
   * @throws RuntimeException If the logger is not configured via __construct
   * @throws InvalidArgumentException If the provided $level is not one of PSR-3's LogLevels
   */
  public static function log(string|Stringable $message, string $level = LogLevel::DEBUG): void
  {
    // If the logger is not set, we cannot log the message. We should throw a runtime exception.
    if (static::$logger === null) {
      throw new \RuntimeException('Logger is not set. Cannot log message.');
    }

    // If the message is a Stringable object, we need to convert it to a string.
    if ($message instanceof Stringable) {
      $message = (string) $message;
    }

    // Check to make sure the level is valid. If not, throw an InvalidArgumentException.
    switch ($level) {
      case LogLevel::EMERGENCY:
      case LogLevel::ALERT:
      case LogLevel::CRITICAL:
      case LogLevel::ERROR:
      case LogLevel::WARNING:
      case LogLevel::NOTICE:
      case LogLevel::INFO:
      case LogLevel::DEBUG:
        static::$logger->{$level}($message);
        break;
      default:
        throw new \InvalidArgumentException("Invalid log level: {$level}");
    }
  }

  /**
   * Register an AJAX action for this handler class.
   *
   * Call add_actions() in the site's configuration callback to add the registered hooks.
   *
   * @param string $action The AJAX action name.
   * @param bool $include_priv Whether to handle requests from authenticated users.
   * @param bool $include_no_priv Whether to handle requests from unauthenticated users.
   * @return void
   */
  public static function register_action(string $action, bool $include_priv = true, bool $include_no_priv = false): void
  {
    static::$registered_actions[static::class][$action] = [
      'include_priv'    => $include_priv,
      'include_no_priv' => $include_no_priv,
    ];
  }

  /**
   * Add WordPress hooks for all actions registered by this handler class.
   *
   * @return void
   */
  public static function add_actions(): void
  {
    foreach (static::$registered_actions[static::class] ?? [] as $action => $options) {
      if ($options['include_priv']) {
        add_action("wp_ajax_{$action}", [static::class, 'handle']);
      }

      if ($options['include_no_priv']) {
        add_action("wp_ajax_nopriv_{$action}", [static::class, 'handle']);
      }
    }
  }

  /**
   * Return the actions registered to this AjaxHandler
   *
   * @return array The array of registered actions.
   */
  public static function get_registered_actions(): array
  {
    return static::$registered_actions[static::class] ?? [];
  }

  /*
   * Instance Methods
   */

  /**
   * Constructor
   *
   * @param array $request the raw request params, i.e. GET/POST
   * @throws LogicException If the request array doesn't contain an action
   */
  public function __construct(array $request, ?LoggerInterface $logger = null)
  {
    if (empty($request['action'])) {
      throw new LogicException(
        'Trying to handle an AJAX call without an action! The "action" request parameter is required.'
      );
    }

    $this->action         = $request['action'];
    $this->request        = $request;
    $this->action_methods = [];
    static::$logger       = $logger;
  }

  /**
   * Send $response as a JSON HTTP response and close the connection.
   *
   * @param array $response the response to be converted to JSON
   */
  protected function send_json_response(array $response)
  {
    wp_send_json($response);
  }

  /**
   * Get a param value from the request
   *
   * @param mixed $name the key of the value to get from the request
   * @return mixed the value for the request param. Defaults to the empty string if not set.
   */
  protected function param($name)
  {
    return isset($this->request[$name]) ? $this->request[$name] : '';
  }

  /**
   * Get a param value from the cookie
   *
   * @param mixed $name the key of the value to get from the cookie
   * @return mixed the value for the cookie param. Defaults to the empty string if not set.
   */
  protected function cookie($name)
  {
    return isset($this->cookie[$name]) ? $this->cookie[$name] : '';
  }

  /**
   * Dispatch a handler method dynamically based on the requested action
   *
   * @return mixed $response the result of calling the corrsponding *_action method.
   * This **should** be an array.
   * @throws LogicException If the specified action doesn't exist in the action_methods array
   * @throws BadMethodCallException If the action method doesn't exist or isn't an instance method
   */
  protected function dispatch_action()
  {
    // check that a handler is configure for the current action
    if (empty($this->action_methods[$this->action])) {
      throw new LogicException("No handler method specified for action: {$this->action}!");
    }

    // check that the handler method has been implemented
    $method     = $this->action_methods[$this->action];
    $reflection = new ReflectionClass($this);
    if (!$reflection->hasMethod($method)) {
      throw new BadMethodCallException("Method `{$method}` for action {$this->action} has not been implemented!");
    }

    // check that we're calling an instance method
    if ($reflection->getMethod($method)->isStatic()) {
      throw new BadMethodCallException("Method {$method} for action {$this->action} must not be static!");
    }

    return $this->{$method}();
  }

  /**
   * Adds the specified action name to the action_methods array as a key,
   * with the specified method name as the value. Used to determine the method
   * user to handle the specified action.
   *
   * @param  string $action The name of the action to be mapper to a method name
   * @param  string $methodName The name of a method name to be used when handling thins action
   * @return AbstractBase The current AbstractBase class instance
   */
  protected function map_action($action, $methodName)
  {
    $this->action_methods[$action] = $methodName;
    return $this;
  }

  /**
   * Saves the request cookie array to this AbstractBase handler instance
   *
   * @param array $cookie The request cookie array
   */
  private function set_cookie(array $cookie)
  {
    $this->cookie = $cookie;
  }
}
