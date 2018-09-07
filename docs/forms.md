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
      $this->add_error($nameField['name'], 'Only people name Bob are worthy');
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

## Form Errors

Once a form submission has been validated, any resulting errors can be used to display an informative message to the user:

```twig
{% if myform.has_errors %}
  <p>Danger, Will Robinson! Your form has the following errors: {{ myForm.get_unique_error_messages|join(', ') }}</p>
{% endif %}
```

Error messages can be retrieved on a per-field basis as well:

```twig
<label for="email">Email</label>
<input type="email" name="email" id="email" value="{{ myForm.get('email') }}" />
{% if myform.has_errors_for('email') %}
  <span class="field-error">{{ myForm.get_errors_for('email')|join(', ') }}</span>
{% endif %}
```

## Why?

There are a [ton](https://www.gravityforms.com/) [of](https://wordpress.org/plugins/ninja-forms/) [form](https://wordpress.org/plugins/contact-form-7/) [plugins](https://wpforms.com/) available for WordPress, so why does Conifer have its own Form API? Good question!

Typical WordPress form plugins are useful for allowing site content admins to generate and maintain simple front-end forms, and often work great for these purposes. But when it comes to mission-critical forms, these solutions fall short in a few key areas:
1. They can be difficult and messy to extend programmatically with your own custom validation a processing logic. See [this example](https://docs.gravityforms.com/gform_validation/#1-validate-a-specific-field) from the Gravity Forms documentation for adding custom validation logic to see what we mean. 😬
2. They're often opinionated on form styles and markup, requiring developers to jump through extra hoops to integrate a form's look and feel into your site design.
3. They open up the possibility of critical fields or even entire forms being removed by an over-eager admin.

By contrast, [extending Conifer's base Form class](#extendBaseClassExample) is all you need to do to get started with custom forms, and while our API has tons of functionality which will [help you render a form](#frontEndFormExample) on the front end of your site, you're free to define your own markup and styles. In other words:

![The power is yours!](https://i.giphy.com/fWfCYufxVgthCxLIHv.gif "The power is yours!")