# Menu

Conifer extends Timber's [Menu](https://timber.github.io/docs/v2/reference/timber-menu/) class.

## Registering Menu

Before using Conifer's `Menu` class, register it in Timber's menu class map. In most cases, you will also register your menu locations and add menu instances to the Timber context.

```php
<?php

use Conifer\Navigation\Menu;
use Conifer\Site;
use Timber\Timber;

$site = new Site();
$site->configure(function () {
    add_filter('timber/menu/classmap', function (array $classmap): array {
        return array_merge($classmap, [
            'primary' => Menu::class,
            'footer'  => Menu::class,
        ]);
    });

    register_nav_menus([
        'primary' => 'Main Navigation',
        'footer'  => 'Footer Navigation',
    ]);

    add_filter('timber/context', function (array $context): array {
        $context['primary_menu'] = Timber::get_menu('primary');
        $context['footer_menu']  = Timber::get_menu('footer');

        return $context;
    });
});
```

After registration, these menu variables are available in Twig.

```twig
{# views/partials/blocks/header.twig #}
{% include 'partials/blocks/nav.twig' with { menu: primary_menu } only %}
```

## Getting the top-level MenuItem

Use `get_current_top_level_item()` to retrieve the top-level menu item that points to the current post (or to an ancestor of the current post).

```twig
{% set top_level_item = menu.get_current_top_level_item() %}

{% if top_level_item %}
    <a href="{{ top_level_item.link }}">{{ top_level_item.title }}</a>

    {% for item in top_level_item.children %}
        {# Render child menu items #}
    {% endfor %}
{% endif %}
```