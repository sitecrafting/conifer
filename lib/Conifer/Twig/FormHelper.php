<?php
/**
 * Custom Twig filters for front-end forms
 */

namespace Conifer\Twig;

use Conifer\Form\AbstractBase as Form;

/**
 * Twig Wrapper for helpful linguistic filters, such as pluralize
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */
class FormHelper implements HelperInterface {
  /**
   * Get the Twig functions to register
   *
   * @return  array an associative array of callback functions, keyed by name
   */
  public function get_filters() : array {
    return [
      'field_class'        => [$this, 'get_field_class'],
      'error_messages_for' => [$this, 'get_error_messages_for'],
      'err'                => [$this, 'get_error_messages_for'],
      'checked_attr'       => [$this, 'checked_attr'],
      'selected_attr'      => [$this, 'selected_attr'],
    ];
  }

  /**
   * Does not supply any additional Twig functions.
   *
   * @return  array
   */
  public function get_functions() : array {
    return [];
  }

  /**
   * Get the class to render for the form field, based on its error state
   *
   * @param  \Conifer\Form\AbstractBase $form a form object
   * @param  string $fieldName the name of the field being rendered
   * @param  string $errorClass the class to give this field if it has errors
   * @return string            the HTML class(es) to render
   */
  public function get_field_class(
    Form $form,
    string $fieldName,
    string $errorClass = 'error'
  ) : string {
    return $form->get_errors_for($fieldName)
      ? $errorClass
      : '';
  }

  /**
   * Get the error messages for a specific field only, as a concatenated string
   * with line breaks between by default
   *
   * @param Form $form the Form being processed
   * @param string $fieldName the name of the field whose error messages we want
   * @param string $separator the separator to place between multiple errors
   * @return string
   */
  public function get_error_messages_for(
    Form $form,
    string $fieldName,
    string $separator = '<br>'
  ) : string {
    return implode($separator, $form->get_error_messages_for($fieldName));
  }

  /**
   * Return the `checked` attribute for a given form input, given the
   * (hydrated) form and the field name, and optionally the value to check
   * against.
   *
   * @param Form $form the Form object containing the field in question
   * @param string $fieldName the `name` of the field
   * @param string $value (optional) the value to check against. This is
   * necessary e.g. for radio inputs, where there's more than one possible
   * value.
   * @return string literally `" checked "` if the field (optionally the one
   * matching `$value`) was checked, or the empty string
   */
  public function checked_attr(
    Form $form,
    string $fieldName,
    string $value = null
  ) : string {
    return $form->checked($fieldName, $value) ? ' checked ' : '';
  }

  /**
   * Return the `selected` attribute for a given form input, given the
   * (hydrated) form and the field name, and optionally the value to check
   * against.
   *
   * @param Form $form the Form object containing the field in question
   * @param string $fieldName the `name` of the field
   * @param string $value (optional) the value to check against. This is
   * necessary e.g. for radio inputs, where there's more than one possible
   * value.
   * @return string literally `" selected "` if the field (optionally the one
   * matching `$value`) was selected, or the empty string
   */
  public function selected_attr(
    Form $form,
    string $fieldName,
    string $value
  ) : string {
    return $form->selected($fieldName, $value) ? ' selected ' : '';
  }
}

