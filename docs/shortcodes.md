# Shortcodes

**NOTE: [Gutenberg](https://wordpress.org/gutenberg/) is on its way! In case you haven't heard, Gutenberg promises to change the entire WordPress editing experience, incuding the relevance of shortcodes. However, as shortcodes can offer server-side logic that Gutenberg blocks just cannot (e.g. authorization), we maintain that shortcodes will continue to serve a purpose, even as many use-cases for them are replaced with a better solution.**

The `Conifer\Shortcode\AbstractBase` class serves as a basis for defining custom shortcodes in an object-oriented style. Shortcode classes know how to render themselves, because the class contract *requires* that you implement a `render()` method which returns a string.

Let's say you want to display a random [bell hooks](https://en.wikipedia.org/wiki/Bell_hooks) quote on every page load, wherever you embed the `bell_hooks` shortcode.

The first step is to define a subclass of `AbstractBase` and implement the `render()` method:

```php
use Conifer\Shortcode\AbstractBase;

class RandomBellHooksQuote extends AbstractBase {
  const QUOTES = [
    'We judge on the basis of what somebody looks like, skin color,'
      . ' whether we think they\'re beautiful or not. That space on the Internet'
      . ' allows you to converse with somebody with none of those things involved.',
    'I will not have my life narrowed down. I will not bow down to somebody else\'s'
      . ' whim or to someone else\'s ignorance.',
    'Life-transforming ideas have always come to me through books.',
  ];

  public function render(array $atts = [], string $content = '') : string {
    return static::QUOTES[array_rand(static::QUOTES)];
  }
}
```

Then register the new shortcode with a tag:

```php
RandomBellHooksQuote::register('bell_hooks');
```

This tells WordPress to call the `render()` function on a new instance of the `RandomBellHooksQuote` class to render the `[bell_hooks]` shortcode. You can pass any valid shortcode tag to the `register()` method.

Note that because of the more locked down method signature, this does require that you declare both arguments to the `render()` method, including default values. We think that's a small price to pay for better type safety.

We're not using the arguments here, but if we were, we could of course specify our own default values in the method signature:

```php
  public function render(
    array $atts = ['example' => 'value'],
    string $content = 'hello!'
  ) : string { /* ... */ }
```