
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
| public | <strong>__construct(</strong><em>\Conifer\Admin\Page</em> <strong>$parent</strong>, <em>\string</em> <strong>$title</strong>, <em>\string</em> <strong>$menuTitle=null</strong>, <em>\string</em> <strong>$capability=null</strong>, <em>\string</em> <strong>$slug=null</strong>)</strong> : <em>void</em><br /><em>Constructor</em> |
| public | <strong>add()</strong> : <em>Page returns this SubPage</em><br /><em>Add this SubPage to the WP Admin menu</em> |
| public | <strong>do_add()</strong> : <em>Page returns this SubPage</em><br /><em>The callback to the `admin_menu` action.</em> |

*This class extends \Conifer\Admin\Page*

