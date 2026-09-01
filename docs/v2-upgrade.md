# Upgrading to V2

Version 2 of Conifer introduces breaking changes. Use this guide to identify the affected APIs and update your code.

- [Upgrading to V2](#upgrading-to-v2)
  - [SimpleNotifier](#simplenotifier)

## SimpleNotifier

`SimpleNotifier` now validates the `$to` argument when it is constructed. It accepts a valid email address, a comma-separated list of valid email addresses, or an array of valid email addresses.

Previously, invalid addresses could cause the notifier to fail silently. In version 2, `SimpleNotifier` throws an `InvalidArgumentException` when `$to` is empty or contains an invalid email address.

When creating a `SimpleNotifier` from untrusted input, such as a form submission, catch this exception and handle the invalid address:

```PHP
try {
  $notifier = new Conifer\Notifier\SimpleNotifier($_POST['reply_to']);
} catch (\InvalidArgumentException $e) {
  // The submitted address was invalid. Bail out or use a known-good notifier.
}
```

For more details about supported destination formats, see [Simple Notifier](/notifiers#simple-notifier).