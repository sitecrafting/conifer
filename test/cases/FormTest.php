<?php
/**
 * Test the Conifer\Form\AbstractBase class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace ConiferTest;

use WP_Mock;

use Conifer\Form\AbstractBase;

class FormTest extends Base {
  protected $form;

  public function setUp() {
    parent::setUp();
    $this->form = $this->getMockForAbstractClass(AbstractBase::class);
  }

  public function test_hydrate() {
    $this->setFields([
      'first_name'  => [],
      'last_name'   => [],
      'favorite_things' => [],
    ]);
    $this->form->hydrate([
      'first_name'      => 'Julie',
      'last_name'       => 'Andrews',
      'favorite_things' => ['kettles', 'mittens'],
    ]);

    $this->assertEquals('Julie', $this->form->get('first_name'));
    $this->assertEquals('Andrews', $this->form->get('last_name'));
    $this->assertEquals(
      ['kettles', 'mittens'],
      $this->form->get('favorite_things')
    );
    $this->assertNull($this->form->get('yes_or_no'));
  }

  public function test_checked_with_single_value() {
    $this->setFields([
      'highest_award' => [],
    ]);
    $this->form->hydrate([
      'highest_award' => 'Academy Award',
    ]);

    $this->assertTrue($this->form->checked('highest_award', 'Academy Award'));
    $this->assertFalse($this->form->checked('highest_award', 'a blue ribbon'));
  }

  public function test_checked_with_multiple_values() {
    $this->setFields([
      'favorite_things' => [],
    ]);
    $this->form->hydrate([
      'favorite_things' => ['kettles', 'mittens'],
    ]);

    $this->assertFalse($this->form->checked('favorite_things', 'raindrops'));
    $this->assertFalse($this->form->checked('favorite_things', 'whiskers'));
    $this->assertTrue($this->form->checked('favorite_things', 'kettles'));
    $this->assertTrue($this->form->checked('favorite_things', 'mittens'));
  }

  public function test_checked_with_nonsense() {
    $this->setFields([]);
    $this->form->hydrate([]);

    $this->assertFalse($this->form->checked('nonsense'));
  }

  public function test_selected_with_single_option() {
    $this->setFields([
      'favorite_thing' => [],
    ]);
    $this->form->hydrate([
      'favorite_thing' => 'raindrops',
    ]);

    $this->assertTrue($this->form->selected('favorite_thing', 'raindrops'));
    $this->assertFalse($this->form->selected('favorite_thing', 'kittens'));
  }

  public function test_selected_with_multiple_options() {
    $this->setFields([
      'favorite_thing' => [],
    ]);
    $this->form->hydrate([
      'favorite_thing' => ['raindrops', 'mittens'],
    ]);

    $this->assertTrue($this->form->selected('favorite_thing', 'raindrops'));
    $this->assertTrue($this->form->selected('favorite_thing', 'mittens'));
    $this->assertFalse($this->form->selected('favorite_thing', 'kittens'));
  }

  public function test_get_errors_for() {
    $this->form->add_error('nationality', 'INVALID NATIONALITY');

    $this->assertEquals([
      [
        'field' => 'nationality',
        'message' => 'INVALID NATIONALITY',
      ],
    ], array_values($this->form->get_errors_for('nationality')));
  }

  public function test_get_error_messages_for() {
    $this->form->add_error('nationality', 'INVALID NATIONALITY');

    $this->assertEquals(
      ['INVALID NATIONALITY'],
      array_values($this->form->get_error_messages_for('nationality'))
    );
  }

  public function test_validate_valid_submission() {
    $isMaryPoppins = function(array $_, string $value) {
      return $value === 'Mary Poppins';
    };

    $this->setFields([
      'nanny' => [
        'validators' => [[$this->form, 'require'], $isMaryPoppins],
      ],
      'field_without_validator' => [],
      'spanish_inquisition' => [], // don't expect this field at all...
    ]);

    $this->assertTrue($this->form->validate([
      'nanny' => 'Mary Poppins',
      'field_without_validator',
    ]));
    $this->assertEmpty($this->form->get_errors());
  }

  public function test_validate_shorthand() {
    $this->setFields([
      'best_band' => [
        'validators' => ['require'],
      ],
    ]);

    $this->assertFalse($this->form->validate([
      'best_band' => '',
    ]));
    $this->assertEquals(1, count($this->form->get_errors()));
  }

  public function test_require_with_empty_value() {
    $bestBand = [
      'name' => 'best_band',
      'required_message' => 'You have to put somethin here broh.',
      'validators' => [[$this->form, 'require']],
    ];

    $this->assertFalse($this->form->require($bestBand, ''));
    $this->assertEquals(
      ['You have to put somethin here broh.'],
      $this->form->get_error_messages_for('best_band')
    );
  }

  public function test_require_with_value() {
    $bestBand = [
      'name' => 'best_band',
      'validators' => [[$this->form, 'require']],
    ];

    $this->assertTrue($this->form->require($bestBand, 'Creed'));
    $this->assertEmpty($this->form->get_error_messages_for('best_band'));
  }

  public function test_get_whitelisted_fields_with_filter() {
    $this->setFields([
      'activity'        => [
        'filter'        => function($val) {
          return "FILTERED->$val<-FILTERED";
        },
      ],
    ]);

    $whitelist = $this->form->get_whitelisted_fields([
      'activity' => 'anything really',
    ]);

    $this->assertEquals(
      'FILTERED->anything really<-FILTERED',
      $whitelist['activity']
    );
  }

  public function test_get_whitelisted_fields_with_default() {
    $this->setFields([
      'adjective'       => [
        'default'       => 'supercalifragilisticexpialidocious',
      ],
    ]);
    $whitelist = $this->form->get_whitelisted_fields(['adjective' => '']);

    $this->assertEquals(
      'supercalifragilisticexpialidocious',
      $whitelist['adjective']
    );
  }


  protected function setFields(array $fields) {
    $this->setProtectedProperty($this->form, 'fields', $fields);
  }

  protected function getDefaultFields() {
    return [
      'first_name'      => [
        'validators'        => [[$this->form, 'require']],
        'required_message'  => 'Kindly tell us your first name.',
      ],
      'last_name'       => [
        'validators'        => [[$this->form, 'require']],
        'required_message'  => 'Kindly tell us your last name.',
      ],
      'yes_or_no'       => [
        'options'       => ['yes', 'no'],
      ],
      'highest_award'   => [],
      'favorite_things' => [
        'options'       => ['raindrops', 'whiskers', 'kettles', 'mittens'],
        // TODO validate at_least
      ],
    ];
  }
}
