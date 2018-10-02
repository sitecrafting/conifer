
### Class: \Conifer\Twig\TextHelper

> Twig Wrapper for helpful linguistic filters, such as pluralize

| Visibility | Function |
|:-----------|:---------|
| public | <strong>get_filters()</strong> : <em>array an associative array of callback functions, keyed by name</em><br /><em>Get the Twig functions to register</em> |
| public | <strong>get_functions()</strong> : <em>array</em><br /><em>Does not supply any additional Twig functions.</em> |
| public | <strong>oxford_comma(</strong><em>array</em> <strong>$items</strong>)</strong> : <em>string</em><br /><em>Returns a human-readable list of things. Uses the Oxford comma convention for listing three or more things.</em> |
| public | <strong>pluralize(</strong><em>string</em> <strong>$noun</strong>, <em>int</em> <strong>$n</strong>)</strong> : <em>string the noun, pluralized or not according to $n</em><br /><em>Pluralize the given noun, if $n is anything other than 1</em> |

*This class implements \Conifer\Twig\HelperInterface*

