<?php

namespace Conifer\Form;

abstract class AbstractBase {
  const MESSAGE_FIELD_REQUIRED = '%s is required';

  /**
   * The fields configured for this form as an array of arrays.
   * Each key in the top-level array is the name of a field; each field is
   * represented simply as an array. Validations are declarative, in that each
   * field tells this class exactly how to validate it
   * For example, you might set up an EmployeeForm in this way:
   *
   * use Conifer\Form\AbstractBase;
   *
   * class EmployeeForm extends AbstractBase {
   *   public function __construct() {
   *     $this->fields = [
   *       'user_login' => [
   *         'label' => 'Username',
   *         'validators' => [
   *           [$this, 'validate_required_field'],
   *           [$this, 'validate_login']
   *         ],
   *       ],
   *       'user_email' => [
   *         'label' => 'Email',
   *         'validators' => [[$this, 'validate_email']],
   *       ],
   *       'first_name' => [
   *         'label' => 'First Name',
   *         'validators' => [
   *           [$this, 'validate_required_field'],
   *           [$this, 'validate_name']
   *         ],
   *       ],
   *       'last_name' => [
   *         'label' => 'Last Name',
   *         'validators' => [
   *           [$this, 'validate_required_field'],
   *           [$this, 'validate_name']
   *         ],
   *       ],
   *     ];
   *   }
   *
   *   public function validate_email(array $emailField, string $email) {
   *     // We can call other pre-defined validators here
   *     $valid = $this->validate_required_field($emailField, $email);
   *
   *     if ($valid) {
   *       // End-user specified an email, but is it valid?
   *       $valid = !empty(filter_var($email, FILTER_VALIDATE_EMAIL));
   *       if (!$valid) {
   *         // NOPE.
   *         $this->add_error($emailField, 'invalid email!');
   *       }
   *     }
   *
   *     return $valid;
   *   }
   *
   *   public function validate_name(array $nameField, string $name) {
   *     // We can get as simple as we want to!
   *     // Here we're just checking that $name matches a certain value.
   *     $valid = $name === 'Bob';
   *     if (!$valid) {
   *       $this->add_error($nameField, 'only people name Bob are worthy');
   *     }
   *     return $valid;
   *   }
   *
   *   public function validate_login(array $loginField, string $login, array $submission) {
   *     // The validate() method passes all submitted data to each validator.
   *     // So, if you need to, you can look at other fields:
   *     $leet = $login === $submission['name'].'_x1337x';
   *
   *     // Check if the login is cool enough.
   *     if (!$leet) {
   *       $this->add_error(
   *         $loginField,
   *         'You login is not cool enough. It must be your name plus the string "_x1337x".'
   *       );
   *     }
   *
   *     return $leet;
   *   }
   * }
   *
   * Note that for a given field, "validators" is a list of callbacks.
   * Validators define their own error messaging; that is, if a validator
   * method finds that a field is invalid for some reason, it is responsible
   * for adding an error message for that field. While this is a little more
   * work, it allows for fine-grained control over the order in which fields
   * are validated, and the messages that are displayed for specific reasons.
   *
   * @var array
   */
  protected $fields;

  /**
   * The errors collected while processing this form, as arrays. Each error array
   * should have a "message" and a "field" index.
   *
   * @var array
   */
  protected $errors;

  /**
   * Whether this form submission was processed successfully.
   *
   * @var boolean
   */
  protected $success;

  /**
   * Process the form submission.
   *
   * @param  array  $request the submitted form data, e.g. $_POST
   */
  abstract public function process(array $request);

  /**
   * Constructor
   */
  public function __construct() {
    $this->errors = [];
    $this->fields = [];
    $this->success = false;
  }

  /**
   * Create a new instance from the request array (e.g. $_POST)
   * and return the hydrated form object. Takes a variable number of arguments,
   * but the first argument MUST be the submitted values, as an associative
   * array. The remaining arguments, if any, are passed to the constructor.
   *
   * @return \Conifer\Form\AbstractBase the form object
   */
  public static function create_from_submission(...$args) {
    list($submission) = array_splice($args, 0, 1);

    if (!is_array($submission)) {
      throw new \InvalidArgumentException(
        'last argument to create_from_submission()'
          . ' must be an array of submitted values'
      );
    }

    // hydrate the fields and return the new instance
    return (new static(...$args))->hydrate($submission);
  }

  /**
   * Get the fields configured for this form
   *
   * @return array an array of form fields.
   */
  public function get_fields() : array {
    return $this->fields;
  }

  /**
   * Get a field by its name
   *
   * @return array|null the field, or null if it doesn't exist
   */
  public function get_field(string $name) {
    return $this->fields[$name] ?? null;
  }

  /**
   * Get the current value for the given form field.
   *
   * @param  string $name the name of the form field whose value you want.
   * @return the submitted value, or the existing persisted value if no
   * value has been submitted, or otherwise null.
   */
  public function get($name) {
    return $this->{$field} ?? null;
  }

  /**
   * Get the errors collected while processing this form, if any
   *
   * @return array
   */
  public function get_errors() : array {
    return $this->errors;
  }

  /**
   * Whether this form has any validation errors
   *
   * @return bool
   */
  public function has_errors() : bool {
    return !empty($this->errors);
  }

  /**
   * Whether this form has been processed without errors
   *
   * @return boolean
   */
  public function processed_successfully() : bool {
    return $this->success;
  }

  /**
   * Get all unique error messages as a flat array,
   * e.g. for displaying in a list at the top of the <form> element
   *
   * @return array
   */
  public function get_unique_error_messages() : array {
    return array_unique(array_map(function(array $error) {
      return $error['message'];
    }, $this->errors));
  }

  /**
   * Add an error message to a specific field. You can also add errors
   * to the form globally or to some aspect of the form, as long as you use the same
   * $fieldName to refer to it at render time using get_errors_for().
   *
   * @param string $fieldName the name of the field this error is associated with
   */
  public function add_error(string $fieldName, string $message) {
    $this->errors[] = [
      'field' => $fieldName,
      'message' => $message,
    ];
  }

  /**
   * Check field values according to their respective validators,
   * returning whether all validations pass.
   * NOTE: This doesn't add any errors explicitly; specific validators are
   * responsible for that.
   *
   * @param  array  $submission the submitted fields as key/value pairs
   * @throws LogicException if validators are configured incorrectly
   * @return boolean            whether the submission is valid
   */
  public function validate(array $submission) : bool {
    $valid = true;

    foreach ($this->get_fields() as $name => $field) {
      if (!is_array($field)) {
        throw new \LogicException("$name field must be defined as an array!");
      }

      // check the validators for this field
      $validators = $field['validators'] ?? [];
      if (!is_array($validators)) {
        throw new \LogicException("$name validators must be defined as an array!");
      }

      $field['name'] = $name;

      // call each validator for this field
      foreach ($validators as $validator) {
        if (!is_callable($validator)) {
          throw new \RuntimeException("$name field validator must be defined as a callable!");
        }

        // get the submitted value for this field, defaulting to empty string
        $fieldValue = $submission[$name] ?? '';

        // validate this field
        $valid = call_user_func_array(
          $validator,
          [$field, $fieldValue, $submission]
        ) && $valid;
      }
    }

    return $valid;
  }

  /**
   * Check whether a value was submitted for the given field,
   * adding an error if not.
   *
   * @param  array $field the field array itself
   * @param  string $value the submitted value
   * @return boolean
   */
  public function validate_required_field(array $field, string $value) : bool {
    $valid = !empty($value);

    if (!$valid) {
      $this->add_error($field['name'], sprintf(
        static::MESSAGE_FIELD_REQUIRED,
        $field['label']
      ));
    }

    return $valid;
  }

  /**
   * Get errors for a specific field
   *
   * @param  string $fieldName the name of the field to get errors for
   * @return array            an array of error arrays
   */
  public function get_errors_for(string $fieldName) : array {
    return array_filter( $this->get_errors(), function(array $error) use($fieldName) {
      return $error['field'] === $fieldName;
    });
  }

  /**
   * Get error messages for a specific field
   *
   * @param  string $fieldName the name of the field to get errors for
   * @return array            an array of error message strings
   */
  public function get_error_messages_for(string $fieldName) : array {
    return array_map(function(array $field) {
      return $field['message'];
    }, $this->get_errors_for($fieldName));
  }


  /**
   * Get error messages for a specific field
   *
   * @param  string $fieldName the name of the field to get errors for
   * @return array            an array of error message strings
   */
  public function getErrorMessagesFor($fieldName) {
    return array_map( function(array $field) {
      return $field['message'];
    }, $this->getErrorsFor($fieldName));
  }

  /**
   * Hydrate this form object with the submitted values
   *
   * @return \Conifer\Form\AbstractBase this form instance
   */
  public function hydrate(array $submission) : AbstractBase {
    foreach ($this->get_whitelisted_fields($submission) as $field => $value) {
      $this->$field = $value;
    }
    return $this;
  }

  /**
   * Get the submitted values, filtered down to only fields decared in the
   * constructor
   *
   * @param array $submission the submitted fields
   * @return array the whitelisted fields
   */
  public function get_whitelisted_fields(array $submission) {
    // get only the submitted fields that have a key in common
    // with the fields we declared
    return array_intersect_key(
      $submission,
      array_flip(array_keys($this->fields))
    );
  }
}
