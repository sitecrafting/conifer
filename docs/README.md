# Conifer Documentation

## NOTE: ALPHA STATUS

Until v1.0.0, we may introduce breaking changes on minor releases.

## What is Conifer?

Conifer is a library plugin for creating WordPress plugins and themes using an opinionated object-oriented architecture, built on top of the amazing [Timber](https://timber.github.io/docs/) plugin.

[Read more about Conifer and its goals](https://www.coniferplug.in/getting-started/what-is-conifer).

## Why would I use Conifer?

- You want to build custom behavior for a WordPress theme or plugin in DRY, object-oriented code.
- You like the power of WordPress, but prefer to interact with a higher-level API.
- You are already a power user of Timber, and want to push your codebase even further.

Conifer shares several of Timber's [stated goals](https://github.com/timber/timber#mission-statement):

> * **Intuitive**: The API is written to be user-centric around a programmer's expectations.
> * **Consistent**: WordPress objects can be accessed through common polymorphic properties like slug, ID and name.
> * **Accessible**: No black boxes. Every effort is made so the developer has access to 100% of their HTML.

We make every effort to stay out of your way in this regard, to add to and not take away from Timber's powerful features.

## Powerful Features

Scan the menu or check out the [Conifer Basics](/getting-started/basics.md) guide for an overview of the powerful features Conifer offers for developing well-architected, object-oriented WordPress code. Here are some of them.

### More Helpers

Simplify your theme with a wealth of utility functions and helpers.

```php
<?php
/* wp-content/my-theme/single.php */
$data = $site->get_context_with_post(new Conifer\Post\BlogPost());
Timber::render('single.twig', $data);
```

```twig
{# wp-content/my-theme/views/single.twig #}
<!doctype html>
<html>
  ...
  
  <article>
  	...
    <h3>Categories</h3>
    <p>{{ post.categories | oxford_comma }}</p>
    {# -> Category 1, Category 2, and Category 3 #}
  </article>
  
  <aside>
  	<h3>Related Posts</h3>
  	{% for related in post.get_related_by_category(3) %}
  		<a href="{{ related.link }}">{{ related.title }}</a>
  	{% endfor %}
  </aside>
</html>	
```

### Easy custom Twig functions/filters

```php
use MyProject\MyCustomTwigHelper;

$site->add_twig_helper(new MyCustomTwigHelper());
```

### AJAX Action API

```php
<?php

/* put this in lib or somethin' */
use Conifer\AjaxHandler\AbstractBase;

class MySimpleAjaxHandler extends AbstractBase {
  protected function execute() {
    $response = ['foo' => 'bar'];
    // do some stuff with $response
    return $response;
  }
}

/* put this in functions.php */
add_action('wp_ajax_some_action', [MySimpleAjaxHandler::class, 'handle']);

/* the `some_action` AJAX action will return the following JSON:
{
	"foo": "bar"
}
*/

```

### Forms

Represent your custom forms as first-class OO citizens, using Conifer's Form API:

```php
use Conifer\Form\AbstractBase;

class EmployeeForm extends AbstractBase {
  public function __construct() {
    $this->fields = [
      'user_email' => [
        'label' => 'Email',
        // Just use the built-in required validator for this field:
        'validators' => [[$this, 'validate_required_field']],
      ],
      'name' => [
        'label' => 'First Name',
        'validators' => [
          // mix and match built-in with custom validators!
          [$this, 'validate_required_field'],
          [$this, 'validate_name']
        ],
      ],
    ];
  }

  public function validate_name(array $nameField, string $name) {
    $valid = $name === 'Bob';
    if (!$valid) {
      $this->add_error($nameField, 'only people name Bob are worthy');
    }
    return $valid;
  }
}
```

### Custom admin columns and filters

Easily add custom admin columns and filters to your admin screens, without having to remember all the arguments to the `manage_*_columns` hooks.

```php
<?php
namespace MyProject;  

use Conifer\Post\Post;

class Company extends Post {
  public static function register {
    register_post_type(...);

    static::add_admin_column(
      'location', // column key
      'Location', // column label
      // content to display in the column:
      function($companyId) {
        $company = new static($companyId);
        return $company->get_location()->title();
      }
    );
  }

  /**
   * Get the associated MyProject\Location instance for this Company
   */
  public function get_location() {
    return new Location($this->location_id);
  }
}

```

### And lots more!

## Quick Install

Install using composer:

```bash
composer require --save sitecrafting/conifer
```

See [Installation](https://www.coniferplug.in/getting-started/installation) for more details.