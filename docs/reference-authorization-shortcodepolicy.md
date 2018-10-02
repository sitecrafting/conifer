
### Class: \Conifer\Authorization\ShortcodePolicy (abstract)

> Abstract class providing a basis for defining shortcodes that filter their content according to custom authorization logic

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>\string</em> <strong>$tag=`'protected'`</strong>)</strong> : <em>void</em><br /><em>Sets the shortcode tag for the new shortcode policy</em> |
| public | <strong>adopt()</strong> : <em>PolicyInterface fluent interface</em><br /><em>Filter the shortcode content based on the implementation of the `decide` method.</em> |
| public | <strong>abstract decide(</strong><em>array</em> <strong>$atts</strong>, <em>\string</em> <strong>$content</strong>, <em>\Timber\User</em> <strong>$user</strong>)</strong> : <em>bool whether `$user` meets the criteria described in `$atts`</em><br /><em>Determine whether the user has access to content based on shortcode attributes, user data, and possibly the content itself.</em> |
| public | <strong>enforce(</strong><em>array</em> <strong>$atts</strong>, <em>\string</em> <strong>$content</strong>, <em>\Timber\User</em> <strong>$user</strong>)</strong> : <em>void</em><br /><em>Filter the shortcode content based on the current user's data</em> |
| protected | <strong>filter_authorized(</strong><em>\string</em> <strong>$content</strong>)</strong> : <em>string the content to display</em><br /><em>Get the filtered shortcode content to display to _authorized_ users. Override this method to display something other thatn the original content.</em> |
| protected | <strong>filter_unauthorized(</strong><em>\string</em> <strong>$content</strong>)</strong> : <em>string the content to display</em><br /><em>Get the filtered shortcode content to display to unauthorized users. Override this method to display something other than the empty string.</em> |
| protected | <strong>get_user()</strong> : <em>\Timber\User</em><br /><em>Get the user to check against shortcode attributes. Override this method to perform authorization against someone other than the current user.</em> |
| protected | <strong>tag()</strong> : <em>string the shortcode tag to declare</em><br /><em>Get the shortcode tag to be declared</em> |

*This class extends \Conifer\Authorization\AbstractPolicy*

*This class implements \Conifer\Authorization\PolicyInterface*

