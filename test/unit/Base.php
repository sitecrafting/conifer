<?php
/**
 * Base class for Conifer test cases
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Unit;

use PHPUnit\Framework\TestCase;
use Timber\User;
use WP_Mock;
use WP_Term;

/**
 * Base test class for the plugin. Declared abstract so that PHPUnit doesn't
 * complain about a lack of tests defined here.
 */
abstract class Base extends TestCase {
  public function setUp() {
    WP_Mock::setUp();
  }

  public function tearDown() {
    WP_Mock::tearDown();
  }


  /**
   * Mock a call to WordPress's get_post
   *
   * @param array $props an array of WP_Post object properties
   * must include a valid (that is, a numeric) post ID
   * @throws \InvalidArgumentException if $props["ID"] is not numeric
   */
  protected function mockPost(array $props, array $options = []) {
    if (empty($props['ID']) || !is_numeric($props['ID'])) {
      throw new \InvalidArgumentException('$props["ID"] must be numeric');
    }

    // allow specifying which class this post will be
    $post = $this->getMockBuilder($options['class'] ?? \Timber\Post::class)
      ->disableOriginalConstructor()
      ->getMock();

    foreach ($props as $prop => $value) {
      $post->{$prop} = $value;
    }

    WP_Mock::userFunction('get_post', array_merge([
      'times'   => 1,
      'args'  => [$props['ID']],
      'return'  => $post,
    ]));

    return $post;
  }

  /**
   * Mock a call to WordPress's get_term
   *
   * @param array $props an array of WP_Term object properties
   * must include a valid (that is, a numeric) term_id, and a taxonomy string,
   * e.g.:
   *
   * ```
   * $props = ['term_id' => 123, 'taxonomy' => 'yeah-im-the-taxmaaaan'];
   * ```
   * @param additional WP_Mock::userFunction objects to merge in.
   * @throws \InvalidArgumentException if $props["ID"] is not numeric
   */
  protected function mockTerm(array $props, array $options = []) {
    if (empty($props['term_id']) || !is_numeric($props['term_id'])) {
      throw new \InvalidArgumentException('$props["term_id"] must be numeric');
    }
    if (empty($props['taxonomy']) || !is_string($props['taxonomy'])) {
      throw new \InvalidArgumentException(
        '$props["taxonomy"] must be a string'
      );
    }

    $term = $this->getMockBuilder(WP_Term::class)
      ->disableOriginalConstructor()
      ->getMock();

    foreach ($props as $prop => $value) {
      $term->{$prop} = $value;
    }

    WP_Mock::userFunction('get_term', array_merge([
      'times'   => 1,
      'args'    => [$props['term_id'], $props['taxonomy']],
      'return'  => $term,
    ], $options));

    return $term;
  }

  protected function getProtectedProperty($object, $name) {
    $reflection = new \ReflectionClass($object);
    $property   = $reflection->getProperty($name);
    $property->setAccessible(true);

    return $property->getValue($object);
  }

  protected function setProtectedProperty($object, $name, $value) {
    $reflection = new \ReflectionClass($object);
    $property   = $reflection->getProperty($name);
    $property->setAccessible(true);

    return $property->setValue($object, $value);
  }

  protected function callProtectedMethod($object, $name, $args = []) {
    $reflection = new \ReflectionClass($object);
    $method     = $reflection->getMethod($name);
    $method->setAccessible(true);

    return $method->invokeArgs($object, $args);
  }

  protected function mockCurrentUser($id, $data = [], $meta = []) {
    $this->mockCurrentUserId($id);
    $this->mockCurrentUserData($data);

    if ($meta) {
      foreach ($meta as $key => $value) {
        WP_Mock::userFunction('get_user_meta', [
          'args' => ['*', $key, WP_Mock\Functions::type('bool')],
          'return' => $value,
        ]);
      }
    }

    WP_Mock::userFunction('get_avatar_url', [
      'return' => 'https://example.com/avatar.gif',
    ]);

    return new User($id);
  }

  protected function mockCurrentUserId($id) {
    WP_Mock::userFunction('get_current_user_id', [
      'return' => $id,
    ]);
  }

  protected function mockCurrentUserData($data = []) {
    WP_Mock::userFunction('get_userdata', [
      'return' => $data,
    ]);
  }
}
