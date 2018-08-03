# Admin Helpers

Conifer comes with some helpful goodies to make working with the WP admin easier.

## Admin Notices

The `Conifer\Admin\Notice` provides a simple, intuitive API for adding notices to the top of the WP Admin screen. Just tell it what message should be displayed (and optionally a class to put on the message `<div>`) and `Notice` will display it for you:

```php

```



### Displaying Flash Notices

Many web frameworks and libraries, such as Ruby on Rails, provide "flash" notices: messages that appear once and then disappear on the next page load. We thought: *what makes those frameworks so special?* Nothing, that's what.

**NOTE: To use Flash Notices, you must have sessions enabled. Some web hosts, notably [Pantheon](https://pantheon.io/), disable sessions by default. Check with your web host and/or server configuration before assuming sessions are enabled.** 

First, enable flash notices:

```php
use Conifer\Admin\Notice;

Notice::enable_flash_notices();
```

Then, display a flash message to appear on the next page load:

```php
if (isset($_POST['do_a_thing'])) {
    do_a_thing();
    Notice::flash('You got redirected!!!1');
    wp_redirect('/wp-admin/page=some-admin-page');
    exit;
}
```

As you can see, we can redirect after calling `Notice::flash()` and the message will display after the redirect. This is useful if you want to redirect after a non-idempotent change, such as deleting or updating a resource, so that folks can't accidentally replay the action by simply refreshing.

## Admin Pages

TODO

## Sub-Pages

TODO