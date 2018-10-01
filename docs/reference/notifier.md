## Table of contents

- [\Conifer\Notifier\AdminNotifier](#class-conifernotifieradminnotifier)
- [\Conifer\Notifier\EmailNotifier (abstract)](#class-conifernotifieremailnotifier-abstract)
- [\Conifer\Notifier\SimpleNotifier](#class-conifernotifiersimplenotifier)

<hr /><a id="class-conifernotifieradminnotifier"></a>
### Class: \Conifer\Notifier\AdminNotifier

> Class for emailing the default WordPress admin contact

| Visibility | Function |
|:-----------|:---------|
| public | <strong>to()</strong> : <em>void</em><br /><em>Get the admin email address configured in General Settings</em> |

*This class extends [\Conifer\Notifier\EmailNotifier](#class-conifernotifieremailnotifier-abstract)*

<hr /><a id="class-conifernotifieremailnotifier-abstract"></a>
### Class: \Conifer\Notifier\EmailNotifier (abstract)

> Class for emailing WordPress admins

| Visibility | Function |
|:-----------|:---------|
| public | <strong>notify(</strong><em>mixed</em> <strong>$args</strong>)</strong> : <em>void</em><br /><em>Alias of notify_html</em> |
| public | <strong>notify_html(</strong><em>\string</em> <strong>$subject</strong>, <em>\string</em> <strong>$message</strong>, <em>array</em> <strong>$headers=array()</strong>)</strong> : <em>bool whether the messages were sent successfully</em><br /><em>Send an HTML notification email</em> |
| public | <strong>notify_plaintext(</strong><em>\string</em> <strong>$subject</strong>, <em>\string</em> <strong>$message</strong>, <em>array</em> <strong>$headers=array()</strong>)</strong> : <em>bool whether the messages were sent successfully</em><br /><em>Send a plaintext notification email</em> |
| public | <strong>send_html_message(</strong><em>array/string</em> <strong>$to</strong>, <em>\string</em> <strong>$subject</strong>, <em>\string</em> <strong>$message</strong>, <em>array</em> <strong>$headers=array()</strong>)</strong> : <em>bool whether the messages were sent successfully</em><br /><em>Send a UTF-8-encoded HTML email to send to</em> |
| public | <strong>send_plaintext_message(</strong><em>array/string</em> <strong>$to</strong>, <em>\string</em> <strong>$subject</strong>, <em>\string</em> <strong>$message</strong>, <em>array</em> <strong>$headers=array()</strong>)</strong> : <em>bool whether the messages were sent successfully</em><br /><em>Send a UTF-8-encoded plaintext email to send to</em> |
| public | <strong>abstract to()</strong> : <em>mixed the email(s) to send to, as a comma-separated string or array</em><br /><em>Get the destination email address(es)</em> |
| protected | <strong>get_valid_to_address()</strong> : <em>mixed</em><br /><em>Call the user-defined to() method, and throw an exception if returned value is invalid</em> |

<hr /><a id="class-conifernotifiersimplenotifier"></a>
### Class: \Conifer\Notifier\SimpleNotifier

> Class for emailing arbitrary email addresses

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>mixed</em> <strong>$to</strong>)</strong> : <em>void</em><br /><em>Constructor. Pass the to email here. Can be a comma-separated string or an array</em> |
| public | <strong>to()</strong> : <em>void</em><br /><em>Get the admin email address configured in General Settings</em> |

*This class extends [\Conifer\Notifier\EmailNotifier](#class-conifernotifieremailnotifier-abstract)*

