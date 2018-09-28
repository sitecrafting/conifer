# AJAX Handlers

## Getting Started
Conifer provides an elegant and flexible abstraction of the standard [WordPress AJAX handler pattern](https://developer.wordpress.org/plugins/javascript/enqueuing/#ajax-action). To get started, all you need to do is extend the provided base class and implement the `execute` method:

```PHP
use Conifer\AjaxHandler\AbstractBase;
​
class MyAjaxHandler extends AbstractBase {
  protected function execute() {
    /* 
    * Your custom logic goes here
    * Request data is accessible via $this->request
    * Return an array with the appropriate response data
    */
    return ['foo' => 'bar'];
  }
}
```

From here, all you need to do is add the appropriate `wp_ajax_{action}` and/or `wp_ajax_nopriv_{action}` actions in functions.php (most likely via `Site::configure()`):

```PHP
add_action('wp_ajax_my_action', [MyAjaxHandler::class, 'handle']);
```

Subsequent AJAX calls which include the action of `my_action` will respond with the following JSON, based on our class definition above:

```JS
{
  foo: 'bar'
}
```

## Handling Multiple Requests
The above example is great for one-off AJAX requests, but chances are you might have more than one type of request for a given feature in your project. Instead of creating separate AjaxHandler classes for each request, we can group related requests into a single class which can handle multiple actions:

```PHP
use Conifer\AjaxHandler\AbstractBase;
  
class RobotAjaxHandler extends AbstractBase {
  // Implement the abstract execute method
  protected function execute() {
    // Map actions to instance methods dynamically
    $this->map_action('talk_to_robot', 'talk_to_robot');
    $this->map_action('ask_robot_to_dance', 'robot_dance');
    $this->map_action('buy_robot_insurance', 'buy_robot_insurance');

    return $this->dispatch_action();
  }

  // Talk to our robot, in the hopes that we can talk it out of attacking us
  private function talk_to_robot() {
    return $this->shared_logic([
      'result' => '01000010 01100101 01100101 01110000 00100000 01100010 01101111 01101111 01110000'
    ]);
  }

  // Ask our robot to dance, in the hopes that it will forget about attacking us
  private function robot_dance() {
    return $this->shared_logic(['result' => 'https://gph.is/VwzmgU']);
  }

  // Purchase robot insurance, for the inevitable scenario of a robot attack
  private function buy_robot_insurance() {
    return $this->shared_logic(['result' => 'https://www.nbc.com/saturday-night-live/video/old-glory-insurance/n10766']);
  }

  // With this setup, our AJAX actions can have shared logic! For example, this will add the action to our response.
  private function shared_logic($response) {
    $response['action'] = $this->action;
    return $response;
  }
}
```

Now we add our actions like before, and we're ready to interact with our robot overlords via AJAX calls:

```PHP
add_action('wp_ajax_talk_to_robot', [RobotAjaxHandler::class, 'handle']);
add_action('wp_ajax_ask_robot_to_dance', [RobotAjaxHandler::class, 'handle']);
add_action('wp_ajax_buy_robot_insurance', [RobotAjaxHandler::class, 'handle']);
```

## API
The AjaxHandler class provides a relatively thin layer of abstraction, but there are a handful of available methods you should be aware of:


### `handle(array $data)`

The method which is called when AJAX requests and received. Utilizes data from the [$_REQUEST](http://php.net/manual/en/reserved.variables.request.php) suberglobal, which will include data from both POST and GET requests.


### `handle_post()`

Can be used in place of the `handle` method when adding your AJAX action. Only utilizes data from the [$_POST](http://php.net/manual/en/reserved.variables.post.php) suberglobal, which limits your AJAX handler to only accepting POST requests.


### `handle_get()`

Can be used in place of the `handle` method when adding your AJAX action. Only utilizes data from the [$_GET](http://php.net/manual/en/reserved.variables.get.php) suberglobal, which limits your AJAX handler to only accepting GET requests.


### `param(mixed $name)`

Takes the name of a data parameter from the AJAX request (typically a string), and returns the corresponding value if it exists. Returns an empty string if no matching parameter was found in the request data.


### `cookie(mixed $name)`

Takes the name of a data parameter from the [$_COOKIE](http://php.net/manual/en/reserved.variables.cookies.php) superglobal (typically a string), and returns the corresponding value if it exists. Returns an empty string if no matching parameter was found in the cookie data.


### `map_action(string $action, string $methodName)`

Takes the name of an action and the name of the corresponding instance method which should be used to handle AJAX requests made with this action. Used when an AjaxHandler class handles more than one action.


### `dispatch_action()`

Dynamically determines which action handler should be utilized, based on data defined previously using the `map_action` method. Used when an AjaxHandler class handles more than one action.