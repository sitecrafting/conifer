<?php
/**
 * Test the FormHelper methods exposed to Twig
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author Coby Tamayo
 */

namespace Conifer\Unit;

use Conifer\Form\AbstractBase as Form;
use Conifer\Twig\FormHelper;

class FormHelperTest extends Base {
  public function setUp() {
    parent::setUp();
    $this->wrapper = new FormHelper();
  }

  public function test_field_class() {
    // mock up some form errors
    $form = $this->getMockForAbstractClass(Form::class);
    $form->add_error('foo', 'error message for foo');

    $this->assertEquals(
      'error',
      $this->wrapper->get_field_class($form, 'foo')
    );

    $this->assertEquals(
      'my-error-class',
      $this->wrapper->get_field_class($form, 'foo', 'my-error-class')
    );
  }

  public function test_get_error_messages_for() {
    $form = $this->getMockForAbstractClass(Form::class);
    $form->add_error('foo', 'error message for foo');
    $form->add_error('foo', 'another error for foo');

    $this->assertEquals(
      'error message for foo<br>another error for foo',
      $this->wrapper->get_error_messages_for($form, 'foo')
    );

    $this->assertEquals(
      'error message for foo; another error for foo',
      $this->wrapper->get_error_messages_for($form, 'foo', '; ')
    );
  }

  public function test_checked_attr() {
    $form = $this->setup_form([
      // field config
      'my_checkbox' => [],
    ], [
      // field config
      'my_checkbox' => '1',
    ]);

    $this->assertEquals(
      ' checked ',
      $this->wrapper->checked_attr($form, 'my_checkbox', '1')
    );
    $this->assertEquals(
      '',
      $this->wrapper->checked_attr($form, 'my_checkbox', 'something else')
    );
  }

  public function test_selected_attr() {
    $form = $this->setup_form([
      // field config
      'my_select' => [],
    ], [
      // field config
      'my_select' => '1',
    ]);

    $this->assertEquals(
      ' selected ',
      $this->wrapper->selected_attr($form, 'my_select', '1')
    );
    $this->assertEquals(
      '',
      $this->wrapper->selected_attr($form, 'my_select', 'something else')
    );
  }

  protected function setup_form(array $fields, array $values = []) {
    $form = $this->getMockForAbstractClass(Form::class);
    $this->setProtectedProperty($form, 'fields', $fields);
    $form->hydrate($values);

    return $form;
  }
}
