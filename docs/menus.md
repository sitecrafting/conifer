# Menu

Conifer extends the functionality of the [Menu](https://timber.github.io/docs/v2/reference/timber-menu/) class.

## Registering Menu

Like in Timber, you must add the Menu to the classmap before it can be added to the Site's context. Additionally, you must also register the nav menu and add it to the Site's context.

```php
// In functions.php
<?php>

use Conifer\Site;
use Conifer\Navigation\Menu;
use Conifer\Navigation\MenuItem;

$site = new Site();
$site->configure(function() {
    //... rest of configure function

    // Add Menu to classmap
    add_filter('timber/menu/classmap', function ($classmap) {
        $custom_classmap = [
            'primary' => Menu::class,
            'footer' => Menu::class,
        ];
        return array_merge($classmap, $custom_classmap);
    }, 10);

    // Register navigation
    register_nav_menus([
        'primary' => 'Main Navigation', // main page/nav structure
        'footer' => 'Footer Navigation', // footer nav
    ]);

    add_filter('timber/context', function(array $context) : array {

        $context['primary_menu']    = Timber::get_menu('primary');
        $context['footer_menu']    = Timber::get_menu('footer');

        return $context;
    });

    //... remainder of configure function
```

After your menu is registered, you can use it in your Twig files and it will automatically be fetched from the context

```twig
<!-- \app\themes\{your_theme}\views\parials\blocks\header.twig -->

<!-- primary_menu is available to use because it was registered in functions.php -->
{% include 'partials/blocks/nav.twig' with { menu: primary_menu } only %}
```

## Getting the top level MenuItem

Conifer adds a utility function to get the top level MenuItem when viewing a Post, `get_current_top_level_item()`

```twig

{% set topLevelMenuItem = menu.get_current_top_level_item() %}

<a href="{{ menu.get_current_top_level_item().get_path }}">{{ menu.get_current_top_level_item().title }}</a>

{% for item in menu.get_current_top_level_item().get_children() %}
    <!-- Render children MenuItems -->
{% endfor %}
```