# Admin Helpers

Conifer comes with some helpful goodies to make working with the WP admin easier.

## Admin Notices

The `Conifer\Admin\Notice` provides a simple, intuitive API for [adding notices](https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices) to the top of the WP Admin screen. Just tell it what message should be displayed (and optionally a class to put on the message `<div>`) and `Notice` will display it for you:

```php
use Conifer\Admin\Notice;

$notice = new Notice('all your base are belong to us');
$notice->error();
```

This will display an error message in the admin:

```php
<div class="notice notice-error"><p>all your base are belong to us</p></div>
```

### Notice styles

You can call any of the following methods to get a different style notice:

* `$notice->success()` renders a success message (green bar)
* `$notice->info()` renders an info message (blue bar)
* `$notice->warning()` renders a warning message (yellowish orange bar)
* `$notice->error()` renders an error message (red bar)

### Dismissable Notices

WordPress supports dismissable notices: when a notice `<div>` has a special class, WordPress renders a close button that the user can click to make it go away. You can add this to any notice by calling `dismissable()`:

```php
use Conifer\Admin\Notice;

$notice = new Notice('all your base are belong to us');
$notice->dismissable()
  ->error();
```

The `dismissable()` method returns the `Notice` object itself, for a "fluent" interface.

### Adding classes

For customizing notice front-end behavior/styles, you can specify optional extra classes to put on the `<div>` by with `add_class()`:

```php
$notice = new Notice('fancy custom notice!');
$notice->add_class('fancy custom') 
  ->info();
```

You can also specify a second argument to the constructor:

```php
$notice = new Notice('fancy custom notice!', 'fancy custom');
$notice->info();
```

Either of these will render:

```php
<div class="notice notice-info fancy custom"><p>fancy custom notice!</p></div>
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
    $notice = new Notice('You got redirected!!!1');
    $notice->flash_success();
    wp_redirect('/wp-admin/page=some-admin-page');
    exit;
}
```

As you can see, we can redirect after calling `Notice::flash()` and the message will display after the redirect. This is useful if you want to redirect after a non-idempotent change, such as deleting or updating a resource, so that folks can't accidentally replay the action by simply refreshing.

Flash notices have an almost identical API as regular notices:

- `$notice->flash_success()` renders a success message (green bar)
- `$notice->flash_info()` renders an info message (blue bar)
- `$notice->flash_warning()` renders a warning message (yellowish orange bar)
- `$notice->flash_error()` renders an error message (red bar)

## Admin Pages

TODO

## Sub-Pages

TODO