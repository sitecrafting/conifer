# Authorization API

Conifer provides a high-level API for defining authorization policies that declare who can see certain types of content, and what to do when users aren't authorized. For example, you might want to restrict public/logged-out users from viewing all pages using a certain template. Or, you might want to enable admins to protect certain pieces of content within a post by using a shortcode.

Implementing a custom authorization policy is as simple as implementing the abstract `adopt()` method:

```php
// SoupNaziPolicy.php
use Conifer\Authorization\AbstractPolicy;
use Timber\User;

class SoupNaziPolicy extends AbstractPolicy {
  public function adopt() : PolicyInterface {
    if ($this->user_is_named_jerry()) {
      die('no soup for you!');
    }

    return $this;
  }
    
  protected function user_is_named_jerry() {
    $user = new User();
    return strtolower($user->first_name) === 'jerry';
  }
}

// functions.php
$policy = new SoupNaziPolicy();
$policy->adopt();
```

This policy restricts users named Jerry from viewing the site (and getting soup). This is an extreme example, but you can imagine more useful (and lenient) policies that check various things about the current user.

Read on to learn about Conifer's built-in authorization policy classes and how to use them.

## User Role Shortcode Policy

The `UserRoleShortcodePolicy` class helps admins lock down content on pages and posts by using shortcodes so only specified user roles can see it. In your code, all you need to do is "adopt" the shortcode (this registers the shortcode with a default shortcode tag of `"protected"`):

```php
use Conifer\Authorization\UserRoleShortcodePolicy;

$policy = new UserRoleShortcodePolicy();
$policy->adopt();
```

The protected content can now be set up using a simple shortcode. All the site admin needs to do in the shortcode tag is specify what roles can see the content:

```
[protected role="editor"]

Hello Editors

[/protected]
```

## Custom authorization shortcodes

The `UserRoleShortcodePolicy` class extends an abstract class called `ShortcodePolicy`. This abstract class provides a basis for defining shortcodes that filter their content according to custom authorization logic.

For example, say that instead of checking a user's `role` we instead want to check a meta field called `moral_character`, and determine whether to show them the shortcode content based on the value of that field:

```
[protected character="upstanding"]

Upstanding Characters Only!

[/protected]
```

The `ShortcodePolicy` class actually implements the `adopt()` method for us: we just need to implement a `decide()` method to tell Conifer whether the current user is authorized to view the content inside the shortcode. Here's what that method signature looks like:

```php
public function decide(array $atts, string $content, User $user) : bool;
```

As you can see, we just need to return `true` or `false` to convey whether or not the user is authorized.

The `ShortcodePolicy` class knows to initialize the current user before passing it to decide, so we don't have to worry about that step. Implementing our test of moral character is a simple matter of grabbing user metadata:

```php
use Conifer\Authorization\ShortcodePolicy;
use Timber\User;

class MoralCharacterPolicy extends ShortcodePolicy {
  public function decide(array $atts, string $_, User $user) : bool {
    // get the required moral_character value from the shortcode atts,
    // displaying the content to any old scoundrel by default.
    $expectedCharacter = $atts['character'] ?? 'scoundrel';
    return $user->meta('moral_character') === $expectedCharacter;
  }
}
```

And that's it! "Upstanding Characters Only" will only display to users whose `moral_character` field is `"upstanding"`.

### Defining a custom shortcode tag

The first parameter to `UserRoleShortcodePolicy`'s constructor is the shortcode tag name, which defaults to `"protected"`. So to use a different shortcode tag, simply pass a different string to the constructor:

```php
$policy = new UserRoleShortcodePolicy('nice_try_hackerz');
```

Now your admins can gloat in the satisfaction of hiding content from hackerz using your amazing new custom shortcode:

```
[nice_try_hackerz role="editor"]

Hello Editors

[/nice_try_hackerz]
```

Note that this works for anything that extends `ShortcodePolicy`, not just the `UserRoleShortcodePolicy` class!

### Defining custom content for authorized vs. unauthorized users

The `ShortcodePolicy` class uses your concrete implementation of the `decide()` method to determine whether to display the shortcode content as-is, or nothing at all. But what if you *don't* want to display the content *as-is* to authorized users? What if, for example, you also want to remind authorized users of how nice you are to show them that content? Conversely, what if you want to remind unauthorized users of what they're missing out on?

Enter `filter_authorized()` and `filter_unauthorized()`. These are the methods that are called respectively to show, or not show, the shortcode content.

Let's build on our `MoralCharacterPolicy` example from above:

```php
use Conifer\Authorization\ShortcodePolicy;
use Timber\User;

class MoralCharacterPolicy extends ShortcodePolicy {
  public function decide(array $atts, string $_, User $user) : bool {
    // get the required moral_character value from the shortcode atts,
    // displaying the content to any old scoundrel by default.
    $expectedCharacter = $atts['character'] ?? 'scoundrel';
    return $user->meta('moral_character') === $expectedCharacter;
  }
}
```

To remind users with `"upstanding"` `moral_character` of how nice you are, simply override `filter_authorized()`:

```php
  protected function filter_authorized(string $content) : string {
    // wrap the regular content in vainglorious narcissism
    return "<strong>I usually don't tell anyone this, but...</strong>"
      . $content
      . "<br>You don't have to thank me for being so nice..."
      . "<strong>But you could, if you wanted to.</strong>";
  }
```

And to encourage the odd `"scoundrel"` on your site to clean up their act and attain a higher `moral_character`, override `filter_unathorized()`:

```php
  protected function filter_unauthorized(string $content) : string {
    // wrap the regular content in vainglorious narcissism
    return substr($content, 0, 25) . '...'
      . "<br><br>You can't see the rest and you should feel bad about it.";
  }
```

