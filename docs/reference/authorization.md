## Table of contents

- [\Conifer\Authorization\AbstractPolicy (abstract)](#class-coniferauthorizationabstractpolicy-abstract)
- [\Conifer\Authorization\PolicyInterface (interface)](#interface-coniferauthorizationpolicyinterface)
- [\Conifer\Authorization\ShortcodePolicy (abstract)](#class-coniferauthorizationshortcodepolicy-abstract)
- [\Conifer\Authorization\TemplatePolicy (abstract)](#class-coniferauthorizationtemplatepolicy-abstract)
- [\Conifer\Authorization\UserRoleShortcodePolicy](#class-coniferauthorizationuserroleshortcodepolicy)

<hr /><a id="class-coniferauthorizationabstractpolicy-abstract"></a>
### Class: \Conifer\Authorization\AbstractPolicy (abstract)

> Abstract class providing a basis for defining custom, template-level authorization logic

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>register()</strong> : <em>void</em><br /><em>Create and adopt a new instance</em> |

*This class implements [\Conifer\Authorization\PolicyInterface](#interface-coniferauthorizationpolicyinterface)*

<hr /><a id="interface-coniferauthorizationpolicyinterface"></a>
### Interface: \Conifer\Authorization\PolicyInterface

> Interface for a high-level authorization API

| Visibility | Function |
|:-----------|:---------|
| public | <strong>adopt()</strong> : <em>void</em><br /><em>Put this policy in place, typically via an action or filter</em> |
| public static | <strong>register()</strong> : <em>void</em><br /><em>Create and adopt a new instance of this interface</em> |

<hr /><a id="class-coniferauthorizationshortcodepolicy-abstract"></a>
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

*This class extends [\Conifer\Authorization\AbstractPolicy](#class-coniferauthorizationabstractpolicy-abstract)*

*This class implements [\Conifer\Authorization\PolicyInterface](#interface-coniferauthorizationpolicyinterface)*

<hr /><a id="class-coniferauthorizationtemplatepolicy-abstract"></a>
### Class: \Conifer\Authorization\TemplatePolicy (abstract)

> Abstract class providing a basis for defining custom, template-level authorization logic

| Visibility | Function |
|:-----------|:---------|
| public | <strong>adopt()</strong> : <em>PolicyInterface fluent interface</em><br /><em>Adopt this policy</em> |
| public | <strong>abstract enforce(</strong><em>\string</em> <strong>$template</strong>, <em>\Timber\User</em> <strong>$user</strong>)</strong> : <em>void</em><br /><em>Enforce this template-level policy</em> |

*This class extends [\Conifer\Authorization\AbstractPolicy](#class-coniferauthorizationabstractpolicy-abstract)*

*This class implements [\Conifer\Authorization\PolicyInterface](#interface-coniferauthorizationpolicyinterface)*

<hr /><a id="class-coniferauthorizationuserroleshortcodepolicy"></a>
### Class: \Conifer\Authorization\UserRoleShortcodePolicy

> A ShortcodePolicy that filters content based on the current user's role

| Visibility | Function |
|:-----------|:---------|
| public | <strong>decide(</strong><em>array</em> <strong>$atts</strong>, <em>\string</em> <strong>$content</strong>, <em>\Timber\User</em> <strong>$user</strong>)</strong> : <em>bool whether `$user` meets the criteria described in `$atts`</em><br /><em>Determine whether the user has access to content based on shortcode attributes, user data, and possibly the content itself.</em> |

*This class extends [\Conifer\Authorization\ShortcodePolicy](#class-coniferauthorizationshortcodepolicy-abstract)*

*This class implements [\Conifer\Authorization\PolicyInterface](#interface-coniferauthorizationpolicyinterface)*

