## Table of contents

- [\Conifer\Shortcode\AbstractBase (abstract)](#class-conifershortcodeabstractbase-abstract)
- [\Conifer\Shortcode\Button](#class-conifershortcodebutton)

<hr /><a id="class-conifershortcodeabstractbase-abstract"></a>
### Class: \Conifer\Shortcode\AbstractBase (abstract)

> Easily add shortcodes by calling register() on a class that implements this abstract class

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>register(</strong><em>string</em> <strong>$tag</strong>)</strong> : <em>void</em><br /><em>Register a shortcode with the given "tag". Tells WP to call render() to render the shortcode content.</em> |
| public | <strong>abstract render(</strong><em>array</em> <strong>$atts=array()</strong>, <em>\string</em> <strong>$content=`''`</strong>)</strong> : <em>void</em><br /><em>Output the result of this shortcode</em> |

<hr /><a id="class-conifershortcodebutton"></a>
### Class: \Conifer\Shortcode\Button

> Implements a custom button shortcode that allows the client to turn any <a> tag in an RTE into a styled button. Only works for content-style shortcodes with start/end tags, e.g. [button]<a href="...">Click Here</a>[/button]

| Visibility | Function |
|:-----------|:---------|
| public | <strong>render(</strong><em>array</em> <strong>$atts=array()</strong>, <em>\string</em> <strong>$html=`''`</strong>)</strong> : <em>string the modified <a> tag HTML</em><br /><em>Get the HTML for rendering the button link Acceptable params: * `class`: the class to add to the `<a>` tag (default is `"btn"`)</em> |

*This class extends [\Conifer\Shortcode\AbstractBase](#class-conifershortcodeabstractbase-abstract)*

