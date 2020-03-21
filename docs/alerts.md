# Alerts

Sometimes you may want to broadcast information to your users on short notice. You don't want to inundante your users or train them to ignore important information, so you need to keep such broadcasts succinct, rare, and easy to dismiss. The succinct and rare part is up to you, but Conifer can help with dismissable alerts.

Persisting alert dismissals is a cross-cutting concern that is tricky to get right. You need to:

* Make sure that when an alert is dismissed, it stays dismissed for good (at least for that user).
* Make sure you're dismissing the right alert, and not eagerly dismissing all future alerts. In other words, you need a way of uniquely mapping persisted alert dismissals to past alerts.

Conifer includes a `DismissableAlert` class that uses cookies to "remember" who has dismissed what, on a _per-user, per-alert_ basis.

Here's how you use it:

```php
/* my-template.php */
use Conifer\Alert\DismissableAlert;
use Timber\Timber;

// Load the alert into Timber's context
$alert   = new DismissableAlert('IMPORTANT ALERT!!!');
$context = $site->context(['alert' => $alert]);

// Render your view as you normally would
Timber::render('my-view.twig', $context);
```

```twig
{# my-view.twig, or perhaps even your main layout file... #}
{% if not alert.dismissed %}

<script>
  window.addEventListener('DOMContentLoaded', function() {
    var closeBtn = document.querySelector('.close-alert');
    closeBtn.addEventListener('click', function(e) {
      e.preventDefault();
      // hide the alert bar
      document.querySelector('.global-alert').style.display = 'none';
      // persist the dismissal in a cookie
      document.cookie = closeBtn.dataset.alertCookie;
    });
  });
</script>

<aside class="global-alert">
  {{ alert.message }}
  <a href="#"
     data-alert-cookie="{{ alert.cookie_text }}"
     class="close-alert">Dismiss</a>
</aside>

{% endif %}
```