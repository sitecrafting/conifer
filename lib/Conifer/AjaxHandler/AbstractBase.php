<?php

namespace Conifer\AjaxHandler;

use LogicException;
use ReflectionClass;

// TODO: Need to address logging for AJAX requests. Stripped logging out for now. See #25

/**
 * Class to encapsulate handling AJAX calls. Handling a WP AJAX action
 * requires only implementing a child class with an execute() method,
 * and adding a hook to call the handle() method, i.e.:
 * 
 *   add_action('wp_ajax_some_action', [MyAjaxHandler::class, 'handle']);
 *   // or, use handle_post or handle_get, e.g.:
 *   add_action('wp_ajax_some_action', [MyAjaxHandler::class, 'handle_post']);
 * 
 * *NOTE:* To avoid duplicating logic, it's recommended that you map the action
 * to handle(), which instantiates a handler object for you and correctly
 * encodes the JSON response, rather than directly mapping to execute().
 * 
 * Your execute() method should return the response as an array.
 * 
 * #### Simple usage, for ad-hoc/standalone handlers:
 * 
 *   use Conifer\Form\AbstractBase;
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
 *   use Conifer\Form\AbstractBase;
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
abstract class AbstractBase {
  protected $request;
  protected $cookie;
  protected $action;
  protected $action_methods;  
  
  
  abstract protected function execute(array $request);


  /*
   * Static handler methods
   */

  /**
   * Handle the AJAX action for the given $request
   * @param array $request the HTTP request params.
   * Defaults to the $_REQUEST superglobal.
   */
  public static function handle(array $request = []) {
    $request = $request ?: $_REQUEST;
    $handler = new static($request);
    $handler->set_cookie($_COOKIE);
    $handler->send_json_response($handler->execute($request));
  }

  /**
   * Handle an HTTP POST request.
   */
  public static function handle_post() {
    static::handle($_POST);
  }

  /**
   * Handle an HTTP GET request.
   */
  public static function handle_get() {
    static::handle($_GET);
  }


  /*
   * Instance Methods
   */

  /**
   * Constructor.
   * TODO decide whether to require Monolog??
   * @param array $request the raw request params, i.e. GET/POST
   */
  public function __construct(array $request) {
    if (empty($request['action'])) {
      throw new LogicException(
        'Trying to handle an AJAX call without an action! The "action" request parameter is required.'
      );
    }

    $this->action = $request['action'];
    $this->request = $request;
    $this->action_methods = [];
  }

  /**
   * Send $response as a JSON HTTP response and close the connection.
   * @param array $response the response to be converted to JSON
   */
  protected function send_json_response(array $response) {
    wp_send_json($response);
  }

  /**
   * Get a param value from the request
   * @param mixed $name the key of the value to get from the request
   * @return mixed the value for the request param. Defaults to the empty string if not set.
   */
  protected function param($name) {
    return isset($this->request[$name]) ? $this->request[$name] : '';
  }
  
  /**
   * Get a param value from the cookie
   * @param mixed $name the key of the value to get from the cookie
   * @return mixed the value for the cookie param. Defaults to the empty string if not set.
   */
  protected function cookie($name) {
    return isset($this->cookie[$name]) ? $this->cookie[$name] : '';
  }
  
  /**
   * Dispatch a handler method dynamically based on the requested action
   * @return mixed $response the result of calling the corrsponding *_action method.
   * This **should** be an array.
   */
  protected function dispatch_action() {
    // check that a handler is configure for the current action
    if (empty($this->action_methods[$this->action])) {
      throw new LogicException("No handler method specified for action: {$this->action}!");
    }
    
    // check that the handler method has been implemented
    $method = $this->action_methods[$this->action];
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
  
  protected function map_action($action, $methodName) {
    $this->action_methods[$action] = $methodName;
    return $this;
  }
  
  private function set_cookie(array $cookie) {
    $this->cookie = $cookie;
  }
}