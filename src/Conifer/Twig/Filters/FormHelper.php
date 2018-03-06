<?php
/**
 * Custom Twig filters for front-end forms
 */

namespace Conifer\Twig\Filters;

use Conifer\Form\AbstractBase as Form;

/**
 * Twig Wrapper for helpful linguistic filters, such as pluralize
 *
 * @copyright 2015 SiteCrafting, Inc.
 * @author Coby Tamayo
 * @package  Groot
 */
class FormHelper extends AbstractBase {
  /**
   * Get the Twig functions to register
   * @return  array an associative array of callback functions, keyed by name
   */
  public function get_filters() {
    return [
      'field_class' => [$this, 'get_field_class'],
      'error_messages_for' => [$this, 'get_error_messages_for'],
    ];
  }

  /**
   * Get the class to render for the form field, based on its error state
   * @param  \Conifer\Form\AbstractBase $form a form object
   * @param  string $fieldName the name of the field being rendered
   * @return string            the HTML class(es) to render
   */
  public function get_field_class(Form $form, $fieldName) {
    return $form->getErrorsFor($fieldName)
      ? 'error'
      : '';
  }

  public function get_error_messages_for(Form $form, $fieldName) {
    return implode('; ', $form->getErrorMessagesFor($fieldName));
  }
}

