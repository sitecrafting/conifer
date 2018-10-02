
### Class: \Conifer\Site

> Wrapper for any and all theme-specific behavior.

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string/int</em> <strong>$identifier=null</strong>)</strong> : <em>void</em><br /><em>Construct a Conifer Site object.</em> |
| public | <strong>add_default_twig_helpers()</strong> : <em>void</em><br /><em>Tell Conifer to add its default Twig functions when loading the Twig environment, before rendering a view</em> |
| public | <strong>add_to_context(</strong><em>array</em> <strong>$context</strong>)</strong> : <em>array the updated context</em><br /><em>Add arbitrary data to the site-wide context array</em> |
| public | <strong>add_twig_helper(</strong><em>\Conifer\Twig\HelperInterface</em> <strong>$helper</strong>)</strong> : <em>void</em><br /><em>Add a Twig helper that implements Twig filters and/or functions to the Twig environment that Timber uses to render views. Twig\HelperInterface that implements the functions/filters to add</em> |
| public | <strong>configure(</strong><em>\callable</em> <strong>$userDefinedConfig=null</strong>, <em>bool/\Conifer\boole/\boolean</em> <strong>$configureDefaults=true</strong>)</strong> : <em>Conifer\Site the Site object it was called on</em><br /><em>Configure any WordPress hooks and register site-wide components, such as nav menus from theme code. configuration code. Defaults to `true`.</em> |
| public | <strong>configure_default_twig_extensions()</strong> : <em>void</em><br /><em>Load Twig's String Loader and Debug extensions</em> |
| public | <strong>configure_defaults()</strong> : <em>void</em><br /><em>Configure useful defaults for Twig functions/filters, custom image sizes, shortcodes, etc.</em> |
| public | <strong>configure_twig_view_cascade()</strong> : <em>void</em><br /><em>Tell Timber/Twig which directories to look in for Twig view files.</em> |
| public | <strong>enqueue_script(</strong><em>\string</em> <strong>$scriptName</strong>, <em>\string</em> <strong>$fileName</strong>, <em>array</em> <strong>$dependencies=array()</strong>, <em>bool/string/bool/null</em> <strong>$version=true</strong>, <em>\boolean</em> <strong>$inFooter=true</strong>)</strong> : <em>void</em><br /><em>Enqueue a script within the script cascade path. Calls wp_enqueue_script transparently, except that it defaults to enqueueing in the footer instead of the header. the URL rendered in the <script> tag. Accepts any valid value of the $ver argument to `wp_enqueue_script`, plus the literal value `true`, which tells Conifer to look for an assets version file to use for cache-busting. Defaults to `true`. the same argument to the core `wp_enqueue_script` functions, this defaults to `true`.</em> |
| public | <strong>enqueue_style(</strong><em>\string</em> <strong>$stylesheetName</strong>, <em>\string</em> <strong>$fileName</strong>, <em>array</em> <strong>$dependencies=array()</strong>, <em>bool/string/bool/null</em> <strong>$version=true</strong>, <em>\string</em> <strong>$media=`'all'`</strong>)</strong> : <em>void</em><br /><em>Enqueue a stylesheet within the style cascade path. Calls `wp_enqueue_style` transparently. the URL rendered in the <link> tag. Accepts any valid value of the $ver argument to `wp_enqueue_style`, plus the literal value `true`, which tells Conifer to look for an assets version file to use for cache-busting. Defaults to `true`. passed transparently to `wp_enqueue_style`. Defaults to "all" (as does `wp_enqueue_style` itself).</em> |
| public | <strong>find_file(</strong><em>\string</em> <strong>$file</strong>, <em>array</em> <strong>$dirs</strong>)</strong> : <em>the path of the first file found. If $file is not found in any directory, returns the empty string.</em><br /><em>Search an arbitrary list of directories for $file and return the first existent file path found</em> |
| public | <strong>get_assets_version()</strong> : <em>the hash for</em><br /><em>Get the build-tool-generated hash for global assets</em> |
| public | <strong>get_assets_version_filepath()</strong> : <em>string the absolute path to the assets version file</em><br /><em>Get the filepath to the assets version file</em> |
| public | <strong>get_context_with_post(</strong><em>\Conifer\Post\Post</em> <strong>$post</strong>)</strong> : <em>array the Timber context</em><br /><em>Get the current Timber context, with the "post" index set to $post</em> |
| public | <strong>get_context_with_posts(</strong><em>array</em> <strong>$posts</strong>)</strong> : <em>array the Timber context</em><br /><em>Get the current Timber context, with the "posts" index set to $posts</em> |
| public | <strong>get_script_directory_cascade()</strong> : <em>array</em><br /><em>Get the array of directories where Conifer will look for JavaScript files when `Site::enqueue_script()` is called.</em> |
| public | <strong>get_script_uri(</strong><em>\string</em> <strong>$file</strong>)</strong> : <em>the script's full URI. If $file is not found in any directory, returns the empty string.</em><br /><em>Get the full URI for a script file. Returns the URI for the first file it finds in the script directory cascade.</em> |
| public | <strong>get_style_directory_cascade()</strong> : <em>array</em><br /><em>Get the array of directories where Conifer will look for CSS files when `Site::enqueue_style()` is called.</em> |
| public | <strong>get_stylesheet_uri(</strong><em>\string</em> <strong>$file</strong>)</strong> : <em>the stylesheet's full URI. If $file is not found in any directory, returns the empty string.</em><br /><em>Get the full URI for a stylesheet. Returns the URI for the first file it finds in the style directory cascade.</em> |
| public | <strong>get_theme_file(</strong><em>\string</em> <strong>$file</strong>)</strong> : <em>string the absolute path to the file</em><br /><em>Get an arbitrary file, relative to the theme directory</em> |
| public | <strong>get_twig_with_helper(</strong><em>\Twig_Environment</em> <strong>$twig</strong>, <em>\Conifer\Twig\HelperInterface</em> <strong>$helper</strong>)</strong> : <em>\Conifer\Twig_Environment</em><br /><em>Add any filters/functions implemented by `$helper` to the Twig instance `$twig`. filters/functions to add</em> |
| public | <strong>get_view_directory_cascade()</strong> : <em>array</em><br /><em>Get the array of directories where Twig should look for view files.</em> |
| public | <strong>set_script_directory_cascade(</strong><em>array</em> <strong>$cascade</strong>)</strong> : <em>void</em><br /><em>Set the array of directories where Conifer will look for CSS files when `Site::enqueue_style()` is called. in the order declared.</em> |
| public | <strong>set_style_directory_cascade(</strong><em>array</em> <strong>$cascade</strong>)</strong> : <em>void</em><br /><em>Set the array of directories where Conifer will look for CSS files when `Site::enqueue_style()` is called. in the order declared.</em> |
| public | <strong>set_view_directory_cascade(</strong><em>array</em> <strong>$cascade</strong>)</strong> : <em>void</em><br /><em>Set the array of directories where Twig should look for view files when `render` or `compile` is called. *NOTE: This will have no effect without also running `configure_twig_view_cascade`, or equivalent.*</em> |
###### Examples of Site::__construct()
```
```php
  use Conifer\Site;
// non-multisite setup:
  $site = new Site();
// multisite setup:
  $site = new Site(1);
  ```
```

*This class extends \Timber\Site*

*This class implements \Timber\CoreInterface*

