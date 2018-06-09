# Posts and Post Types

To leverage the power of Conifer, extend the `Conifer\Post\Post` class rather than the more generic `Timber\Post`. Since `Conifer\Post\Post` in turn extends `Timber\Post`, you get all the benefits of Timber's class, with some added functionality for querying posts of specific types, along with other goodies.

Conifer comes with a few built-in post classes that extend `Conifer\Post\Post`. These are:

* `BlogPost` for representing WP posts of type `post`
* `FrontPage` for representing the homepage of the site
* `Page` for representing WP posts of type `page`

