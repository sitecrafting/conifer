<?php
/**
 * Custom Twig filters for manipulating text
 */

namespace Conifer\Twig;

/**
 * Twig Wrapper for helpful linguistic filters, such as pluralize
 *
 * @package Conifer
 */
class TextHelper implements HelperInterface {
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
  public function get_filters() : array {
    return [
      'oxford_comma'    => [$this, 'oxford_comma'],
      'pluralize'       => [$this, 'pluralize'],
      'capitalize_each' => [$this, 'capitalize_each'],
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

  /**
   * Capitalize each word in the given $phrase, other than "small" words such
   * as "a," "the," etc.
   *
   * @param string $phrase a string to capitalize each word of
   * @param array $options an optional array of options including:
   * - `small_words`: words not to capitalize. Defaults to the normal rules
   *   of the English language.
   * - `split_by`: the delimiter for splitting out words when calling
   *   `explode()`. Defaults to a single space, i.e. `" "`.
   * @return the capitalized string, e.g. "The Old Man and the Sea"
   */
  public function capitalize_each(string $phrase, array $options = []) : string {
    // @codingStandardsIgnoreStart WordPress.Arrays.ArrayDeclarationSpacing
    $smallWords = $options['small_words'] ?? [
      'a', 'and', 'the', 'or', 'of', 'as', 'for', 'but', 'yet', 'so', 'at',
      'around', 'by', 'after', 'along', 'from', 'on', 'to', 'with', 'without',
    ];

    $words = explode(($options['split_by'] ?? ' '), $phrase);

    // capitalize each word
    $capitalizedWords = array_map(
      function(string $word, int $i) use ($smallWords) : string {
        // always capitalize the first word; capitalize other "big" words
        $capitalize = $i === 0 || !in_array(strtolower($word), $smallWords, true);
        return $capitalize ? ucfirst($word) : lcfirst($word);
      },
      $words,
      array_keys($words)
    );

    return implode(' ', $capitalizedWords);
  }
}
