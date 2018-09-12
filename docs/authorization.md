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

The `UserRoleShortcodePolicy` class helps admins lock down content on pages and posts by using shortcodes so only specified user roles can see it. In your code, all you need to do is register the shortcode (this registers the shortcode with a default shortcode tag of `"protected"`):

```php
use Conifer\Authorization\UserRoleShortcodePolicy;

UserRoleShortcodePolicy::register();
```

The protected content can now be set up using a simple shortcode. All the site admin needs to do in the shortcode tag is specify what roles can see the content:

```
[protected role = "editor"]

Hello Editors

[/protected]
```

### Register your User Role Shortcode

The User Role Shortcode Class extends an abstract class called Shortcode Policy. This abstract class provides a basis for defining shortcodes that filter their content according to custom authorization logic.  

The Shortcode Policy Class extends an Abstract Policy Class. This Abstract class provides a basis for defining custom, template-level authorization logic

Finally, the Abstract Policy Class implements a custom interface called the Policy Interface, which is an abstract interface for a high-level authorization API. 

 ### Policy Interface
The Policy Interface contains two functions that all classes must implement. 
 ## Adopt
 ```php
public function adopt() : self; 
 ```
This is required so we can put this policy in place, typically via an action or filter.

## Register
```php
public static function register() : self;
```
This is required to create and adopt a new instance of this interface.

### Abstract Policy
The abstarct policy class has one function that will register the new policy interface and call the adopt function set up in the Shortcode Policy class
```php
/**
 * Create and adopt a new instance
 */
public static function register() : PolicyInterface {
return (new static())->adopt();
} 
```
### Shortcode Policy
The Shortcode policy class is where the logic is implemented so we can set up a shortcode.
It has one protected variable, $tag which is the name we want to use when implementing the shortcode in the content. In the construct method for this class you can pass a string with the name you want to use for the shortcode. If you do not set the string, by default the string 'protected' is used.  

```php
protected $tag;
  /**
   * Sets the shortcode tag for the new shortcode policy
   *
   * @param string $tag
   */
  public function __construct(string $tag = 'protected') {
      $this->tag = $tag;
  }
```

  The interface requires an adopt function, which will use the WordPress function add_shotcode to create a new shortcode with our tag, and the result of the class function enforce, which will return the content the user is allowed to see.

```php
  public function adopt() : PolicyInterface {
    add_shortcode($this->tag(), function(
      array $atts,
      string $content = ''
    ) : string {
      return $this->enforce($atts, $content, $this->get_user());
    });
```

  Our enforce function requires an array of attributes, string content and a wordpres user. depending on if the user is authorized or not, if will retun some form of the filtered content. 
  ```php
$authorized = $this->decide($atts, $content, $user);

return $authorized
    ? $this->filter_authorized($content)
    : $this->filter_unauthorized($content);
  }
  ```
  The get_user function will return a new Wordpress user which we wil need to have to see if they have the proper permissions. You can override this method to perform authorization against someone other than the current user. 
  ```php
  protected function get_user() : User {
    return new User();
  }
  ```
  The filter unauthorized function will get the filtered shortcode content to display to unauthorized users.  You can override this method to display something other than the empty string.
  ```php
  protected function filter_unauthorized(string $content) : string {
    return '';
  }
  ```

  The filter authorized function will get the filtered shortcode content to display to _authorized_ users.
  ```php
  protected function filter_authorized(string $content) : string {
    return $content;
  }
  ```

  ### User Role Shortcode Policy
  This class will build a ShortcodePolicy that filters content based on the current user's role. There is one function that we need to implement based on the shortcode policy class, decide, which contais the logic to determine if the user is authorized or not.  

  ```php
  public function decide(
    array $atts,
    string $content,
    User $user
  ) : bool {
    // Parse the role[s] attribute to determine which roles are authorized
    $roleAttr        = $atts['role'] ?? $atts['roles'] ?? 'administrator';
    $authorizedRoles = array_map('trim', explode(',', $roleAttr));

    // Get the user's roles for comparison
    // WP returns user roles in an idiosyncratic way: role names are keys and
    // `true` values means the user has that role. We just want to flatten
    // this to a simple array of role/capability strings
    // If the user is not logged in and has no roles the users wp_capabilities returns false and we want an empty array
    if ($user->meta('wp_capabilities') === false) {
      $userRoles = [];
    } else {
      $userRoles = array_keys(array_filter($user->meta('wp_capabilities')));
    }

    // Make sure the user has at least one authorized role
    return !empty(array_intersect($authorizedRoles, $userRoles));
  }
  ```
