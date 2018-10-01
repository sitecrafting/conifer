## Table of contents

- [\Conifer\Admin\Notice](#class-coniferadminnotice)
- [\Conifer\Admin\Page (abstract)](#class-coniferadminpage-abstract)
- [\Conifer\Admin\SubPage (abstract)](#class-coniferadminsubpage-abstract)

<hr /><a id="class-coniferadminnotice"></a>
### Class: \Conifer\Admin\Notice

> Provides a high-level API for dislaying all sorts of WP Admin notices

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>\string</em> <strong>$message</strong>, <em>\string</em> <strong>$extraClasses=`''`</strong>)</strong> : <em>void</em><br /><em>Constructor Multiple classes can be specified with a space-separated string, e.g. `"one two three"`</em> |
| public | <strong>add_class(</strong><em>\string</em> <strong>$class</strong>)</strong> : <em>[\Conifer\Admin\Notice](#class-coniferadminnotice)</em><br /><em>Add an HTML class to be rendered on this notice</em> |
| public static | <strong>clear_flash_notices()</strong> : <em>void</em><br /><em>Clear all flash notices in session</em> |
| public static | <strong>disable_flash_notices()</strong> : <em>void</em><br /><em>Disable flash notices</em> |
| public | <strong>display()</strong> : <em>void</em><br /><em>Display the admin notice</em> |
| public static | <strong>display_flash_notices()</strong> : <em>void</em><br /><em>Display any flash notices stored in session during the admin_notices hook</em> |
| public static | <strong>enable_flash_notices()</strong> : <em>void</em><br /><em>Enable flash notices to be stored in the `$_SESSION` superglobal</em> |
| public | <strong>error()</strong> : <em>void</em><br /><em>Display this notice as an error</em> |
| public | <strong>flash()</strong> : <em>void</em><br /><em>Display this notice on the next page load</em> |
| public | <strong>flash_error()</strong> : <em>void</em><br /><em>Display this notice as an error message on the next page load</em> |
| public | <strong>flash_info()</strong> : <em>void</em><br /><em>Display this notice as an info message on the next page load</em> |
| public static | <strong>flash_notices_enabled()</strong> : <em>bool</em><br /><em>Whether flash notices are enabled</em> |
| public | <strong>flash_success()</strong> : <em>void</em><br /><em>Display this notice as a success message on the next page load</em> |
| public | <strong>flash_warning()</strong> : <em>void</em><br /><em>Display this notice as a warning on the next page load</em> |
| public | <strong>get_class()</strong> : <em>string e.g. `"notice notice-error"`</em><br /><em>Get the HTML class or classes to be rendered in the notice markup</em> |
| public static | <strong>get_flash_notices()</strong> : <em>Notice[] an array of Notice instances</em><br /><em>Get the flash notices to be displayed based on session data</em> |
| public | <strong>has_class(</strong><em>\string</em> <strong>$class</strong>)</strong> : <em>bool</em><br /><em>Whether this Notice has the given $class</em> |
| public | <strong>has_style_class()</strong> : <em>bool</em><br /><em>Whether this notice has a special style class that WordPress targets in its built-in admin styles.</em> |
| public | <strong>html()</strong> : <em>string the HTML to be rendered</em><br /><em>Get the message `<div>` markup</em> |
| public | <strong>info()</strong> : <em>void</em><br /><em>Display this notice as an info message</em> |
| public | <strong>success()</strong> : <em>void</em><br /><em>Display this notice as a success message</em> |
| public | <strong>warning()</strong> : <em>void</em><br /><em>Display this notice as a warning</em> |
| protected static | <strong>valid_session_notice(</strong><em>mixed</em> <strong>$notice</strong>)</strong> : <em>bool</em><br /><em>Validate a session notice array</em> |

<hr /><a id="class-coniferadminpage-abstract"></a>
### Class: \Conifer\Admin\Page (abstract)

> Class for abstracting WP Admin pages

###### Example
```
```php
use Conifer\Admin\Page as AdminPage;
class MySettingsPage extends AdminPage {
  public function render() : string {
return '<h1>ALL THE SETTINGS</h1> ...';
  }
}
$settingsPage = new MySettingsPage('My Theme Settings');
$settingsPage->add();
```
```

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>\string</em> <strong>$title</strong>, <em>\string</em> <strong>$menuTitle=`''`</strong>, <em>\string</em> <strong>$capability=`'manage_options'`</strong>, <em>\string</em> <strong>$slug=`''`</strong>, <em>\string</em> <strong>$iconUrl=`''`</strong>)</strong> : <em>void</em><br /><em>Constructor</em> |
| public | <strong>add()</strong> : <em>Page returns this Page</em><br /><em>Add this Admin Page to the admin main menu</em> |
| public | <strong>add_sub_page(</strong><em>\string</em> <strong>$class</strong>, <em>\string</em> <strong>$title</strong>, <em>\string</em> <strong>$menuTitle=`''`</strong>, <em>\string</em> <strong>$capability=`''`</strong>, <em>\string</em> <strong>$slug=`''`</strong>)</strong> : <em>Page returns this Page.</em><br /><em>Add a sub-menu admin page to this Page.</em> |
| public | <strong>do_add()</strong> : <em>Page returns this Page</em><br /><em>The callback to the `admin_menu` action.</em> |
| public | <strong>get_capability()</strong> : <em>string</em><br /><em>Get the `capability` to be passed to WP when this Page is added.</em> |
| public | <strong>get_icon_url()</strong> : <em>string</em><br /><em>Get the `icon_url` to be passed to WP when this Page is added.</em> |
| public | <strong>get_menu_title()</strong> : <em>string</em><br /><em>Get the `menu_title` to be passed to WP when this Page is added.</em> |
| public | <strong>get_slug()</strong> : <em>string</em><br /><em>Get the `menu_slug` to be passed to WP when this Page is added. When adding sub-pages, this is what is passed as `parent_slug`</em> |
| public | <strong>get_title()</strong> : <em>string</em><br /><em>Get the `page_title` to be passed to WP when this Page is added.</em> |
| public | <strong>abstract render()</strong> : <em>string</em><br /><em>Render the content of this admin Page.</em> |
| public | <strong>set_capability(</strong><em>\string</em> <strong>$capability</strong>)</strong> : <em>Page returns this Page object</em><br /><em>Set the capability required to view this Admin Page.</em> |
| public | <strong>set_icon_url(</strong><em>\string</em> <strong>$url</strong>)</strong> : <em>Page returns this Page object</em><br /><em>Set the icon_url for this Admin Page. Admin Page.</em> |
| public | <strong>set_menu_title(</strong><em>\string</em> <strong>$menuTitle</strong>)</strong> : <em>Page returns this Page object</em><br /><em>Set the menu_title for this Admin Page.</em> |
| public | <strong>set_slug(</strong><em>\string</em> <strong>$slug</strong>)</strong> : <em>Page returns this Page object</em><br /><em>Set the slug for this Admin Page.</em> |
| public | <strong>set_title(</strong><em>\string</em> <strong>$title</strong>)</strong> : <em>Page returns this Page object</em><br /><em>Set the title for this Admin Page.</em> |

<hr /><a id="class-coniferadminsubpage-abstract"></a>
### Class: \Conifer\Admin\SubPage (abstract)

> Class for abstracting WP Admin pages

###### Example
```
```php
use Conifer\Admin\Page as AdminPage;
use Conifer\Admin\SubPage;
class MySettingsPage extends AdminPage {
  public function render() : string {
return '<h1>This is the top-level settings page</h1>';
  }
}
class MoreSettingsPage extends SubPage {
  public function render() : string {
return '<h1>This is a second-tier settings page</h1>';
  }
}
$page = new MySettingsPage('My Theme Settings');
// add your pages like this...
$page
  ->add()
  ->add_sub_page(MoreSettingsPage::class, 'More Theme Settings');
// ...or like this:
$page->add();
$subPage = new MoreSettingsPage($page, 'More Theme Settings');
$subPage->add();
```
```

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>[\Conifer\Admin\Page](#class-coniferadminpage-abstract)</em> <strong>$parent</strong>, <em>\string</em> <strong>$title</strong>, <em>\string</em> <strong>$menuTitle=`''`</strong>, <em>\string</em> <strong>$capability=`''`</strong>, <em>\string</em> <strong>$slug=`''`</strong>)</strong> : <em>void</em><br /><em>Constructor</em> |
| public | <strong>add()</strong> : <em>Page returns this SubPage</em><br /><em>Add this SubPage to the WP Admin menu</em> |
| public | <strong>do_add()</strong> : <em>Page returns this SubPage</em><br /><em>The callback to the `admin_menu` action.</em> |

*This class extends [\Conifer\Admin\Page](#class-coniferadminpage-abstract)*

