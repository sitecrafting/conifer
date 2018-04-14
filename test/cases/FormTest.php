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
  ];

  public function setUp() {
    $this->form = $this->getMockForAbstractClass(AbstractBase::class);
    $this->setProtectedProperty($this->form, 'fields', [
      'first_name'      => [
        'validators'    => [[$this->form, 'require']],
      ],
      'last_name'       => [
        'validators'    => [[$this->form, 'require']],
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
}
