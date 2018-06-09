# Conifer Documentation

## NOTE: ALPHA STATUS

Until v1.0.0, we may introduce breaking changes on minor releases.

## What is Conifer?

Conifer is a library plugin for creating WordPress plugins and themes using an opinionated object-oriented architecture, built on top of the amazing [Timber](https://timber.github.io/docs/) plugin.

[Read more about Conifer and its goals](/what-is-conifer.md).

## More Helpers

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
  	{% for related in post.get_related(3) %}
  		<a href="{{ related.link }}">{{ related.title }}</a>
  	{% endfor %}
  </aside>
</html>	
```

## DRY Architecture

Get more stuff done without so much boilerplate.

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

```php
<?php
use Conifer\Post\Post;
use Conifer\Post\HasCustomAdminColumns;

class Company extends Post {
  use HasCustomAdminColumns;

  public static function register {
    register_post_type(...);

    static::add_admin_column(
      'location', // column key
      'Location', // column label
      function($companyId) {
        $company = new static($companyId);
        return $company->get_location()->title();
      }
    );
  }

  public function get_location() {
    // ...
  }
}

```

### And lots more!

## Quick Install

Install using composer:

```bash
composer require --save sitecrafting/conifer
```

See [Installation](/installation.md) for more details.