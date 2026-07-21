# MenuItem

Conifer extends Timber's [MenuItem](https://timber.github.io/docs/v2/reference/timber-menuitem/) class with helpers for rendering hierarchical navigation.

## Registering MenuItem

As with Timber, register your custom MenuItem class in the class map so Timber knows which class to use when fetching MenuItems

```php
<?php

use Conifer\Navigation\MenuItem;
use Conifer\Site;

$site = new Site();
$site->configure(function () {
    add_filter('timber/menuitem/classmap', function (array $classmap): array {
        return array_merge($classmap, [
            'primary' => MenuItem::class,
            'footer'  => MenuItem::class,
        ]);
    });
});
```

## Determining the current post

Use `points_to_current_post_or_ancestor()` to determine whether a menu item points to the current post or one of its ancestors.

```twig
{% if item.points_to_current_post_or_ancestor %}
    <p>This item points to the current post (or one of its ancestors).</p>
{% endif %}
```

## Rendering children

Conifer provides two related helpers:

- `has_children()` checks whether the item has child menu items.
- `display_children()` returns `true` only when the item has children and points to the current post (or an ancestor).

```twig
{% if item.has_children %}
    {% if item.display_children %}
        {% for child in item.children %}
            <li class="nav-child-item">
                <a class="nav-child-link" href="{{ child.link }}">{{ child.title }}</a>
            </li>
        {% endfor %}
    {% endif %}
{% endif %}
```

