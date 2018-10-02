
### Class: \Conifer\AjaxHandler\AbstractBase (abstract)

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>array</em> <strong>$request</strong>)</strong> : <em>void</em><br /><em>Constructor. TODO decide whether to require Monolog??</em> |
| public static | <strong>handle(</strong><em>array</em> <strong>$request=array()</strong>)</strong> : <em>void</em><br /><em>Handle the AJAX action for the given $request Defaults to the $_REQUEST superglobal.</em> |
| public static | <strong>handle_get()</strong> : <em>void</em><br /><em>Handle an HTTP GET request.</em> |
| public static | <strong>handle_post()</strong> : <em>void</em><br /><em>Handle an HTTP POST request.</em> |
| protected | <strong>cookie(</strong><em>mixed</em> <strong>$name</strong>)</strong> : <em>mixed the value for the cookie param. Defaults to the empty string if not set.</em><br /><em>Get a param value from the cookie</em> |
| protected | <strong>dispatch_action()</strong> : <em>mixed $response the result of calling the corrsponding *_action method. This **should** be an array.</em><br /><em>Dispatch a handler method dynamically based on the requested action</em> |
| protected | <strong>abstract execute(</strong><em>array</em> <strong>$request</strong>)</strong> : <em>array The response after handling the request</em><br /><em>Abstract method used to define the functionality when handling an AJAX request. Should return an array to be encoded in the response.</em> |
| protected | <strong>map_action(</strong><em>string</em> <strong>$action</strong>, <em>string</em> <strong>$methodName</strong>)</strong> : <em>Conifer\AjaxHandler\AbstractBase The current AbstractBase class instance</em><br /><em>Adds the specified action name to the action_methods array as a key, with the specified method name as the value. Used to determine the method user to handle the specified action.</em> |
| protected | <strong>param(</strong><em>mixed</em> <strong>$name</strong>)</strong> : <em>mixed the value for the request param. Defaults to the empty string if not set.</em><br /><em>Get a param value from the request</em> |
| protected | <strong>send_json_response(</strong><em>array</em> <strong>$response</strong>)</strong> : <em>void</em><br /><em>Send $response as a JSON HTTP response and close the connection.</em> |

