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

The `AdminPage` class is a simple abstraction around the `add_menu_page()` function. Simply implement the abstract `render` method...

```php
use Conifer\Admin\Page;

class HelloPage extends Page {
  public function render() : string {
    return 'HELLO';
  }
}
```

...and add the page:

```php
$page = new HelloPage('Hello');
$page->add();
```

Or, you can specify individual parameters using a fluent interface:

```php
$page = new HelloPage('Hello');
$page
  ->set_title('WHY HELLO THERE')
  ->set_menu_title('O HAI')
  ->set_slug('hai')
  ->set_capability
  ->add();
```

Conifer provides sane defaults for individual parameters. Just make sure to check existing menu slugs to avoid overriding them.

## Sub-Pages

Adding sub-pages is just as easy. Again, just implement `render`...

```php
use Conifer\Admin\SubPage;

class HelloAgainPage extends SubPage {
  public function render() : string {
    return 'HELLO AGAIN';
  }
}
```

...and add the sub-page to its parent:

```php
$parent = new HelloPage('Hello');
$parent->add();

// instantiate the sub-page and add it...
$child = new HelloAgainPage('Hello Again', $parent);
$child->add();
```

You can also add a sub-page using `Admin\Page`'s fluent interface. This is equivalent to the example above:

```php
$parent = new HelloPage('Hello');
$parent->add()
  ->add_sub_page(HelloAgainPage::class, 'Hello Again');
```

Of course, this means you can also add a whole slew of sub-pages in one fell swoop:

```php
$parent = new HelloPage('Hello');
$parent->add()
  ->add_sub_page(HelloAgainPage::class, 'Hello Again')
  ->add_sub_page(GoodbyePage::class, 'Goodbye')
  ->add_sub_page(GoodbyeForRealzPage::class, 'Goodbye for Realz');
```

`SubPage` is a subclass of `AdminPage` and therefore inherits the same fluent interface. Instances of `SubPage` inherit their required capability from their parent page.

## Hotkeys

Admin hotkeys are a special feature of Conifer and one of the few things it does out of the box. These hotkeys allow you type, for example, `gd` to go to the Dashboard from any page in the WP Admin.

You can add your own custom hotkeys from your config callback:

```php
$this->set_custom_admin_hotkeys([
  // typing "ga" redirects us to the home page
  '97' => '/a-page'
]);
```

Note that we're passing a character code here, not a letter. JavaScript uses character codes to recognize the letter that the user typed. 97 is the code for a lower-case "a", so this is telling the JavaScript that when the user types `ga` it should redirect to `/a-page`.

> ####Info::Cursor focus must be on the window
>
> Your focus must be on the wider window in order for hotkeys to work. This is to prevent them from taking effect inside text boxes.

Here are the default hotkeys and their corresponding character codes. Note that `g` is the universal prefix for all hotkeys (its character code is 103, by the way):

| Hotkey | Code | Redirects to... |
| -------|------|-----------------|
| `gd` | 100 | Dashboard (`/wp-admin/index.php`) |
| `gp` | 112 | Posts (`/wp-admin/edit.php`) |
| `gP` | 80 | New Post (`/wp-admin/post-new.php`) |
| `gc` | 99 | Categories (`/wp-admin/edit-tags.php?taxonomy=category`) |
| `gt` | 116 | Tags (`/wp-admin/edit-tags.php`) |
| `gm` | 109 | Media (`/wp-admin/upload.php`) |
| `ga` | 97 | Pages (`/wp-admin/edit.php?post_type=page`) |
| `gA` | 65 | New Page (`/wp-admin/post-new.php?post_type=page`) |
| `gT` | 84 | Themes (`/wp-admin/themes.php`) |
| `gC` | 67 | Customizer (`"/wp-admin/customize.php?return=" + location.pathname`) |
| `gl` | 108 | Plugins (`/wp-admin/plugins.php`) |
| `gL` | 108 | Install Plugin (`/wp-admin/plugin-install.php`) |
| `gu` | 117 | Users (`/wp-admin/users.php`) |
| `gg` | 103 | General Settings (`/wp-admin/options-general.php`) |
| `gw` | 119 | Writing (`/wp-admin/options-writing.php`) |
| `gr` | 114 | Reading (`/wp-admin/options-reading.php`) |
| `gi` | 105 | Discussion (`/wp-admin/options-discussion.php`) |
| `gk` | 107 | General Settings (`/wp-admin/options-permalink.php`) |
| `gh` | 104 | Home Page (`/`) |

### Disabling hotkeys

You can disable admin hotkeys from your config callback by calling:

```php
$this->disable_admin_hotkeys();
```

