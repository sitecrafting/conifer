<?php

namespace Conifer\Form;

abstract class AbstractBase {
  const MESSAGE_FIELD_REQUIRED = '%s is required';

  /**
   * The fields configured for this form as an array of arrays.
   * For example, an EmployeeForm might have this config:
   *
   * [
   *   'user_login' => [
   *     'label' => 'Username',
   *     'validators' => [[$this, 'validateRequiredField'], [$this, 'validateLogin']],
   *   ],
   *   'user_email' => [
   *     'label' => 'Email',
   *     'validators' => [[$this, 'validateEmail']],
   *   ],
   *   'first_name' => [
   *     'label' => 'First Name',
   *     'validators' => [[$this, 'validateRequiredField'], [$this, 'validateNameField']],
   *   ],
   *   'last_name' => [
   *     'label' => 'Last Name',
   *     'validators' => [[$this, 'validateRequiredField'], [$this, 'validateNameField']],
   *   ],
   * ]
   *
   * Note that for a given field, "validators" is a list of callbacks.
   *
   * @var array
   */
  protected $fields;

  /**
   * The errors collected while processing this form, as arrays. Each error array
   * should have a "message" and a "field" index.
   * @var array
   */
  protected $errors;

  /**
   * Whether this form submission was processed successfully.
   * @var boolean
   */
  protected $success;

  /**
   * Process the form submission.
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
   * Get the fields configured for this form
   * @return array an array of form fields.
   */
  public function getFields() {
    return $this->fields;
  }

  /**
   * Get the current value for the given form field.
   * @param  string $name the name of the form field whose value you want.
   * @return the submitted value, or the existing persisted value if no
   * value has been submitted, or otherwise null.
   */
  public function get($name) {
    if (!isset($this->fields[$name])) {
      return null;
    } else {
      $field = $this->fields[$name];
    }

    if (isset($field['submitted_value'])) {
      $value = $field['submitted_value'];
    } else if (isset($field['value'])) {
      $value = $field['value'];
    } else {
      $value = null;
    }

    return $value;
  }

  /**
   * Get the errors collected while processing this form, if any
   * @return array
   */
  public function getErrors() {
    return $this->errors;
  }

  /**
   * Whether this form has been processed without errors
   * @return boolean
   */
  public function processedSuccessfully() {
    return $this->success;
  }

  /**
   * Add an error message to a specific field. You can also add errors
   * to the form globally or to some aspect of the form, as long as you use the same
   * $fieldName to refer to it at render time using getErrorsFor().
   * @param string $fieldName the name of the field this error is associated with
   */
  public function addError($fieldName, $message) {
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
   * @param  array  $submission the submitted fields as key/value pairs
   * @return boolean            whether the submission is valid
   */
  public function validate(array $submission) {
    $valid = true;

    foreach ($this->getFields() as $name => $field) {
      if (!is_array($field)) {
        throw new \RuntimeException("$name field must be defined as an array!");
      }
      if (isset($field['validators'])) {
        if (!is_array($field['validators'])) {
          throw new \RuntimeException("$name validators must be defined as an array!");
        }

        $field['name'] = $name;

        foreach ($field['validators'] as $validator) {
          if (!is_callable($validator)) {
            throw new \RuntimeException("$name field validator must be defined as a callable!");
          }

          $fieldValue = isset($submission[$name]) ? $submission[$name] : '';
          $valid = call_user_func_array($validator, [$field, $fieldValue]) && $valid;
        }
      }
    }

    return $valid;
  }

  /**
   * Check whether a value was submitted for the given field,
   * adding an error if not.
   * @param  string $field the name of the field
   * @param  string $value the submitted value
   * @return boolean
   */
  public function validateRequiredField($field, $value) {
    $valid = !empty($value);

    if (!$valid) {
      $this->addError($field['name'], sprintf(
        static::MESSAGE_FIELD_REQUIRED,
        $field['label']
      ));
    }

    return $valid;
  }

  /**
   * Get errors for a specific field
   * @param  string $fieldName the name of the field to get errors for
   * @return array            an array of error arrays
   */
  public function getErrorsFor($fieldName) {
    return array_filter( $this->getErrors(), function(array $error) use($fieldName) {
      return $error['field'] === $fieldName;
    });
  }

  /**
   * Get error messages for a specific field
   * @param  string $fieldName the name of the field to get errors for
   * @return array            an array of error message strings
   */
  public function getErrorMessagesFor($fieldName) {
    return array_map( function(array $field) {
      return $field['message'];
    }, $this->getErrorsFor($fieldName));
  }
}
