<?php
/**
 * Custom Twig filters for front-end forms
 */

namespace Conifer\Twig\Filters;

use Conifer\Form\AbstractBase as Form;

/**
 * Twig Wrapper for helpful linguistic filters, such as pluralize
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */
class FormHelper extends AbstractBase {
  /**
   * Get the Twig functions to register
   *
   * @return  array an associative array of callback functions, keyed by name
   */
  public function get_filters() {
    return [
      'field_class'        => [$this, 'get_field_class'],
      'error_messages_for' => [$this, 'get_error_messages_for'],
      'err'                => [$this, 'get_error_messages_for'],
    ];
  }

  /**
   * Get the class to render for the form field, based on its error state
   *
   * @param  \Conifer\Form\AbstractBase $form a form object
   * @param  string $fieldName the name of the field being rendered
   * @return string            the HTML class(es) to render
   */
  public function get_field_class(Form $form, $fieldName) : string {
    return $form->get_errors_for($fieldName)
      ? 'error'
      : '';
  }

  /**
   * Get the error messages for a specific field only, as a
   * semicolon-separated string
   *
   * @param Form $form the Form being processed
   * @param string $fieldName the name of the field whose error messages we want
   * @return string
   */
  public function get_error_messages_for(Form $form, $fieldName) : string {
    return implode('<br>', $form->get_error_messages_for($fieldName));
  }
}

