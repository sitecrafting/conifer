# Glossary

## config callback

The anonymous function you pass to the `Conifer\Site::configure()` method. See The Site Class docs.

Example:

```
/* functions.php */
use Conifer\Site;
$site = new Site();
$site->configure(function() {
  /* now we're in the config callback; call add_action() and stuff here... */
});
```

## Timber

The foundation of Conifer, Timber is a library plugin that implements the basic object-oriented API for posts, users, terms, etc. and brings Twig to WordPress.

## Twig

The templating system that Timber and, by extension, Conifer use to render markup.
