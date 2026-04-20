# Twig Helpers

Conifer defines the `Conifer\Twig\HelperInterface` for quickly defining custom Twig functions and filters.

The interface is simple:

```php
<?php

declare(strict_types=1);

namespace Conifer\Twig;

interface HelperInterface {
    public function get_functions(): array;
    public function get_filters(): array;
}
```

To register custom Twig helpers, pass an instance of `HelperInterface` to the `Conifer\Site::add_twig_helper()` method:

```php
$site->add_twig_helper(new ThemeTwigHelper());
```

Each public method must return an array of callables, keyed by the function/filter name to be used in Twig templates. For example, say you want to define a `zany_caps()` Twig filter, for getting all zany with capitalization:

```twig
<span>{{ 'hello world' | zany_caps }}</span>
{# renders: #}
<span>HeLlO WoRlD</span>
```

First, write a `ThemeTwigHelper` and define your filter as an instance method. Inside the array returned by `get_filters()`, include the method as a `callable`:

```php
<?php

declare(strict_types=1);

use Conifer\Twig\HelperInterface;

class ThemeTwigHelper implements HelperInterface {

    public function get_functions(): array { 
        return [
            'all_caps' => function(string $input): string {
                return strtoupper($input);
            },
            'no_caps' => function(string $input): string {
                return strtolower($input);
            },
        ];
    }

    public function get_filters() : array {
        return [
            'zany_caps' => [$this, 'zany_caps'],
        ];
    }

    public function zany_caps(string $input) {
        $chars = explode('', $input);
        $zanyChars = array_map(function(string $char, int $i) {
            // capitalize even letters; force odd letters to lowercase
            return $i % 2 === 0 ? strtoupper($char) : strtolower($char);
        }, $chars, array_keys($chars));
        // glue everything back together
        return implode('', $zanyChars);
    }
}
```

Now, instantiate and register your helper:

```php
$site->add_twig_helper(new ThemeTwigHelper());
```

The text being filtered is always the first argument to the callback, just like when you register a callback directly with `Twig_SimpleFilter`.

Note that in this example, we didn't define any Twig functions, but to keep PHP happy, we still have to implement both public methods, where one simply returns an empty array.

## Filters and Functions can be any callable

Note that each callable returned in `get_filters()` or `get_functions()` can be *any* callable. It doesn't have to be an instance method! For example, say you wanted to use PHP's [`ltrim()`](http://us3.php.net/manual/en/function.ltrim.php) as a Twig filter:

```twig
<span>{{ 'asdfg' | ltrim('sad') }}</span>
{# renders: #}
<span>fg</span>
```

Because the string `"ltrim"` is a callable, you can return it inside the result of `get_filters()`:

```php
public function get_filters() : array {
    return [
        'ltrim' => 'ltrim',
    ]
}
```

## Built-in Helpers

Conifer comes with several built-in helpers defining various utility functions and filters.
These built-in helpers can change in-between releases, so please check the [GitHub repository](https://github.com/sitecrafting/conifer/tree/main/lib/Conifer/Twig) for the most up-to-date list.
