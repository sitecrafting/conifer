
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

*This class implements \Conifer\Twig\HelperInterface*

