
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

