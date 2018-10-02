
### Class: \Conifer\Admin\Notice

> Provides a high-level API for dislaying all sorts of WP Admin notices

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>\string</em> <strong>$message</strong>, <em>\string</em> <strong>$extraClasses=null</strong>)</strong> : <em>void</em><br /><em>Constructor Multiple classes can be specified with a space-separated string, e.g. `"one two three"`</em> |
| public | <strong>add_class(</strong><em>\string</em> <strong>$class</strong>)</strong> : <em>[\Conifer\Admin\Notice](#class-coniferadminnotice)</em><br /><em>Add an HTML class to be rendered on this notice</em> |
| public static | <strong>clear_flash_notices()</strong> : <em>void</em><br /><em>Clear all flash notices in session</em> |
| public static | <strong>disable_flash_notices()</strong> : <em>void</em><br /><em>Disable flash notices</em> |
| public | <strong>display()</strong> : <em>void</em><br /><em>Display the admin notice</em> |
| public static | <strong>display_flash_notices()</strong> : <em>void</em><br /><em>Display any flash notices stored in session during the admin_notices hook</em> |
| public static | <strong>enable_flash_notices()</strong> : <em>void</em><br /><em>Enable flash notices to be stored in the `$_SESSION` superglobal</em> |
| public | <strong>error()</strong> : <em>void</em><br /><em>Display this notice as an error</em> |
| public | <strong>flash()</strong> : <em>void</em><br /><em>Display this notice on the next page load</em> |
| public | <strong>flash_error()</strong> : <em>void</em><br /><em>Display this notice as an error message on the next page load</em> |
| public | <strong>flash_info()</strong> : <em>void</em><br /><em>Display this notice as an info message on the next page load</em> |
| public static | <strong>flash_notices_enabled()</strong> : <em>bool</em><br /><em>Whether flash notices are enabled</em> |
| public | <strong>flash_success()</strong> : <em>void</em><br /><em>Display this notice as a success message on the next page load</em> |
| public | <strong>flash_warning()</strong> : <em>void</em><br /><em>Display this notice as a warning on the next page load</em> |
| public | <strong>get_class()</strong> : <em>string e.g. `"notice notice-error"`</em><br /><em>Get the HTML class or classes to be rendered in the notice markup</em> |
| public static | <strong>get_flash_notices()</strong> : <em>Notice[] an array of Notice instances</em><br /><em>Get the flash notices to be displayed based on session data</em> |
| public | <strong>has_class(</strong><em>\string</em> <strong>$class</strong>)</strong> : <em>bool</em><br /><em>Whether this Notice has the given $class</em> |
| public | <strong>has_style_class()</strong> : <em>bool</em><br /><em>Whether this notice has a special style class that WordPress targets in its built-in admin styles.</em> |
| public | <strong>html()</strong> : <em>string the HTML to be rendered</em><br /><em>Get the message `<div>` markup</em> |
| public | <strong>info()</strong> : <em>void</em><br /><em>Display this notice as an info message</em> |
| public | <strong>success()</strong> : <em>void</em><br /><em>Display this notice as a success message</em> |
| public | <strong>warning()</strong> : <em>void</em><br /><em>Display this notice as a warning</em> |
| protected static | <strong>valid_session_notice(</strong><em>mixed</em> <strong>$notice</strong>)</strong> : <em>bool</em><br /><em>Validate a session notice array</em> |

