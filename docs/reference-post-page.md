
### Class: \Conifer\Post\Page

> Class to represent WordPress pages.

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>get_blog_page()</strong> : <em>[\Conifer\Post\Page](#class-coniferpostpage)</em><br /><em>Get the Blog Landing Page.</em> |
| public static | <strong>get_by_template(</strong><em>\string</em> <strong>$template</strong>)</strong> : <em>Page the first page found matching the template</em><br /><em>Get a page by its template filename, relative to the theme root.</em> |
| public | <strong>get_title_from_nav_or_post(</strong><em>\Conifer\Post\Menu</em> <strong>$menu</strong>)</strong> : <em>string the title to display</em><br /><em>Get the top-level title to display from the nav structure, fall back on this Page object's title it it's outside the nav hierarchy.</em> |

*This class extends \Conifer\Post\Post*

*This class implements \Timber\CoreInterface*

