## Table of contents

- [\Conifer\Twig\FormHelper](#class-conifertwigformhelper)
- [\Conifer\Twig\HelperInterface (interface)](#interface-conifertwighelperinterface)
- [\Conifer\Twig\ImageHelper](#class-conifertwigimagehelper)
- [\Conifer\Twig\NumberHelper](#class-conifertwignumberhelper)
- [\Conifer\Twig\TermHelper](#class-conifertwigtermhelper)
- [\Conifer\Twig\TextHelper](#class-conifertwigtexthelper)
- [\Conifer\Twig\WordPressHelper](#class-conifertwigwordpresshelper)

<hr /><a id="class-conifertwigformhelper"></a>
### Class: \Conifer\Twig\FormHelper

> Twig Wrapper for helpful linguistic filters, such as pluralize

| Visibility | Function |
|:-----------|:---------|
| public | <strong>checked_attr(</strong><em>\Conifer\Twig\Form/\Conifer\Form\AbstractBase</em> <strong>$form</strong>, <em>\string</em> <strong>$fieldName</strong>, <em>\string</em> <strong>$value=null</strong>)</strong> : <em>string literally `" checked "` if the field (optionally the one matching `$value`) was checked, or the empty string</em><br /><em>Return the `checked` attribute for a given form input, given the (hydrated) form and the field name, and optionally the value to check against. necessary e.g. for radio inputs, where there's more than one possible value.</em> |
| public | <strong>get_error_messages_for(</strong><em>\Conifer\Twig\Form/\Conifer\Form\AbstractBase</em> <strong>$form</strong>, <em>\string</em> <strong>$fieldName</strong>, <em>\string</em> <strong>$separator=`'<br>'`</strong>)</strong> : <em>string</em><br /><em>Get the error messages for a specific field only, as a concatenated string with line breaks between by default</em> |
| public | <strong>get_field_class(</strong><em>\Conifer\Form\AbstractBase</em> <strong>$form</strong>, <em>\string</em> <strong>$fieldName</strong>, <em>\string</em> <strong>$errorClass=`'error'`</strong>)</strong> : <em>string the HTML class(es) to render</em><br /><em>Get the class to render for the form field, based on its error state</em> |
| public | <strong>get_filters()</strong> : <em>array an associative array of callback functions, keyed by name</em><br /><em>Get the Twig functions to register</em> |
| public | <strong>get_functions()</strong> : <em>array</em><br /><em>Does not supply any additional Twig functions.</em> |
| public | <strong>selected_attr(</strong><em>\Conifer\Twig\Form/\Conifer\Form\AbstractBase</em> <strong>$form</strong>, <em>\string</em> <strong>$fieldName</strong>, <em>\string</em> <strong>$value</strong>)</strong> : <em>string literally `" selected "` if the field (optionally the one matching `$value`) was selected, or the empty string</em><br /><em>Return the `selected` attribute for a given form input, given the (hydrated) form and the field name, and optionally the value to check against. necessary e.g. for radio inputs, where there's more than one possible value.</em> |

*This class implements [\Conifer\Twig\HelperInterface](#interface-conifertwighelperinterface)*

<hr /><a id="interface-conifertwighelperinterface"></a>
### Interface: \Conifer\Twig\HelperInterface

> Easily define custom functions to add to Twig by extending this class.

| Visibility | Function |
|:-----------|:---------|
| public | <strong>get_filters()</strong> : <em>array</em><br /><em>Get the Twig filters implemented by this helper, keyed by the filter name to call from Twig views</em> |
| public | <strong>get_functions()</strong> : <em>array</em><br /><em>Get the Twig functions implemented by this helper, keyed by the function name to call from Twig views</em> |

<hr /><a id="class-conifertwigimagehelper"></a>
### Class: \Conifer\Twig\ImageHelper

> Twig Wrapper around high-level image functions

| Visibility | Function |
|:-----------|:---------|
| public | <strong>get_filters()</strong> : <em>array an associative array of callback functions, keyed by name</em><br /><em>Get the Twig functions to register</em> |
| public | <strong>get_functions()</strong> : <em>array an associative array of callback functions, keyed by name</em><br /><em>Does not supply any additional Twig functions.</em> |
| public | <strong>src_to_retina(</strong><em>\string</em> <strong>$src</strong>)</strong> : <em>string the retina version of `$src`</em><br /><em>Convert the image URL `$src` to its retina equivalent</em> |

*This class implements [\Conifer\Twig\HelperInterface](#interface-conifertwighelperinterface)*

<hr /><a id="class-conifertwignumberhelper"></a>
### Class: \Conifer\Twig\NumberHelper

> Twig Wrapper around generic filters for numbers

| Visibility | Function |
|:-----------|:---------|
| public | <strong>get_filters()</strong> : <em>array an associative array of callback functions, keyed by name</em><br /><em>Get the Twig functions to register</em> |
| public | <strong>get_functions()</strong> : <em>array</em><br /><em>Does not supply any additional Twig functions.</em> |
| public | <strong>us_phone(</strong><em>string</em> <strong>$phone</strong>)</strong> : <em>string the formatted phone number</em><br /><em>Filter any 10-digit number into a formatted US phone number digits (with an optional leading "1") or it won't filter anything.</em> |

*This class implements [\Conifer\Twig\HelperInterface](#interface-conifertwighelperinterface)*

<hr /><a id="class-conifertwigtermhelper"></a>
### Class: \Conifer\Twig\TermHelper

> Twig Wrapper around filters for WP/Timber terms and taxonomies

| Visibility | Function |
|:-----------|:---------|
| public | <strong>get_filters()</strong> : <em>array an associative array of callback functions, keyed by name</em><br /><em>Get the Twig functions to register</em> |
| public | <strong>get_functions()</strong> : <em>array</em><br /><em>Does not supply any additional Twig functions.</em> |
| public | <strong>term_item_class(</strong><em>\Conifer\Twig\TimberTerm/\Timber\Term</em> <strong>$term</strong>, <em>\Conifer\Twig\TimberCoreInterface/\Timber\CoreInterface</em> <strong>$currentPostOrArchive</strong>)</strong> : <em>string the formatted phone number</em><br /><em>Filters the given term into a class for an <li>; considers $term "current" if $currentPostOrArchive is a TimberTerm instance (meaning we're on an archive page for that term), and it represents the same term as $term. the current archive page (e.g. a category listing)</em> |

*This class implements [\Conifer\Twig\HelperInterface](#interface-conifertwighelperinterface)*

<hr /><a id="class-conifertwigtexthelper"></a>
### Class: \Conifer\Twig\TextHelper

> Twig Wrapper for helpful linguistic filters, such as pluralize

| Visibility | Function |
|:-----------|:---------|
| public | <strong>get_filters()</strong> : <em>array an associative array of callback functions, keyed by name</em><br /><em>Get the Twig functions to register</em> |
| public | <strong>get_functions()</strong> : <em>array</em><br /><em>Does not supply any additional Twig functions.</em> |
| public | <strong>oxford_comma(</strong><em>array</em> <strong>$items</strong>)</strong> : <em>string</em><br /><em>Returns a human-readable list of things. Uses the Oxford comma convention for listing three or more things.</em> |
| public | <strong>pluralize(</strong><em>string</em> <strong>$noun</strong>, <em>int</em> <strong>$n</strong>)</strong> : <em>string the noun, pluralized or not according to $n</em><br /><em>Pluralize the given noun, if $n is anything other than 1</em> |

*This class implements [\Conifer\Twig\HelperInterface](#interface-conifertwighelperinterface)*

<hr /><a id="class-conifertwigwordpresshelper"></a>
### Class: \Conifer\Twig\WordPressHelper

> Twig Wrapper around generic or global functions, such as WordPress template tags.

| Visibility | Function |
|:-----------|:---------|
| public | <strong>get_filters()</strong> : <em>array</em><br /><em>Does not supply any additional Twig filters.</em> |
| public | <strong>get_functions()</strong> : <em>array an associative array of callback functions, keyed by name</em><br /><em>Get the Twig functions to register</em> |

*This class implements [\Conifer\Twig\HelperInterface](#interface-conifertwighelperinterface)*

