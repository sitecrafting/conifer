# Forms and Validation

## Getting Started
Conifer's Form API allows you to represent your custom forms as first-class OO citizens and helps to streamline validation, entry processing, and front-end output. To get started, extend the `Conifer\Form\AbstractBase` class and add your custom fields, validators, and processing logic:

<a name="extendBaseClassExample"></a>
```php
use Conifer\Form\AbstractBase;

class MyForm extends AbstractBase {
  public function __construct(array $files = null) {
    parent::__construct($files);
    $this->fields = [
      'email' => [
        'label' => 'Email',
        'validators' => [
          [$this, 'require'],
        ],
      ],
      'name' => [
        'label' => 'Name',
        'validators' => [
          [$this, 'validate_name'],
        ],
      ],
      'profession' => [
        'label' => 'Profession',
      ],
      'file' => [
        'label' => 'File Upload',
        'validators' => [
          [$this, 'require_file'],
          [$this, 'validate_file_mime_type', ['text/plain']],
        ],
      ],
    ];
  }

  // Process an incoming form submission 
  public function process(array $request) {
    $valid = $this->validate($request);
    if ($valid) {
      $this->success = true;
      echo 'Submission succeeded. Normally we would do something with the submitted data now (i.e. save it to the database)';
    }
  }

  // Custom validation for the name field
  public function validate_name(array $nameField, string $name) {
    // We can get as simple as we want to!
    // Here we're just checking that $name matches a certain value.
    $valid = $name === 'Bob';
    if (!$valid) {
      $this->add_error($nameField['name'], 'Only people named Bob are worthy');
    }
    return $valid;
  }
```

From here, your `MyForm` class can be used to process and validate incoming submissions with minimal effort:

<a name="instantiateClassExample"></a>
```php
$form = Conifer\Form\MyForm::create_from_submission($_POST, $_FILES);
if( $_POST || $_FILES ){
    $form->process( $_POST );
}

Timber::render('my-form-page.twig', ['myForm' => $form]);
```

Displaying validation errors and submitted values is just as simple:

<a name="frontEndFormExample"></a>
```twig
{% if myform.has_errors %}
  <p>Danger, Will Robinson! Your form has the following errors: {{ myForm.get_unique_error_messages|join(', ') }}</p>
{% elseif myForm.succeeded %}
  <p>Success, thanks for the info!</p>
{% endif %}

<form method="POST" enctype="multipart/form-data">
  <label for="name">Name</label>
  <input type="text" name="name" id="name" value="{{ myForm.get('name') }}" />

  <label for="email">Email</label>
  <input type="email" name="email" id="email" value="{{ myForm.get('email') }}" />

  <label for="profession">Profession</label>
  <select name="profession" id="profession">
    <option value="">Select a profession</option>
    <option value="Programmer" {{ myForm.selected('profession', 'Programmer') ? 'selected' : '' }}>Programmer</option>
    <option value="Software Engineer" {{ myForm.selected('profession', 'Software Engineer') ? 'selected' : '' }}>Software Engineer</option>
    <option value="Script Kiddie" {{ myForm.selected('profession', 'Script Kiddie') ? 'selected' : '' }}>Script Kiddie</option>
  </select>

  <label for="file">File</label>
  <input type="file" name="file" id="file" />

  <button type="submit">Submit</button>
</form>
```

## Validator API

Validators can be added to a field's definition array as a [Callable](http://php.net/manual/en/language.types.callable.php) or [Closure](http://php.net/manual/en/class.closure.php) value (typically a public function of the current form class) in the `validators` array:

<a name="basicValidatorExample"></a>
```php
$this->fields = [
  'email' => [
    'label' => 'Email',
    'validators' => [
      [$this, 'require'],
    ],
  ],
]
```

When they are executed, all validators will have access to the following parameters in this order:
1. `array $field` - The array definition of the field being validated, typically defined in your class constructor.
2. `string $value` - The value of the field being validated, or an empty string if the field wasn't present in the form submission data.
3. `array $submission` - The complete submitted form data array (typically the contents of the $_POST subperglobal)

Additional parameters can be passed to validators as well. In the following example, an array of valid file MIME types will be passed to the `validate_file_mime_type` function:

<a name="mimeTypeValidatorExample"></a>
```php
$this->fields = [
  'file' => [
      'label' => 'File Upload',
      'validators' => [
          [$this, 'validate_file_mime_type', ['text/plain']],
      ],
  ],
]
```

## Default Validators

Conifer supports the following validators out of the box:

`validate_required_field` _(alias: `require`)_

Validates that a field's value is not [empty](http://php.net/manual/en/function.empty.php).

`validate_required_file` _(alias: `require_file`)_

Validates that a file field [is set](http://php.net/manual/en/function.isset.php) in the array of files for this form class' `files` property, and that there is no [file upload error](http://php.net/manual/en/features.file-upload.errors.php).

`validate_file_mime_type`

Validates that an uploaded file field's type is in an array of whitelisted [MIME types](https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Complete_list_of_MIME_types). Takes an additional `array` parameter of strings with whitelisted types.

## Field State

Forms know about the state of their fields. You can call `$form->get('field_name')`  to get the submitted value of any field.

Select, checkbox, and radio fields have their own helpers. You can ask whether a field is `selected` or `checked` with the respective methods.

```php
$form->hydrate([
  'my_select' => 'user selection',
  'my_checkbox' => '123',
]);

$form->selected('my_select'); // true
$form->selected('my_select', 'user selection'); // true
$form->selected('my_select', 'some other value'); // false

$form->checked('my_checkbox'); // true
$form->checked('my_checkbox', '123'); // true
$form->checked('my_checkbox', '456'); // false

$form->selected('bogus field'); // false
$form->checked('bogus field'); // false
```

Note that both methods support array values, so multiselect inputs and checkbox groups (checkboxes with attributes like `name="field_name[]"`) will be considered `selected` or `checked` if the array contains the given value:

```php
$form->hydrate([
  'my_multiselect' => ['option A', 'option B'],
  'my_checkbox_group' => ['selection 1', 'selection 2'],
]);

$form->selected('my_multiselect', 'option A'); // true
$form->selected('my_multiselect', 'option B'); // true
$form->selected('my_multiselect', 'option XYZ'); // false

$form->checked('my_checkbox_group', 'selection 1'); // true
$form->checked('my_checkbox_group', 'selection 2'); // true
$form->checked('my_checkbox_group', 'some other value'); // false
```

## Form Errors

Once a form submission has been validated, any resulting errors can be used to display an informative message to the user:

```php
// let's set up an error case
$form->add_error('my_field', 'something bad happened! 🙀');
$form->add_error('another_field', 'something even worse happened!! 😿');

$form->has_errors(); // true
$form->has_errors_for('my_field'); // true
$form->has_errors_for('bogus field'); // false
$form->get_error_messages_for('my_field'); // ['something bad happened! 🙀']
$form->get_error_messages_for('another_field');
// ['something even worse happened!! 😿']
```

Of course, you can always call these from Twig:

```twig
{% if myForm.has_errors %}
  <p>Danger, Will Robinson! Your form has the following errors: {{ myForm.get_unique_error_messages|join(', ') }}</p>
{% endif %}
```

## Twig helpers

For rendering forms in Twig you have even more power. The `Conifer\Twig\FormHelper`, loaded into Twig by default, provides some nice filters you can use directly in your templates:

```twig
{# add the "error" class on label/input IFF there are errors for this field! #}
<label class="{{ myForm | field_class('email') }}" for="email">Email</label>
<input
	class="{{ myForm | field_class('email') }}"
	type="email"
	name="email"
	id="email"
	value="{{ myForm.get('email') }}"
/>

{% if myForm.has_errors_for('email') %}
  {# render all errors on the email field, separated with <br> tags #}
  <span class="field-error">{{ myForm | err('email') }}</span>
{% endif %}
```

Note that you can also use the more verbose `error_messages_for` in place of the `err` filter, if you're not into the whole brevity thing.

### Custom error classes

By passing an optional second parameter to the `field_class` filter, you can specify your own error class to render when the field has errors:

```twig
<input class="{{ myForm | field_class('field-name','validation-error') }}" />
```

### Custom error message separators

You can specify your own separator when rendering a list of error messages by passing an optional parameter to the filter. Here we're using a semicolon:

```twig
<span class="field-error">{{ myForm | err('email', ';') }}</span>
```

### Select/checkbox/radio filters

The special `selected_attr` and `checked_attr` Twig filters know how to render attributes for select, checkbox, and radio fields. These call into the corresponding `selected()` and `checked()` methods on the `Form` class.

These are great for freeing your Twig views from the shackles of conditional logic:

```twig
{# instead of this: #}
<input type="checkbox" {% if myForm.checked('my_field') %}checked{% endif %} />

{# ...we can do this: #}
<input type="checkbox" {{ myForm | checked_attr('my_field') }} />
```

They're also handy for looping through options in a `<select>` element or a radio/checkbox group:

```twig
<select name="my_field">
  {% for value, label in my_select_options %}
		<option {{ myForm | selected_attr('my_select_field', value) }}>{{ label }}</option>
  {% endfor %}
</select>

{% for value, label in my_radio_options %}
	<input
		type="radio"
		id="my-radio-field-{{ loop.index }}"
		value="{{ value }}"
		{{ myForm | checked_attr('my_select_field', value) }}
	/>
	<label for="my-radio-field-{{ loop.index }}">{{ label }}</label>
{% endfor %}
```

## Why?

There are a [ton](https://www.gravityforms.com/) [of](https://wordpress.org/plugins/ninja-forms/) [form](https://wordpress.org/plugins/contact-form-7/) [plugins](https://wpforms.com/) available for WordPress, so why does Conifer have its own Form API? Good question!

Typical WordPress form plugins are useful for allowing site content admins to generate and maintain simple front-end forms, and often work great for these purposes. But when it comes to mission-critical forms, these solutions fall short in a few key areas:
1. They can be difficult and messy to extend programmatically with your own custom validation a processing logic. See [this example](https://docs.gravityforms.com/gform_validation/#1-validate-a-specific-field) from the Gravity Forms documentation for adding custom validation logic to see what we mean. 😬
2. They're often opinionated on form styles and markup, requiring developers to jump through extra hoops to integrate a form's look and feel into your site design.
3. They open up the possibility of critical fields or even entire forms being removed by an over-eager admin.

By contrast, [extending Conifer's base Form class](#extendBaseClassExample) is all you need to do to get started with custom forms, and while our API has tons of functionality which will [help you render a form](#frontEndFormExample) on the front end of your site, you're free to define your own markup and styles. In other words:

![The power is yours!](https://i.giphy.com/fWfCYufxVgthCxLIHv.gif "The power is yours!")
