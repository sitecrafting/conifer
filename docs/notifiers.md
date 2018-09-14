# Notifiers

Notifiers are a way to send emails while keeping the message and destination address decoupled. Built-in notifier classes address the most common scenarios, while the abstract `EmailNotifier` class provides a simple way to create custom notifiers.

## The Notifier API

All notifiers share a common set of simple public methods for sending email: `notify_html()` and `notify_plaintext()`:

```php
use Conifer\Notifier\AdminNotifier;
$notifier = new AdminNotifier();

// send an HTML message with cat gif
$notifier->notify_html(
  'A Funny Thing Happened on the Internet',
  'O joy of joy! O dream of dreams!<br/><br/><img src="/cat.gif" alt="its a kitty"/>'
);

// send a plaintext message with URL
$notifier->notify_plaintext(
	'A Funny Thing Happened on the Internet',
  "O joy of joy! O dream of dreams!\n\nhttp://example.com/cat.gif"
);
```

Note that `notify()` is an alias for the `notify_html()` method.

You may have noticed that in the above example, **we never specify an email address.** This is by design: we want to decouple the destination from the sending, and from the *decision* to send an email, i.e. your business logic. Let's look at a more realistic example to see why.

### Example

Say you're [processing a form submission](/features/forms.md) for your semi-annual **Interwebz Cat Contezt**. You want to scan the submission for the word "doggo" in case the user submitted to the wrong contest by mistake (**Hecking Internet Doggo Contest** is over there, folks). Either way, you want to notify an admin that there's a submission. Assuming we want to send an HTML message, what would this look like with the vanilla `wp_mail()` function?

```php
// Lol/CatContestForm.php
namespace Lol;

use Conifer\Form\AbstractBase;

class CatContestForm extends AbstractBase {
  public function process(array $cat) {
    if (stripos($cat['description'], 'doggo') !== false) {
      wp_mail(
      	get_option('admin_email'),
        'doggo detected!',
        'o hai a new doggo submishun wuz detected <strong>just meow.</strong>',
        ['Content-Type: text/html; charset=UTF-8']
      );
      wp_mail(
      	$cat['email'],
        'ur submishunz bein revued n stuf',
        'o hai we saw ur kitty but <strong>maybe its a doggo??</strong>',
        'Content-Type: text/html; charset=UTF-8'
      );

      $this->submit_possible_doggo_to_review_process();
    
    } else {

      wp_mail(
        get_option('admin_email'),
        'kitty submishun!',
        'o hai a new kitty went up <strong>just meow.</strong>',
        ['Content-Type: text/html; charset=UTF-8']
      );
      wp_mail(
        $cat['email'],
        'ur submishunz up rite meow',
        'o hai we saw ur kitty <strong>n its gr8!!!1</strong>',
        ['Content-Type: text/html; charset=UTF-8']
      );

      $this->submit_and_publish_immediately();
    }
  }
}
```

This is not so bad, but we can simplify this code by creating our notifiers at the beginning, pulling those email addresses out of the 

```php
use Conifer\Form\AbstractBase;
use Conifer\Notifier\AdminNotifier;
use Conifer\Notifier\SimpleNotifier;

class CatContestForm extends AbstractBase {
  public function process(array $cat) {
    $adminNotifier = new AdminNotifier();
    $userNotifier  = new SimpleNotifier($cat['email']);
    
    if (stripos($cat['description'], 'doggo') !== false) {
      $adminNotifier->notify(
        'doggo detected!',
        'o hai a new doggo submishun wuz detected <strong>just meow.</strong>'
      );
      $userNotifier->notify(
        'ur submishunz bein revued n stuf',
        'o hai we saw ur kitty but <strong>maybe its a doggo??</strong>'
      );

      $this->submit_possible_doggo_to_review_process();
    
    } else {

      $adminNotifier->notify(
        'kitty submishun!',
        'o hai a new kitty went up <strong>just meow.</strong>'
      );
      $userNotifier->notify(
        'ur submishunz up rite meow',
        'o hai we saw ur kitty <strong>n its gr8!!!1</strong>'
      );

      $this->submit_and_publish_immediately();
    }
  }
}
```

Notice the distinct advantages of this code:

* It's more concise in general
* Our business logic (code inside the if/else) doesn't have to worry about the destination email: notifiers just know where they're sending
* The `notify()` method sets the `Content-Type` header for us

### How notifiers work

The `EmailNotifier` class has a single abstract method, `to()`:

```php
public abstract function to();
```

This means that all you need to do implement your own notifier is implement `to()`.

Note that `to()` does not take any arguments and does not have any return type hinting. This is because it can return any valid `$to` argument to [the `wp_mail()` function](https://developer.wordpress.org/reference/functions/wp_mail/), which can be a string or an array. Internally, however, `notify_html()` and `notify_plaintext()` will throw a `LogicException` exception if `to()` does not return one of these types.

The `notify_*` methods call `to()` and pass that value to `wp_mail()`.

## Admin Notifier

The `Conifer\Notifier\AdminNotifier` class knows how to get the main admin email from WordPress.

```php
$notifier = new Conifer\Notifier\AdminNotifier();
$notifier->notify('A thing happened', 'Just thought you\'d like to know.');
```

You don't have to specify an email address at all when using this class. It uses the email set in the WP admin UI in **Settings > General**.

## Simple Notifier

The `Conifer\Notifier\SimpleNotifier` class sends to arbitrary emails. You can pass the destination email as a string or an array to its constructor, and its `to()` method will simply return this value.

```php
$notifier = new Conifer\Notifier\SimpleNotifier('someone@example.com');
$nofitier->notify('Hello', 'Just saying hi. So, uh...hi.');

// send to multiple addresses
$notifier = new Conifer\Notifier\SimpleNotifier([
  'someone@example.com',
  'someone_else@example.com'
]);
$notifier->notify('Hello from Dr. Nick', 'Hi, everybody!');
```

## Building custom Notifiers

Say you're building a notification system with some complex logic, and you need to notify users affected by certain events. You might, for example, have an `AccountsEvent` class that talks to an external CRM and updates it in batches about happenings on your site:

```php
// some controller/AJAX action

$event = new AccountsEvent('event_code', ['some' => 'event data']);
$event->process();

// notify affected users somehow...

$logger->info('AccountsEvent: event_code', $event->get_info());
```

You want your `AccountsEvent` class to be concerned purely with telling the CRM that something has happened, so you don't want it to have to know about user notifications, too. At the same time, you want to keep this code as high-level as possible. What to do?

This is a good job for a custom notifier:

```php
// MyProject/EventNotifier.php
namespace MyProject;

use Conifer\Notifier\EmailNotifier;
use MyProject\AccountsEvent;
use Timber\User;

class EventNotifier extends EmailNotifier {
  protected $event;

  public function __construct(AccountsEvent $event = null) {
    $this->event = $event;
  }
  
  public function to() {    
    return array_map(function(User $user) {
      return $user->get_secondary_account_email();
    }, $event->get_affected_users());
  }
  
  public function notify_affected_users() {
    $this->notify(
    	"A {$this->event->name()} happened",
      "Here are the deets: {$this->event->friendly_description()}"
    );
  }
}
```

To notify affected users, we can simply pass our custom notifier the event itself. Note that here, to keep it high-level, we delegate building the subject and message to the notifier, too:

```php
// some controller/AJAX action

$event = new AccountsEvent('event_code', ['some' => 'event data']);
$event->process();

$notifier = new MyProject\EventNotifier($event);
$notifier->notify_affected_users();

$logger->info('AccountsEvent: event_code', $event->get_info());
```

Encapsulation FTW!