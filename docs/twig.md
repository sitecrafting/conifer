# Twig Helpers

Conifer defines the `Conifer\Twig\HelperInterface` for quickly defining custom Twig functions and filters.

The interface is simple:

```php
namespace Conifer\Twig;

interface HelperInterface {
  public function get_functions() : array;
  public function get_filters() : array;
}
```

To register custom Twig helpers, just pass an instance of `HelperInterface` to the `Conifer\Site::add_twig_helper()` method:

```php
$site->add_twig_helper(new MyTwigHelper());
```

Each public method must return an array of callables, keyed by the function/filter name to be used in Twig templates. For example, say you want to define a `zany_caps()` Twig filter, for getting all zany with capitalization:

```twig
<span>{{ 'hello world' | zany_caps }}</span>
{# renders: #}
<span>HeLlO WoRlD</span>
```

First, write a `CapsHelper` and define your filter as an instance method. Inside the array returned by `get_filters()`, include the method as a `callable`:

```php
use Conifer\Twig\HelperInterface;

class CapsHelper implements HelperInterface {
  public function get_filters() : array {
    return [
      'zany_caps' => [$this, 'zany_caps'],
    ];
  }

  public function zany_caps(string $text) {
    $chars = explode('', $text);
    $zanyChars = array_map(function(string $char, int $i) {
      // capitalize even letters; force odd letters to lowercase
      return $i % 2 === 0 ? strtoupper($char) : strtolower($char);
    }, $chars, array_keys($chars));
    // glue everything back together
    return implode('', $zanyChars);
  }
    
  public function get_functions() : array { return []; }
}
```

Now, simply instantiate and register your helper:

```php
$site->add_twig_helper(new CapsHelper());
```

The text being filtered is always the first argument to the callback, just like when you register a callback directly with `Twig_SimpleFilter`.

Note that in this example, we didn't define any Twig functions, but to keep PHP happy, we still have to implement both public methods, where one simply returns an empty array.

## Filters and Functions can be any callable

Note too that each callable returned in `get_filters()` or `get_functions()` can be *any* callable, as long as it operates on strings. It doesn't have to be an instance method! For example, say you wanted to use PHP's [`ltrim()`](http://us3.php.net/manual/en/function.ltrim.php) as a Twig filter:

```twig
<span>{{ 'asdfg' | ltrim('sad') }}</span>
{# renders: #}
<span>fg</span>
```

Because the string `"ltrim"` is a callable, you can simply return it inside the result of `get_filters()`:

```php
  public function get_filters() : array {
    return [
      'ltrim' => 'ltrim',
    ]
  }
```

## Built-in Helpers

Conifer comes with several built-in helpers defining various utility functions and filters. See the API reference for details about what they do:

* `FormHelper`
* `ImageHelper`
* `NumberHelper`
* `TermHelper`
* `TextHelper`
* `WordPressHelper`

