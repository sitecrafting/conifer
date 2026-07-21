# MenuItem

Conifer extends the functionality of the [MenuItem](https://timber.github.io/docs/v2/reference/timber-menuitem/) class. These functions are helpful when rendering large side navigation menus with a complicated hierarchy.

## Registering MenuItem

Like in Timber, you must add the MenuItem to the classmap in order for Timber to know which class to use when calling `Timber::get_menu()`

```php
// In functions.php
<?php>

use Conifer\Site;
use Conifer\Navigation\Menu;
use Conifer\Navigation\MenuItem;

$site = new Site();
$site->configure(function() {
    //... rest of configure function

    // Add MenuItem to classmap
    add_filter('timber/menuitem/classmap', function ($classmap) {
        $custom_classmap = [
            'primary' => MenuItem::class,
        ];
    
        return array_merge($classmap, $custom_classmap);
    });
```

## Determining current post

Conifer includes a helper function `points_to_current_post_or_ancestor()` which determines if a MenuItem points to the current post, or an ancestor of the current post

```twig
{% if menuItem.points_to_current_post_or_ancestor %}
    <p>This MenuItem points to the current post, or an ancestor of the current post!</p>
{% endif %}
```

## Children

Conifer includes two utility functions for rendering child MenuItems, `has_children()` and `display_children()`

```twig
<!-- Check if this MenuItem has child MenuItems -->
{% if menuItem.has_children %}
    <!-- This MenuItem has children. You can also check if those child MenuItems should be rendered -->

    <!-- Check if those menu items should be rendered -->
    {% if menuItem.display_children %}
        <!-- Render child MenuItems -->
        {% for child in menuItem.children %}
            <li class="nav-child-item">
                <a
                    class="nav-child-link"
                    href="{{ child.link }}"
                >{{ child.title }}</a>
            </li>
        {% endfor %}
    {% endif %}
{% else %}
    <!-- This MenuItem does not have any children -->
{% endif %}
```

