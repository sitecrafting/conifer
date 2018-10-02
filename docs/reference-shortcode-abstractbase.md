
### Class: \Conifer\Shortcode\AbstractBase (abstract)

> Easily add shortcodes by calling register() on a class that implements this abstract class

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>register(</strong><em>string</em> <strong>$tag</strong>)</strong> : <em>void</em><br /><em>Register a shortcode with the given "tag". Tells WP to call render() to render the shortcode content.</em> |
| public | <strong>abstract render(</strong><em>array</em> <strong>$atts=array()</strong>, <em>\string</em> <strong>$content=`''`</strong>)</strong> : <em>void</em><br /><em>Output the result of this shortcode</em> |

