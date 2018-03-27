<?php
/**
 * Custom Twig filters for manipulating text
 */

namespace Conifer\Twig\Filters;

/**
 * Twig Wrapper for helpful linguistic filters, such as pluralize
 *
 * @package Conifer
 */
class TextHelper extends AbstractBase {
  /**
   * Plural inflections for English words
   * TODO public static function add_plurals(array $plurals)
   *
   * @var array
   */
  static protected $plurals = [
    'person' => 'people',
  ];

  /**
   * Get the Twig functions to register
   *
   * @return  array an associative array of callback functions, keyed by name
   */
  public function get_filters() {
    return [
      'oxford_comma' => [$this, 'oxford_comma'],
      'pluralize' => [$this, 'pluralize'],
    ];
  }

  /**
   * Pluralize the given noun, if $n is anything other than 1
   *
   * @param  string $noun the noun to pluralize (or not)
   * @param  int $n the number of the given nouns, so that we know whether to pluralize
   * @return string the noun, pluralized or not according to $n
   */
  public function pluralize( $noun, $n ) {
    if ($n !== 1) {
      $noun = isset(static::$plurals[$noun])
        ? static::$plurals[$noun]
        : $noun . 's';
    }

    return $noun;
  }

  /**
   * Returns a human-readable list of things. Uses the Oxford comma convention
   * for listing three or more things.
   *
   * @param  array  $items an array of strings
   * @return string
   */
  public function oxford_comma( array $items ) {
    switch (count($items)) {
      case 0:
        $list = '';
        break;

      case 1:
        $list = $items[0];
        break;

      case 2:
        $list = $items[0] . ' and ' . $items[1];
        break;

      default:
        $last = array_splice($items, -1)[0];
        $list = implode(', ', $items) . ', and ' . $last;
    }

    return $list;
  }
}
