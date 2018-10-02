
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

