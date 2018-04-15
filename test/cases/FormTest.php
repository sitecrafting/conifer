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

  const VALID_SUBMISSION = [
    'first_name'      => 'Julie',
    'last_name'       => 'Andrews',
    'yes_or_no'       => null,
    'highest_award'   => 'Academy Award',
    'favorite_things' => ['kettles', 'mittens'],
    'nationality'     => 'British',
  ];

  const INVALID_SUBMISSION = [
    'first_name'      => '',
    'last_name'       => '',
    'favorite_things' => ['dog bites'],
    'nationality'     => 'MURCAN',
  ];

  public function setUp() {
    $this->form = $this->getMockForAbstractClass(AbstractBase::class);

    // Let's pretend our form subscribes to a weird type of xenophobia
    $worldlyXenophobe = function($field, $value) {
      // ONLY ALLOW THESE THREE NATIONALITIES
      $valid = in_array(strtolower($value), ['british', 'canadian', 'australian']);

      if (!$valid) {
        $this->add_error('nationality', 'wrong nationality, go away');
      }

      return $valid;
    };

    $this->setProtectedProperty($this->form, 'fields', [
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
        'default'       => 'yes', // TODO default values
      ],
      'highest_award'   => [],
      'favorite_things' => [
        'options'       => ['raindrops', 'whiskers', 'kettles', 'mittens'],
        // TODO validate at_least
      ],
      'nationality'     => [
        'validators'    => [$worldlyXenophobe],
      ],
      'adjective'       => [
        // TODO implement defaults
        'default'       => 'supercalifragilisticexpialidocious',
      ],
      'activity'        => [
        // TODO implement filters
        'filter'        => function() {},
      ],
    ]);
  }

  public function test_hydrate() {
    $this->form->hydrate(self::VALID_SUBMISSION);

    $this->assertEquals('Julie', $this->form->get('first_name'));
    $this->assertEquals('Andrews', $this->form->get('last_name'));
    $this->assertEquals(
      ['kettles', 'mittens'],
      $this->form->get('favorite_things')
    );
    // TODO default values
    $this->assertNull($this->form->get('yes_or_no'));
  }

  public function test_checked() {
    $this->form->hydrate(self::VALID_SUBMISSION);

    $this->assertFalse($this->form->checked('favorite_things', 'raindrops'));
    $this->assertFalse($this->form->checked('favorite_things', 'whiskers'));
    $this->assertTrue($this->form->checked('favorite_things', 'kettles'));
    $this->assertTrue($this->form->checked('favorite_things', 'mittens'));

    // TODO default values
    $this->assertFalse($this->form->checked('yes_or_no'));

    $this->assertTrue($this->form->checked('highest_award', 'Academy Award'));
    $this->assertFalse($this->form->checked('highest_award', 'a blue ribbon'));

    $this->assertFalse($this->form->checked('nonsense'));
  }

  public function test_get_errors_for() {
    $this->form->add_error('favorite_things', 'WELL WHAT ARE THEY');
    $this->form->add_error('nationality', 'INVALID NATIONALITY');

    $this->assertEquals(
      [['field' => 'nationality', 'message' => 'INVALID NATIONALITY']],
      array_values($this->form->get_errors_for('nationality'))
    );
  }

  public function test_get_error_messages_for() {
    $this->form->add_error('favorite_things', 'WELL WHAT ARE THEY');
    $this->form->add_error('nationality', 'INVALID NATIONALITY');

    $this->assertEquals(
      ['INVALID NATIONALITY'],
      array_values($this->form->get_error_messages_for('nationality'))
    );
  }

  public function test_validate_valid_submission() {
    $this->assertTrue($this->form->validate(self::VALID_SUBMISSION));
    $this->assertEmpty($this->form->get_errors());
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

  public function test_get_with_default() {
    $this->markTestSkipped();
  }

  public function test_get_with_filter() {
    $this->markTestSkipped();
  }
}
