<?php
/**
 * Interface for declarative, OO Twig functions and filters
 *
 */

namespace Conifer\Twig;

/**
 * Easily define custom functions to add to Twig by implementing this interface
 *
 * @example
 * Define a class that implements this interface (typically in a lib file
 * in your theme):
 *
 * ```php
 * // my-project-theme/lib/MyProject/Twig/AnimalTwigHelper.php
 * namespace MyProject\Twig;
 *
 * use Conifer\Twig\HelperInterface;
 *
 * class AnimalTwigHelper implements HelperInterface {
 *   public function get_functions() : array {
 *     return [
 *       'list_animals' => function() { return ['cat', 'dog', 'fox']; },
 *     ];
 *   }
 *
 *   public function get_filters() : array {
 *     return [
 *       'speak' => [$this, 'speak'],
 *     ];
 *   }
 *
 *   public function speak(string $animal) {
 *     return [
 *       'dog' => 'woof',
 *       'cat' => 'meow',
 *     ][$animal] ?? 'o hai';
 *   }
 * }
 * ```
 *
 * Add the functions/filters to your Site in your callback config:
 *
 * ```php
 * // functions.php
 * use MyProject\Twig\AnimalTwigHelper;
 *
 * $site->configure(function() {
 *   // ...
 *
 *   $this->add_twig_helper(new AnimalTwigHelper());
 * });
 * ```
 *
 * Finally, call it from your Twig:
 *
 * ```twig
 * {# call our custom Twig function to get the list of animals #}
 * {% for animal in list_animals() %}
 *   {# what does the fox say?? #}
 *   <p>The {{ animal }} says: "{{ animal | speak }}!"</p>
 * {% endfor %}
 * ```
 *
 * This will output:
 *
 * ```html
 * <p>The dog says: "woof!"</p>
 * <p>The cat says: "meow!"</p>
 * <p>The fox says: "o hai!"</p>
 * ```
 * @copyright 2019 SiteCrafting, Inc.
 * @author Coby Tamayo
 */
interface HelperInterface {
  /**
   * Get the Twig functions implemented by this helper, keyed by the function
   * name to call from Twig views
   *
   * @return array
   */
  public function get_functions() : array;

  /**
   * Get the Twig filters implemented by this helper, keyed by the filter
   * name to call from Twig views
   *
   * @return array
   */
  public function get_filters() : array;
}
