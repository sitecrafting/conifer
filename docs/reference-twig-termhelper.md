
### Class: \Conifer\Twig\TermHelper

> Twig Wrapper around filters for WP/Timber terms and taxonomies

| Visibility | Function |
|:-----------|:---------|
| public | <strong>get_filters()</strong> : <em>array an associative array of callback functions, keyed by name</em><br /><em>Get the Twig functions to register</em> |
| public | <strong>get_functions()</strong> : <em>array</em><br /><em>Does not supply any additional Twig functions.</em> |
| public | <strong>term_item_class(</strong><em>\Conifer\Twig\TimberTerm/\Timber\Term</em> <strong>$term</strong>, <em>\Conifer\Twig\TimberCoreInterface/\Timber\CoreInterface</em> <strong>$currentPostOrArchive</strong>)</strong> : <em>string the formatted phone number</em><br /><em>Filters the given term into a class for an <li>; considers $term "current" if $currentPostOrArchive is a TimberTerm instance (meaning we're on an archive page for that term), and it represents the same term as $term. the current archive page (e.g. a category listing)</em> |

*This class implements \Conifer\Twig\HelperInterface*

