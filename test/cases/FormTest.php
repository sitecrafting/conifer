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
    // Set the uploaded files for this form to our mocked $_FILES superglobal
    $this->setFiles($this->getDefaultFiles());
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

  public function test_get_file() {
    $this->assertNotEmpty($this->form->get_file('favoriteThings'));
  }

  public function test_required_file_missing() {
    $this->setFields([
      'leastFavoriteThings' => [
        'validators' => [[$this->form, 'require_file']],
      ],
    ]);

    $this->assertFalse($this->form->validate([]));
    $this->assertNotEmpty($this->form->get_error_messages_for('leastFavoriteThings'));
  }

  public function test_file_mime_type_valid() {
    $this->setFields([
      'favoriteThings' => [
        'validators' => [[$this->form, 'validate_file_mime_type', ['text/plain']]],
      ],
    ]);

    $this->assertTrue($this->form->validate([]));
    $this->assertEmpty($this->form->get_errors());
  }

  public function test_file_mime_type_invalid() {
    $this->setFields([
      'favoriteThings' => [
        'validators' => [[$this->form, 'validate_file_mime_type', ['application/pdf']]],
      ],
    ]);

    $this->assertFalse($this->form->validate([]));
    $this->assertNotEmpty($this->form->get_error_messages_for('favoriteThings'));
    $this->assertEquals(
      [sprintf($this->form::MESSAGE_INVALID_MIME_TYPE, 'favoriteThings')],
      $this->form->get_error_messages_for('favoriteThings')
    );
  }

  public function test_file_upload_error_ini_size() {
    $this->setFields([
      'uploadErrorSizeIni' => [
        'validators' => [[$this->form, 'require_file']],
      ],
    ]);

    $this->assertFalse($this->form->validate([]));
    $this->assertNotEmpty($this->form->get_error_messages_for('uploadErrorSizeIni'));
    $this->assertEquals(
      [sprintf($this->form::MESSAGE_FILE_SIZE, 'uploadErrorSizeIni')],
      $this->form->get_error_messages_for('uploadErrorSizeIni')
    );
  }

  public function test_file_upload_error_form_size() {
    $this->setFields([
      'uploadErrorSizeForm' => [
        'validators' => [[$this->form, 'require_file']],
      ],
    ]);

    $this->assertFalse($this->form->validate([]));
    $this->assertNotEmpty($this->form->get_error_messages_for('uploadErrorSizeForm'));
    $this->assertEquals(
      [sprintf($this->form::MESSAGE_FILE_SIZE, 'uploadErrorSizeForm')],
      $this->form->get_error_messages_for('uploadErrorSizeForm')
    );
  }

  public function test_file_upload_error_partial() {
    $this->setFields([
      'uploadErrorPartialFile' => [
        'validators' => [[$this->form, 'require_file']],
      ],
    ]);

    $this->assertFalse($this->form->validate([]));
    $this->assertNotEmpty($this->form->get_error_messages_for('uploadErrorPartialFile'));
    $this->assertEquals(
      [sprintf($this->form::MESSAGE_UPLOAD_ERROR, 'uploadErrorPartialFile')],
      $this->form->get_error_messages_for('uploadErrorPartialFile')
    );
  }

  public function test_file_upload_error_no_file() {
    $this->setFields([
      'austrianAbbeyMembership' => [
        'validators' => [[$this->form, 'require_file']],
      ],
    ]);

    $this->assertFalse($this->form->validate([]));
    $this->assertNotEmpty($this->form->get_error_messages_for('austrianAbbeyMembership'));
    $this->assertEquals(
      [sprintf($this->form::MESSAGE_FIELD_REQUIRED, 'austrianAbbeyMembership')],
      $this->form->get_error_messages_for('austrianAbbeyMembership')
    );
  }

  public function test_no_files_exception_get_files() {
    $this->setFiles(null);
    $this->expectException(\LogicException::class);

    $this->form->get_files();
  }

  public function test_no_files_exception_get_file() {
    $this->setFiles(null);
    $this->expectException(\LogicException::class);

    $this->form->get_file('favoriteThings');
  }

  public function test_no_files_exception_require_file() {
    $this->setFiles(null);
    $this->setFields([
      'austrianAbbeyMembership' => [
        'validators' => [[$this->form, 'require_file']],
      ],
    ]);

    $this->expectException(\LogicException::class);

    $this->form->validate([]);
  }

  protected function setFields(array $fields) {
    $this->setProtectedProperty($this->form, 'fields', $fields);
  }

  protected function setFiles(array $files = null) {
    $this->setProtectedProperty($this->form, 'files', $files);
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

  protected function getDefaultFiles() {
    return [
      'favoriteThings' => [
        'name' => 'My%20Favorite%20Things.txt',
        'type' => 'text/plain',
        'tmp_name' => '/tmp/php/somethingarbitrary',
        'error' => UPLOAD_ERR_OK,
        'size' => 16.99,
      ],
      'uploadErrorSizeIni' => [
        'name' => 'My%20Favorite%20Things.txt',
        'type' => 'text/plain',
        'tmp_name' => '/tmp/php/somethingarbitrary',
        'error' => UPLOAD_ERR_INI_SIZE,
        'size' => 17,
      ],
      'uploadErrorSizeForm' => [
        'name' => 'My%20Favorite%20Things.txt',
        'type' => 'text/plain',
        'tmp_name' => '/tmp/php/somethingarbitrary',
        'error' => UPLOAD_ERR_FORM_SIZE,
        'size' => 17,
      ],
      'uploadErrorPartialFile' => [
        'name' => 'My%20Favorite%20Things.txt',
        'type' => 'text/plain',
        'tmp_name' => '/tmp/php/somethingarbitrary',
        'error' => UPLOAD_ERR_PARTIAL,
        'size' => 16.99,
      ],
      'austrianAbbeyMembership' => [
        'name' => 'Nonnberg_Abbey_Nun_ID.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => '/tmp/php/somethingarbitrary',
        'error' => UPLOAD_ERR_NO_FILE,
        'size' => 16.99,
      ],
    ];
  }
}
